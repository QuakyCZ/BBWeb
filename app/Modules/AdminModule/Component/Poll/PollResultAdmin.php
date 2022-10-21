<?php

namespace App\Modules\AdminModule\Component\Poll;

use App\Modules\ClientModule\Component\Poll\PollResult;
use App\Repository\Primary\PollOptionRepository;
use App\Repository\Primary\PollParticipantRepository;
use App\Repository\Primary\UserRepository;
use Nette\Application\BadRequestException;

class PollResultAdmin extends PollResult
{
    /**
     * @return void
     * @throws BadRequestException
     */
    public function render(): void
    {
        $poll = $this->getPoll();
        if ($poll === null) {
            throw new BadRequestException();
        }

        $participants = $poll->related(PollParticipantRepository::TABLE_NAME)
            ->where(PollParticipantRepository::COLUMN_POLL_OPTION_ID . ' IS NOT NULL')
            ->fetchAll();

        $usersByOption = [];
        foreach ($participants as $participant) {
            $usersByOption[$participant->ref(PollOptionRepository::TABLE_NAME)[PollOptionRepository::COLUMN_ID]][] = $participant->ref(UserRepository::TABLE_NAME)[UserRepository::COLUMN_USERNAME];
        }

        $this->template->usersByOption = $usersByOption;

        parent::render();
    }
}

interface IPollResultAdminFactory {
    /**
     * @param int|null $id
     * @return PollResultAdmin
     */
    public function create(?int $id): PollResultAdmin;
}