<?php

declare(strict_types=1);

require_once __DIR__ . '/../configuration/init.rc.php';

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

// set up request routing and response environment and DI container
$responseFactory = new \Laminas\Diactoros\ResponseFactory();

$container = new \League\Container\Container();
$container->delegate(new \League\Container\ReflectionContainer());
$container->add(
    \Psr\Http\Message\ServerRequestInterface::class,
    $request
);

// set up the game repository to use a file for storing game state
$repositoryFilePath = __DIR__ . '/../data/game_state.json';
$repository = new \Angorb\RestedSnake\BasicFileRepository($repositoryFilePath);
$container->add(\Angorb\RestedSnake\GameRepositoryInterface::class, $repository);

// add requests to the container so they can be injected into route handlers
//$container->add(\Namespace\Somthingt::class)->addArgument($request);

$applicationStrategy = new \League\Route\Strategy\ApplicationStrategy();
$applicationStrategy->setContainer($container);
$jsonStrategy = new \League\Route\Strategy\JsonStrategy($responseFactory);
$jsonStrategy->setContainer($container);

$router = new \League\Route\Router();
$router->setStrategy($applicationStrategy);

// API
$router->group('/v1/snake', function ($router) {
    \Angorb\RestedSnake\Controller::registerRoutes($router);
})// TODO ->middleware(\Authentication\Token::auth($request))
  ->setStrategy($jsonStrategy);

// emit the HTTP response
try {
    // DEBUG
    ray($router, $container, $request);
    $response = $router->dispatch($request);
    (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
} catch (\Error | \Exception $ex) {
  // TODO - log the error
    throw $ex; // rethrow the exception to be handled by the framework or error handler
}
