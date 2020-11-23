<?php


namespace app\controllers;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class BaseController
{
    protected ContainerInterface $container;
    protected ServerRequestInterface $request;
    protected ResponseInterface $response;
    public function __construct(ContainerInterface $container)
//    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
//        var_dump($this->container->get('request'));
//        $this->request = $request;
//        $this->response = $response;
    }

    /**
     * @param $template
     * @param array $args
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function render($template, $args=[]){
        $view = Twig::fromRequest($this->request);
//        return $view->render($this->response, $template, $args);
        $this->response->getBody()->write($view->fetch($template, $args));
//        $view->fetch($template, $data);
    }
}