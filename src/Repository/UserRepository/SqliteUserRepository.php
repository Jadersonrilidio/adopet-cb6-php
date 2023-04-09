<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Repository\UserRepository;

use Jayrods\ScubaPHP\Entity\State;
use Jayrods\ScubaPHP\Entity\User\Role;
use Jayrods\ScubaPHP\Entity\User\User;
use Jayrods\ScubaPHP\Infrastructure\Database\PdoConnection;
use Jayrods\ScubaPHP\Repository\UserRepository\UserRepository;
use Jayrods\ScubaPHP\Traits\DatabaseTransactionControl;
use PDO;
use PDOStatement;

class SqliteUserRepository implements UserRepository
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
    public function save(User $user): bool
    {
        return $this->find($user->id())
            ? $this->update($user)
            : $this->create($user);
    }

    /**
     * 
     */
    private function create(User $user): bool
    {
        $query = "INSERT INTO users (name, email, email_verified, password, role, created_at, updated_at)
            VALUES (:name, :email, :email_verified, :password, :role, :created_at, :updated_at)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':name', $user->name(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->email(), PDO::PARAM_STR);
        $stmt->bindValue(':email_verified', $user->emailVerified(), PDO::PARAM_BOOL);
        $stmt->bindValue(':password', $user->password(), PDO::PARAM_STR);
        $stmt->bindValue(':role', $user->role()->value, PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $user->createdAt(), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $user->updatedAt(), PDO::PARAM_STR);

        if ($result = $stmt->execute()) {
            $user->identify((int) $this->conn->lastInsertId());
        }

        return $result;
    }

    /**
     * 
     */
    private function update(User $user): bool
    {
        $user->updateDate();

        $query = "UPDATE users
            SET
                name = :name,
                email = :email,
                email_verified = :email_verified,
                password = :password,
                picture = :picture,
                phone = :phone,
                city = :city,
                state = :state,
                about = :about,
                role = :role,
                created_at = :created_at,
                updated_at = :updated_at
            WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id', $user->id(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $user->name(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->email(), PDO::PARAM_STR);
        $stmt->bindValue(':email_verified', $user->emailVerified(), PDO::PARAM_BOOL);
        $stmt->bindValue(':password', $user->password(), PDO::PARAM_STR);
        $stmt->bindValue(':picture', $user->picture(), PDO::PARAM_STR);
        $stmt->bindValue(':phone', $user->phone(), PDO::PARAM_STR);
        $stmt->bindValue(':city', $user->city(), PDO::PARAM_STR);
        $stmt->bindValue(':state', $user->state() ? $user->state()->value : null);
        $stmt->bindValue(':about', $user->about(), PDO::PARAM_STR);
        $stmt->bindValue(':role', $user->role()->value, PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $user->createdAt(), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $user->updatedAt(), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * 
     */
    public function remove(User $user): bool
    {
        $query = "DELETE FROM users WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id', (int) $user->id(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * 
     */
    public function all(): array
    {
        $query = "SELECT * FROM users";

        $stmt = $this->conn->query($query);

        return $this->hidrateUser($stmt) ?? [];
    }

    /**
     * 
     */
    public function find(?int $id): User|false
    {
        if (is_null($id)) {
            return false;
        }

        $query = "SELECT * FROM users WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $this->hidrateUser($stmt)[0] ?? false;
    }

    /**
     * 
     */
    public function findByEmail(string $email): User|false
    {
        $query = "SELECT * FROM users WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $this->hidrateUser($stmt)[0] ?? false;
    }

    /**
     * 
     */
    private function hidrateUser(PDOStatement $stmt): ?array
    {
        $users = [];

        while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                name: $userData['name'],
                email: $userData['email'],
                emailVerified: (bool) $userData['email_verified'],
                password: $userData['password'],
                id: $userData['id'],
                picture: $userData['picture'],
                phone: $userData['phone'],
                city: $userData['city'],
                state: $userData['state'] ? State::tryFrom($userData['state']) : null,
                about: $userData['about'],
                role: Role::from($userData['role']),
                created_at: $userData['created_at'],
                updated_at: $userData['updated_at'],
            );
        }

        return count($users) > 0 ? $users : null;
    }
}
