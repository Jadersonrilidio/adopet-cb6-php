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
    public function jsonSerialize(): mixed
    {
        return array(
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
            'updated_at' => $this->updatedAt(),
            'adoption' => $this->adoption,
            'user' => $this->user
        );
    }
}
