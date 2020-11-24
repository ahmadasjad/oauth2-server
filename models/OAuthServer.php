<?php


namespace app\models;


use app\lib\Record;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

/**
 * @Entity(
 *     name="oauth_server_registry"
 * )
 */
class OAuthServer extends Record
{
    /**
     * @Column(type="primary")
     * @var int
     */
    protected $id;


    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    protected $application_url;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    protected $callback_url;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $public_key;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $secret_key;

}
