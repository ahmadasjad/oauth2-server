<?php


namespace app\lib\twig\extension;


use Psr\Container\ContainerInterface;
use Slim\Flash\Messages;
use Twig\Extension\AbstractExtension;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class FlashMessage extends AbstractExtension
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('flashes', [$this, 'getFlashes']),
        ];
    }

    public function getFlashes(){
        $flash = $this->container->get('flash');
        /**
         * @var $flash Messages
         */
        return $flash->getMessages();
        //return print_r($flash->getMessages());
    }
}