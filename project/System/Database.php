<?php
namespace System;

use System\RedisDatabase;

require_once 'redisDatabase.php';
class Database
{
    private $host = null;
    private $user = null;
    private $password = null;
    private $dbname = null;

    private $dbSqlConnection = null;
    private $dbRedisConnection = null;

    private $queryStatement;
    private $redisObjHashes;
    private $error;


    public function __construct() {
        $this->host = getenv('DB_HOST');
        $this->host_redis = getenv('DB_HOST_ADDR_REDIS');
        $this->port_sql = getenv('DB_PORT_SQL');
        $this->port_redis = getenv('DB_PORT_REDIS');
        $this->dbname = getenv('DB_DATABASE');
        $this->user = getenv('DB_USERNAME');
        $this->password = getenv('DB_PASSWORD');

        try {
            $this->dbSqlConnection = new \PDO("mysql:host=$this->host;port=$this->port_sql;charset=utf8mb4;dbname=$this->dbname", $this->user, $this->password);
            $redisTmp = new RedisDatabase();
            $this->dbRedisConnection = $redisTmp->getConnect();
            $this->dbRedisConnection->connect($this->host_redis, $this->port_redis);
        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
            exit($this->error);
        }
    }

    public function getConnectionSql()
    {
        return $this->dbSqlConnection;
    }
    
    public function getConnectionRedis()
    {
        return $this->dbRedisConnection;
    }

    /**
     * Prepare query to execute and prepare to create or return Redis hashes. Generate Obj Json store in Redis
     *
     * @param [type] $sql - query to prepare
     * hMSet to set a value
     * @return void
     */
    public function loadQuery($sql)
    {
        $this->queryStatement = $this->dbSqlConnection->prepare($sql);
        //$this->redisObjHashes = $this->dbRedisConnection->hMSet();
    }

    /**
     * Execute query and set Redis Hash Map
     *
     * @param [type] $array - list of parameters
     * @return void
     */
    public function executeQuery($array = null)
    {
        return $this->queryStatement->execute($array);
    }

    public function resultSet() 
    {
        $this->queryStatement->execute();
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function single()
    {
        $this->queryStatement->execute();
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function rowCount()
    {
        $this->queryStatement->rowCount();
    }

    // public function bind($param, $value, $type = null)
    // {
    //     if(is_null($type))
    //     {
    //         switch(true)
    //         {
    //             case is_int($value):
    //                 $type = \PDO::PARAM_INT;
    //             break;
    //             case is_bool($value):
    //                 $type = \PDO::PARAM_BOOL;
    //             break;
    //             case is_null($value):
    //                 $type = \PDO::PARAM_NULL;
    //             break;
    //             default:
    //                 $type = \PDO::PARAM_STR;
    //         }
    //     }
        
    //     $this->queryStatement->bindValue($param, $value, $type);
    // }
        
}
?>