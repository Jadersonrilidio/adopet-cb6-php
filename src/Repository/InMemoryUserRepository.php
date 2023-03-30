<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository;

use Jayrods\ScubaPHP\Controller\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Entity\User;
use Jayrods\ScubaPHP\Repository\UserRepository;

class InMemoryUserRepository implements UserRepository
{
    use PasswordHandler;

    /**
     * @var User[]
     */
    private static array $users = [];

    /**
     * 
     */
    public function save(User $user): bool
    {
        return $this->findByEmail($user->email())
            ? $this->update($user)
            : $this->create($user);
    }

    /**
     * 
     */
    public function create(User $user): bool
    {
        $hash = hash('md5', $user->email());

        self::$users[$hash] = $user;

        return true;
    }

    /**
     * 
     */
    public function update(User $user): bool
    {
        if ($this->findByEmail($user->email())) {
            $hash = hash('md5', $user->email());
            self::$users[$hash] = $user;

            return true;
        }

        return false;
    }

    /**
     * 
     */
    public function remove(User $user): bool
    {
        if ($this->findByEmail($user->email())) {
            $hash = hash('md5', $user->email());
            unset(self::$users[$hash]);

            return true;
        }

        return false;
    }

    /**
     * 
     */
    public function all(): array
    {
        return self::$users;
    }

    /**
     * 
     */
    public function findByEmail(string $email): User|false
    {
        $hash = hash('md5', $email);

        return array_key_exists($hash, self::$users) ? self::$users[$hash] : false;
    }
}
