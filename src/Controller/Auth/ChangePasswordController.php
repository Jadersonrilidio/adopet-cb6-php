<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Auth;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Controller\Traits\{PasswordHandler, SSLEncryption};
use Jayrods\ScubaPHP\Controller\Validation\ChangePasswordValidator;
use Jayrods\ScubaPHP\Http\Core\{Request, Response, Router, View};
use Jayrods\ScubaPHP\Entity\User;
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;
use Jayrods\ScubaPHP\Repository\JsonUserRepository;

class ChangePasswordController extends Controller
{
    use PasswordHandler,
        SSLEncryption;

    /**
     * 
     */
    private const EXPIRATION_TIME = 86400;

    /**
     * 
     */
    private JsonUserRepository $userRepository;

    /**
     * 
     */
    private ChangePasswordValidator $changePasswordValidator;

    /**
     * 
     */
    public function __construct(Request $request, View $view, FlashMessage $flashMsg)
    {
        parent::__construct($request, $view, $flashMsg);

        $this->changePasswordValidator = new ChangePasswordValidator($flashMsg);
        $this->userRepository = new JsonUserRepository();
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

        $passwordErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('password-errors')
        );

        $passwordConfirmErrorComponent = $this->view->renderErrorMessageComponent(
            errorMessages: $this->flashMsg->getArray('password-confirm-errors')
        );

        $content = $this->view->renderView(
            template: 'auth/change_password',
            content: array(
                'status' => $statusComponent,
                'password-errors' => $passwordErrorComponent,
                'password-confirm-errors' => $passwordConfirmErrorComponent,
                'token-value' => $this->request->queryParams('token'),
                'password-value' => $this->flashMsg->get('password-value'),
                'password-confirm-value' => $this->flashMsg->get('password-confirm-value'),
            )
        );

        $page = $this->view->renderlayout('Change Passoword', $content);

        return new Response($page);
    }

    /**
     * 
     */
    public function alterPassword(): Response
    {
        $this->changePasswordValidator->validate($this->request);

        $token = $this->SSLDecrypt($this->request->postVars('token'));
        $token = explode('=', $token);

        $email = $token[0];

        $requestTimestamp = $token[1];
        $today = time();
        $elapsedTime = $today - $requestTimestamp;

        if ($elapsedTime > self::EXPIRATION_TIME) {
            $this->flashMsg->set(array(
                'status-class' => 'mensagem-error',
                'status-message' => 'Change password token has expired.',
            ));

            Router::redirect('login');
        }

        $user = $this->userRepository->findByEmail($email);

        $updatedUser = new User(
            name: $user->name(),
            email: $user->email(),
            verified: $user->verified(),
            password: $this->passwordHash($this->request->postVars('password')),
        );

        if (!$this->userRepository->update($updatedUser)) {
            $this->flashMsg->set(array(
                'status-class' => 'mensagem-erro',
                'status-message' => 'Not possible to update user.',
            ));

            Router::redirect('login');
        }

        $this->flashMsg->set(array(
            'status-class' => 'mensagem-sucesso',
            'status-message' => 'Password redefined with sucess.',
        ));

        Router::redirect('login');
        exit;
    }
}
