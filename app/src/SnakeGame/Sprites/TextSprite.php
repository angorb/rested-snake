<?php

declare(strict_types=1);

namespace Angorb\RestedSnake\SnakeGame\Sprites;

class TextSprite implements SpriteInterface
{
    public function getFood(): string
    {
        return 'F';
    }

    public function getWall(): string
    {
        return '#';
    }

    public function getSnake(): string
    {
        return 'S';
    }

    public function getEmpty(): string
    {
        return '.';
    }
}
