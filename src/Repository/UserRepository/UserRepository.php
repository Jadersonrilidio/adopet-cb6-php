<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository\UserRepository;

use Jayrods\ScubaPHP\Entity\User\User;

interface UserRepository
{
    /**
     * 
     */
    public function save(User $user): bool;

    /**
     * 
     */
    public function remove(User $user): bool;

    /**
     * 
     */
    public function all(): array;

    /**
     * 
     */
    public function find(int $id): User|false;

    /**
     * 
     */
    public function findByEmail(string $email): User|false;
}
