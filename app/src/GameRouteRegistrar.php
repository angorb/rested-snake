<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

use Angorb\RestedSnake\GameRequestHandlerInterface;

class GameRouteRegistrar implements RouteRegistrarInterface
{
    public static function registerRoutes(\League\Route\RouteCollectionInterface $router): void
    {
        // Register the routes for this controller. Example:
        //$router->map('GET', '/app/html', [self::class, 'htmlResponse']);
        //$router->map('GET', '/app/json', [self::class, 'jsonResponse']);

        // TODO create router group

        // Register the route for creating a new game
        $router->map('POST', '/game', [GameRequestHandlerInterface::class, 'createGame']);

        // Register the route for getting the current game state
        $router->map('GET', '/game', [GameRequestHandlerInterface::class, 'getGameState']);

        // Register the route for moving the snake
        $router->map('POST', '/move', [GameRequestHandlerInterface::class, 'moveSnake']);
    }
}
