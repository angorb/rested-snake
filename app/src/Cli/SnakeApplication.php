<?php

declare(strict_types=1);

namespace Angorb\RestedSnake\Cli;

use Angorb\RestedSnake\AsciiRenderer;
use Angorb\RestedSnake\Board;
use Angorb\RestedSnake\FoodSpawner;
use Angorb\RestedSnake\GameEngine;
use Angorb\RestedSnake\Snake;
use League\CLImate\CLImate;

final class SnakeApplication
{
    private CLImate $cli;
    private GameEngine $game;
    private AsciiRenderer $renderer;

    public function __construct()
    {
        $this->cli = new CLImate();

        $board = new Board(20, 10);
        $snake = new Snake(10, 5, 'right');

        $foodSpawner = new FoodSpawner($board);
        $foodSpawner->spawnFood($snake);

        $this->game = new GameEngine(
            $board,
            $snake,
            $foodSpawner
        );

        $this->renderer = new AsciiRenderer();
    }


    /**
     * Runs the Snake CLI application.
     *
     * @return void
     */
    public function run(): void
    {
        $this->cli->clear();

        $this->cli->bold()->green('RESTed Snake CLI');
        $this->cli->out('');
        $this->cli->out('Controls:');
        $this->cli->out('  w = up');
        $this->cli->out('  s = down');
        $this->cli->out('  a = left');
        $this->cli->out('  d = right');
        $this->cli->out('  q = quit');
        $this->cli->out('');

        while (!$this->game->isGameOver()) {
            $this->draw();

            $input = strtolower(
                trim($this->cli->input('Move')->prompt())
            );

            if ($input === 'q') {
                break;
            }

            $this->handleInput($input);

            $this->game->tick();
        }

        $this->cli->out('');

        $this->cli
            ->red()
            ->out('Game Over!');

        $this->cli->out(
            sprintf(
                'Final score: %d',
                $this->game->getScore()
            )
        );
    }

    /**
     * Draws the current game state to the CLI.
     *
     * @return void
     */
    private function draw(): void
    {
        $this->cli->clear();

        $this->cli->out(
            $this->renderer->render(
                $this->game->getBoard(),
                $this->game->getSnake()
            )
        );

        $this->cli->out(
            sprintf(
                'Score: %d',
                $this->game->getScore()
            )
        );
    }

    /**
     * Handles user input and changes the snake's direction accordingly.
     *
     * @param string $input
     * @return void
     */
    private function handleInput(string $input): void
    {
        $direction = match ($input) {
            'w' => 'up',
            's' => 'down',
            'a' => 'left',
            'd' => 'right',
            default => null,
        };

        if ($direction !== null) {
            $this->game->changeSnakeDirection($direction);
        }
    }
}
