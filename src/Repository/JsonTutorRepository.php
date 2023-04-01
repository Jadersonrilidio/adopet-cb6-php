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
    public function save(Tutor $tutor): bool
    {
        return $this->find($tutor->id())
            ? $this->update($tutor)
            : $this->create($tutor);
    }

    /**
     * 
     */
    private function create(Tutor $tutor): bool
    {
        $tutors = $this->loadTutors();
        $tutors[] = $tutor;

        return (bool) $this->flushTutors($tutors);
    }

    /**
     * 
     */
    private function update(Tutor $currentTutor): bool
    {
        $tutors = $this->loadTutors();
        
        foreach ($tutors as $i => $tutor) {
            if ($tutor->id() === $currentTutor->id()) {
                $currentTutor->updateDate();
                $tutors[$i] = $currentTutor;

                return (bool) $this->flushTutors($tutors);
            }
        }

        return false;
    }

    /**
     * 
     */
    public function remove(Tutor $currentTutor): bool
    {
        $tutors = $this->loadTutors();

        foreach ($tutors as $i => $tutor) {
            if ($tutor->id() === $currentTutor->id()) {
                unset($tutors[$i]);

                return (bool) $this->flushTutors([...$tutors]);
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
    public function find(int $id): Tutor|false
    {
        $tutors = $this->loadTutors();

        foreach ($tutors as $tutor) {
            if ($tutor->id() === $id) {
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
            $tutors[] = new Tutor(
                name: $tutorData->name,
                email: $tutorData->email,
                password: $tutorData->password,
                id: $tutorData->id,
                picture: $tutorData->picture,
                phone: $tutorData->phone,
                city: $tutorData->city,
                about: $tutorData->about,
                created_at: $tutorData->created_at,
                updated_at: $tutorData->updated_at,
            );
        }

        return $tutors;
    }
}
