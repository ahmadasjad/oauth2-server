<?php


namespace App\Application\Controllers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class Dashboard
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args=[]): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'dashboard/index.html', [
        ]);
    }

}
