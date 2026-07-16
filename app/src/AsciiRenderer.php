<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

final class AsciiRenderer
{
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
                    $output .= 'S';
                    continue;
                }

                $output .= match ($board->getCell($x, $y)) {
                    CellType::Empty => '.',
                    CellType::Food  => 'F',
                    CellType::Wall  => '#',
                };
            }

            $output .= PHP_EOL;
        }

        return $output;
    }
}
