<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\Pet;

enum Status: int
{
    case Available = 0;
    case Adopted = 1;
    case Suspended = 2;

    /**
     * 
     */
    public function toString(): string
    {
        return match ($this) {
            self::Available => 'available',
            self::Adopted => 'adopted',
            self::Suspended => 'suspended',
        };
    }
}
