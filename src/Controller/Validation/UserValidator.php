<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Validation;

use Jayrods\ScubaPHP\Controller\Validation\Validator;
use Jayrods\ScubaPHP\Entity\State;
use Jayrods\ScubaPHP\Entity\User\User;
use Jayrods\ScubaPHP\Infrastructure\ErrorMessage;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Repository\UserRepository\SQLiteUserRepository;
use Jayrods\ScubaPHP\Repository\UserRepository\UserRepository;

class UserValidator implements Validator
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
    private UserRepository $userRepository;

    /**
     * 
     */
    public function __construct(SQLiteUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * 
     */
    public function validate(Request $request): bool
    {
        $validation = [];

        $validation['name'] = $request->inputs('name')
            ? $this->validateName(name: $request->inputs('name'))
            : true;

        $validation['email'] = $request->inputs('email')
            ? $this->validateEmail(email: $request->inputs('email'), request: $request)
            : true;

        $validation['password'] = ($request->inputs('password') and $request->inputs('password-confirm'))
            ? $this->validatePassword(password: $request->inputs('password'))
            : true;

        $validation['passwordsMatch'] = ($request->inputs('password') and $request->inputs('password-confirm'))
            ? $this->passwordsMatch(password: $request->inputs('password'), passwordConfirm: $request->inputs('password-confirm'))
            : true;

        $validation['picture'] = $request->files('picture')
            ? $this->validatePicture(picture: $request->files('picture'))
            : true;

        $validation['phone'] = $request->inputs('phone')
            ? $this->validatePhone(phone: $request->inputs('phone'))
            : true;

        $validation['city'] = $request->inputs('city')
            ? $this->validateCity(city: $request->inputs('city'))
            : true;

        $validation['state'] = $request->inputs('state')
            ? $this->validateState(state: $request->inputs('state'))
            : true;

        $validation['about'] = $request->inputs('about')
            ? $this->validateAbout(about: $request->inputs('about'))
            : true;

        return $this->check($validation);
    }

    /**
     * 
     */
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
    public function validateName(string $name): bool
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
    public function validateEmail(string $email, Request $request): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ErrorMessage::add('email', 'Invalid email input.');
            return false;
        }

        $tutor = $this->userRepository->findByEmail($email);

        if ($request->httpMethod() == 'POST' and $tutor instanceof User) {
            ErrorMessage::add('email', 'Email already in use.');
            return false;
        }

        if ($request->httpMethod() != 'POST' and $tutor instanceof User and $tutor->id() != $request->uriParams('id')) {
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
    public function validatePassword(string $password): bool
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
    public function passwordsMatch(string $password, string $passwordConfirm): bool
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
    public function validatePicture(array $picture): bool
    {
        $realFile = $picture['tmp_name'];
        $realFileSize = filesize($realFile);
        $realFileType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $realFile);

        if ($picture['error'] != UPLOAD_ERR_OK) {
            ErrorMessage::add('picture', 'Something went wrong on upload.');
            return false;
        }

        if (in_array($realFileType, $this->allowedFileFormats) === false) {
            ErrorMessage::add('picture', 'Picture must be on formats ' . $this->allowedFileFormatsToString() . ' only.');
            return false;
        }

        if ($realFileSize >= ((int) ini_get("upload_max_filesize")) * 1024 * 1024) {
            ErrorMessage::add('picture', 'Picture should have less than 10MB size.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function allowedFileFormatsToString(): string
    {
        $allowedFormatsString = implode(', ', $this->allowedFileFormats);

        return str_replace('image/', '', $allowedFormatsString);
    }

    /**
     * 
     */
    public function validatePhone(string $phone): bool
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
    public function validateCity(string $city): bool
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
    public function validateState(string $state): bool
    {
        if (State::tryFrom($state) === null) {
            ErrorMessage::add('state', 'Invalid state.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    public function validateAbout(string $about): bool
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
