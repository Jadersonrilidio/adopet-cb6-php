<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\Adoption;

use DomainException;
use Jayrods\ScubaPHP\Entity\Adoption\Adoption;
use Jayrods\ScubaPHP\Entity\Pet\Pet;
use Jayrods\ScubaPHP\Entity\User\User;

class AdoptionWithRelationship extends Adoption
{
    /**
     * 
     */
    private ?User $user = null;

    /**
     * 
     */
    private ?Pet $pet = null;

    /**
     * 
     */
    public function addUser(?User $user): void
    {
        if (is_null($user) or $this->user_id === $user->id()) {
            throw new DomainException("Wrong User id.");
        }

        $this->user = $user;
    }

    /**
     * 
     */
    public function addPet(?Pet $pet): void
    {
        if (is_null($pet) or $this->pet_id === $pet->id()) {
            throw new DomainException("Wrong Pet id.");
        }

        $this->pet = $pet;
    }

    /**
     * 
     */
    public function user(): ?User
    {
        return $this->user;
    }

    /**
     * 
     */
    public function pet(): ?Pet
    {
        return $this->pet;
    }

    /**
     * 
     */
    public function adoption(): Adoption
    {
        return new Adoption(
            id: $this->id,
            user_id: $this->user_id,
            pet_id: $this->pet_id,
            status: $this->status,
            created_at: $this->createdAt(),
            updated_at: $this->updatedAt(),
        );
    }

    /**
     * 
     */
    public function jsonSerialize(): mixed
    {
        $adoptionArray = array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'pet_id' => $this->pet_id,
            'status' => $this->status->value,
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        );

        if (!is_null($this->pet)) {
            $adoptionArray['pet'] = $this->pet;
        }

        if (!is_null($this->user)) {
            $adoptionArray['user'] = $this->user;
        }

        return $adoptionArray;
    }
}
