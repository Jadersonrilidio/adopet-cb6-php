<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\API;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Controller\Validation\TutorValidator;
use Jayrods\ScubaPHP\Entity\Tutor;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\JsonResponse;
use Jayrods\ScubaPHP\Http\Core\View;
use Jayrods\ScubaPHP\Infrastructure\ErrorMessage;
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;
use Jayrods\ScubaPHP\Repository\SQLiteTutorRepository;
use Jayrods\ScubaPHP\Repository\TutorRepository;

class TutorController extends Controller
{
    use PasswordHandler;

    /**
     * 
     */
    private TutorRepository $tutorRepository;

    /**
     * 
     */
    private TutorValidator $tutorValidator;

    /**
     * 
     */
    public function __construct(Request $request, View $view, FlashMessage $flashMsg)
    {
        parent::__construct($request, $view, $flashMsg);

        $this->tutorRepository = new SQLiteTutorRepository();
        $this->tutorValidator = new TutorValidator();
    }

    /**
     * 
     */
    public function all(): JsonResponse
    {
        $content = $this->tutorRepository->all();

        return new JsonResponse($content, 200);
    }

    /**
     * 
     */
    public function store(): JsonResponse
    {
        if (!$this->tutorValidator->validate($this->request)) {
            return new JsonResponse(['errors' => ErrorMessage::errorMessages()], 400);
        }

        $tutor = new Tutor(
            name: $this->request->postVars('name'),
            email: $this->request->postVars('email'),
            password: $this->passwordHash($this->request->postVars('password'))
        );

        if (!$this->tutorRepository->save($tutor)) {
            return new JsonResponse(['error' => 'Not possible to create tutor.'], 404);
        }

        return new JsonResponse($tutor, 201);
    }

    /**
     * 
     */
    public function find(): JsonResponse
    {
        $tutor = $this->tutorRepository->find((int) $this->request->uriParams('id'));

        if (!$tutor instanceof Tutor) {
            return new JsonResponse(['error' => 'Tutor not found.'], 404);
        }

        return new JsonResponse($tutor, 200);
    }

    /**
     * 
     */
    public function update(): JsonResponse
    {
        if (!$this->tutorValidator->validate($this->request)) {
            return new JsonResponse(['errors' => ErrorMessage::errorMessages()], 400);
        }
        
        $tutor = $this->tutorRepository->find((int) $this->request->uriParams('id'));

        if (!$tutor instanceof Tutor) {
            return new JsonResponse(['error' => 'Tutor not found.'], 404);
        }

        $newTutor = new Tutor(
            name: $this->request->putVars('name') ?? $tutor->name(),
            email: $this->request->putVars('email') ?? $tutor->email(),
            password: $tutor->password(),
            id: $tutor->id(),
            picture: $this->request->putVars('picture') ?? $tutor->picture(),
            phone: $this->request->putVars('phone') ?? $tutor->phone(),
            city: $this->request->putVars('city') ?? $tutor->city(),
            about: $this->request->putVars('about') ?? $tutor->about(),
            created_at: $tutor->createdAt(),
            updated_at: $tutor->updatedAt()
        );

        if (!$this->tutorRepository->save($newTutor)) {
            return new JsonResponse(['error' => 'Error on update tutor.'], 404);
        }

        return new JsonResponse($newTutor, 201);
    }

    /**
     * 
     */
    public function remove(): JsonResponse
    {
        $tutor = $this->tutorRepository->find((int) $this->request->uriParams('id'));

        if (!$tutor instanceof Tutor) {
            return new JsonResponse(['error' => 'Tutor not found.'], 404);
        }

        $this->tutorRepository->remove($tutor);

        return new JsonResponse($tutor, 200);
    }
}
