<?php

declare(strict_types=1);

namespace Angorb\RestedSnake\SnakeGame\Sprites;

interface SpriteInterface
{
    public function getFood(): string;
    public function getWall(): string;
    public function getSnake(): string;
    public function getEmpty(): string;
}
