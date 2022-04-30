<?php

namespace App\Security;

use App\Repository\Primary\UserDetailsRepository;
use App\Repository\Primary\UserRepository;
use App\Repository\Primary\UserRoleRepository;
use Nette;
use Nette\Security\SimpleIdentity;

class Authenticator implements \Nette\Security\Authenticator
{

    private $database;
    private $passwords;

    private UserRepository $userRepository;
    private UserRoleRepository $userRoleRepository;
    private UserDetailsRepository $userDetailsRepository;

    public function __construct(
        Nette\Database\Explorer  $database,
        Nette\Security\Passwords $passwords,
        UserRepository $userRepository,
        UserRoleRepository $userRoleRepository,
        UserDetailsRepository $userDetailsRepository
    )
    {
        $this->database = $database;
        $this->passwords = $passwords;
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userDetailsRepository = $userDetailsRepository;
    }

    public function authenticate(string $email, string $password): SimpleIdentity
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user)
        {
            throw new Nette\Security\AuthenticationException('User not found.');
        }


        if (!$this->passwords->verify($password, $user->password))
        {
            throw new Nette\Security\AuthenticationException('Invalid password.');
        }

        $roles = $this->userRoleRepository->getUsersRoleNames($user->id);

        $detailsRow = $this->userDetailsRepository->getDetails($user->id);

        return new SimpleIdentity(
            $user->id,
            $roles, // nebo pole více rolí
            [
                'name' => $user->username,
                'email' => $user->email,
                'registrationDate' => $user->created,
                'details' => $detailsRow->fetch()->toArray()
                ]
        );
    }
}