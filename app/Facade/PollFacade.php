<?php

namespace App\Facade;

use App\Repository\Primary\PollOptionRepository;
use App\Repository\Primary\PollParticipantRepository;
use App\Repository\Primary\PollRepository;
use App\Repository\Primary\PollRoleRepository;
use App\Repository\Primary\UserRepository;
use App\Repository\Primary\UserRoleRepository;
use DateTime;
use http\Client\Curl\User;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

class PollFacade
{
    public function __construct(
        private PollRepository $pollRepository,
        private PollRoleRepository $pollRoleRepository,
        private PollOptionRepository $pollOptionRepository,
        private PollParticipantRepository $pollParticipantRepository,
        private UserRoleRepository $userRoleRepository,
        private UserRepository $userRepository,
    ) {
    }


    /**
     * Kontrola, jestli uživatel má povolení hlasovat.
     * @param int $pollId Id hlasování
     * @param int $userId Id uživatele
     * @return bool
     */
    public function isAllowed(int $pollId, int $userId): bool
    {
        $poll = $this->pollRepository->getRow($pollId);
        if ($poll === null) {
            return false;
        }

        if (!$poll[PollRepository::COLUMN_IS_PRIVATE]) {
            return true;
        }

        return $this->pollRoleRepository->database
            ->query(
                "(SELECT role_id as id FROM poll_role WHERE poll_id = ?) INTERSECT (SELECT role_id FROM user_role WHERE user_id = ?)",
                $pollId,
                $userId
            )
            ->fetch() !== null;
    }


    /**
     * @throws BadRequestException
     */
    public function savePoll(ArrayHash $formValues, int $userId, ?int $pollId = null): void
    {
        if ($pollId !== null && $this->pollRepository->isActive($pollId)) {
            throw new BadRequestException('Nelze upravit aktivní hlasování.');
        }

        $this->pollRepository->runInTransaction(function () use ($formValues, $userId, $pollId) {
            $dates = explode(' - ', $formValues['active']);


            if (count($dates) !== 2) {
                throw new BadRequestException('Špatný formát od do.');
            }

            $from = DateTime::createFromFormat('d. m. Y H:i', $dates[0]);
            $to = DateTime::createFromFormat('d. m. Y H:i', $dates[1]);

            if (!$from || !$to) {
                throw new BadRequestException('Špatný formát od do.');
            }

            /** @var ActiveRow $poll */
            $poll = $this->pollRepository->save([
                PollRepository::COLUMN_ID => $pollId,
                PollRepository::COLUMN_QUESTION => $formValues['question'],
                PollRepository::COLUMN_CREATED_USER_ID => $userId,
                PollRepository::COLUMN_IS_PRIVATE => $formValues[PollRepository::COLUMN_IS_PRIVATE],
                PollRepository::COLUMN_FROM => $from,
                PollRepository::COLUMN_TO => $to,
                PollRepository::COLUMN_ICON => $formValues[PollRepository::COLUMN_ICON],
            ]);

            $pollId = $poll[PollRepository::COLUMN_ID];

            $this->pollParticipantRepository->deleteParticipants($pollId);

            $this->createOptions($pollId, $formValues['options'], $userId);

            if ($formValues[PollRepository::COLUMN_IS_PRIVATE]) {
                $this->allowUsers($pollId, $formValues['user_ids']);
                $this->allowRoles($pollId, $formValues['role_ids']);
            }
        });
    }

    /**
     * @param int $pollId
     * @param ArrayHash $options
     * @param int $userId
     * @return void
     */
    private function createOptions(int $pollId, ArrayHash $options, int $userId): void
    {
        $this->pollOptionRepository->findBy([
            PollOptionRepository::COLUMN_POLL_ID => $pollId
        ])->delete();

        foreach ($options as $option) {
            $this->pollOptionRepository->save([
                PollOptionRepository::COLUMN_POLL_ID => $pollId,
                PollOptionRepository::COLUMN_TEXT => $option['text'],
                PollOptionRepository::COLUMN_CREATED_USER_ID => $userId
            ]);
        }
    }

    /**
     * @param int $pollId
     * @param int[] $roleIds
     * @return void
     */
    private function allowRoles(int $pollId, array $roleIds): void
    {
        // Odstranit stávající role
        $this->pollRoleRepository->findBy([PollRoleRepository::COLUMN_POLL_ID => $pollId])->delete();

        // Povolit nové role
        foreach ($roleIds as $roleId) {
            $this->pollRoleRepository->save([
                PollRoleRepository::COLUMN_POLL_ID => $pollId,
                PollRoleRepository::COLUMN_ROLE_ID => $roleId
            ]);
        }
    }

    /**
     * @param int $pollId
     * @param int[] $userIds
     * @return void
     */
    private function allowUsers(int $pollId, array $userIds): void
    {
        foreach ($userIds as $userId) {
            $this->pollParticipantRepository->save([
                PollParticipantRepository::COLUMN_POLL_ID => $pollId,
                PollParticipantRepository::COLUMN_POLL_OPTION_ID => null,
                PollParticipantRepository::COLUMN_USER_ID => $userId,
                PollParticipantRepository::COLUMN_IS_EXTRA => true
            ]);
        }
    }

    /**
     * @param int $userId
     * @return ActiveRow[][]
     */
    public function getPollsForUser(int $userId): array
    {
        $roles = $this->userRoleRepository->getUsersRoles($userId)->fetchPairs(UserRoleRepository::COLUMN_ROLE_ID, UserRoleRepository::COLUMN_ROLE_ID);


        $active = $this->pollRepository->getActivePolls()
            ->whereOr([
                PollRepository::COLUMN_IS_PRIVATE => 0,
                ':' . PollParticipantRepository::TABLE_NAME . '.' . PollParticipantRepository::COLUMN_USER_ID => $userId,
                ':' . PollRoleRepository::TABLE_NAME . '.' . PollRoleRepository::COLUMN_ROLE_ID . ' IN (?)' => $roles
            ])
            ->fetchAll();

        $finished = $this->pollRepository->getFinishedPolls()
            ->whereOr([
                PollRepository::COLUMN_IS_PRIVATE => 0,
                ':' . PollParticipantRepository::TABLE_NAME . '.' . PollParticipantRepository::COLUMN_USER_ID => $userId,
                ':' . PollRoleRepository::TABLE_NAME . '.' . PollRoleRepository::COLUMN_ROLE_ID . ' IN (?)' => $roles
            ])->fetchAll();

        return [
            'active' => $active,
            'finished' => $finished
        ];
    }


    /**
     * @throws BadRequestException
     */
    public function vote(int $userId, int $optionId): void
    {
        $optionRow = $this->pollOptionRepository->getRow($optionId);
        if ($optionRow === null) {
            throw new BadRequestException();
        }

        $pollRow = $optionRow->ref(PollRepository::TABLE_NAME);

        if ($pollRow === null) {
            throw new BadRequestException('Hlasování neexistuje.');
        }

        $participant = $this->pollParticipantRepository->findBy([
            PollParticipantRepository::COLUMN_USER_ID => $userId,
            PollParticipantRepository::COLUMN_POLL_ID => $pollRow[PollRepository::COLUMN_ID]
        ])->fetch();

        if ($participant !== null) {
            if ($participant[PollParticipantRepository::COLUMN_POLL_OPTION_ID] !== null) {
                throw new BadRequestException('Už jsi hlasoval.');
            }
            $participant->update([
                PollParticipantRepository::COLUMN_POLL_OPTION_ID => $optionId,
                PollParticipantRepository::COLUMN_CHANGED => new DateTime()
            ]);
            return;
        }

        $this->pollParticipantRepository->save([
            PollParticipantRepository::COLUMN_POLL_ID => $pollRow[PollRepository::COLUMN_ID],
            PollParticipantRepository::COLUMN_USER_ID => $userId,
            PollParticipantRepository::COLUMN_POLL_OPTION_ID => $optionId,
            PollParticipantRepository::COLUMN_CHANGED => new DateTime()
        ]);
    }

    public function hasVoted(int $pollId, int $userId): bool
    {
        return $this->pollParticipantRepository->findBy([
            PollParticipantRepository::COLUMN_POLL_ID => $pollId,
            PollParticipantRepository::COLUMN_USER_ID => $userId
        ])
            ->where(PollParticipantRepository::COLUMN_POLL_OPTION_ID . ' IS NOT NULL')->fetch() !== null;
    }

    /**
     * TODO: Udělat samotnou třídu pro poll
     * @param int $id
     * @return ?ActiveRow
     */
    public function getPoll(int $id): ?ActiveRow
    {
        return $this->pollRepository->getRow($id);
    }

    /**
     * [
     *  'Text' => number_of_votes
     * ]
     * @param int $pollId
     * @return int[]
     */
    public function getResults(int $pollId): array
    {
        $votesAlias = 'votes';

        $optionVotes = $this->pollOptionRepository->findBy([
            PollOptionRepository::TABLE_NAME . '.' . PollOptionRepository::COLUMN_POLL_ID => $pollId
        ])
            ->select(PollOptionRepository::TABLE_NAME . '.' . PollOptionRepository::COLUMN_ID)
            ->select(PollOptionRepository::TABLE_NAME . '.' . PollOptionRepository::COLUMN_TEXT)
            ->select('COUNT(:'. PollParticipantRepository::TABLE_NAME . '.' . PollParticipantRepository::COLUMN_POLL_OPTION_ID . ') AS ' . $votesAlias)
            ->group(PollOptionRepository::TABLE_NAME . '.' . PollOptionRepository::COLUMN_ID)
            ->order($votesAlias . ' DESC, ' . PollOptionRepository::TABLE_NAME . '.' . PollOptionRepository::COLUMN_TEXT . ' ASC')
            ->fetchAll();

        $result = [];

        foreach ($optionVotes as $option) {
            $result[$option[PollOptionRepository::COLUMN_ID]] = [
                'text' => $option[PollOptionRepository::COLUMN_TEXT],
                'votes' => $option[$votesAlias]
            ];
        }

        return $result;
    }


    public function getNumberOfAllParticipants(int $pollId): int
    {
        if (!$this->getPoll($pollId)[PollRepository::COLUMN_IS_PRIVATE]) {
            return $this->userRepository->findAll()->select('COUNT(*) AS c')->fetch()['c'] ?? 0;
        }
        return $this->pollRepository->database->query(
            "
            SELECT COUNT(*) as participants FROM " . UserRepository::TABLE_NAME . " WHERE ". UserRepository::COLUMN_ID ." IN (
                (SELECT pp.user_id FROM poll_participant pp WHERE pp.poll_id = ?)
                UNION 
                (SELECT ur.user_id FROM poll_role pr JOIN user_role ur ON ur.role_id=pr.role_id WHERE pr.poll_id = ?)
            ) AND not_deleted = 1;
        ",
            $pollId,
            $pollId
        )->fetch()['participants'];
    }
}
