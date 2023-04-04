<?php

namespace Jayrods\ScubaPHP\Entity\User;

use BackedEnum;

enum Role: int implements BackedEnum
{

    case User = 0;
    case Admin = 1;

    /**
     * 
     */
    public function toString(): string
    {
        return match ($this) {
            self::User => 'user',
            self::Admin => 'admin',
            default => 'user',
        };
    }
}
