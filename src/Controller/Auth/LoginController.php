<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Auth;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Traits\PasswordHandler;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\Response;
use Jayrods\ScubaPHP\Http\Core\Router;
use Jayrods\ScubaPHP\Http\Core\View;
use Jayrods\ScubaPHP\Entity\User;
use Jayrods\ScubaPHP\Infrastructure\Auth;
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;
use Jayrods\ScubaPHP\Repository\UserRepository\JsonUserRepository;
use Jayrods\ScubaPHP\Repository\UserRepository\UserRepository;

class LoginController extends Controller
{
    use PasswordHandler;

    /**
     * 
     */
    private UserRepository $userRepository;

    /**
     * 
     */
    private Auth $auth;

    /**
     * 
     */
    public function __construct(Request $request, View $view, FlashMessage $flashMsg)
    {
        parent::__construct($request, $view, $flashMsg);

        $this->userRepository = new JsonUserRepository();
        $this->auth = new Auth();
    }

    /**
     * 
     */
    public function index(): Response
    {
        $statusComponent = $this->view->renderStatusComponent(
            statusClass: $this->flashMsg->get('status-class'),
            statusMessage: $this->flashMsg->get('status-message')
        );

        $emailErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('email-errors')
        );

        $passwordErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('password-errors')
        );

        $content = $this->view->renderView(
            template: 'auth/login',
            content: [
                'status' => $statusComponent,
                'email-errors' => $emailErrorComponent,
                'password-errors' => $passwordErrorComponent,
                'email-value' => $this->flashMsg->get('email-value'),
                'password-value' => $this->flashMsg->get('password-value'),
            ]
        );

        $page = $this->view->renderLayout('Login', $content);

        return new Response($page);
    }

    /**
     * 
     */
    public function login(): Response
    {
        $user = $this->userRepository->findByEmail(
            email: $this->request->inputs('email')
        );

        $passwordCheck = $this->passwordVerify(
            password: $this->request->inputs('password'),
            hash: $user instanceof User ? $user->password() : ''
        );

        $validEmailAndPassword = $this->validEmailAndPassword(
            passwordCheck: $passwordCheck
        );

        $emailIsVerified = $this->emailIsVerified(
            user: $user
        );

        if (!$validEmailAndPassword or !$emailIsVerified) {
            Router::redirect('login');
        }

        if ($this->passwordNeedRehash($user->password())) {
            $this->passwordRehash(
                user: $user,
                password: $this->request->inputs('password')
            );
        }

        $this->auth->authenticate($user);

        $this->flashMsg->set(array(
            'status-class' => 'mensagem-sucesso',
            'status-message' => "Welcome back, {$user->name()}!"
        ));

        Router::redirect();
        exit;
    }

    /**
     * 
     */
    private function validEmailAndPassword(bool $passwordCheck): bool
    {
        if (!$passwordCheck) {
            !$this->flashMsg->set([
                'status-class' => 'mensagem-erro',
                'status-message' => 'Invalid email or password.',
                'email-value' => $this->request->inputs('email'),
                'password-value' => $this->request->inputs('password'),
            ]);

            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function emailIsVerified(User|bool $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!$user->verified()) {
            $this->flashMsg->set([
                'status-class' => 'mensagem-erro',
                'status-message' => 'Email not verified.',
                'email-value' => $this->request->inputs('email'),
                'password-value' => $this->request->inputs('password'),
            ]);

            return false;
        }

        return true;
    }

    /**
     * 
     */
    private function passwordRehash(User $user, string $password): bool
    {
        return $this->userRepository->update(
            new User(
                name: $user->name(),
                email: $user->email(),
                password: $this->passwordHash($password),
                verified: $user->verified()
            )
        );
    }
}
