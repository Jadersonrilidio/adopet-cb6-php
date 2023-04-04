<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\API;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\FileStorageHandler;
use Jayrods\ScubaPHP\Controller\Traits\StandandJsonResponse;
use Jayrods\ScubaPHP\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Controller\Validation\UserValidator;
use Jayrods\ScubaPHP\Entity\User\Role;
use Jayrods\ScubaPHP\Entity\User\User;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\JsonResponse;
use Jayrods\ScubaPHP\Http\Core\View;
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;
use Jayrods\ScubaPHP\Repository\UserRepository\SQLiteUserRepository;
use Jayrods\ScubaPHP\Repository\UserRepository\UserRepository;

class UserController extends Controller
{
    use FileStorageHandler,
        PasswordHandler,
        StandandJsonResponse;

    /**
     * 
     */
    private UserRepository $userRepository;

    /**
     * 
     */
    private UserValidator $userValidator;

    /**
     * 
     */
    public function __construct(Request $request, View $view, FlashMessage $flashMsg)
    {
        parent::__construct($request, $view, $flashMsg);

        $this->userRepository = new SQLiteUserRepository();
        $this->userValidator = new UserValidator();
    }

    /**
     * 
     */
    public function all(): JsonResponse
    {
        $content = $this->userRepository->all();

        return new JsonResponse($content, 200);
    }

    /**
     * 
     */
    public function store(): JsonResponse
    {
        if (!$this->userValidator->validate($this->request)) {
            return $this->errorMessagesJsonResponse();
        }

        $user = new User(
            name: $this->request->inputs('name'),
            email: $this->request->inputs('email'),
            password: $this->passwordHash($this->request->inputs('password'))
        );

        if (!$this->userRepository->save($user)) {
            return $this->errorJsonResponse('Not possible to create user.');
        }

        return new JsonResponse($user, 201);
    }

    /**
     * 
     */
    public function find(): JsonResponse
    {
        $user = $this->userRepository->find((int) $this->request->uriParams('id'));

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found.'], 404);
            return $this->notFoundJsonResponse('User not found.');
        }

        return new JsonResponse($user, 200);
    }

    /**
     * 
     */
    public function update(): JsonResponse
    {
        if (!$this->userValidator->validate($this->request)) {
            return $this->errorMessagesJsonResponse();
        }

        $user = $this->userRepository->find((int) $this->request->uriParams('id'));

        if (!$user instanceof User) {
            return $this->notFoundJsonResponse('User not found.');
        }

        $newUser = new User(
            name: $this->request->inputs('name') ?? $user->name(),
            email: $this->request->inputs('email') ?? $user->email(),
            emailVerified: $user->emailVerified(),
            password: $user->password(),
            id: $user->id(),
            picture: $this->request->files('picture')['hashname'] ?? $user->picture(),
            phone: $this->request->inputs('phone') ?? $user->phone(),
            city: $this->request->inputs('city') ?? $user->city(),
            about: $this->request->inputs('about') ?? $user->about(),
            role: Role::from($this->request->inputs('role') ?? $user->roleValue()),
            created_at: $user->createdAt(),
            updated_at: $user->updatedAt()
        );

        if (!$this->userRepository->save($newUser)) {
            return $this->errorJsonResponse('Error on update user.');
        }

        if ($this->request->files('picture') !== null and !$this->storeFile($this->request->files('picture'))) {
            return $this->errorJsonResponse('Error on storing files.');
        }

        $this->deleteFile($user->picture());

        return new JsonResponse($newUser, 200);
    }

    /**
     * 
     */
    public function remove(): JsonResponse
    {
        $user = $this->userRepository->find((int) $this->request->uriParams('id'));

        if (!$user instanceof User) {
            return $this->notFoundJsonResponse('User not found.');
        }

        $this->userRepository->remove($user);

        $this->deleteFile($user->picture());

        return new JsonResponse($user, 200);
    }
}
