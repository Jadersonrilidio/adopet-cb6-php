<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository;

use Jayrods\ScubaPHP\Entity\Tutor;
use Jayrods\ScubaPHP\Repository\TutorRepository;
use PDO;
use PDOStatement;

class SQLiteTutorRepository implements TutorRepository
{
    /**
     * 
     */
    private const TUTOR_DATA_PATH = DATABASE_PATH . 'database.sqlite';

    /**
     * 
     */
    private PDO $conn;

    /**
     * 
     */
    public function __construct(?PDO $conn = null)
    {
        $this->conn = $conn ?? new PDO('sqlite:' . self::TUTOR_DATA_PATH);
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
        $query = "INSERT INTO tutors (name, email, password, created_at, updated_at)
            VALUES (:name, :email, :password, :created_at, :updated_at)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':name', $tutor->name(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $tutor->email(), PDO::PARAM_STR);
        $stmt->bindValue(':password', $tutor->password(), PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $tutor->createdAt(), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $tutor->updatedAt(), PDO::PARAM_STR);

        if ($result = $stmt->execute()) {
            $tutor->identify((int) $this->conn->lastInsertId());
        }

        return $result;
    }

    /**
     * 
     */
    private function update(Tutor $tutor): bool
    {
        $tutor->updateDate();

        $query = "UPDATE tutors
            SET name = :name,
                email = :email,
                password = :password,
                picture = :picture,
                phone = :phone,
                city = :city,
                about = :about,
                created_at = :created_at,
                updated_at = :updated_at
            WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id', (int) $tutor->id(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $tutor->name(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $tutor->email(), PDO::PARAM_STR);
        $stmt->bindValue(':password', $tutor->password(), PDO::PARAM_STR);
        $stmt->bindValue(':picture', $tutor->picture(), PDO::PARAM_STR);
        $stmt->bindValue(':phone', $tutor->phone(), PDO::PARAM_STR);
        $stmt->bindValue(':city', $tutor->city(), PDO::PARAM_STR);
        $stmt->bindValue(':about', $tutor->about(), PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $tutor->createdAt(), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $tutor->updatedAt(), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * 
     */
    public function remove(Tutor $tutor): bool
    {
        $query = "DELETE FROM tutors WHERE tutors.id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id', $tutor->id(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * 
     */
    public function all(): array
    {
        $query = "SELECT * FROM tutors";

        $stmt = $this->conn->query($query);

        return $this->hidrateTutor($stmt) ?? [];
    }

    /**
     * 
     */
    public function find(?int $id): Tutor|false
    {
        if (is_null($id)) {
            return false;
        }

        $query = "SELECT * FROM tutors WHERE tutors.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();


        return $this->hidrateTutor($stmt)[0] ?? false;
    }

    /**
     * 
     */
    public function findByEmail(string $email): Tutor|false
    {
        $query = "SELECT * FROM tutors WHERE tutors.email = :email";

        $stmt = $this->conn->query($query);
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $this->hidrateTutor($stmt)[0] ?? false;
    }

    /**
     * 
     */
    private function hidrateTutor(PDOStatement $stmt): ?array
    {
        $tutors = [];

        while ($tutorData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tutors[] = new Tutor(
                name: $tutorData['name'],
                email: $tutorData['email'],
                password: $tutorData['password'],
                id: $tutorData['id'],
                picture: $tutorData['picture'],
                phone: $tutorData['phone'],
                city: $tutorData['city'],
                about: $tutorData['about'],
                created_at: $tutorData['created_at'],
                updated_at: $tutorData['updated_at'],
            );
        }

        return count($tutors) > 0 ? $tutors : null;
    }
}
