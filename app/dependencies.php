<?php
declare(strict_types=1);

use DI\ContainerBuilder;
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
                'default'     => 'default',
                'databases'   => [
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
                'directories' => [ROOT_DIR.'/models'],
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
};
