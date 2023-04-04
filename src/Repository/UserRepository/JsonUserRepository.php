<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository\UserRepository;

use Jayrods\ScubaPHP\Entity\User\Role;
use Jayrods\ScubaPHP\Entity\User\User;
use Jayrods\ScubaPHP\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Repository\UserRepository\UserRepository;

class JsonUserRepository implements UserRepository
{
    use PasswordHandler;

    /**
     * 
     */
    private const USER_DATA_PATH = DATABASE_PATH . 'user.json';

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
    public function save(User $user): bool
    {
        return $this->find($user->id())
            ? $this->update($user)
            : $this->create($user);
    }

    /**
     * 
     */
    private function create(User $user): bool
    {
        $users = $this->loadUsers();
        $users[] = $user;

        return (bool) $this->flushUsers($users);
    }

    /**
     * 
     */
    private function update(User $currentUser): bool
    {
        $users = $this->loadUsers();
        
        foreach ($users as $i => $user) {
            if ($user->id() === $currentUser->id()) {
                $currentUser->updateDate();
                $users[$i] = $currentUser;

                return (bool) $this->flushUsers($users);
            }
        }

        return false;
    }

    /**
     * 
     */
    public function remove(User $currentUser): bool
    {
        $users = $this->loadUsers();

        foreach ($users as $i => $user) {
            if ($user->id() === $currentUser->id()) {
                unset($users[$i]);

                return (bool) $this->flushUsers([...$users]);
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
    public function find(int $id): User|false
    {
        $users = $this->loadUsers();

        foreach ($users as $user) {
            if ($user->id() === $id) {
                return $user;
            }
        }

        return false;
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
                emailVerified: $userData->emailVerified,
                password: $userData->password,
                id: $userData->id,
                picture: $userData->picture,
                phone: $userData->phone,
                city: $userData->city,
                about: $userData->about,
                role: Role::from($userData->role),
                created_at: $userData->created_at,
                updated_at: $userData->updated_at,
            );
        }

        return $users;
    }
}
