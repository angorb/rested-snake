<?php

declare(strict_types=1);

namespace Angorb\RestedSnake\SnakeGame;

use Angorb\RestedSnake\SnakeGame\Sprites\SpriteInterface;

final class AsciiRenderer
{
    public function __construct(private SpriteInterface $sprite)
    {
    }

    /**
     * Gets the sprite used by the renderer.
     *
     * @return \Angorb\RestedSnake\SnakeGame\Sprites\SpriteInterface
     */
    public function getSprite(): SpriteInterface
    {
        return $this->sprite;
    }

    /**
     * Renders the game board and snake as an ASCII string.
     *
     * @param Board $board
     * @param Snake $snake
     * @return string
     */
    public function render(Board $board, Snake $snake): string
    {
        $output = '';

        for ($y = 0; $y < $board->getHeight(); $y++) {
            for ($x = 0; $x < $board->getWidth(); $x++) {
                if ($snake->occupies($x, $y)) {
                    $output .= $this->sprite->getSnake();
                    continue;
                }

                $output .= match ($board->getCell($x, $y)) {
                    CellType::Empty => $this->sprite->getEmpty(),
                    CellType::Food  =>  $this->sprite->getFood(),
                    CellType::Wall  => $this->sprite->getWall(),
                };
            }

            $output .= PHP_EOL;
        }

        return $output;
    }
}
