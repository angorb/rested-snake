<?php

declare(strict_types=1);

namespace Angorb\RestedSnake\SnakeGame;

enum CellType: int
{
    case Empty = 0;
    case Food = 1;
    case Wall = 2;
}
