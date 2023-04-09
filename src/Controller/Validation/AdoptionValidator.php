<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Validation;

use Jayrods\ScubaPHP\Controller\Validation\Validator;
use Jayrods\ScubaPHP\Entity\Adoption\Status;
use Jayrods\ScubaPHP\Infrastructure\ErrorMessage;
use Jayrods\ScubaPHP\Http\Core\Request;

class AdoptionValidator implements Validator
{
    /**
     * 
     */
    public function validate(Request $request): bool
    {
        $validation = [];

        $validation['status'] = $request->inputs('status')
            ? $this->validateStatus(status: (int) $request->inputs('status'))
            : true;

        $validation['pet_id'] = $request->inputs('pet_id')
            ? $this->validatePetId(pet_id: (int) $request->inputs('pet_id'))
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
    public function validatePetId(int $pet_id): bool
    {
        if (is_integer($pet_id)) {
            ErrorMessage::add('pet_id', 'Invalid pet id: must be integer number.');
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
}
