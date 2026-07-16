<?php

declare(strict_types=1);

namespace Angorb\RestedSnake\SnakeGame\Sprites;

class EmojiSprite implements SpriteInterface
{
    public function getFood(): string
    {
        return mb_chr(0x1F42D); // mouse emoji
    }

    public function getWall(): string
    {
        return mb_chr(0x1F9F1); // brick emoji
    }

    public function getSnake(): string
    {
        return mb_chr(0x1F40D); // snake emoji
    }

    public function getEmpty(): string
    {
        return ' ';
    }
}
