<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository\TutorRepository;

use Jayrods\ScubaPHP\Entity\Tutor;

interface TutorRepository
{
    /**
     * 
     */
    public function save(Tutor $tutor): bool;

    /**
     * 
     */
    public function remove(Tutor $tutor): bool;

    /**
     * 
     */
    public function all(): array;

    /**
     * 
     */
    public function find(int $id): Tutor|false;

    /**
     * 
     */
    public function findByEmail(string $email): Tutor|false;
}
