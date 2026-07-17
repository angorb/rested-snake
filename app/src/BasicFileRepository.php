<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

class BasicFileRepository implements GameRepositoryInterface
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function saveGameState(array $gameState): void
    {
        file_put_contents($this->filePath, json_encode($gameState));
    }

    public function loadGameState(): ?array
    {
        if (!file_exists($this->filePath)) {
            return null;
        }

        $data = file_get_contents($this->filePath);
        return json_decode($data, true);
    }
}
