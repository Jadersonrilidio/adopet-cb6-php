<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository\PetRepository;

use Jayrods\ScubaPHP\Entity\Pet\Pet;

interface PetRepository
{
    /**
     * 
     */
    public function save(Pet $pet): bool;

    /**
     * 
     */
    public function updateStatus(Pet $pet): bool;

    /**
     * 
     */
    public function remove(Pet $pet): bool;

    /**
     * 
     */
    public function all(): array;

    /**
     * 
     */
    public function find(int $id): Pet|false;
}
