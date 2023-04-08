<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Entity\Pet;

enum Species: int
{
    case Dog = 0;
    case Cat = 1;
    case Rabbit = 2;
    case Bird = 3;

    /**
     * 
     */
    public function toString(): string
    {
        return match ($this) {
            self::Dog => 'dog',
            self::Cat => 'cat',
            self::Rabbit => 'rabbit',
            self::Bird => 'bird',
        };
    }
}
