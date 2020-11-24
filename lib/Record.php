<?php

namespace app\lib;

use Cycle\ORM\Transaction;
use Psr\Container\ContainerInterface;
use Throwable;

abstract class Record {
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * @throws Throwable
     */
    public function save()
    {
        $tr = new Transaction($this->container->get('orm'));
        $tr->persist($this);
        $tr->run();
    }
}
