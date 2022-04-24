<?php
//Load content .env file
require 'vendor/autoload.php';
require 'databaseController.php';
use Dotenv\Dotenv;
use Src\System\DatabaseController;
$dotenv = new DotEnv(__DIR__);
$dotenv->load();

$databaseConnectionSql = (new DatabaseController())->getConnectionSql();
$databaseConnectionRedis = (new DatabaseController())->getConnectionRedis();
$databaseConnectionRedis->set("test", "Witaj");

