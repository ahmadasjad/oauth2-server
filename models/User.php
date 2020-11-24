<?php

namespace app\models;

use app\lib\Record;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Relation\Embedded;
use app\models\OAuthServer;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Psr\Container\ContainerInterface;


/**
 * @Entity
 */
class User extends Record
{

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->oauth = new OAuthServer($container);
    }

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
     * @Column(type="timestamp", default="CURRENT_TIMESTAMP")
     *  @var string
     */
    protected $created_at;

    /**
     * @Column(type="timestamp", default="CURRENT_TIMESTAMP", onUpdate="CURRENT_TIMESTAMP")
     *  @var string
     */
    protected $updated_at;

    /**
     * @HasOne(target = "OAuthServer")
     */
    protected $oauth;

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