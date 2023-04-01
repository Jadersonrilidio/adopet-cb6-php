<?php

declare(strict_types=1);

/**
 * Mapping, with keys representing the route and values containing the parameters:
 * controller class, controller method and an array of middlewares.
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
    'GET|/api/tutor' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'all', []],
    'GET|/api/tutor/{id}' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'find', []],
    'POST|/api/tutor' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'store', []],
    'PUT|/api/tutor/{id}' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'update', []],
    'PATCH|/api/tutor/{id}' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'update', []],
    'DELETE|/api/tutor/{id}' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'remove', []],
);
