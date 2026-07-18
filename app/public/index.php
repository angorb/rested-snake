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

$applicationStrategy = new \League\Route\Strategy\ApplicationStrategy();
$applicationStrategy->setContainer($container);
$jsonStrategy = new \League\Route\Strategy\JsonStrategy($responseFactory);
$jsonStrategy->setContainer($container);

$router = new \League\Route\Router();
$router->setStrategy($applicationStrategy);

// API
$router->group('/v1/snake', function ($router) {
    \Angorb\RestedSnake\Controller::registerRoutes($router);
})
// TODO ->middleware(\Authentication\Token::auth($request))
->setStrategy($jsonStrategy);

// emit the HTTP response
try {
    $response = $router->dispatch($request);
} catch (\League\Route\Http\Exception $ex) {
    $response = $ex->buildJsonResponse($responseFactory->createResponse());
} catch (\Error | \Exception $ex) {
  // TODO - log the error
    $response = new \Laminas\Diactoros\Response\JsonResponse([
        'error' => $ex->getMessage(),
    ], 500);
}

 (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
