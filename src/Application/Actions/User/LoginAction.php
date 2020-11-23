<?php


namespace App\Application\Actions\User;


use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Views\Twig;

class LoginAction extends \App\Application\Actions\Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        // your code to access items in the container... $this->container->get('');
        $view = Twig::fromRequest($this->request);
        return $view->render($this->response, 'user/login.twig', [
            // 'name' => $args['name'],
            'name' => 'Ahmad',
        ]);
//        $this->response->getBody()->write("Hello world!");
//        return $this->response;
    }
}