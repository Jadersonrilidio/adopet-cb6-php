<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Validation;

use Jayrods\ScubaPHP\Controller\Validation\Validator;
use Jayrods\ScubaPHP\Infrastructure\ErrorMessage;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Repository\SQLiteTutorRepository;
use Jayrods\ScubaPHP\Repository\TutorRepository;

class TutorValidator implements Validator
{
    /**
     * 
     */
    private TutorRepository $userRepository;

    /**
     * 
     */
    public function __construct()
    {
        $this->userRepository = new SQLiteTutorRepository();
    }

    /**
     * 
     */
    public function validate(Request $request): bool
    {
        $inputs = array_merge($request->postVars(), $request->putVars());
        $files = $request->files();

        $validation = [];

        $validation['name'] = isset($inputs['name'])
            ? $this->validateName(name: $inputs['name'])
            : true;

        $validation['email'] = isset($inputs['email'])
            ? $this->validateEmail(email: $inputs['email'], httpMethod: $request->httpMethod())
            : true;

        $validation['password'] = isset($inputs['password'], $inputs['password-confirm'])
            ? $this->validatePassword(password: $inputs['password'])
            : true;

        $validation['passwordsMatch'] = isset($inputs['password'], $inputs['password-confirm'])
            ? $this->passwordsMatch(password: $inputs['password'], passwordConfirm: $inputs['password-confirm']) : true;

        // todo. validate files parameters, size, etc...
        $validation['picture'] = isset($files['picture'])
            ? $this->validatePicture(picture: $files['picture'])
            : true;

        $validation['phone'] = isset($inputs['phone'])
            ? $this->validatePhone(phone: $inputs['phone'])
            : true;

        $validation['city'] = isset($inputs['city'])
            ? $this->validateCity(city: $inputs['city'])
            : true;

        $validation['about'] = isset($inputs['about'])
            ? $this->validateAbout(about: $inputs['about'])
            : true;

        return $this->check($validation);
    }

    private function check(array $validation): bool
    {
        foreach ($validation as $value) {
            if (!$value) {
                return false;
            }
        }

        return true;
    }

    /**
     * 
     */
    private function validateName(string $name): bool
    {
        if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
            ErrorMessage::add('name', 'Invalid username.');
            return false;
        }

        if (strlen($name) > 128) {
            ErrorMessage::add('name', 'Username should have less than 128 characters.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function validateEmail(string $email, string $httpMethod): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ErrorMessage::add('email', 'Invalid email input.');
            return false;
        }

        if ($httpMethod === 'POST' and $this->userRepository->findByEmail($email)) {
            ErrorMessage::add('email', 'Email already in use.');
            return false;
        }

        if (strlen($email) > 128) {
            ErrorMessage::add('email', 'Email should have less than 128 characters.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function validatePassword(string $password): bool
    {
        if (!preg_match('/^[a-zA-Z0-9\.\_\#]+$/', $password)) {
            ErrorMessage::add('password', 'Invalid password input.');
            return false;
        }

        if (strlen($password) < 8) {
            ErrorMessage::add('password', 'Password should have at least 8 characters.');
            return false;
        }

        if (strlen($password) > 128) {
            ErrorMessage::add('password', 'Password should have less than 128 characters.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function passwordsMatch(string $password, string $passwordConfirm): bool
    {
        if ($password !== $passwordConfirm) {
            ErrorMessage::add('password', 'Passwords does not match.');
            ErrorMessage::add('password-confirm', 'Passwords does not match.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function validatePicture(string $picture): bool
    {
        //todo
        // uploaded formats jpeg, jpg, png only
        // max size = 5MB

        // max-lenght 256 characters
        if (strlen($picture) > 256) {
            ErrorMessage::add('picture', 'Picture should have less than 128 characters.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function validatePhone(string $phone): bool
    {
        if (!is_numeric($phone)) {
            ErrorMessage::add('phone', 'Phone should be numeric.');
            return false;
        }

        if (strlen($phone) != 11) {
            ErrorMessage::add('phone', 'Phone must have 11 digits.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function validateCity(string $city): bool
    {
        if (!preg_match('/^[a-zA-Z\s]+$/', $city)) {
            ErrorMessage::add('city', 'Invalid city name input.');
            return false;
        }

        if (strlen($city) > 128) {
            ErrorMessage::add('city', 'City name should have less than 128 characters.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function validateAbout(string $about): bool
    {
        if (!preg_match('/^[\w\d\s\,\.\?\!\:\;\"\']+$/', $about)) {
            ErrorMessage::add('about', 'Invalid about field input.');
            return false;
        }

        if (strlen($about) > 500) {
            ErrorMessage::add('about', 'About field should have less than 500 characters.');
            return false;
        }

        return true;
    }
}
