<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\Adoption;

use DomainException;
use Jayrods\ScubaPHP\Entity\Adoption\Status;
use Jayrods\ScubaPHP\Entity\EntityWithDates;
use Jayrods\ScubaPHP\Entity\Pet\Pet;
use Jayrods\ScubaPHP\Entity\User\User;
use JsonSerializable;

class Adoption extends EntityWithDates implements JsonSerializable
{
    /**
     * 
     */
    protected ?int $id;

    /**
     * 
     */
    protected int $user_id;

    /**
     * 
     */
    protected int $pet_id;

    /**
     * 
     */
    protected Status $status;

    /**
     * 
     */
    public function __construct(
        int $user_id,
        int $pet_id,
        Status $status = Status::Requested,
        ?int $id = null,
        ?string $created_at = null,
        ?string $updated_at = null,
    ) {
        parent::__construct($created_at, $updated_at);

        $this->id = $id;
        $this->user_id = $user_id;
        $this->pet_id = $pet_id;
        $this->status = $status;
    }

    /**
     * 
     */
    public function identify(int $id): void
    {
        if (!is_null($this->id)) {
            throw new DomainException('Adoption already has identity.');
        }

        $this->id = $id;
    }

    /**
     * 
     */
    public function id(): ?int
    {
        return $this->id;
    }

    /**
     * 
     */
    public function userId(): int
    {
        return $this->user_id;
    }

    /**
     * 
     */
    public function petId(): int
    {
        return $this->pet_id;
    }

    /**
     * 
     */
    public function requestAdoption(): void
    {
        $this->status = Status::Requested;
    }

    /**
     * 
     */
    public function confirmAdoption(): void
    {
        $this->status = Status::Adopted;
    }

    /**
     * 
     */
    public function cancelAdoption(): void
    {
        $this->status = Status::Canceled;
    }

    /**
     * 
     */
    public function reproveAdoption(): void
    {
        $this->status = Status::Reproved;
    }

    /**
     * 
     */
    public function suspendAdoption(): void
    {
        $this->status = Status::Suspended;
    }

    /**
     * 
     */
    public function status(): Status
    {
        return $this->status;
    }

    /**
     * 
     */
    public function jsonSerialize(): mixed
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'pet_id' => $this->pet_id,
            'status' => $this->status->value,
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        );
    }
}
