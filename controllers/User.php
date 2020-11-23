<?php

namespace app\controllers;

use app\models\User as ModelsUser;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Transaction;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Throwable;

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
//        echo '<pre>';
//        var_dump($data);
//        echo '</pre>';
        $view = Twig::fromRequest($request);
        return $view->render($response, 'user/list.twig', [
            // 'name' => $args['name'],
            'users' => $data,
        ]);
    }

}

