<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Validation;

use DateTime;
use DateTimeInterface;
use Jayrods\ScubaPHP\Controller\Validation\Validator;
use Jayrods\ScubaPHP\Entity\Pet\Size;
use Jayrods\ScubaPHP\Entity\Pet\Species;
use Jayrods\ScubaPHP\Entity\Pet\Status;
use Jayrods\ScubaPHP\Entity\State;
use Jayrods\ScubaPHP\Infrastructure\ErrorMessage;
use Jayrods\ScubaPHP\Http\Core\Request;

class PetValidator implements Validator
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
    public function validate(Request $request): bool
    {
        $validation = [];

        $validation['name'] = $request->inputs('name')
            ? $this->validateName(name: $request->inputs('name'))
            : true;

        $validation['description'] = $request->inputs('description')
            ? $this->validateDescription(description: $request->inputs('description'))
            : true;

        $validation['species'] = ($request->inputs('species'))
            ? $this->validateSpecies(species: (int) $request->inputs('species'))
            : true;

        $validation['size'] = ($request->inputs('size'))
            ? $this->validateSize(size: (int) $request->inputs('size'))
            : true;

        $validation['status'] = ($request->inputs('status'))
            ? $this->validateStatus(status: (int) $request->inputs('status'))
            : true;

        $validation['birth_date'] = ($request->inputs('birth_date'))
            ? $this->validatebirthDate(birth_date: $request->inputs('birth_date'))
            : true;

        $validation['picture'] = $request->files('picture')
            ? $this->validatePicture(picture: $request->files('picture'))
            : true;

        $validation['city'] = $request->inputs('city')
            ? $this->validateCity(city: $request->inputs('city'))
            : true;

        $validation['state'] = $request->inputs('state')
            ? $this->validateState(state: $request->inputs('state'))
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
            ErrorMessage::add('name', 'Invalid pet name.');
            return false;
        }

        if (strlen($name) > 64) {
            ErrorMessage::add('name', 'Pet name should have less than 64 characters.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    public function validateDescription(string $description): bool
    {
        if (!preg_match('/^[a-zA-Z\s]+$/', $description)) {
            ErrorMessage::add('description', 'Invalid description.');
            return false;
        }

        if (strlen($description) > 128) {
            ErrorMessage::add('description', 'Description should have less than 128 characters.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    public function validateSpecies(int $species): bool
    {
        if (Species::tryFrom($species) === null) {
            ErrorMessage::add('species', 'Invalid pet species.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    public function validateSize(int $size): bool
    {
        if (Size::tryFrom($size) === null) {
            ErrorMessage::add('size', 'Invalid pet size.');
            return false;
        }

        return true;
    }

    /**
     * 
     */
    public function validateStatus(int $status): bool
    {
        if (Status::tryFrom($status) === null) {
            ErrorMessage::add('status', 'Invalid status.');
            return false;
        }

        return true;
    }

    /**
     * //todo check if this function really works
     */
    public function validateBirthDate(string $birth_date): bool
    {
        $date = DateTime::createFromFormat('Y-m-d', $birth_date);

        if (!$date instanceof DateTimeInterface or $date->format('Y-m-d') !== $birth_date) {
            ErrorMessage::add('birth_date', 'Invalid birthdate format (try: YYYY-MM-DD).');
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
}
