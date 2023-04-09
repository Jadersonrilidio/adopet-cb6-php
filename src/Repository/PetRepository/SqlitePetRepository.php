<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository\PetRepository;

use Jayrods\ScubaPHP\Entity\Pet\Pet;
use Jayrods\ScubaPHP\Entity\Pet\Size;
use Jayrods\ScubaPHP\Entity\Pet\Species;
use Jayrods\ScubaPHP\Entity\Pet\Status;
use Jayrods\ScubaPHP\Entity\State;
use Jayrods\ScubaPHP\Infrastructure\Database\PdoConnection;
use Jayrods\ScubaPHP\Repository\PetRepository\PetRepository;
use Jayrods\ScubaPHP\Traits\DatabaseTransactionControl;
use PDO;
use PDOStatement;

class SqlitePetRepository implements PetRepository
{
    use DatabaseTransactionControl;

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
    public function save(Pet $pet): bool
    {
        return $this->find($pet->id())
            ? $this->update($pet)
            : $this->create($pet);
    }

    /**
     * 
     */
    private function create(Pet $pet): bool
    {
        $query = "INSERT INTO pets
            (name, description, user_id, species, size, status, birth_date, city, state, picture, created_at, updated_at)
            VALUES
            (:name, :description, :user_id, :species, :size, :status, :birth_date, :city, :state, :picture, :created_at, :updated_at)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':name', $pet->name(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $pet->description(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $pet->userId(), PDO::PARAM_INT);
        $stmt->bindValue(':species', $pet->species()->value, PDO::PARAM_INT);
        $stmt->bindValue(':size', $pet->size()->value, PDO::PARAM_INT);
        $stmt->bindValue(':status', $pet->status()->value, PDO::PARAM_INT);
        $stmt->bindValue(':birth_date', $pet->birthDate(), PDO::PARAM_STR);
        $stmt->bindValue(':city', $pet->city(), PDO::PARAM_STR);
        $stmt->bindValue(':state', $pet->state()->value, PDO::PARAM_STR);
        $stmt->bindValue(':picture', $pet->picture(), PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $pet->createdAt(), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $pet->updatedAt(), PDO::PARAM_STR);

        if ($result = $stmt->execute()) {
            $pet->identify((int) $this->conn->lastInsertId());
        }

        return $result;
    }

    /**
     * 
     */
    private function update(Pet $pet): bool
    {
        $pet->updateDate();

        $query = "UPDATE pets
            SET
                name = :name,
                description = :description,
                user_id = :user_id,
                species = :species,
                size = :size,
                status = :status,
                birth_date = :birth_date,
                city = :city,
                state = :state,
                picture = :picture,
                created_at = :created_at,
                updated_at = :updated_at
            WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':name', $pet->name(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $pet->description(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $pet->userId(), PDO::PARAM_INT);
        $stmt->bindValue(':species', $pet->species()->value, PDO::PARAM_INT);
        $stmt->bindValue(':size', $pet->size()->value, PDO::PARAM_INT);
        $stmt->bindValue(':status', $pet->status()->value, PDO::PARAM_INT);
        $stmt->bindValue(':birth_date', $pet->birthDate(), PDO::PARAM_STR);
        $stmt->bindValue(':city', $pet->city(), PDO::PARAM_STR);
        $stmt->bindValue(':state', $pet->state()->value, PDO::PARAM_STR);
        $stmt->bindValue(':picture', $pet->picture(), PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $pet->createdAt(), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $pet->updatedAt(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $pet->id(), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * 
     */
    public function updateStatus(Pet $pet): bool
    {
        $pet->updateDate();

        $query = "UPDATE pets SET status = :status, updated_at = :updated_at WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':status', $pet->status()->value, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', $pet->updatedAt(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $pet->id(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * 
     */
    public function remove(Pet $pet): bool
    {
        $query = "DELETE FROM pets WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id', (int) $pet->id(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * 
     */
    public function all(): array
    {
        $query = "SELECT * FROM pets";

        $stmt = $this->conn->query($query);

        return $this->hidratePet($stmt) ?? [];
    }

    /**
     * 
     */
    public function find(?int $id): Pet|false
    {
        if (is_null($id)) {
            return false;
        }

        $query = "SELECT * FROM pets WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $this->hidratePet($stmt)[0] ?? false;
    }

    /**
     * 
     */
    private function hidratePet(PDOStatement $stmt): ?array
    {
        $pets = [];

        while ($petData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pets[] = new Pet(
                name: $petData['name'],
                description: $petData['description'],
                id: $petData['id'],
                user_id: $petData['user_id'],
                species: Species::from($petData['species']),
                size: Size::from($petData['size']),
                status: Status::from($petData['status']),
                birth_date: $petData['birth_date'],
                picture: $petData['picture'],
                city: $petData['city'],
                state: State::from($petData['state']),
                created_at: $petData['created_at'],
                updated_at: $petData['updated_at']
            );
        }

        return count($pets) > 0 ? $pets : null;
    }
}
