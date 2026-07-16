<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

final class CollisionDetector
{
    /**
     * Checks if the given snake has a collision with the board boundaries or itself.
     *
     * @param Snake $snake
     * @param Board $board
     * @return bool
     */
    public static function hasCollision(
        Snake $snake,
        Board $board
    ): bool {

        [$x, $y] = $snake->getHead();

        if (
            $x < 0 ||
            $x >= $board->getWidth() ||
            $y < 0 ||
            $y >= $board->getHeight()
        ) {
            return true;
        }

        $body = $snake->getBody();
        $last = count($body) - 1;

        foreach ($body as $index => [$bx, $by]) {
            if (
                $index !== $last &&
                $bx === $x &&
                $by === $y
            ) {
                return true;
            }
        }

        return false;
    }
}
