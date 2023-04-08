<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\API;

use Jayrods\ScubaPHP\Controller\API\ApiController;
use Jayrods\ScubaPHP\Controller\Traits\FileStorageHandler;
use Jayrods\ScubaPHP\Controller\Traits\StandandJsonResponse;
use Jayrods\ScubaPHP\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Controller\Validation\UserValidator;
use Jayrods\ScubaPHP\Entity\State;
use Jayrods\ScubaPHP\Entity\User\User;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\JsonResponse;
use Jayrods\ScubaPHP\Repository\UserRepository\SqliteUserRepository;
use Jayrods\ScubaPHP\Repository\UserRepository\UserRepository;

class UserController extends ApiController
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
    public function __construct(SqliteUserRepository $userRepository, UserValidator $userValidator)
    {
        $this->userRepository = $userRepository;
        $this->userValidator = $userValidator;
    }

    /**
     * 
     */
    public function all(Request $request): JsonResponse
    {
        $content = $this->userRepository->all();

        return new JsonResponse($content, 200);
    }

    /**
     * 
     */
    public function store(Request $request): JsonResponse
    {
        if (!$this->userValidator->validate($request)) {
            return $this->errorMessagesJsonResponse();
        }

        $user = new User(
            name: $request->inputs('name'),
            email: $request->inputs('email'),
            password: $this->passwordHash($request->inputs('password'))
        );

        if (!$this->userRepository->save($user)) {
            return $this->errorJsonResponse('Not possible to create user.');
        }

        return new JsonResponse($user, 201);
    }

    /**
     * 
     */
    public function find(Request $request): JsonResponse
    {
        $user = $this->userRepository->find((int) $request->uriParams('id'));

        if (!$user instanceof User) {
            return $this->notFoundJsonResponse('User not found.');
        }

        return new JsonResponse($user, 200);
    }

    /**
     * 
     */
    public function update(Request $request): JsonResponse
    {
        if (!$this->userValidator->validate($request)) {
            return $this->errorMessagesJsonResponse();
        }

        $user = $this->userRepository->find((int) $request->uriParams('id'));

        if (!$user instanceof User) {
            return $this->notFoundJsonResponse('User not found.');
        }

        $updatedUser = new User(
            name: $request->inputs('name') ?? $user->name(),
            email: $request->inputs('email') ?? $user->email(),
            emailVerified: $user->emailVerified(),
            password: $user->password(),
            id: $user->id(),
            picture: $request->files('picture')['hashname'] ?? $user->picture(),
            phone: $request->inputs('phone') ?? $user->phone(),
            city: $request->inputs('city') ?? $user->city(),
            state: $request->inputs('state') ? State::from($request->inputs('state')) : ($user->state() ? $user->state() : null),
            about: $request->inputs('about') ?? $user->about(),
            role: $user->role(),
            created_at: $user->createdAt(),
            updated_at: $user->updatedAt()
        );

        if (!$this->userRepository->save($updatedUser)) {
            return $this->errorJsonResponse('Error on update user.');
        }

        if ($request->files('picture') !== null and !$this->storeFile($request->files('picture'))) {
            return $this->errorJsonResponse('Error on storing files.');
        }

        $this->deleteFile($user->picture());

        return new JsonResponse($updatedUser, 200);
    }

    /**
     * 
     */
    public function remove(Request $request): JsonResponse
    {
        $user = $this->userRepository->find((int) $request->uriParams('id'));

        if (!$user instanceof User) {
            return $this->notFoundJsonResponse('User not found.');
        }

        $this->userRepository->remove($user);

        $this->deleteFile($user->picture());

        return new JsonResponse($user, 200);
    }
}
