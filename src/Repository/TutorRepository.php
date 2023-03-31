<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository;

use Jayrods\ScubaPHP\Entity\Tutor;

interface TutorRepository
{
    /**
     * 
     */
    public function save(Tutor $tutor): int|bool;

    /**
     * 
     */
    public function create(Tutor $tutor): int|bool;

    /**
     * 
     */
    public function update(Tutor $currentTutor): int|bool;

    /**
     * 
     */
    public function remove(Tutor $currentTutor): int|bool;

    /**
     * 
     */
    public function all(): array;

    /**
     * 
     */
    public function find(string $uid): Tutor|false;

    /**
     * 
     */
    public function findByEmail(string $email): Tutor|false;
}
