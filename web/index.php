<?php
declare(strict_types=1);

use app\controllers\Client;
use app\controllers\Dashboard;
use app\controllers\User;
use Cycle\Annotated;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema as ORMSchema;
use Cycle\Schema;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseManager;
use Spiral\Tokenizer;

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

//Initia constants
define('ROOT_DIR', dirname(__DIR__));
define('DEBUG', true);

require_once(ROOT_DIR.'/vendor/autoload.php');

//Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_DIR);
$dotenv->load();

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require ROOT_DIR . '/app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require ROOT_DIR . '/app/dependencies.php';
$dependencies($containerBuilder);


// Build PHP-DI Container instance
$container = $containerBuilder->build();

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();
$container->set(Request::class, $request);
//$container->set(Response::class, \DI\create(\Slim\psr7\Response::class));

// Set container to create App with on AppFactory
//AppFactory::setContainer($container);
//$app = AppFactory::createFromContainer($container);
$app = \DI\Bridge\Slim\Bridge::create($container);

// Create Twig
$twig = Twig::create(ROOT_DIR. '/templates',
    [
        'debug' => DEBUG,
        // 'cache' => dirname(__DIR__). '/cache',
        'cache' => !DEBUG,
    ]);

// Add Middlewares
$app->add(TwigMiddleware::create($app, $twig));

// $app->get('/', function (Request $request, Response $response, $args) {
//     $response->getBody()->write("Hello world!");
//     return $response;
// });
$app->get('/', [Dashboard::class, 'index']);
$app->get('/user/register', \App\Application\Actions\User\RegisterAction::class);
$app->post('/user/register', \App\Application\Actions\User\SaveAction::class);
$app->get('/user/login', \App\Application\Actions\User\LoginAction::class);
$app->post('/user/login', [User::class , 'authenticate']);
$app->get('/user/authorize', [User::class , 'authorize']);
$app->get('/user/list', [User::class , 'list']);
$app->get('/client/register', [Client::class , 'register']);

$app->run();
