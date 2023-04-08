<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\User;

enum Role: int
{

    case Tutor = 0;
    case Shelter = 1;
    case Admin = 2;

    /**
     * 
     */
    public function toString(): string
    {
        return match ($this) {
            self::Tutor => 'tutor',
            self::Shelter => 'shelter',
            self::Admin => 'admin'
        };
    }
}
