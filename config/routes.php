<?php

declare(strict_types=1);

/**
 * Routes map, with keys containing the routes and values containing the parameters controller, method and middlewares to execute.
 */
return array(
    // Web Auth Routes
    'GET|/register' => [Jayrods\ScubaPHP\Controller\Auth\RegisterController::class, 'index', ['guest']],
    'POST|/register' => [Jayrods\ScubaPHP\Controller\Auth\RegisterController::class, 'register', ['guest']],
    'GET|/login' => [Jayrods\ScubaPHP\Controller\Auth\LoginController::class, 'index', ['guest']],
    'POST|/login' => [Jayrods\ScubaPHP\Controller\Auth\LoginController::class, 'login', ['guest']],
    'GET|/logout' => [Jayrods\ScubaPHP\Controller\Auth\LogoutController::class, 'logout', ['auth']],
    'GET|/delete-account' => [Jayrods\ScubaPHP\Controller\Auth\DeleteAccountController::class, 'deleteAccount', ['auth']],
    'GET|/forget-password' => [Jayrods\ScubaPHP\Controller\Auth\ForgetPasswordController::class, 'index', ['guest']],
    'POST|/forget-password' => [Jayrods\ScubaPHP\Controller\Auth\ForgetPasswordController::class, 'sendMail', ['guest']],
    'GET|/change-password' => [Jayrods\ScubaPHP\Controller\Auth\ChangePasswordController::class, 'index', ['guest']],
    'POST|/change-password' => [Jayrods\ScubaPHP\Controller\Auth\ChangePasswordController::class, 'alterPassword', ['guest']],
    'GET|/verify-email' => [Jayrods\ScubaPHP\Controller\Auth\EmailVerificationController::class, 'verifyEmail', ['guest']],

    // Web Routes
    'GET|/' => [Jayrods\ScubaPHP\Controller\HomeController::class, 'index', ['auth']],

    // Web Fallback Route
    'fallback' => [Jayrods\ScubaPHP\Controller\NotFoundController::class, 'index'],

    // API Routes
    'GET|/api/users' => [Jayrods\ScubaPHP\Controller\API\UserController::class, 'all'],
    'GET|/api/users/{id}' => [Jayrods\ScubaPHP\Controller\API\UserController::class, 'find'],
    'POST|/api/users' => [Jayrods\ScubaPHP\Controller\API\UserController::class, 'store'],
    'PUT|/api/users/{id}' => [Jayrods\ScubaPHP\Controller\API\UserController::class, 'update'],
    'PATCH|/api/users/{id}' => [Jayrods\ScubaPHP\Controller\API\UserController::class, 'update'],
    'DELETE|/api/users/{id}' => [Jayrods\ScubaPHP\Controller\API\UserController::class, 'remove'],

    'GET|/api/pets' => [Jayrods\ScubaPHP\Controller\API\PetController::class, 'all'],
    'GET|/api/pets/{id}' => [Jayrods\ScubaPHP\Controller\API\PetController::class, 'find'],
    'POST|/api/pets' => [Jayrods\ScubaPHP\Controller\API\PetController::class, 'store'],
    'PUT|/api/pets/{id}' => [Jayrods\ScubaPHP\Controller\API\PetController::class, 'update'],
    'PATCH|/api/pets/{id}' => [Jayrods\ScubaPHP\Controller\API\PetController::class, 'update'],
    'DELETE|/api/pets/{id}' => [Jayrods\ScubaPHP\Controller\API\PetController::class, 'remove'],

    'GET|/api/adoptions' => [Jayrods\ScubaPHP\Controller\API\AdoptionController::class, 'all'],
    'GET|/api/adoptions/{id}' => [Jayrods\ScubaPHP\Controller\API\AdoptionController::class, 'find'],
    'POST|/api/adoptions' => [Jayrods\ScubaPHP\Controller\API\AdoptionController::class, 'store'],
    'PUT|/api/adoptions/{id}/{status}' => [Jayrods\ScubaPHP\Controller\API\AdoptionController::class, 'update'],
    'PATCH|/api/adoptions/{id}/{status}' => [Jayrods\ScubaPHP\Controller\API\AdoptionController::class, 'update'],
    'DELETE|/api/adoptions/{id}' => [Jayrods\ScubaPHP\Controller\API\AdoptionController::class, 'remove'],
);
