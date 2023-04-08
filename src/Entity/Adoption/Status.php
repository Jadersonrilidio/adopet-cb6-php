<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\Adoption;

enum Status: int
{
    case Requested = 0;
    case Adopted = 1;
    case Canceled = 2;
    case Reproved = 3;
    case Suspended = 4;

    /**
     * 
     */
    public function toString(): string
    {
        return match ($this) {
            self::Requested => 'requested',
            self::Adopted => 'adopted',
            self::Canceled => 'canceled',
            self::Reproved => 'reproved',
            self::Suspended => 'suspended',
        };
    }
}
