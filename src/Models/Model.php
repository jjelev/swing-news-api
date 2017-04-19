<?php

namespace Swing\Models;

use PDO;

abstract class Model
{
    protected $pdo;

    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    function __construct()
    {
        $host = getenv('MYSQL_HOST');
        $port = getenv('MYSQL_PORT');
        $db = getenv('MYSQL_DATABASE');
        $username = getenv('MYSQL_USER');
        $password = getenv('MYSQL_PASSWORD');
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";

        $this->pdo = new PDO($dsn, $username, $password, $this->options);
    }
}