<?php
//Load content .env file and bootstrap
require '../vendor/autoload.php';
require_once 'Utility.php';
//require_once 'baseClasses/Database.php';
use Dotenv\Dotenv;
use System\Database;
$dotenv = new DotEnv('../');
$dotenv->load();

//Auto load based classes
spl_autoload_register(function($className) 
{
    str_replace('/', trim('\ '), $className);
    //echo $className . '<br>';

    if(str_contains($className, 'Models'))
    {
        //echo 'Models';
    }
    elseif(str_contains($className, 'Controllers'))
    {
        //echo 'Controller';
    }
    else
    {
        $className = explode('\\', $className);
        $className = $className[array_key_last($className)];
        //echo $className;
    }
    //echo __DIR__ . trim('\ ')  . __NAMESPACE__ . $className . '.php';
    require_once __DIR__ . trim('\ ') .  $className . '.php';
});


