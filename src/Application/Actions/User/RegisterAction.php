<?php


namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class RegisterAction extends Action
{

    protected function action(): Response
    {
        $view = Twig::fromRequest($this->request);
        return $view->render($this->response, 'user/register.twig', [
        ]);
    }
}