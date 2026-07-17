<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

use Psr\Http\Message\ServerRequestInterface;

interface GameRequestHandlerInterface
{
    public function createGame(ServerRequestInterface $request, array $args): array;

    public function getGameState(ServerRequestInterface $request, array $args): array;

    public function moveSnake(ServerRequestInterface $request, array $args): array;
}
