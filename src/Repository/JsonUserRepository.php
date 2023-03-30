<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository;

use Jayrods\ScubaPHP\Controller\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Entity\User;
use Jayrods\ScubaPHP\Repository\UserRepository;

class JsonUserRepository implements UserRepository
{
    use PasswordHandler;

    /**
     * 
     */
    private const USER_DATA_PATH = DATABASE_PATH . 'users.json';

    /**
     * 
     */
    private function loadUsers(): array
    {
        $users = file_get_contents(self::USER_DATA_PATH);
        $users = json_decode($users);
        $users = $this->hidrateUser($users);

        return $users;
    }

    /**
     * 
     */
    private function flushUsers(array $users): int|bool
    {
        $users = json_encode($users);
        return file_put_contents(self::USER_DATA_PATH, $users);
    }

    /**
     * 
     */
    public function save(User $user): int|bool
    {
        return $this->findByEmail($user->email())
            ? $this->update($user)
            : $this->create($user);
    }

    /**
     * 
     */
    public function create(User $user): int|bool
    {
        $users = $this->loadUsers();
        $users[] = $user;

        return $this->flushUsers($users);
    }

    /**
     * 
     */
    public function update(User $currentUser): int|bool
    {
        $users = $this->loadUsers();

        foreach ($users as $i => $user) {
            if ($user->email() === $currentUser->email()) {
                $users[$i] = $currentUser;

                return $this->flushUsers($users);
            }
        }

        return false;
    }

    /**
     * 
     */
    public function remove(User $currentUser): int|bool
    {
        $users = $this->loadUsers();

        foreach ($users as $i => $user) {
            if ($user->email() === $currentUser->email()) {
                unset($users[$i]);

                return $this->flushUsers([...$users]);
            }
        }

        return false;
    }

    /**
     * 
     */
    public function all(): array
    {
        return $this->loadUsers();
    }

    /**
     * 
     */
    public function findByEmail(string $email): User|false
    {
        $users = $this->loadUsers();

        foreach ($users as $user) {
            if ($user->email() === $email) {
                return $user;
            }
        }

        return false;
    }

    /**
     * 
     */
    private function hidrateUser(array $dataset): array
    {
        $users = [];

        foreach ($dataset as $userData) {
            $users[] = new User(
                name: $userData->name,
                email: $userData->email,
                password: $userData->password,
                verified: $userData->verified
            );
        }

        return $users;
    }
}
