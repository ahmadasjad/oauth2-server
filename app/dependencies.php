<?php
declare(strict_types=1);

use App\OAuth2\Repositories\AccessTokenRepository;
use App\OAuth2\Repositories\ClientRepository;
use App\OAuth2\Repositories\RefreshTokenRepository;
use App\OAuth2\Repositories\ScopeRepository;
use App\OAuth2\Repositories\UserRepository;
use DI\ContainerBuilder;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseManager;

use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Spiral\Tokenizer;
use Cycle\Schema;
use Cycle\Annotated;
use Cycle\ORM\Schema as ORMSchema;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
    ]);
    $containerBuilder->addDefinitions([
        'dbal' => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $dbConfig = new DatabaseConfig([
                'default' => 'default',
                'databases' => [
                    'default' => [
                        'connection' => 'mysql'
                    ]
                ],
                'connections' => $settings['connections'],
            ]);
            $dbal = new DatabaseManager($dbConfig);
            return $dbal;
        },
    ]);
    $containerBuilder->addDefinitions([
        'orm' => function (ContainerInterface $c) {

            // Class locator
            $cl = (new Tokenizer\Tokenizer(new Tokenizer\Config\TokenizerConfig([
                'directories' => [ROOT_DIR . '/models'],
            ])))->classLocator();
            $dbal = $c->get('dbal');
            $schema = (new Schema\Compiler())->compile(new Schema\Registry($dbal), [
                new Annotated\Embeddings($cl),            // register embeddable entities
                new Annotated\Entities($cl),              // register annotated entities
                new Schema\Generator\ResetTables(),       // re-declared table schemas (remove columns)
                new Annotated\MergeColumns(),             // copy column declarations from all related classes (@Table annotation)
                new Schema\Generator\GenerateRelations(), // generate entity relations
                new Schema\Generator\ValidateEntities(),  // make sure all entity schemas are correct
                new Schema\Generator\RenderTables(),      // declare table schemas
                new Schema\Generator\RenderRelations(),   // declare relation keys and indexes
                new Annotated\MergeIndexes(),             // copy index declarations from all related classes (@Table annotation)
                new Schema\Generator\SyncTables(),        // sync table changes to database
                new Schema\Generator\GenerateTypecast(),  // typecast non string columns
            ]);

            $orm = new ORM(new Factory($dbal));
            $orm = $orm->withSchema(new ORMSchema($schema));
            return $orm;
        },
    ]);
    $containerBuilder->addDefinitions([
        'flash' => function (ContainerInterface $c) {
            if (!session_id()) {
                session_start();
            }
            return new \Slim\Flash\Messages();
        }
    ]);
    $containerBuilder->addDefinitions([
        AuthorizationServer::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            // Init our repositories
            $clientRepository = new ClientRepository();
            $accessTokenRepository = new AccessTokenRepository();
            $scopeRepository = new ScopeRepository();
            $refreshTokenRepository = new RefreshTokenRepository();

            // Setup the authorization server
            $server = new AuthorizationServer(
                $clientRepository,
                $accessTokenRepository,
                $scopeRepository,
                $settings['privateKeyPath'],
                'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen'
            );

            // Enable the refresh token grant on the server
            $refreshTokenGrant = new RefreshTokenGrant($refreshTokenRepository);
            $refreshTokenGrant->setRefreshTokenTTL(new \DateInterval('P1M')); // The refresh token will expire in 1 month
            $server->enableGrantType(
                $refreshTokenGrant,
                new \DateInterval('PT1H') // The new access token will expire after 1 hour
            );

            $passwordGrant = new PasswordGrant(
                new UserRepository(),           // instance of UserRepositoryInterface
                $refreshTokenRepository    // instance of RefreshTokenRepositoryInterface
            );
            $passwordGrant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month
            // Enable the password grant on the server with a token TTL of 1 hour
            $server->enableGrantType(
                $passwordGrant,
                new \DateInterval('PT1H') // access tokens will expire after 1 hour
            );

            return $server;
        },
        ResourceServer::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');


            $server = new ResourceServer(
                new AccessTokenRepository(),
                $settings['publicKeyPath']
            );

            return $server;
        },
    ]);
};
