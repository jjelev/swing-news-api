<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    // In case somebody forgets to put .env in root directory.
    echo $e->getMessage();
    exit;
}

$kernel = new Swing\Kernel();

$kernel->run();