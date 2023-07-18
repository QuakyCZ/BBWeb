<?php

namespace App\Repository\Primary;

use App\Model\ContextLocator;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Security\Passwords;

class UserRepository extends PrimaryRepository
{
    public const TABLE_NAME = "user";

    public const COLUMN_ID = 'id';
    public const COLUMN_USERNAME = 'username';
    public const COLUMN_EMAIL = 'email';
    public const COLUMN_PASSWORD = 'password';
    public const COLUMN_VERIFICATION_TOKEN = 'verification_token';
    public const COLUMN_ACTIVE = 'active';

    public const COLUMN_CREATED = 'created';
    public const COLUMN_CREATED_USER_ID = 'created_user_id';
    public const COLUMN_REMOVED_AT = 'removed_at';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    public const COLUMN_SUB_REQUIRED = 'sub_required';


    protected string $tableName = self::TABLE_NAME;

    public function __construct(
        ContextLocator $contextLocator,
        private Passwords $passwords
    ) {
        parent::__construct($contextLocator);
    }

    /**
     * @return Selection
     */
    public function getForListing(): Selection
    {
        return $this->findAll()
            ->select(implode(', ', [
                self::COLUMN_ID, self::COLUMN_USERNAME, self::COLUMN_EMAIL, self::COLUMN_CREATED
            ]));
    }

    /**
     * @param string $email
     * @return ActiveRow|null
     */
    public function findByEmail(string $email): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_EMAIL => $email
        ])->fetch();
    }

    /**
     * @param string $username
     * @return ActiveRow|null
     */
    public function findByUsername(string $username): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_USERNAME => $username,
            self::COLUMN_ACTIVE => 1
        ])->fetch();
    }

    /**
     * @param string $login
     * @return ActiveRow|null
     */
    public function findByUsernameOrEmail(string $login): ?ActiveRow
    {
        return $this->findAll()
            ->where('(username=? OR email=?) AND active=1', $login, $login)
            ->fetch();
    }

    /**
     * @param int $id
     * @param bool $value
     * @return int
     */
    public function setActive(int $id, bool $value): int
    {
        return $this->findBy(['id' => $id], true)->update(['active' => $value]);
    }

    /**
     * Vrátí aktualizovaný řádek s uživatelem nebo null pokud neodpovídá token
     * @param int $userId
     * @param string $token
     * @return ActiveRow|null
     */
    public function verifyUser(int $userId, string $token): ?ActiveRow
    {
        $user = $this->findBy([
            self::COLUMN_ID => $userId,
            self::COLUMN_VERIFICATION_TOKEN => $token,
        ])->fetch();

        if ($user === null || $user[self::COLUMN_ACTIVE] === true) {
            return null;
        }

        $user->update([
            self::COLUMN_ACTIVE => 1,
            self::COLUMN_VERIFICATION_TOKEN => null
        ]);

        return $user;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isUserActive(int $userId): bool
    {
        return $this->findBy([
            self::COLUMN_ID => $userId,
            self::COLUMN_ACTIVE => 1
        ])->fetch() !== null;
    }

    /**
     * @param int $id
     * @param bool $onlyActive
     * @return ActiveRow|null
     */
    public function findById(int $id, bool $onlyActive = true): ?ActiveRow
    {
        return $this->findBy([
            self::COLUMN_ID => $id,
            self::COLUMN_ACTIVE => $onlyActive
        ])->fetch();
    }


    /**
     * @param bool $onlyAdmins
     * @return array
     */
    public function fetchForChoiceControl(bool $onlyAdmins = false): array
    {
        $result = $this->findAll();
        if ($onlyAdmins) {
            $result->where(':' . UserRoleRepository::TABLE_NAME . '.' . UserRoleRepository::COLUMN_ROLE_ID . '.' . RoleRepository::COLUMN_NAME, 'ADMIN');
        }
        return $result->fetchPairs('id', 'username');
    }


    /**
     * @param ActiveRow $row
     * @param string $salt
     * @return string
     */
    public function getVerificationToken(ActiveRow $row, string $salt = ""): string
    {
        return $this->passwords->hash($row[self::COLUMN_ID] . $row[self::COLUMN_USERNAME] . $row[self::COLUMN_EMAIL] . $row[self::COLUMN_CREATED]->getTimestamp() . $salt);
    }


    /**
     * @param string $token
     * @param ActiveRow $row
     * @param string $salt
     * @return bool
     */
    public function checkVerificationToken(string $token, ActiveRow $row, string $salt = ""): bool
    {
        return $this->passwords->verify($row[self::COLUMN_ID] . $row[self::COLUMN_USERNAME] . $row[self::COLUMN_EMAIL] . $row[self::COLUMN_CREATED]->getTimestamp() . $salt, $token);
    }


    /**
     * @param $password
     * @return string
     */
    public function getPasswordHash($password): string
    {
        return $this->passwords->hash($password);
    }


    /**
     * @return Selection
     */
    public function getRequiredBroadcastersForSubscription(): Selection
    {
        return $this->findBy([
            self::COLUMN_SUB_REQUIRED => 1
        ]);
    }
}
