<?php
declare(strict_types=1);

use App\Application\Controllers\Client;
use App\Application\Controllers\Dashboard;
use App\Application\Controllers\User;
use DI\Container;
use DI\ContainerBuilder;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;


//Initial constants
define('ROOT_DIR', dirname(__DIR__));
define('DEBUG', true);

if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

require_once(ROOT_DIR . '/vendor/autoload.php');

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
$twig = Twig::create(ROOT_DIR . '/templates',
    [
        'debug' => DEBUG,
        // 'cache' => dirname(__DIR__). '/cache',
        'cache' => !DEBUG,
    ]);
$twig->addExtension($container->get(\app\lib\twig\extension\FlashMessage::class));
//$twig->getLoader()->

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
$app->post('/user/login', [User::class, 'authenticate']);
$app->get('/user/authorize', [User::class, 'authorize']);
$app->get('/user/list', [User::class, 'list']);
$app->get('/client/register', [Client::class, 'viewForm']);
$app->post('/client/register', [Client::class, 'register']);

$app->group('/protected', function (RouteCollectorProxy $group) {
    $group->get('/test', function (Request $request, Response $response, array $args) {
        $response->getBody()->write('Your are accessing content restricted with oauth2');

        return $response;
    });
})->add(new \App\OAuth2\ResourceServerMiddleware($app->getContainer()->get(ResourceServer::class)));

$app->run();
