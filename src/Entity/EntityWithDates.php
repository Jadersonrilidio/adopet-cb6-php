<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use DomainException;
use Jayrods\ScubaPHP\Entity\Entity;

abstract class EntityWithDates implements Entity
{
    /**
     * 
     */
    protected DateTimeInterface $created_at;

    /**
     * 
     */
    protected DateTimeInterface $updated_at;

    /**
     * 
     */
    public function __construct(?string $created_at, ?string $updated_at)
    {
        $this->created_at = $this->setCreationDate($created_at);
        $this->updated_at = $this->setUpdatedDate($updated_at);
    }

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
    public function createdAt(): ?string
    {
        return $this->created_at->format(DATETIME_FORMAT);
    }

    /**
     * 
     */
    public function updatedAt(): ?string
    {
        return $this->updated_at->format(DATETIME_FORMAT);
    }

    /**
     * 
     */
    private function setCreationDate(?string $created_at): DateTimeInterface
    {
        if (is_null($created_at)) {
            return new DateTimeImmutable('now');
        }

        return DateTimeImmutable::createFromFormat(DATETIME_FORMAT, $created_at);
    }

    /**
     * 
     */
    private function setupdatedDate(?string $updated_at): DateTimeInterface
    {
        if (is_null($updated_at)) {
            return $this->created_at ?? throw new DomainException('Creation date is not set. Must be set first.');
        }

        return DateTimeImmutable::createFromFormat(DATETIME_FORMAT, $updated_at);
    }
}
