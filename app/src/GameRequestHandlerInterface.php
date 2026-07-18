<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface GameRequestHandlerInterface
{
    public function createGame(ServerRequestInterface $request, array $args): ResponseInterface;

    public function getGameState(ServerRequestInterface $request, array $args): ResponseInterface;
}
