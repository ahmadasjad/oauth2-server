<?php

namespace App\Application\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class Client
{
    public function register(ServerRequestInterface $request, ResponseInterface $response, array $args=[]): ResponseInterface
   {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'client/register.twig', [
        ]);
   }

}

