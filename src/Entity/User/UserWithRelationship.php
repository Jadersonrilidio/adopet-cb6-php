<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\User;

use Jayrods\ScubaPHP\Entity\Adoption\Adoption;
use Jayrods\ScubaPHP\Entity\Pet\Pet;
use Jayrods\ScubaPHP\Entity\User\User;

class UserWithRelationship extends User
{
    /**
     * @var Pet[]
     */
    private array $pets = [];

    /**
     * @var Adoption[]
     */
    private array $adoptions = [];

    /**
     * 
     */
    public function addAdoption(?Adoption $adoption): void
    {
        $this->adoptions[] = $adoption;
    }

    /**
     * 
     */
    public function addPet(?Pet $pet): void
    {
        $this->pets[] = $pet;
    }

    /**
     * @return Adoption[]
     */
    public function adoptions(): array
    {
        return $this->adoptions;
    }

    /**
     * @return Pet[]
     */
    public function pets(): array
    {
        return $this->pets;
    }

    /**
     * 
     */
    public function jsonSerialize(): mixed
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'emailVerified' => $this->emailVerified,
            'password' => $this->password,
            'picture' => $this->picture,
            'phone' => $this->phone,
            'city' => $this->city,
            'state' => $this->state ? $this->state->value : null,
            'about' => $this->about,
            'role' => $this->role->value,
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt(),
            'adoptions' => $this->adoptions,
            'pets' => $this->pets
        );
    }
}
