<?php

declare(strict_types=1);

namespace Angorb\RestedSnake\SnakeGame;

final class GameEngine implements \JsonSerializable
{
    /**
     * The current score of the game.
     *
     * @var int
     */
    private int $score = 0;
    /**
     * Indicates whether the game is over.
     *
     * @var bool
     */
    private bool $gameOver = false;

    public function __construct(
        private Board $board,
        private Snake $snake,
        private FoodSpawner $foodSpawner
    ) {
    }

    /**
     * Returns the current game board.
     *
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * Returns the current snake.
     *
     * @return Snake
     */
    public function getSnake(): Snake
    {
        return $this->snake;
    }

    /**
     * Returns the current score of the game.
     *
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * Returns true if the game is over.
     *
     * @return bool
     */
    public function isGameOver(): bool
    {
        return $this->gameOver;
    }

    /**
     * Resets the game to the initial state.
     *
     * @param int $startX
     * @param int $startY
     * @param string $initialDirection
     * @return void
     */
    public function resetGame(
        int $startX,
        int $startY,
        string $initialDirection
    ): void {
        $this->score = 0;
        $this->gameOver = false;

        $this->board = new Board(
            $this->board->getWidth(),
            $this->board->getHeight()
        );

        $this->snake = new Snake(
            $startX,
            $startY,
            $initialDirection
        );

        $this->foodSpawner = new FoodSpawner($this->board);
        $this->foodSpawner->spawnFood($this->snake);
    }

    /**
     * Advance the game by one tick.
     *
     * @return void
     */
    public function tick(): void
    {
        if ($this->gameOver) {
            return;
        }

        [$nextX, $nextY] = $this->snake->getNextHeadPosition();

        try {
            $willEatFood = $this->board->getCell($nextX, $nextY) === CellType::Food;
        } catch (\OutOfBoundsException $e) {
            $this->gameOver = true;
            return;
        }

        $this->snake->move($willEatFood);

        if (CollisionDetector::hasCollision($this->snake, $this->board)) {
            $this->gameOver = true;
            return;
        }

        if ($willEatFood) {
            $this->score++;

            $this->board->setCell($nextX, $nextY, CellType::Empty);

            $this->foodSpawner->spawnFood($this->snake);
        }
    }

    /**
     * Changes the direction of the snake.
     *
     * @param string $direction
     * @return void
     */
    public function changeSnakeDirection(string $direction): void
    {
        if ($this->gameOver) {
            return;
        }

        $this->snake->setDirection($direction);
    }

    /**
     * Moves the snake in the given direction and advances the game by one tick.
     *
     * @param string $direction
     * @return void
     */
    public function move(string $direction): void
    {
        $this->changeSnakeDirection($direction);
        $this->tick();
    }

    /**
     * Returns the game state as an array suitable for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'score'      => $this->score,
            'gameOver'   => $this->gameOver,
            'board'      => $this->board,
            'snake'      => $this->snake,
        ];
    }

    /**
     * Creates a GameEngine instance from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $board = Board::fromArray($data['board']);
        $snake = Snake::fromArray($data['snake']);
        $foodSpawner = new FoodSpawner($board);

        $gameEngine = new self($board, $snake, $foodSpawner);
        $gameEngine->score = $data['score'];
        $gameEngine->gameOver = $data['gameOver'] ?? false;

        return $gameEngine;
    }
}
