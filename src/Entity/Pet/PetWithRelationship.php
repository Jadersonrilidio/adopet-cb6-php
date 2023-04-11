<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\Pet;

use Jayrods\ScubaPHP\Entity\Adoption\Adoption;
use Jayrods\ScubaPHP\Entity\User\User;

class PetWithRelationship extends Pet
{
    /**
     * 
     */
    private ?Adoption $adoption = null;

    /**
     * 
     */
    private ?User $user = null;

    /**
     * 
     */
    public function addAdoption(?Adoption $adoption): void
    {
        $this->adoption = $adoption;
    }

    /**
     * 
     */
    public function addUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * 
     */
    public function adoption(): ?Adoption
    {
        return $this->adoption;
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
    public function petWithoutRelationship(): Pet
    {
        return new Pet(
            id: $this->id,
            user_id: $this->user_id,
            name: $this->name,
            description: $this->description,
            picture: $this->picture,
            species: $this->species,
            size: $this->size,
            status: $this->status,
            city: $this->city,
            state: $this->state,
            birth_date: $this->birthDate(),
            created_at: $this->createdAt(),
            updated_at: $this->updatedAt()
        );
    }

    /**
     * 
     */
    public function jsonSerialize(): mixed
    {
        $petArray = array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'species' => $this->species->value,
            'size' => $this->size->value,
            'status' => $this->status->value,
            'birth_date' => $this->birthDate(),
            'city' => $this->city,
            'state' => $this->state->value,
            'picture' => $this->picture,
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        );

        if (!is_null($this->adoption)) {
            $petArray['adoption'] = $this->adoption;
        }

        if (!is_null($this->user)) {
            $petArray['user'] = $this->user;
        }

        return $petArray;
    }
}
