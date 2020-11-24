<?php

namespace App\Application\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;

class Client extends BaseController
{
    public function viewForm(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $flash = $this->container->get('flash');
        /**
         * @var $flash Messages
         */
        $flash->addMessage('Test', 'This is a message');
        $flash->addMessage('Test', 'This is a message');
        $flash->addMessage('danger', 'This is a message');
        $flash->addMessage('primary', 'This is a message');
        $view = Twig::fromRequest($request);
        return $view->render($response, 'client/register.twig');
    }

    public function register(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {

    }

}

