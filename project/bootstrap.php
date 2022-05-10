<?php
//Load content .env file
require 'vendor/autoload.php';
//require_once 'baseClasses/Database.php';
use Dotenv\Dotenv;
use System\Database;
$dotenv = new DotEnv(__DIR__);
$dotenv->load();

//Auto load based classes
spl_autoload_register(function($className) 
{
    require_once __DIR__ . '/' . $className . '.php';
});

// change to handle models
$databaseConnectionSql = (new Database())->getConnectionSql();
$databaseConnectionRedis = (new Database())->getConnectionRedis();
// some stuff

