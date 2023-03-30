<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Validation;

use Jayrods\ScubaPHP\Controller\Traits\{PasswordHandler, SSLEncryption};
use Jayrods\ScubaPHP\Controller\Validation\Validator;
use Jayrods\ScubaPHP\Http\Core\{Request, Router};
use Jayrods\ScubaPHP\Infrastructure\{Auth, FlashMessage};
use Jayrods\ScubaPHP\Repository\JsonUserRepository;

class ChangePasswordValidator implements Validator
{
    use PasswordHandler,
        SSLEncryption;

    /**
     * 
     */
    private JsonUserRepository $userRepository;

    /**
     * 
     */
    private FlashMessage $flashMsg;

    /**
     * 
     */
    private Auth $auth;

    /**
     * 
     */
    public function __construct(FlashMessage $flashMsg)
    {
        $this->userRepository = new JsonUserRepository();
        $this->flashMsg = $flashMsg;
        $this->auth = new Auth();
    }

    /**
     * 
     */
    public function validate(Request $request): bool
    {
        $passwordHasAtLeastTenChars = $this->validatePasswordHasAtLeastTenChars(
            password: $request->postVars('password')
        );

        $passwordsMatch = $this->validatePasswordsMatch(
            password: $request->postVars('password'),
            passwordConfirm: $request->postVars('password-confirm')
        );

        if (!$passwordHasAtLeastTenChars or !$passwordsMatch) {
            $this->flashMsg->set([
                'status-class' => 'mensagem-erro',
                'status-message' => 'Error: Invalid inputs.',
                'password-value' => $request->postVars('password'),
                'password-confirm-value' => $request->postVars('password-confirm'),
            ]);

            Router::redirect('change-password?token=' . $request->postVars('token'));
        }

        return true;
    }

    /**
     * 
     */
    private function validatePasswordHasAtLeastTenChars(string $password): bool
    {
        if (strlen($password) < 10) {
            $this->flashMsg->add([
                'password-errors' => 'Password must have at least 10 characters.'
            ]);

            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function validatePasswordsMatch(string $password, string $passwordConfirm): bool
    {
        if ($password !== $passwordConfirm) {
            $this->flashMsg->add([
                'password-errors' => 'Passwords does not match.',
                'password-confirm-errors' => 'Passwords does not match.'
            ]);

            return false;
        }

        return true;
    }
}
