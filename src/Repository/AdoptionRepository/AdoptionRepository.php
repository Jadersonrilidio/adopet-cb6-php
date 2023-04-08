<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository\AdoptionRepository;

use Jayrods\ScubaPHP\Entity\Adoption\Adoption;

interface AdoptionRepository
{
    /**
     * 
     */
    public function save(Adoption $adoption): bool;

    /**
     * 
     */
    public function remove(Adoption $adoption): bool;

    /**
     * 
     */
    public function all(): array;

    /**
     * 
     */
    public function find(int $id): Adoption|false;
}
