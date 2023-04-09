<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\Pet;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DomainException;
use Jayrods\ScubaPHP\Entity\EntityWithDates;
use Jayrods\ScubaPHP\Entity\Pet\Size;
use Jayrods\ScubaPHP\Entity\Pet\Species;
use Jayrods\ScubaPHP\Entity\Pet\Status;
use Jayrods\ScubaPHP\Entity\State;
use JsonSerializable;

class Pet extends EntityWithDates implements JsonSerializable
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
    protected string $name;

    /**
     * 
     */
    protected string $description;

    /**
     * 
     */
    protected Species $species;

    /**
     * 
     */
    protected Size $size;

    /**
     * 
     */
    protected Status $status;

    /**
     * 
     */
    protected DateTimeInterface $birth_date;

    /**
     * 
     */
    protected string $city;

    /**
     * 
     */
    protected State $state;

    /**
     * 
     */
    protected string $picture;

    /**
     * 
     */
    public function __construct(
        string $name,
        string $description,
        int $user_id,
        string $picture,
        Species $species,
        Size $size,
        Status $status = Status::Available,
        string $city,
        State $state,
        string $birth_date,
        ?int $id = null,
        ?string $created_at = null,
        ?string $updated_at = null,
    ) {
        parent::__construct($created_at, $updated_at);

        $this->id = $id;
        $this->user_id = $user_id;
        $this->name = $name;
        $this->description = $description;
        $this->picture = $picture;
        $this->species = $species;
        $this->size = $size;
        $this->status = $status;
        $this->city = $city;
        $this->state = $state;
        $this->birth_date = DateTimeImmutable::createFromFormat(DATE_FORMAT, $birth_date);
    }

    /**
     * 
     */
    public function identify(int $id): void
    {
        if (!is_null($this->id)) {
            throw new DomainException('Pet already has identity.');
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
    public function name(): string
    {
        return $this->name;
    }

    /**
     * 
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * 
     */
    public function species(): Species
    {
        return $this->species;
    }

    /**
     * 
     */
    public function size(): Size
    {
        return $this->size;
    }

    /**
     * 
     */
    public function available(): void
    {
        $this->status = Status::Available;
    }

    /**
     * 
     */
    public function adopt(): void
    {
        $this->status = Status::Adopted;
    }

    /**
     * 
     */
    public function suspend(): void
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
    public function birthDate(): string
    {
        return $this->birth_date->format(DATE_FORMAT);
    }

    /**
     * 
     */
    public function age()
    {
        $today = new DateTimeImmutable('now');

        $dateInterval = date_diff($today, $this->birth_date, true);

        $format = $this->ageFormatBuilder($dateInterval);

        return $dateInterval->format($format);
    }

    /**
     * 
     */
    private function ageFormatBuilder(DateInterval $dateInterval): string
    {
        $years = $dateInterval->y;
        $months = $dateInterval->m;

        $format = '';

        if ($years == 0 and $months == 0) {
            return 'Less than a month';
        }

        if ($years > 0) {
            $format .= '%Y years ';
        }


        if ($months > 0) {
            $format .= '%m months ';
        }

        return $format;
    }

    /**
     * 
     */
    public function city(): string
    {
        return $this->city;
    }

    /**
     * 
     */
    public function state(): State
    {
        return $this->state;
    }

    /**
     * 
     */
    public function picture(): string
    {
        return $this->picture;
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
        );
    }
}
