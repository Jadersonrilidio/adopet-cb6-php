<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity;

use DomainException;
use JsonSerializable;

class Tutor implements JsonSerializable
{
    /**
     * 
     */
    private ?string $uid = null;

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
    public function __construct(string $name, string $email, ?string $password = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @throws DomainException
     */
    public function createIdentity(?string $uid = null): void
    {
        if (!is_null($this->uid)) {
            throw new DomainException('Tutor already has identity.');
        }

        if (!is_null($uid)) {
            $this->uid = $uid;
            return;
        }

        $this->uid = uniqid(
            prefix: 'uid_',
            more_entropy: true
        );
    }

    /**
     * 
     */
    public function uid(): string
    {
        return $this->uid;
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
    public function jsonSerialize(): mixed
    {
        return array(
            'uid' => $this->uid,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password
        );
    }
}
