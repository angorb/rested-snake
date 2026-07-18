<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

interface GameRepositoryInterface
{
    public function saveGameState(array $gameState): void;
    public function loadGameState(): ?array;
}
