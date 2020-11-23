<?php

namespace App\Application\Controllers;

use App\Application\Controllers\BaseController;
use app\models\User as ModelsUser;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class User extends BaseController
{

    public function authenticate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $response->getBody()->write("Hello world!");
        return $response;
    }

    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args=[]): ResponseInterface
    {
        $orm = $this->container->get('orm');
        $r = $orm->getRepository(ModelsUser::class);
        $data = $r->findAll();
        $view = Twig::fromRequest($request);
        return $view->render($response, 'user/list.twig', [
            'users' => $data,
        ]);
    }

}

