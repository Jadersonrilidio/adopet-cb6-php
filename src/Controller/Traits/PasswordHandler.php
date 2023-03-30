<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Traits;

trait PasswordHandler
{
    /**
     * 
     */
    public function passwordHash(string $password): string|false|null
    {
        return password_hash(
            password: $password,
            algo: env ('PASSWORD_ALGO', PASSWORD_ARGON2ID)
        );
    }

    /**
     * 
     */
    public function passwordNeedRehash(string $hash): bool
    {
        return password_needs_rehash(
            hash: $hash,
            algo: env ('PASSWORD_ALGO', PASSWORD_ARGON2ID)
        );
    }

    /**
     * 
     */
    public function passwordVerify(string $password, string $hash): bool
    {
        return password_verify(
            password: $password,
            hash: $hash
        );
    }
}
