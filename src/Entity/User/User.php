<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\User;

use DomainException;
use Jayrods\ScubaPHP\Entity\EntityWithDates;
use Jayrods\ScubaPHP\Entity\State;
use Jayrods\ScubaPHP\Entity\User\Role;
use JsonSerializable;

class User extends EntityWithDates implements JsonSerializable
{
    /**
     * Auto_generated, if present, also MUST exist on database
     */
    private ?int $id;

    /**
     * Only letters upper/lower case, and space allowed
     */
    private string $name;

    /**
     * email format example@mail.com
     */
    private string $email;

    /**
     * boolean
     */
    private bool $emailVerified;

    /**
     * number of characters? symbols allowed? numbers? UpperCase?
     */
    private ?string $password;

    /**
     * uploaded formats jpeg, jpg, png only
     */
    private ?string $picture;

    /**
     * only numeric and 11 digits XX X XXXX-XXXX
     */
    private ?string $phone;

    /**
     * Only letters upper/lower case, and space allowed
     */
    private ?string $city;

    /**
     * Only State cases, string value
     */
    private ?State $state;

    /**
     * Only 'word' characters allowed
     */
    private ?string $about;

    /**
     * enum (User => 0, Admin => 1)
     */
    private Role $role;

    /**
     * 
     */
    public function __construct(
        string $name,
        string $email,
        bool $emailVerified = false,
        ?string $password = null,
        ?int $id = null,
        ?string $picture = null,
        ?string $phone = null,
        ?string $city = null,
        ?State $state = null,
        ?string $about = null,
        Role $role = Role::Tutor,
        ?string $created_at = null,
        ?string $updated_at = null,
    ) {
        parent::__construct($created_at, $updated_at);

        $this->name = $name;
        $this->email = $email;
        $this->emailVerified = $emailVerified;
        $this->password = $password;
        $this->id = $id;
        $this->picture = $picture;
        $this->phone = $phone;
        $this->city = $city;
        $this->state = $state;
        $this->about = $about;
        $this->role = $role;
    }

    /**
     * @throws DomainException
     */
    public function identify(int $id): void
    {
        if (!is_null($this->id)) {
            throw new DomainException('User already has identity.');
        }

        $this->id = $id;
    }

    /**
     * 
     */
    public function verifyEmail(): void
    {
        $this->emailVerified = true;
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
    public function name(): string
    {
        return $this->name;
    }

    /**
     * 
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * 
     */
    public function emailVerified(): bool
    {
        return $this->emailVerified;
    }

    /**
     * 
     */
    public function password(): ?string
    {
        return $this->password;
    }

    /**
     * 
     */
    public function picture(): ?string
    {
        return $this->picture;
    }

    /**
     * 
     */
    public function phone(): ?string
    {
        return $this->phone;
    }

    /**
     * 
     */
    public function city(): ?string
    {
        return $this->city;
    }

    /**
     * 
     */
    public function state(): ?State
    {
        return $this->state;
    }

    /**
     * 
     */
    public function about(): ?string
    {
        return $this->about;
    }

    /**
     * 
     */
    public function becomeTutor(): void
    {
        $this->role = Role::Tutor;
    }

    /**
     * 
     */
    public function becomeShelter(): void
    {
        $this->role = Role::Shelter;
    }

    /**
     * 
     */
    public function becomeAdmin(): void
    {
        $this->role = Role::Admin;
    }

    /**
     * 
     */
    public function role(): Role
    {
        return $this->role;
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
        );
    }
}
