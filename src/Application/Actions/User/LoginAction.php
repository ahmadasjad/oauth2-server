<?php


namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class LoginAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        return $this->render('user/login.twig');
    }
}