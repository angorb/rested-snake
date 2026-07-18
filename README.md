# RESTed Snake

*Nobody asked for this.*

## Features

- The hit game "Snake"
- Enterprise-grade PHP 8.4
- Now in Early Access!

## Installation

Ensure you have a functional Docker installation, including the `docker compose` plugin.

From the root of the project: 

##### Install the Composer dependencies:

```docker
docker run --rm  composer install
```

##### Start up the server:

```docker
docker compose up
```

##### Try out SnakePHP on the command like:

```docker
docker compose run --rm php-cli bin/snake
```

## API Documentation

The API server will be available by default at `http://localhost:8080`

### Create a new game: `POST /v1/snake/game`

Example: 

```bash
curl -X POST http://localhost:8080/v1/snake/game
```

### Get the current game state: `GET /v1/snake/game`

Example: 

```bash
curl http://localhost:8080/v1/snake/game
```

### Move the snake: `POST /v1/snake/move/:direction`

| Path Variable | Type | Values | 
|---|---|---|
| `:direction` | string | `up`, `down`, `left`, `right` |

Example: 

```bash
curl -X POST http://localhost:8080/v1/snake/move/up
```

### Render the board: `GET /v1/snake/render/:type`

| Path Variable | Type | Values | 
|---|---|---|
| `:type` | string | `text`, `html` |

Example: 

```bash
curl http://localhost:8080/v1/snake/render/text
```

Response: HTML or plaintext

### JSON Response

**Content-Type:** application/json

```json
{
    "score": int,
    "gameOver" bool,
    "board": {
        "width": int,
        "height": int,
        "cells": [ // multidimensional array of rows (n = height)
            [
                // CellType int: 0 = Empty, 1 = Food, 2 = Wall ( n = width)
            ]
        ]
    },
    "snake": {
        "body": [
            [x, y] // int position, tail to head
        ],
        "direction": string, // direction the head is pointing
        "length": int
    }
}
```

### Plaintext Response

**Content-Type:** text/plain

**Example:**

```
....................
....................
....................
...F................
....................
....................
....................
..............S.....
..............S.....
....................
```

### HTML Response

**Content-Type:** text/html

**Example:**

```html
<pre>....................
....................
....................
...F................
....................
....................
....................
..............S.....
..............S.....
....................
</pre>
```
