<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository\AdoptionRepository;

use Jayrods\ScubaPHP\Entity\Adoption\Adoption;
use Jayrods\ScubaPHP\Entity\Adoption\Status;
use Jayrods\ScubaPHP\Infrastructure\Database\PdoConnection;
use Jayrods\ScubaPHP\Repository\AdoptionRepository\AdoptionRepository;
use PDO;
use PDOStatement;

class SqliteAdoptionRepository implements AdoptionRepository
{
    /**
     * 
     */
    private PDO $conn;

    /**
     * 
     */
    public function __construct(PdoConnection $connection)
    {
        $this->conn = $connection->getConnection();
    }

    /**
     * 
     */
    public function save(Adoption $adoption): bool
    {
        return $this->find($adoption->id())
            ? $this->update($adoption)
            : $this->create($adoption);
    }

    /**
     * 
     */
    private function create(Adoption $adoption): bool
    {
        $query = "INSERT INTO adoptions (user_id, pet_id, status, created_at, updated_at)
            VALUES (:user_id, :pet_id, :status, :created_at, :updated_at)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':user_id', $adoption->userId(), PDO::PARAM_INT);
        $stmt->bindValue(':pet_id', $adoption->petId(), PDO::PARAM_INT);
        $stmt->bindValue(':status', $adoption->status()->value, PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $adoption->createdAt(), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $adoption->updatedAt(), PDO::PARAM_STR);

        if ($result = $stmt->execute()) {
            $adoption->identify((int) $this->conn->lastInsertId());
        }

        return $result;
    }

    /**
     * 
     */
    private function update(Adoption $adoption): bool
    {
        $adoption->updateDate();

        $query = "UPDATE adoptions
            SET user_id = :user_id,
                pet_id = :pet_id,
                status = :status,
                created_at = :created_at,
                updated_at = :updated_at
            WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id', $adoption->id(), PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $adoption->userId(), PDO::PARAM_INT);
        $stmt->bindValue(':pet_id', $adoption->petId(), PDO::PARAM_INT);
        $stmt->bindValue(':status', $adoption->status()->value, PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $adoption->createdAt(), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $adoption->updatedAt(), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * 
     */
    public function remove(Adoption $adoption): bool
    {
        $query = "DELETE FROM adoptions WHERE pets.id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id', (int) $adoption->id(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * 
     */
    public function all(): array
    {
        $query = "SELECT * FROM adoptions";

        $stmt = $this->conn->query($query);

        return $this->hidrateAdoption($stmt) ?? [];
    }

    /**
     * 
     */
    public function find(?int $id): Adoption|false
    {
        if (is_null($id)) {
            return false;
        }

        $query = "SELECT * FROM adoptions WHERE adoptions.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();


        return $this->hidrateAdoption($stmt)[0] ?? false;
    }

    /**
     * 
     */
    private function hidrateAdoption(PDOStatement $stmt): ?array
    {
        $adoptions = [];

        while ($adoptionData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $adoptions[] = new Adoption(
                id: (int) $adoptions['id'],
                user_id: (int) $adoptions['user_id'],
                pet_id: (int) $adoptions['user_id'],
                status: Status::from($adoptions['status']),
                created_at: $adoptions['created_at'],
                updated_at: $adoptions['updated_at']
            );
        }

        return count($adoptions) > 0 ? $adoptions : null;
    }
}
