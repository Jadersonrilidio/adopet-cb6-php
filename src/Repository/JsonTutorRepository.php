<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository;

use Jayrods\ScubaPHP\Controller\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Entity\Tutor;
use Jayrods\ScubaPHP\Repository\TutorRepository;

class JsonTutorRepository implements TutorRepository
{
    use PasswordHandler;

    /**
     * 
     */
    private const TUTOR_DATA_PATH = DATABASE_PATH . 'tutors.json';

    /**
     * 
     */
    private function loadTutors(): array
    {
        $tutors = file_get_contents(self::TUTOR_DATA_PATH);
        $tutors = json_decode($tutors);
        $tutors = $this->hidrateTutor($tutors);

        return $tutors;
    }

    /**
     * 
     */
    private function flushTutors(array $tutors): int|bool
    {
        $tutors = json_encode($tutors);
        return file_put_contents(self::TUTOR_DATA_PATH, $tutors);
    }

    /**
     * 
     */
    public function save(Tutor $tutor): int|bool
    {
        return $this->find($tutor->uid())
            ? $this->update($tutor)
            : $this->create($tutor);
    }

    /**
     * 
     */
    public function create(Tutor $tutor): int|bool
    {
        $tutors = $this->loadTutors();
        $tutors[] = $tutor;

        return $this->flushTutors($tutors);
    }

    /**
     * 
     */
    public function update(Tutor $currentTutor): int|bool
    {
        $tutors = $this->loadTutors();

        foreach ($tutors as $i => $tutor) {
            if ($tutor->uid() === $currentTutor->uid()) {
                $tutors[$i] = $currentTutor;

                return $this->flushTutors($tutors);
            }
        }

        return false;
    }

    /**
     * 
     */
    public function remove(Tutor $currentTutor): int|bool
    {
        $tutors = $this->loadTutors();

        foreach ($tutors as $i => $tutor) {
            if ($tutor->uid() === $currentTutor->uid()) {
                unset($tutors[$i]);

                return $this->flushTutors([...$tutors]);
            }
        }

        return false;
    }

    /**
     * 
     */
    public function all(): array
    {
        return $this->loadTutors();
    }

    /**
     * 
     */
    public function find(string $uid): Tutor|false
    {
        $tutors = $this->loadTutors();

        foreach ($tutors as $tutor) {
            if ($tutor->uid() === $uid) {
                return $tutor;
            }
        }

        return false;
    }

    /**
     * 
     */
    public function findByEmail(string $email): Tutor|false
    {
        $tutors = $this->loadTutors();

        foreach ($tutors as $tutor) {
            if ($tutor->email() === $email) {
                return $tutor;
            }
        }

        return false;
    }

    /**
     * 
     */
    private function hidrateTutor(array $dataset): array
    {
        $tutors = [];

        foreach ($dataset as $tutorData) {
            $tutor = new Tutor(
                name: $tutorData->name,
                email: $tutorData->email,
                password: $tutorData->password
            );

            $tutor->createIdentity($tutorData->password);

            $tutors[] = $tutor;
        }

        return $tutors;
    }
}
