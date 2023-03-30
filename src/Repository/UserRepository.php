<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository;

use Jayrods\ScubaPHP\Entity\User;

interface UserRepository
{
    /**
     * 
     */
    public function save(User $user): int|bool;

    /**
     * 
     */
    public function create(User $user): int|bool;

    /**
     * 
     */
    public function update(User $currentUser): int|bool;

    /**
     * 
     */
    public function remove(User $currentUser): int|bool;

    /**
     * 
     */
    public function all(): array;

    /**
     * 
     */
    public function findByEmail(string $email): User|false;
}
