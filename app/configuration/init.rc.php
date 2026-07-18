<?php

// load composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// set up error handling with Whoops
$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();
