<?php

namespace App\Security;

use Nette;
use Nette\Security\SimpleIdentity;

class Authenticator implements \Nette\Security\Authenticator
{

    private $database;
    private $passwords;

    public function __construct(
        Nette\Database\Explorer  $database,
        Nette\Security\Passwords $passwords
    )
    {
        $this->database = $database;
        $this->passwords = $passwords;
    }

    public function authenticate(string $email, string $password): SimpleIdentity
    {
        $row = $this->database->table('user')
            ->where('email=? AND active=1 AND not_deleted=1', $email)
            ->fetch();

        if (!$row)
        {
            throw new Nette\Security\AuthenticationException('User not found.');
        }

        if (!$this->passwords->verify($password, $row->password))
        {
            throw new Nette\Security\AuthenticationException('Invalid password.');
        }

        $roleRows = $this->database->table('user_role')
            ->where('user_id', $row['id'])
            ->fetchAll();

        $roles = [];

        foreach ($roleRows as $role)
        {
            $roles[] = $role->role->name;
        }

        return new SimpleIdentity(
            $row->id,
            $roles, // nebo pole více rolí
            [
                'name' => $row->username,
                'email' => $row->email,
                'registrationDate' => $row->created
                ]
        );
    }
}