<?php

namespace app\controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class Client
{
    public function register(ServerRequestInterface $request, ResponseInterface $response, array $args=[]): ResponseInterface
   {
        // your code to access items in the container... $this->container->get('');
        $view = Twig::fromRequest($request);
        return $view->render($response, 'client/register.twig', [
          // 'name' => $args['name'],
          'name' => 'Ahmad',
        ]);
        $response->getBody()->write("Hello world!");
        return $response;
   }

}

