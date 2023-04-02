<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Validation;

use Jayrods\ScubaPHP\Controller\Validation\Validator;
use Jayrods\ScubaPHP\Entity\Tutor;
use Jayrods\ScubaPHP\Infrastructure\ErrorMessage;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Repository\SQLiteTutorRepository;
use Jayrods\ScubaPHP\Repository\TutorRepository;

class TutorValidator implements Validator
{
    /**
     * 
     */
    private array $allowedFileFormats = array(
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/svg',
    );

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
        $inputs = $request->inputs();
        $files = $request->files();

        $validation = [];

        $validation['name'] = $request->inputs('name')
            ? $this->validateName(name: $request->inputs('name'))
            : true;

        $validation['email'] = $request->inputs('email')
            ? $this->validateEmail(email: $request->inputs('email'), httpMethod: $request->httpMethod(), request: $request)
            : true;

        $validation['password'] = ($request->inputs('password') and $request->inputs('password-confirm'))
            ? $this->validatePassword(password: $request->inputs('password'))
            : true;

        $validation['passwordsMatch'] = ($request->inputs('password') and $request->inputs('password-confirm'))
            ? $this->passwordsMatch(password: $request->inputs('password'), passwordConfirm: $request->inputs('password-confirm')) : true;

        $validation['picture'] = $request->files('picture')
            ? $this->validatePicture(picture: $request->files('picture'))
            : true;

        $validation['phone'] = $request->inputs('phone')
            ? $this->validatePhone(phone: $request->inputs('phone'))
            : true;

        $validation['city'] = $request->inputs('city')
            ? $this->validateCity(city: $request->inputs('city'))
            : true;

        $validation['about'] = $request->inputs('about')
            ? $this->validateAbout(about: $request->inputs('about'))
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
    private function validateEmail(string $email, string $httpMethod, Request $request): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ErrorMessage::add('email', 'Invalid email input.');
            return false;
        }

        $tutor = $this->userRepository->findByEmail($email);

        if ($httpMethod === 'POST' and $tutor instanceof Tutor) {
            ErrorMessage::add('email', 'Email already in use.');
            return false;
        }

        if ($httpMethod !== 'POST' and $tutor instanceof Tutor and $tutor->id() != $request->uriParams('id')) {
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
    private function validatePicture(array $picture): bool
    {
        if (array_search($picture['type'], $this->allowedFileFormats) === false) {
            ErrorMessage::add('picture', 'Picture must be on formats ' . $this->allowedFormatsToString() . ' only.');
            return false;
        }

        if ($picture['error'] != 0) {
            ErrorMessage::add('picture', 'Something went wrong on upload.');
            return false;
        }

        if ($picture['size'] > 10240000) {
            ErrorMessage::add('picture', 'Picture should have less than 10MB size.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function allowedFormatsToString(): string
    {
        $allowedFormatsString = implode(', ', $this->allowedFileFormats);

        return str_replace('image/', '', $allowedFormatsString);
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
