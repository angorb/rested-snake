<?php

declare(strict_types=1);

namespace Angorb\RestedSnake\SnakeGame;

final class Snake implements \JsonSerializable
{
    public const UP = 'up';
    public const DOWN = 'down';
    public const LEFT = 'left';
    public const RIGHT = 'right';

    /**
     * Each segment is [x, y].
     * Tail is at index 0, head is the last element.
     *
     * @var list<array{0:int,1:int}>
     */
    private array $body;

    /**
     * The current direction of the snake.
     *
     * @var string
     */
    private string $direction;

    public function __construct(int $startX, int $startY, string $initialDirection)
    {
        $this->assertValidDirection($initialDirection);

        $this->body = [
            [$startX, $startY],
        ];

        $this->direction = $initialDirection;
    }

    /**
     * Read-only copy of the snake body.
     *
     * @return list<array{0:int,1:int}>
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Returns the coordinates of the head of the snake.
     *
     * @return array{0:int,1:int}
     */
    public function getHead(): array
    {
        $headIndex = array_key_last($this->body);

        return $this->body[$headIndex];
    }

    /**
     * Returns the coordinates of the tail of the snake.
     *
     * @return array{0:int,1:int}
     */
    public function getTail(): array
    {
        return $this->body[0];
    }

    /**
     * Returns the current direction of the snake.
     *
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * Returns the length of the snake.
     *
     * @return int
     */
    public function getLength(): int
    {
        return count($this->body);
    }

    /**
     * Sets the new direction of the snake.
     *
     * @param string $newDirection
     * @return void
     */
    public function setDirection(string $newDirection): void
    {
        $this->assertValidDirection($newDirection);

        // Ignore attempts to reverse direction.
        if ($this->isOppositeDirection($this->direction, $newDirection)) {
            return;
        }

        $this->direction = $newDirection;
    }

    /**
     * Move the snake one tile.
     *
     * If $grow is true, the tail is not removed.
     *
     * @param bool $grow
     * @return void
     */
    public function move(bool $grow = false): void
    {
        [$x, $y] = $this->getHead();

        [$newX, $newY] = match ($this->direction) {
            self::UP    => [$x, $y - 1],
            self::DOWN  => [$x, $y + 1],
            self::LEFT  => [$x - 1, $y],
            self::RIGHT => [$x + 1, $y],
        };

        // Add new head.
        $this->body[] = [$newX, $newY];

        // Remove tail unless growing.
        if (!$grow) {
            array_shift($this->body);
        }
    }

    /**
     * Returns true if the snake occupies the given coordinate.
     *
     * @param int $x
     * @param int $y
     * @return bool
     */
    public function occupies(int $x, int $y): bool
    {
        foreach ($this->body as [$segmentX, $segmentY]) {
            if ($segmentX === $x && $segmentY === $y) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the head collides with the body.
     *
     * @return bool
     */
    public function hasSelfCollision(): bool
    {
        [$headX, $headY] = $this->getHead();

        $lastIndex = array_key_last($this->body);

        foreach ($this->body as $index => [$x, $y]) {
            if ($index === $lastIndex) {
                continue;
            }

            if ($x === $headX && $y === $headY) {
                return true;
            }
        }

        return false;
    }

    /**
     * Serializes the snake to a JSON-friendly format.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'body' => $this->body,
            'direction' => $this->direction,
            'length' => $this->getLength(),
        ];
    }

    /**
     * Asserts that the given direction is valid.
     *
     * @param string $direction
     * @return void
     */
    private function assertValidDirection(string $direction): void
    {
        if (
            !in_array($direction, [
            self::UP,
            self::DOWN,
            self::LEFT,
            self::RIGHT,
            ], true)
        ) {
            throw new \InvalidArgumentException(
                sprintf('Invalid direction: %s', $direction)
            );
        }
    }

    /**
     * Returns true if the given direction is opposite to the current direction.
     *
     * @param string $current
     * @param string $next
     * @return bool
     */
    private function isOppositeDirection(
        string $current,
        string $next
    ): bool {
        return match ($current) {
            self::UP    => $next === self::DOWN,
            self::DOWN  => $next === self::UP,
            self::LEFT  => $next === self::RIGHT,
            self::RIGHT => $next === self::LEFT,
        };
    }

    /**
     * Returns the coordinates of the next head position.
     *
     * @return array
     */
    public function getNextHeadPosition(): array
    {
        [$x, $y] = $this->getHead();

        return match ($this->direction) {
            self::UP    => [$x, $y - 1],
            self::DOWN  => [$x, $y + 1],
            self::LEFT  => [$x - 1, $y],
            self::RIGHT => [$x + 1, $y],
        };
    }
}
