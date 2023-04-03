<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Auth;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Http\Core\Request;
use Jayrods\ScubaPHP\Http\Core\Response;
use Jayrods\ScubaPHP\Http\Core\Router;
use Jayrods\ScubaPHP\Http\Core\View;
use Jayrods\ScubaPHP\Infrastructure\Auth;
use Jayrods\ScubaPHP\Infrastructure\FlashMessage;

class LogoutController extends Controller
{
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

        $this->auth = new Auth();
    }

    /**
     * 
     */
    public function logout(): Response
    {
        if ($this->auth->authLogout()) {
            $this->flashMsg->set(array(
                'status-class' => 'mensagem-sucesso',
                'status-message' => 'User logged out.',
            ));

            Router::redirect('login');
        }

        Router::redirect();
        exit;
    }
}
