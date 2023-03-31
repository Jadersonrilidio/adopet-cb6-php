<?php

namespace Jayrods\ScubaPHP\Controller\API;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Entity\Tutor;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\Response;
use Jayrods\ScubaPHP\Http\Core\View;
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;
use Jayrods\ScubaPHP\Repository\JsonTutorRepository;

class TutorController extends Controller
{
    use PasswordHandler;

    /**
     * 
     */
    private JsonTutorRepository $tutorRepository;

    /**
     * 
     */
    public function __construct(Request $request, View $view, FlashMessage $flashMsg)
    {
        parent::__construct($request, $view, $flashMsg);

        $this->tutorRepository = new JsonTutorRepository();
    }

    /**
     * 
     */
    public function all(): Response
    {
        $content = $this->tutorRepository->all();

        return new Response(
            content: json_encode($content),
            httpCode: 200,
            contentType: 'application/json',
            headers: []
        );
    }

    /**
     * 
     */
    public function store()
    {
        $tutor = new Tutor(
            name: $this->request->postVars('name'),
            email: $this->request->postVars('email'),
            password: $this->request->postVars('password')
        );

        $tutor->createIdentity();

        if (!$this->tutorRepository->create($tutor)) {
            return new Response(
                content: json_encode(['error' => 'Not possible to create tutor.']),
                httpCode: 404,
                contentType: 'application/json',
                headers: []
            );
        }

        return new Response(
            content: json_encode($tutor),
            httpCode: 201,
            contentType: 'application/json',
            headers: []
        );
    }

    /**
     * 
     */
    public function find()
    {
        $tutor = $this->tutorRepository->find($this->request->uriParams('uid'));

        if (!$tutor instanceof Tutor) {
            return new Response(
                content: json_encode(['error' => 'Tutor not found']),
                httpCode: 404,
                contentType: 'application/json',
                headers: []
            );
        }

        return new Response(
            content: json_encode($tutor),
            httpCode: 200,
            contentType: 'application/json',
            headers: []
        );
    }

    /**
     * 
     */
    public function update()
    {
        $tutor = $this->tutorRepository->findByEmail($this->request->uriParams('uid'));

        if (!$tutor instanceof Tutor) {
            return new Response(
                content: json_encode(['error' => 'Tutor not found']),
                httpCode: 404,
                contentType: 'application/json',
                headers: []
            );
        }

        $newTutor = new Tutor(
            name: $this->request->postVars('name') ?? $tutor->name(),
            email: $this->request->postVars('email') ?? $tutor->email(),
            password: $this->passwordHash($this->request->postVars('password')) ?? $tutor->password(),
        );

        $newTutor->createIdentity($this->request->uriParams('uid'));

        if (!$this->tutorRepository->update($newTutor)) {
            return new Response(
                content: json_encode(['error' => 'Error on update tutor.']),
                httpCode: 404,
                contentType: 'application/json',
                headers: []
            );
        }

        return new Response(
            content: json_encode($newTutor),
            httpCode: 201,
            contentType: 'application/json',
            headers: []
        );
    }

    /**
     * 
     */
    public function remove()
    {
        $tutor = $this->tutorRepository->find($this->request->uriParams('uid'));

        if (!$tutor instanceof Tutor) {
            return new Response(
                content: json_encode(['error' => 'Tutor not found']),
                httpCode: 404,
                contentType: 'application/json',
                headers: []
            );
        }

        $this->tutorRepository->remove($tutor);

        return new Response(
            content: json_encode($tutor),
            httpCode: 200,
            contentType: 'application/json',
            headers: []
        );
    }
}
