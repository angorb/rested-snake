<?php

declare(strict_types=1);

namespace Angorb\RestedSnake\SnakeGame;

final class Board implements \JsonSerializable
{
    /**
     * @var CellType[][]
     */
    private array $cells;

    public function __construct(
        private readonly int $width,
        private readonly int $height
    ) {
        $this->cells = array_fill(
            0,
            $this->height,
            array_fill(0, $this->width, CellType::Empty)
        );
    }

    /**
     * Returns the width of the board.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Returns the height of the board.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Checks if the given coordinates are inside the board.
     *
     * @param int $x
     * @param int $y
     * @return bool
     */
    public function isInside(int $x, int $y): bool
    {
        return
            $x >= 0 &&
            $x < $this->width &&
            $y >= 0 &&
            $y < $this->height;
    }

    /**
     * Returns the cell type at the given coordinates.
     *
     * @param int $x
     * @param int $y
     * @return CellType
     */
    public function getCell(int $x, int $y): CellType
    {
        if (!$this->isInside($x, $y)) {
            throw new \OutOfBoundsException(
                sprintf('Coordinates (%d, %d) are outside the board.', $x, $y)
            );
        }

        return $this->cells[$y][$x];
    }

    /**
     * Sets the cell type at the given coordinates.
     *
     * @param int $x
     * @param int $y
     * @param CellType $cellType
     * @return void
     */
    public function setCell(int $x, int $y, CellType $cellType): void
    {
        if (!$this->isInside($x, $y)) {
            throw new \OutOfBoundsException(
                sprintf('Coordinates (%d, %d) are outside the board.', $x, $y)
            );
        }

        $this->cells[$y][$x] = $cellType;
    }

    /**
     * Checks if there is food at the given coordinates.
     *
     * @param int $x
     * @param int $y
     * @return bool
     */
    public function hasFoodAt(int $x, int $y): bool
    {
        return $this->getCell($x, $y) === CellType::Food;
    }

    /**
     * Clears the cell at the given coordinates (sets it to empty).
     *
     * @param int $x
     * @param int $y
     * @return void
     */
    public function clearCell(int $x, int $y): void
    {
        $this->setCell($x, $y, CellType::Empty);
    }

    /**
     * Returns the raw cell grid.
     *
     * Primarily intended for rendering and serialization.
     *
     * @return CellType[][]
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    /**
     * Serializes the board to a JSON-friendly format.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'cells' => array_map(
                static fn(array $row): array => array_map(
                    static fn(CellType $cell): int => $cell->value,
                    $row
                ),
                $this->cells
            ),
        ];
    }

    /**
     * Creates a Board instance from a JSON-friendly array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $width = $data['width'];
        $height = $data['height'];
        $cellsData = $data['cells'];

        $board = new self($width, $height);

        foreach ($cellsData as $y => $row) {
            foreach ($row as $x => $cellValue) {
                $cellType = CellType::from($cellValue);
                $board->setCell($x, $y, $cellType);
            }
        }

        return $board;
    }
}
