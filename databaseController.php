<?php
namespace Src\System;

use RedisInstance;

require_once 'redisController.php';
class DatabaseController
{
    private $dbConnection = null;
    private $redis = null;

    public function __construct() {
        $host = getenv('DB_HOST');
        $host_redis = getenv('DB_HOST_ADDR_REDIS');
        $port_sql = getenv('DB_PORT_SQL');
        $port_redis = getenv('DB_PORT_REDIS');
        $db = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        try {
            $this->dbConnection = new \PDO("mysql:host=$host;port=$port_sql;charset=utf8mb4;dbname=$db", $user, $password);
            $redisTmp = new RedisInstance();
            $this->redis = $redisTmp->run();
            $this->redis->connect($host_redis, $port_redis);
            echo 'Gotowe';
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnectionSql()
    {
        return $this->dbConnection;
    }

    public function getConnectionRedis()
    {
        return $this->redis;
    }
}
?>