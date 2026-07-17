<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

use Angorb\RestedSnake\SnakeGame\Board;
use Angorb\RestedSnake\SnakeGame\FoodSpawner;
use Angorb\RestedSnake\SnakeGame\GameEngine;
use Angorb\RestedSnake\SnakeGame\Snake;
use Psr\Http\Message\ServerRequestInterface;

class Controller implements GameRequestHandlerInterface, RouteRegistrarInterface
{
    public function __construct(
        private GameRepositoryInterface $gameRepository
    ) {
    }

    /**
     * Creates a new game instance
     *
     * @return array
     */
    public function createGame(ServerRequestInterface $request, array $args): array
    {
        $board = new Board(20, 10);
        $snake = new Snake(10, 5, 'right');

        $foodSpawner = new FoodSpawner($board);
        $foodSpawner->spawnFood($snake);

        $game = new GameEngine(
            $board,
            $snake,
            $foodSpawner
        );

        $this->gameRepository->saveGameState($game->jsonSerialize());
        return $this->loadGameState();
    }

    /**
     * Retrieves the current game state
     *
     * @return array
     */
    public function getGameState(ServerRequestInterface $request, array $args): array
    {
        return $this->loadGameState();
    }

    private function loadGameState(): array
    {
        return $this->gameRepository->loadGameState() ?? [];
    }

    /**
     * Moves the snake in the specified direction and updates the game state
     *
     * @param string $direction The direction to move the snake ('up', 'down', 'left', 'right')
     * @return array The updated game state after moving the snake
     * @throws \RuntimeException If no game state is found when trying to move the snake
     */
    public function moveSnake(ServerRequestInterface $request, array $args): array
    {
        $gameState = $this->loadGameState();
        if (empty($gameState)) {
            throw new \RuntimeException('No game state found. Please create a game first.');
        }

        $game = GameEngine::fromArray($gameState);
        $game->changeSnakeDirection($args['direction']);
        $game->tick();

        $gameState = $game->jsonSerialize();
        $this->gameRepository->saveGameState($gameState);
        return $this->loadGameState();
    }

    public function test(ServerRequestInterface $request, array $args): array
    {
        return [
            'status' => 'ok',
            'message' => "Hello, $args[word]!",
        ];
    }

    public static function registerRoutes(\League\Route\RouteCollectionInterface $router): void
    {
        $router->map('POST', '/game', [self::class, 'createGame']);
        $router->map('GET', '/game', [self::class, 'getGameState']);
        $router->map('POST', '/snake/move/{direction}', [self::class, 'moveSnake']);

        $router->map('GET', '/test/{word}', [self::class, 'test']); // DEBUG route for testing purposes
    }
}
