<?php

declare(strict_types=1);

namespace Angorb\RestedSnake;

interface RouteRegistrarInterface
{
    public static function registerRoutes(\League\Route\RouteCollectionInterface $router): void;
}
