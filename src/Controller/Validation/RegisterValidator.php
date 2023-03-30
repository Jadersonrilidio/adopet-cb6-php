<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Validation;

use Jayrods\ScubaPHP\Controller\Validation\Validator;
use Jayrods\ScubaPHP\Http\Core\{Request, Router};
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;
use Jayrods\ScubaPHP\Repository\{JsonUserRepository, UserRepository};

class RegisterValidator implements Validator
{
    /**
     * 
     */
    private FlashMessage $flashMsg;

    /**
     * 
     */
    private UserRepository $userRepository;

    /**
     * 
     */
    public function __construct(FlashMessage $flashMsg)
    {
        $this->flashMsg = $flashMsg;
        $this->userRepository = new JsonUserRepository();
    }

    /**
     * 
     */
    public function validate(Request $request): bool
    {
        $nameDontContainInvalidCharacters = $this->validateNameDoesNotContainInvalidCharacters(
            name: $request->postVars('name')
        );

        $emailIsNotRegistered = $this->validateEmailIsNotRegistered(
            email: $request->postVars('email')
        );

        $passwordHasAtLeastTenChars = $this->validatePasswordHasAtLeastTenChars(
            password: $request->postVars('password')
        );

        $passwordsMatch = $this->validatePasswordsMatch(
            password: $request->postVars('password'),
            passwordConfirm: $request->postVars('password-confirm')
        );

        if (!$nameDontContainInvalidCharacters or !$emailIsNotRegistered or !$passwordHasAtLeastTenChars or !$passwordsMatch) {
            $this->flashMsg->set([
                'status-class' => 'mensagem-erro',
                'status-message' => 'Error: Not possible to register.',
                'name-value' => $request->postVars('name'),
                'email-value' => $request->postVars('email'),
                'password-value' => $request->postVars('password'),
                'password-confirm-value' => $request->postVars('password-confirm'),
            ]);

            Router::redirect('register');
        }

        $this->flashMsg->set([
            'status-class' => 'mensagem-sucesso',
            'status-message' => 'User sucessfully registered.<br>Please confirm your email before proceed.'
        ]);

        return true;
    }

    /**
     * 
     */
    private function validateNameDoesNotContainInvalidCharacters(string $name): bool
    {
        $regex = '/^[a-zA-Z\s]+$/';

        if (!preg_match($regex, $name)) {
            $this->flashMsg->add([
                'name-errors' => 'Invalid name.'
            ]);

            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function validateEmailIsNotRegistered(string $email): bool
    {
        if ($this->userRepository->findByEmail($email)) {
            $this->flashMsg->add([
                'email-errors' => 'Email already registered.'
            ]);

            return false;
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

