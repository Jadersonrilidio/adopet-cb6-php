<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Auth;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\{PasswordHandler, SSLEncryption};
use Jayrods\ScubaPHP\Controller\Validation\RegisterValidator;
use Jayrods\ScubaPHP\Http\Core\{Request, Response, Router, View};
use Jayrods\ScubaPHP\Entity\User;
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;
use Jayrods\ScubaPHP\Repository\JsonUserRepository;
use Jayrods\ScubaPHP\Service\MailService;

class RegisterController extends Controller
{
    use PasswordHandler,
        SSLEncryption;

    /**
     * 
     */
    private JsonUserRepository $userRepository;

    /**
     * 
     */
    private RegisterValidator $registerValidator;

    /**
     * 
     */
    private MailService $mail;

    /**
     * 
     */
    public function __construct(Request $request, View $view, FlashMessage $flashMsg)
    {
        parent::__construct($request, $view, $flashMsg);

        $this->registerValidator = new RegisterValidator($flashMsg);
        $this->userRepository = new JsonUserRepository();
        $this->mail = new MailService();
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

        $nameErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('name-errors')
        );

        $emailErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('email-errors')
        );

        $passwordErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('password-errors')
        );

        $passwordConfirmErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('password-confirm-errors')
        );

        $content = $this->view->renderView(
            template: 'auth/register',
            content: array(
                'status' => $statusComponent,
                'name-errors' => $nameErrorComponent,
                'email-errors' => $emailErrorComponent,
                'password-errors' => $passwordErrorComponent,
                'password-confirm-errors' => $passwordConfirmErrorComponent,
                'name-value' => $this->flashMsg->get('name-value'),
                'email-value' => $this->flashMsg->get('email-value'),
                'password-value' => $this->flashMsg->get('password-value'),
                'password-confirm-value' => $this->flashMsg->get('password-confirm-value'),
            )
        );

        $page = $this->view->renderLayout('Register', $content);

        return new Response($page);
    }

    /**
     * 
     */
    public function register(): Response
    {
        $this->registerValidator->validate($this->request);

        $user = new User(
            name: $this->request->postVars('name'),
            email: $this->request->postVars('email'),
            password: $this->passwordHash($this->request->postVars('password'))
        );

        $this->userRepository->create($user);

        $token = $this->SSLCrypt($user->email());

        $link = APP_URL . SLASH . "verify-email?token=$token";

        $this->mail->sendMail(
            to: $user->email(),
            name: $user->name(),
            subject: 'Scuba PHP account verification.',
            body: "Hi there! Click on the following link to verify your account: $link."
        );

        Router::redirect('login');
        exit;
    }
}
