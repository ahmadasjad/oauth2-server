<?php

namespace app\models;

use app\lib\Record;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;


/**
 * @Entity
 */
class User extends Record
{
    /**
     * @Column(type="primary")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @Column(type="string", nullable=true)
     *  @var string
     */
    protected $password;

    /**
     * @Column(type="string")
     *  @var string
     */
    protected $email;

    /**
     * @Column(type="date", default="now", nullable=true)
     *  @var string
     */
    protected $created;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}