<?php


namespace App\Application\Controllers;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class BaseController
{
    protected ContainerInterface $container;
    protected ServerRequestInterface $request;
    protected ResponseInterface $response;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

}