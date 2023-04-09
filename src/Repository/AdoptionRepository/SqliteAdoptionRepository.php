<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository\AdoptionRepository;

use Jayrods\ScubaPHP\Entity\Adoption\Adoption;
use Jayrods\ScubaPHP\Entity\Adoption\AdoptionWithRelationships;
use Jayrods\ScubaPHP\Entity\Adoption\Status;
use Jayrods\ScubaPHP\Entity\Pet\Status as PetStatus;
use Jayrods\ScubaPHP\Entity\Pet\Pet;
use Jayrods\ScubaPHP\Entity\Pet\Size;
use Jayrods\ScubaPHP\Entity\Pet\Species;
use Jayrods\ScubaPHP\Entity\State;
use Jayrods\ScubaPHP\Entity\User\Role;
use Jayrods\ScubaPHP\Entity\User\User;
use Jayrods\ScubaPHP\Infrastructure\Database\PdoConnection;
use Jayrods\ScubaPHP\Repository\AdoptionRepository\AdoptionRepository;
use Jayrods\ScubaPHP\Traits\DatabaseTransactionControl;
use PDO;
use PDOStatement;

class SqliteAdoptionRepository implements AdoptionRepository
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
        $query = "INSERT INTO adoptions
            (user_id, pet_id, status, created_at, updated_at)
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
            SET
                user_id = :user_id,
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
    public function updateStatus(Adoption $adoption): bool
    {
        $adoption->updateDate();

        $query = "UPDATE adoptions SET status = :status, updated_at = :updated_at WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':status', $adoption->status()->value, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', $adoption->updatedAt(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $adoption->id(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * 
     */
    public function remove(Adoption $adoption): bool
    {
        $query = "DELETE FROM adoptions WHERE id = :id";

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

        $query = "SELECT * FROM adoptions WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();


        return $this->hidrateAdoption($stmt)[0] ?? false;
    }

    /**
     * 
     */
    public function findWithPet(?int $id): array
    {
        if (is_null($id)) {
            return false;
        }

        $query = "SELECT
                A.id AS adoption_id,
                A.user_id AS adoption_user_id,
                A.user_id AS adoption_pet_id,
                A.status AS adoption_status,
                A.created_at AS adoption_created_at,
                A.updated_at AS adoption_updated_at,
                P.id AS pet_id,
                P.name AS pet_name,
                P.description AS pet_description,
                P.user_id AS pet_user_id,
                P.picture AS pet_picture,
                P.species AS pet_species,
                P.size AS pet_size,
                P.status AS pet_status,
                P.city AS pet_city,
                P.state AS pet_state,
                P.birth_date AS pet_birth_date,
                P.created_at as pet_created_at,
                P.updated_at as pet_updated_at
            FROM adoptions AS A
            INNER JOIN pets AS P
                ON P.id = A.pet_id
            WHERE A.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $this->hidrateAdoptionWithRelationship($stmt)[0] ?? false;
    }

    /**
     * 
     */
    private function hidrateAdoption(PDOStatement $stmt): ?array
    {
        $adoptions = [];

        while ($adoptionData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $adoptions[] = new Adoption(
                id: $adoptionData['id'],
                user_id: $adoptionData['user_id'],
                pet_id: $adoptionData['user_id'],
                status: Status::from($adoptionData['status']),
                created_at: $adoptionData['created_at'],
                updated_at: $adoptionData['updated_at']
            );
        }

        return count($adoptions) > 0 ? $adoptions : null;
    }

    /**
     * 
     */
    private function hidrateAdoptionWithRelationship(PDOStatement $stmt): ?array
    {
        $adoptions = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $adoption = new AdoptionWithRelationships(
                id: $data['adoption_id'],
                user_id: $data['adoption_user_id'],
                pet_id: $data['adoption_pet_id'],
                status: Status::from($data['adoption_status']),
                created_at: $data['adoption_created_at'],
                updated_at: $data['adoption_updated_at']
            );

            $pet = array_key_exists('pet_id', $data)
                ? new Pet(
                    name: $data['pet_name'],
                    description: $data['pet_description'],
                    id: $data['pet_id'],
                    user_id: $data['pet_user_id'],
                    species: Species::from($data['pet_species']),
                    size: Size::from($data['pet_size']),
                    status: PetStatus::from($data['pet_status']),
                    birth_date: $data['pet_birth_date'],
                    picture: $data['pet_picture'],
                    city: $data['pet_city'],
                    state: State::from($data['pet_state']),
                    created_at: $data['pet_created_at'],
                    updated_at: $data['pet_updated_at']
                ) : null;

            $user = array_key_exists('user_id', $data)
                ? new User(
                    name: $data['user_name'],
                    email: $data['user_email'],
                    emailVerified: (bool) $data['user_email_verified'],
                    password: null,
                    id: $data['user_id'],
                    picture: $data['user_picture'],
                    phone: $data['user_phone'],
                    city: $data['user_city'],
                    state: $data['user_state'] ? State::tryFrom($data['user_state']) : null,
                    about: $data['user_about'],
                    role: Role::from($data['user_role']),
                    created_at: $data['user_created_at'],
                    updated_at: $data['user_updated_at'],
                ) : null;

            $adoption->addPet($pet);
            $adoption->addUser($user);

            $adoptions[] = $adoption;
        }

        return count($adoptions) > 0 ? $adoptions : null;
    }
}
