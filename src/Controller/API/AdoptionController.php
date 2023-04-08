<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\API;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\StandandJsonResponse;
use Jayrods\ScubaPHP\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Controller\Validation\AdoptionValidator;
use Jayrods\ScubaPHP\Entity\Adoption\Adoption;
use Jayrods\ScubaPHP\Entity\Adoption\Status as AdoptionStatus;
use Jayrods\ScubaPHP\Entity\Pet\Pet;
use Jayrods\ScubaPHP\Entity\Pet\Status as PetStatus;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\JsonResponse;
use Jayrods\ScubaPHP\Repository\AdoptionRepository\SqliteAdoptionRepository;
use Jayrods\ScubaPHP\Repository\AdoptionRepository\AdoptionRepository;
use Jayrods\ScubaPHP\Repository\PetRepository\PetRepository;
use Jayrods\ScubaPHP\Repository\PetRepository\SqlitePetRepository;

class AdoptionController extends Controller
{
    use PasswordHandler,
        StandandJsonResponse;

    /**
     * 
     */
    private AdoptionRepository $adoptionRepository;

    /**
     * 
     */
    private AdoptionValidator $adoptionValidator;

    /**
     * 
     */
    private PetRepository $petRepository;

    /**
     * 
     */
    public function __construct(SqliteAdoptionRepository $adoptionRepository, AdoptionValidator $adoptionValidator, SqlitePetRepository $petRepository)
    {
        $this->adoptionRepository = $adoptionRepository;
        $this->adoptionValidator = $adoptionValidator;

        $this->petRepository = $petRepository;
    }

    /**
     * 
     */
    public function all(Request $request): JsonResponse
    {
        $content = $this->adoptionRepository->all();

        return new JsonResponse($content, 200);
    }

    /**
     * 
     */
    public function store(Request $request): JsonResponse
    {
        //todo: validate whether user request adoption
        if ($this->auth->authUser('role') !== Role::Tutor) {
            return $this->forbiddenJsonResponse();
        }

        $pet = $this->petRepository->find((int) $request->inputs('pet_id'));

        if ($pet->status() !== PetStatus::Available) {
            return $this->errorJsonResponse('Pet not available.');
        }

        $newAdoption = new Adoption(
            user_id: (int) $this->auth->authUser('id'),
            pet_id: (int) $request->inputs('pet_id'),
            status: AdoptionStatus::Requested
        );

        if (!$this->adoptionRepository->save($newAdoption)) {
            return $this->errorJsonResponse('Not possible to create adoption.');
        }

        //test: update pet status after adoption request

        $pet->suspend();

        if (!$this->petRepository->save($pet)) {
            return $this->errorJsonResponse('Error on update pet status.');
        }

        //todo: Database must commit all saved changes at once or roll back modifications

        return new JsonResponse($newAdoption, 201);
    }

    /**
     * 
     */
    public function find(Request $request): JsonResponse
    {
        $adoption = $this->adoptionRepository->find((int) $request->uriParams('id'));

        if (!$adoption instanceof Adoption) {
            return $this->notFoundJsonResponse('Adoption not found.');
        }

        return new JsonResponse($adoption, 200);
    }

    /**
     * 
     */
    private function update(Request $request)
    {
        //todo: validate whether user change adoption & pet status
        // Tutor IS ABLE to cancel adoption ONLY
        if ($this->auth->authUser('role') === Role::Tutor and AdoptionStatus::from((int) $request->uriParams('status')) !== AdoptionStatus::Canceled) {
            return $this->forbiddenJsonResponse();
        }

        // Shelter IS ABLE to confirm or reprove adoption
        if ($this->auth->authUser('role') === Role::Shelter and (AdoptionStatus::from((int) $request->uriParams('status')) !== AdoptionStatus::Adopted or Status::from((int) $request->uriParams('status')) !== Status::Reproved)) {
            return $this->forbiddenJsonResponse();
        }

        $adoption = $this->adoptionRepository->find((int) $request->uriParams('id'));

        if (!$adoption instanceof Adoption) {
            return $this->notFoundJsonResponse('Adoption not found.');
        }

        $pet = $this->petRepository->find($adoption->petId());

        if (!$pet instanceof Pet) {
            return $this->notFoundJsonResponse('Pet not found.');
        }

        if ($this->adoptionValidator->validateStatus((int) $request->uriParams('status'))) {
            return $this->errorMessagesJsonResponse();
        }

        $status = AdoptionStatus::from((int) $request->uriParams('status'));

        $this->changeAdoptionStatus($status, $adoption);
        $this->changePetStatus($status, $pet);

        $adoption->updateDate();

        if (!$this->adoptionRepository->save($adoption)) {
            return $this->errorJsonResponse('Error on update adoption status.');
        }

        if (!$this->petRepository->save($pet)) {
            return $this->errorJsonResponse('Error on update pet status.');
        }

        //todo: Database must commit all saved changes at once or roll back modifications

        return new JsonResponse($adoption, 200);
    }

    /**
     * 
     */
    private function changeAdoptionStatus(AdoptionStatus $status, Adoption $adoption): void
    {
        match ($status) {
            AdoptionStatus::Requested => $adoption->requestAdoption(),
            AdoptionStatus::Adopted => $adoption->confirmAdoption(),
            AdoptionStatus::Canceled => $adoption->cancelAdoption(),
            AdoptionStatus::Reproved => $adoption->reproveAdoption(),
            AdoptionStatus::Suspended => $adoption->suspendAdoption(),
        };
    }

    /**
     * 
     */
    private function changePetStatus(AdoptionStatus $status, Pet $pet): void
    {
        match ($status) {
            AdoptionStatus::Requested => $pet->suspend(),
            AdoptionStatus::Adopted => $pet->adopt(),
            AdoptionStatus::Canceled => $pet->available(),
            AdoptionStatus::Reproved => $pet->available(),
            AdoptionStatus::Suspended => $pet->available()
        };
    }

    /**
     * 
     */
    public function remove(Request $request): JsonResponse
    {
        //todo: validate whether user can delete adoption
        // There has no rules for Adoption removal from database, only final status as Canceled, Reproved, Adopted or Suspended.

        $adoption = $this->adoptionRepository->find((int) $request->uriParams('id'));

        if (!$adoption instanceof Adoption) {
            return $this->notFoundJsonResponse('Adoption not found.');
        }

        $this->adoptionRepository->remove($adoption);

        //todo: update pet status after adoption deleted

        $pet = $this->petRepository->find($adoption->petId());

        if (!$pet instanceof Pet) {
            return $this->notFoundJsonResponse('Pet not found.');
        }

        $pet->available();

        if (!$this->petRepository->save($pet)) {
            return $this->errorJsonResponse('Error on update pet status.');
        }

        return new JsonResponse($adoption, 200);
    }
}
