<?php


namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Views\Twig;

class RegisterAction extends Action
{

    protected function action(): Response
    {
//        return $this->render('user/register.twig');
        $view = Twig::fromRequest($this->request);
        return $view->render($this->response, 'user/register.twig', [
            // 'name' => $args['name'],
//            'name' => 'Ahmad',
        ]);
//        $this->response->getBody()->write("Hello world!");
//        return $this->response;
    }
}