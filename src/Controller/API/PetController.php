<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\API;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\FileStorageHandler;
use Jayrods\ScubaPHP\Controller\Traits\StandandJsonResponse;
use Jayrods\ScubaPHP\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Controller\Validation\PetValidator;
use Jayrods\ScubaPHP\Entity\Pet\Pet;
use Jayrods\ScubaPHP\Entity\Pet\Size;
use Jayrods\ScubaPHP\Entity\Pet\Species;
use Jayrods\ScubaPHP\Entity\Pet\Status;
use Jayrods\ScubaPHP\Entity\State;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\JsonResponse;
use Jayrods\ScubaPHP\Repository\PetRepository\SqlitePetRepository;

class PetController extends Controller
{
    use FileStorageHandler,
        PasswordHandler,
        StandandJsonResponse;

    /**
     * 
     */
    private SqlitePetRepository $petRepository;

    /**
     * 
     */
    private PetValidator $petValidator;

    /**
     * 
     */
    public function __construct(SqlitePetRepository $petRepository, PetValidator $petValidator)
    {
        $this->petRepository = $petRepository;
        $this->petValidator = $petValidator;
    }

    /**
     * 
     */
    public function all(Request $request): JsonResponse
    {
        $pets = $this->petRepository->all();

        return new JsonResponse($pets, 200);
    }

    // /**
    //  * //test
    //  */
    // public function search(Request $request): JsonResponse
    // {
    //     // http://localhost:8001/api/pets?with=user&by=size:mini,small,medium;species:dog,cat;
    //     //fix: A Better alternative would be: http://localhost:8001/api/pets?size=mini,small,medium&species=dog,cat;

    //     $with = $request->queryParams('with');
    //     $by = $request->queryParams('by');

    //     $by = explode(';', $by);

    //     $searchParams = [];

    //     foreach ($by as $attribute) {
    //         $tempArray = explode(':', $attribute);

    //         $attribute = $tempArray[0];
    //         $valuesArray = explode(',', $tempArray[1]);

    //         $searchParams[$attribute] = $valuesArray;
    //     }

    //     $content = $this->petRepository->search($searchParams, $with);

    //     return new JsonResponse($content, 200);
    // }

    /**
     * 
     */
    public function store(Request $request): JsonResponse
    {
        //todo: validate whether user can create a pet
        // if ($this->auth->authUser('role') !== Role::Shelter) {
        //     return $this->forbiddenJsonResponse();
        // }

        if (!$this->petValidator->validate($request)) {
            return $this->errorMessagesJsonResponse();
        }

        $pet = new Pet(
            name: $request->inputs('name'),
            description: $request->inputs('description'),
            user_id: (int) $request->inputs('user_id') /*(int) $this->auth->authUser('id')*/, //todo
            species: Species::from((int) $request->inputs('species')),
            size: Size::from((int) $request->inputs('size')),
            status: Status::Available,
            birth_date: $request->inputs('birth_date'),
            picture: $request->files('picture')['hashname'],
            city: $request->inputs('city'),
            state: State::from($request->inputs('state'))
        );

        if (!$this->petRepository->save($pet)) {
            return $this->errorJsonResponse('Not possible to create pet.');
        }

        if ($request->files('picture') !== null and !$this->storeFile($request->files('picture'))) {
            return $this->errorJsonResponse('Error on storing files.');
        }

        return new JsonResponse($pet, 201);
    }

    /**
     * 
     */
    public function find(Request $request): JsonResponse
    {
        $pet = $this->petRepository->find((int) $request->uriParams('id'));

        if (!$pet instanceof Pet) {
            return $this->notFoundJsonResponse('Pet not found.');
        }

        return new JsonResponse($pet, 200);
    }

    /**
     * 
     */
    public function update(Request $request): JsonResponse
    {
        //todo: STEP01 - validate whether user can create a pet
        // if ($this->auth->authUser('role') !== Role::Shelter) {
        //     return $this->forbiddenJsonResponse();
        // }

        if (!$this->petValidator->validate($request)) {
            return $this->errorMessagesJsonResponse();
        }

        $pet = $this->petRepository->find((int) $request->uriParams('id'));

        if (!$pet instanceof Pet) {
            return $this->notFoundJsonResponse('Pet not found.');
        }

        //todo: STEP02 - validate whether user can update a pet
        // if ($pet->userId() != $this->auth->authUser('id')) {
        //     return $this->forbiddenJsonResponse();
        // }

        $updatedPet = new Pet(
            name: $request->inputs('name') ?? $pet->name(),
            description: $request->inputs('description') ?? $pet->description(),
            id: $pet->id(),
            user_id: $pet->userId(),
            species: is_null($request->inputs('species')) ? $pet->species() : Species::from((int) $request->inputs('species')),
            size: is_null($request->inputs('size')) ? $pet->size() : Size::from((int) $request->inputs('size')),
            status: is_null($request->inputs('status')) ? $pet->status() : Status::from((int) $request->inputs('status')),
            birth_date: $request->inputs('birth_date') ?? $pet->birthDate(),
            picture: $request->files('picture')['hashname'] ?? $pet->picture(),
            city: $request->inputs('city') ?? $pet->city(),
            state: is_null($request->inputs('state')) ? $pet->state() : State::from($request->inputs('state')),
            created_at: $pet->createdAt(),
            updated_at: $pet->updatedAt(),
        );

        if (!$this->petRepository->save($updatedPet)) {
            return $this->errorJsonResponse('Error on update pet.');
        }

        if ($request->files('picture') !== null and !$this->storeFile($request->files('picture'))) {
            return $this->errorJsonResponse('Error on storing files.');
        }

        if ($request->files('picture') !== null and !$this->deleteFile($pet->picture())) {
            return $this->errorJsonResponse('Error on deleting files.');
        }

        return new JsonResponse($updatedPet, 200);
    }

    /**
     * 
     */
    public function remove(Request $request): JsonResponse
    {
        $pet = $this->petRepository->find((int) $request->uriParams('id'));

        //todo: validate whether user can delete a pet
        // if ($pet->userId() != $this->auth->authUser('id')) {
        //     return $this->forbiddenJsonResponse();
        // }

        if (!$pet instanceof Pet) {
            return $this->notFoundJsonResponse('Pet not found.');
        }

        if (!$this->petRepository->remove($pet)) {
            return $this->errorJsonResponse('Error on removing pet.');
        }

        if ($pet->picture() !== null and !$this->deleteFile($pet->picture())) {
            return $this->errorJsonResponse('Error on deleting files.');
        }

        return new JsonResponse($pet, 200);
    }
}
