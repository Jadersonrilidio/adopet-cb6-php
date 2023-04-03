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
    'GET|/api/tutors' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'all'],
    'GET|/api/tutors/{id}' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'find'],
    'POST|/api/tutors' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'store'],
    'PUT|/api/tutors/{id}' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'update'],
    'PATCH|/api/tutors/{id}' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'update'],
    'DELETE|/api/tutors/{id}' => [Jayrods\ScubaPHP\Controller\API\TutorController::class, 'remove'],
);
