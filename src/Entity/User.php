<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity;

use DomainException;
use JsonSerializable;

class User implements JsonSerializable
{
    /**
     * 
     */
    private string $name;

    /**
     * 
     */
    private string $email;

    /**
     * 
     */
    private ?string $password;

    /**
     * 
     */
    private bool $verified;

    /**
     * 
     */
    public function __construct(string $name, string $email, ?string $password = null, bool $verified = false)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->verified = $verified;
    }

    /**
     * @throws DomainException
     */
    public function identify(mixed $email): void
    {
        if (isset($this->email) and $this->email !== '') {
            throw new DomainException('User already has identity.');
        }

        $this->email = $email;
    }

    /**
     * 
     */
    public function verify(): void
    {
        $this->verified = true;
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
    public function password(): ?string
    {
        return $this->password;
    }

    /**
     * 
     */
    public function verified(): bool
    {
        return $this->verified;
    }

    /**
     * 
     */
    public function jsonSerialize(): mixed
    {
        return array(
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'verified' => $this->verified
        );
    }
}
