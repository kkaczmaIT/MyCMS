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
$db = new Database;
// some stuff
$db->loadQuery('SELECT * FROM USERS');
$arr = $db->resultSet();
print_r($arr);
echo '<br>';
// foreach($arr as $row)
// {
    $db->dataTableLoadToRedis('users_',$arr);
    echo '<br>';
    $redDb = $db->getConnectionRedis();

    $keys = [];
    foreach($arr as $row)
    {
        //var_dump($row);
        foreach($row as $key => $value)
        {
            //var_dump($key);
            array_push($keys, $key);
        }
        break;
    }
    echo '<br>';
    //print_r($arr);
    echo '<br>';
    //print_r($keys);
    echo '<br>results<br>';
    foreach($arr as $row)
    {
        $result = $redDb->getRecord('users_:' . $row['ID'], $keys);
        echo '<br>';
        var_dump($result);
       
    }
    echo '<br><br>';
    $redDb->clearRecord('users_:1');
    $result = $redDb->getRecord('users_:1', $keys);
        echo '<br>';
        var_dump($result);
        //$redDb = $db->getConnectionRedis();
    $arr3 = $redDb->getStatus('all');
    var_dump($arr3);
    echo '<br><br>';
    $redDb->updateRecord('users_:' . 3, ['ID' => 5, 'loginU' => 'nickname1234', 'password' => 'passy2', 'is_active' => 1, 'home_directory' => 'nickname123DIRa', 'permission' => 8]);
    $db->saveRecordActivity('remove', 'USERS');
//}
