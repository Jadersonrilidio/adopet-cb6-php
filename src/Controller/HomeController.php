<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller;

use Jayrods\ScubaPHP\Controller\Controller;
use Jayrods\ScubaPHP\Http\Core\{Request, Response, View};
use Jayrods\ScubaPHP\Infrastructure\{Auth, FlashMessage};

class HomeController extends Controller
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
    public function index(): Response
    {
        $user = $this->auth->authUser();

        $statusComponent = $this->view->renderStatusComponent(
            statusClass: $this->flashMsg->get('status-class'),
            statusMessage: $this->flashMsg->get('status-message')
        );

        $content = $this->view->renderView(
            template: 'home',
            content: array(
                'status' => $statusComponent,
                'user-name' => $user->name(),
                'user-email' => $user->email(),
                'error-message' => $this->flashMsg->get('error-message')
            )
        );

        $page = $this->view->renderlayout('Home', $content);

        return new Response($page);
    }
}
