<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

use Angorb\RestedSnake\SnakeGame\AsciiRenderer;
use Angorb\RestedSnake\SnakeGame\Board;
use Angorb\RestedSnake\SnakeGame\FoodSpawner;
use Angorb\RestedSnake\SnakeGame\GameEngine;
use Angorb\RestedSnake\SnakeGame\Snake;
use Angorb\RestedSnake\SnakeGame\Sprites\TextSprite;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Controller implements GameRequestHandlerInterface, RouteRegistrarInterface
{
    public function __construct(
        private GameRepositoryInterface $gameRepository
    ) {
    }

    /**
     * Creates a new game instance
     *
     * @return ResponseInterface
     */
    public function createGame(ServerRequestInterface $request, array $args): ResponseInterface
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

        return new \Laminas\Diactoros\Response\JsonResponse($this->loadGameState());
    }

    /**
     * Retrieves the current game state
     *
     * @return ResponseInterface
     */
    public function getGameState(ServerRequestInterface $request, array $args): ResponseInterface
    {
        return new \Laminas\Diactoros\Response\JsonResponse($this->loadGameState());
    }

    /**
     * Moves the snake in the specified direction and updates the game state
     *
     * @param string $direction The direction to move the snake ('up', 'down', 'left', 'right')
     * @return ResponseInterface The updated game state after moving the snake
     * @throws \RuntimeException If no game state is found when trying to move the snake
     */
    public function moveSnake(ServerRequestInterface $request, array $args): ResponseInterface
    {
        if (!isset($args['direction']) || !in_array($args['direction'], ['up', 'down', 'left', 'right'])) {
            return new \Laminas\Diactoros\Response\JsonResponse([
                'error' => 'Invalid direction. Use "up", "down", "left", or "right".',
            ], 400);
        }

        $gameState = $this->loadGameState();
        if (empty($gameState)) {
            return new \Laminas\Diactoros\Response\JsonResponse([
                'error' => 'No game state found. Please create a game first.',
            ], 400);
        }

        $game = GameEngine::fromArray($gameState);
        $game->changeSnakeDirection($args['direction']);
        $game->tick();

        $gameState = $game->jsonSerialize();
        $this->gameRepository->saveGameState($gameState);
        return new \Laminas\Diactoros\Response\JsonResponse($this->loadGameState());
    }

    /**
     * Renders the current state of the snake game
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param array                                    $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function renderSnakeGame(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $gameState = $this->loadGameState();
        if (empty($gameState)) {
            return new \Laminas\Diactoros\Response\JsonResponse([
                'error' => 'No game state found. Please create a game first.',
            ], 400);
        }

        $game = GameEngine::fromArray($gameState);
        $board = $game->getBoard();
        $snake = $game->getSnake();

        $sprite = new TextSprite();
        $renderer = new AsciiRenderer($sprite);

        $renderedGameState = $renderer->render($board, $snake);

        return match ($args['type']) {
            'text' => new \Laminas\Diactoros\Response\TextResponse($renderedGameState),
            'html' => new \Laminas\Diactoros\Response\HtmlResponse(
                '<pre>' . $renderedGameState . '</pre>'
            ),
            default => new \Laminas\Diactoros\Response\JsonResponse([
                'error' => 'Invalid render type. Use "text" or "html".',
            ], 400),
        };
    }

    public static function registerRoutes(\League\Route\RouteCollectionInterface $router): void
    {
        $router->map('POST', '/game', [self::class, 'createGame']);
        $router->map('GET', '/game', [self::class, 'getGameState']);
        $router->map('POST', '/move/{direction}', [self::class, 'moveSnake']);
        $router->map('GET', '/render/{type}', [self::class, 'renderSnakeGame']);
    }

    /**
     * Loads the current game state from the repository
     *
     * @return array
     */
    private function loadGameState(): array
    {
        return $this->gameRepository->loadGameState() ?? [];
    }
}
