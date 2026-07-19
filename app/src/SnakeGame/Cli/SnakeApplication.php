<?php

declare(strict_types=1);

namespace Angorb\RestedSnake\SnakeGame\Cli;

use Angorb\RestedSnake\SnakeGame\AsciiRenderer;
use Angorb\RestedSnake\SnakeGame\Board;
use Angorb\RestedSnake\SnakeGame\FoodSpawner;
use Angorb\RestedSnake\SnakeGame\GameEngine;
use Angorb\RestedSnake\SnakeGame\Snake;
use Angorb\RestedSnake\SnakeGame\Sprites\{EmojiSprite, TextSprite, SpriteInterface};
use League\CLImate\CLImate;

final class SnakeApplication
{
    private const string GAME_STATE_FILE = './data/snake_game_cli_state.json';
    private CLImate $cli;
    private GameEngine $game;
    private AsciiRenderer $renderer;

    public function __construct()
    {
        $this->cli = new CLImate();

        $sprite = $this->getSpriteTypeFromArgs();
        $this->renderer = new AsciiRenderer($sprite);

        $this->loadGameState();

        if (!isset($this->game)) {
            $this->startNewGame();
        }
    }

    /**
     * Starts a new game with a fresh board, snake, and food spawner.
     *
     * @return void
     */
    private function startNewGame(): void
    {
        $board = new Board(20, 10);
        $snake = new Snake(10, 5, 'right');

        $foodSpawner = new FoodSpawner($board);
        $foodSpawner->spawnFood($snake);

        $this->game = new GameEngine(
            $board,
            $snake,
            $foodSpawner
        );
    }

    /**
     * Determines the sprite type to use for rendering based on CLI arguments.
     *
     * @return \Angorb\RestedSnake\SnakeGame\Sprites\SpriteInterface
     */
    private function getSpriteTypeFromArgs(): SpriteInterface
    {
        $this->cli->arguments->add([
            'type' => [
                'longPrefix'   => 'sprite',
                'description'  => 'Sprite type to use for rendering (emoji or text)',
                'defaultValue' => 'text',
                'required'     => true,
            ],
        ]);

        $this->cli->usage();

        $this->cli->arguments->parse();

        $spriteType = $this->cli->arguments->get('type');

        $this->cli->out(
            sprintf(
                'Using sprite type: %s',
                $spriteType
            )
        );

        return match ($spriteType) {
            'emoji' => new EmojiSprite(),
            'text' => new TextSprite(),
            default => new TextSprite(),
        };
    }


    /**
     * Runs the Snake CLI application.
     *
     * @return void
     */
    public function run(): void
    {
        $this->cli->clear();

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

            $this->saveGameState();
        }

        $this->cli->out('');

        if ($this->game->isGameOver()) {
            $this->displayGameOver();
        }
    }

    private function displayGameOver(): void
    {
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
     * Saves the current game state to a JSON file.
     *
     * @return void
     */
    private function saveGameState(): void
    {
        $state = [
            'board' => $this->game->getBoard(),
            'snake' => $this->game->getSnake(),
            'score' => $this->game->getScore(),
        ];

        file_put_contents(
            self::GAME_STATE_FILE,
            json_encode($state, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Loads the game state from a JSON file if it exists.
     *
     * @return void
     */
    private function loadGameState(): void
    {
        if (!file_exists(self::GAME_STATE_FILE)) {
            return;
        }

        try {
            $state = json_decode(
                json: file_get_contents(self::GAME_STATE_FILE),
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );

            $this->game = GameEngine::fromArray($state);
        } catch (\JsonException $e) {
            $this->cli->red()->out('Failed to load game state: ' . $e->getMessage());
            $this->deleteGameState();
        }
    }

    /**
     * Deletes the saved game state file.
     *
     * @return void
     */
    private function deleteGameState(): void
    {
        if (file_exists(self::GAME_STATE_FILE)) {
            unlink(self::GAME_STATE_FILE);
        }
    }

    /**
     * Draws the current game state to the CLI.
     *
     * @return void
     */
    private function draw(): void
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
