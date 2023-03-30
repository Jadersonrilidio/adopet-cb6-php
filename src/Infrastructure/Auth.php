<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Infrastructure;

use Jayrods\ScubaPHP\Controller\Traits\SSLEncryption;
use Jayrods\ScubaPHP\Entity\User;

class Auth
{
    use SSLEncryption;

    /**
     * 
     */
    private const SESSION_AUTH = 'user';

    /**
     * 
     */
    private static ?User $user = null;

    /**
     * 
     */
    public function authenticate(User $user): bool
    {
        $sessionUser = new User(
            name: $user->name(),
            email: $user->email(),
            password: null,
            verified: $user->verified()
        );

        if (!$userData = $this->SSLCrypt($sessionUser)) {
            return false;
        }

        $_SESSION[self::SESSION_AUTH] = $userData;

        return true;
    }

    /**
     * 
     */
    public function authUser(): User|false
    {
        if (!is_null(self::$user) and self::$user instanceof User) {
            return self::$user;
        }

        if ($session = $_SESSION[self::SESSION_AUTH] ?? false) {
            $userData = $this->SSLDecrypt($session);

            self::$user = new User(
                name: $userData->name,
                email: $userData->email,
                password: null,
                verified: $userData->verified
            );

            return self::$user;
        }

        return false;
    }

    /**
     * 
     */
    public function authLogout(): bool
    {
        if (isset($_SESSION[self::SESSION_AUTH])) {
            unset($_SESSION[self::SESSION_AUTH]);

            return true;
        }

        return false;
    }
}
