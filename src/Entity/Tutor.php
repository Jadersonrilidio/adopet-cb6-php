<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity;

use DateTimeImmutable;
use DomainException;
use JsonSerializable;

class Tutor implements JsonSerializable
{
    /**
     * 
     */
    private const DATETIME_FORMAT = 'Y-m-d H:i:s';

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
     * number of characters? symbols allowed? numbers? UpperCase?
     */
    private ?string $password;

    /**
     * uploaded formats jpeg, jpg, png only
     */
    private ?string $picture = null;

    /**
     * only numeric and 11 digits XX X XXXX-XXXX
     */
    private ?string $phone = null;

    /**
     * Only letters upper/lower case, and space allowed
     */
    private ?string $city = null;

    /**
     * Only 'word' characters allowed
     */
    private ?string $about = null;

    /**
     * date and time format (Y-d-M H:i:s)
     */
    private ?DateTimeImmutable $created_at = null;

    /**
     * date and time format (Y-d-M H:i:s)
     */
    private ?DateTimeImmutable $updated_at = null;

    /**
     * 
     */
    public function __construct(
        string $name,
        string $email,
        ?string $password = null,
        ?int $id = null,
        ?string $picture = null,
        ?string $phone = null,
        ?string $city = null,
        ?string $about = null,
        ?string $created_at = null,
        ?string $updated_at = null,
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->id = $id;
        $this->picture = $picture;
        $this->phone = $phone;
        $this->city = $city;
        $this->about = $about;
        $this->created_at = $this->setCreatedAt($created_at);
        $this->updated_at = $this->setUpdatedAt($updated_at);
    }

    /**
     * @throws DomainException
     */
    public function identify(int $id): void
    {
        if (!is_null($this->id)) {
            throw new DomainException('Tutor already has identity.');
        }

        $this->id = $id;
    }

    // /**
    //  * 
    //  */
    // public function addPicture(string $picture)
    // {
    //     $this->picture = $picture;
    // }

    // /**
    //  * 
    //  */
    // public function addPhone(string $phone)
    // {
    //     $this->phone = $phone;
    // }

    // /**
    //  * 
    //  */
    // public function addCity(string $city)
    // {
    //     $this->city = $city;
    // }

    // /**
    //  * 
    //  */
    // public function addAbout(string $about)
    // {
    //     $this->about = $about;
    // }

    /**
     * 
     */
    public function updateDate()
    {
        $this->updated_at = new DateTimeImmutable('now');
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
    public function about(): ?string
    {
        return $this->about;
    }

    /**
     * 
     */
    public function createdAt(): ?string
    {
        return $this->created_at->format(self::DATETIME_FORMAT);
    }

    /**
     * 
     */
    public function updatedAt(): ?string
    {
        return $this->updated_at->format(self::DATETIME_FORMAT);
    }

    /**
     * 
     */
    private function setCreatedAt(?string $created_at = null): DateTimeImmutable
    {
        if (is_null($created_at)) {
            return new DateTimeImmutable('now');
        }

        return DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, $created_at);
    }

    /**
     * 
     */
    private function setupdatedAt(?string $updated_at = null): DateTimeImmutable
    {
        if (is_null($updated_at)) {
            return $this->created_at;
        }

        return DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, $updated_at);
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
            'password' => $this->password,
            'picture' => $this->picture,
            'phone' => $this->phone,
            'city' => $this->city,
            'about' => $this->about,
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt(),
        );
    }
}
