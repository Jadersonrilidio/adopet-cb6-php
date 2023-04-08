<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\Pet;

enum Size: int
{
    case Mini = 0;
    case Small = 1;
    case Medium = 2;
    case Large = 3;
    case Giant = 4;

    /**
     * 
     */
    public function toString(): string
    {
        return match ($this) {
            self::Mini => 'mini',
            self::Small => 'small',
            self::Medium => 'medium',
            self::Large => 'large',
            self::Giant => 'giant',
        };
    }
}
