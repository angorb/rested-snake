<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

final class FoodSpawner
{
    public function __construct(
        private Board $board
    ) {
    }

    /**
     * Spawns food on the board at a random empty location.
     *
     * @param Snake $snake
     * @return void
     */
    public function spawnFood(Snake $snake): void
    {
        do {
            $x = random_int(0, $this->board->getWidth() - 1);
            $y = random_int(0, $this->board->getHeight() - 1);
        } while (
            $this->board->getCell($x, $y) !== CellType::Empty ||
            $snake->occupies($x, $y)
        );

        $this->board->setCell($x, $y, CellType::Food);
    }
}
