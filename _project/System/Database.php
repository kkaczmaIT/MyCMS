<?php
namespace System;

use System\RedisDatabase;
require_once 'Utility.php';
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
            $this->dbRedisConnection = new RedisDatabase($this->host_redis, $this->port_redis);
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
        return $this->queryStatement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function single()
    {
        $this->queryStatement->execute();
        return $this->queryStatement->fetch(\PDO::FETCH_ASSOC);
    }

    public function rowCount()
    {
        $this->queryStatement->rowCount();
    }

    public function getLastID($name)
    {
        // SELECT ID FROM USERS ORDER BY ID DESC LIMIT 1;
        $this->loadQuery('SELECT ID FROM ' . $name . ' ORDER BY ID DESC LIMIT 1');
        $this->executeQuery();
        $ID = $this->single();
        $ID = $ID['ID'];
        return $ID;
    }

    public function startTransaction()
    {
        $this->dbSqlConnection->beginTransaction();
    }

    public function endTransaction($command)
    {
        if($command == 'COMMIT')
            $this->dbSqlConnection->commit();
        else if($command == 'ROLLBACK')
            $this->dbSqlConnection->rollBack();
    }
    /**
     * Load Data from SQL table to Redis and return id's array
     *
     * @param [string] $ID_record_redis - main phrase of id record 
     * @param [array] $data - data to save
     * @return void
     */
    public function dataTableLoadToRedis( $ID_record_redis, $data)
    {
        $redisIDRecord = array();
        foreach($data as $row)
        {
            if($this->dbRedisConnection->saveRecord($ID_record_redis  . $row['ID'], $row))
            {
                array_push($redisIDRecord, $ID_record_redis . $row['ID']);
                infoLog($_ENV['MODE'], 'Row has loaded');
                $this->dbRedisConnection->clearStatus('create_' . $ID_record_redis . $row['ID']);
            }
            else
            {
                infoLog($_ENV['MODE'], 'Row has not loaded');
            }
        }
        return $redisIDRecord;
    }   
    //1. czy ma zwracac tablice id do latwiejszego zarzadzania ?
    //2. czy w klasie db wszystkie funkcje nadpisac z klasy dbredisa ?


    /**
     * Save data to SQL Table from structure data in Redis
     *
     * case create param and table name
     * case modified param and table name
     * case remove table name and id
     * 
     * @param [type] $table_name - name table of SQL
     * @return void
     */
    public function saveRecordActivity($type, $table_name)
    {
        $status = $this->dbRedisConnection->getStatus('all');
        $keys = array_keys($status);
        if($status)
        {
            switch($type)
            {
                case 'create':
                    
                    $query = 'INSERT INTO ' . $table_name . ' (';
                    $columns = '';
                    $values = '';
                    $typeKeys = array_filter($keys, "getCreateStatus");
                    foreach($typeKeys as $key)
                    {
                        //Build query
                        foreach($status[$key] as $column => $value)
                        {
                            if($column == 'ID' || is_numeric($value))
                            {
                                $columns .= $column . ', ';
                                $values .=  $value . ', ';
                            }
                            else
                            {
                                $columns .= $column . ', ';
                                $values .= '"' . $value . '", ';
                            }
                            
                        }
                        $columns = rtrim($columns, ', ');
                        $values = rtrim($values, ', ');
                        $query .= $columns . ') VALUES (' . $values . ')';
                        $this->startTransaction();
                        $this->loadQuery($query);
                        if($this->executeQuery())
                        {
                            $this->endTransaction('COMMIT');
                            infoLog($_ENV['MODE'], 'New record added to ' . $table_name . ' ID: ' . $status[$key]['ID']);
                            $this->dbRedisConnection->clearStatus($key);
                        }
                        else
                        {
                            $this->endTransaction('ROLLBACK');
                            infoLog($_ENV['MODE'], 'Adding new record failed');
                        }
                    }
                break;
                case 'modified':
                    $typeKeys = array_filter($keys, "getModifiedStatus");
                    $query = 'UPDATE ' . $table_name . ' SET ';
                    foreach($typeKeys as $key)
                    {
                        //Build query
                        foreach($status[$key] as $column => $value)
                        {
                            if($column == 'ID')
                            {
                                $IDRecord = $value;
                            }
                            else if(is_numeric($value))
                            {
                                $query .= $column . '=' . $value . ', ';
                            }
                            else
                            {
                                $query .= $column . '="' . $value . '", ';
                            }
                            
                        }
                        if(isset($IDRecord))
                        {
                            $query = rtrim($query, ', ');
                            $query .= ' WHERE ID = ' . $IDRecord;
                            $this->startTransaction();
                            $this->loadQuery($query);
                            if($this->executeQuery())
                            {
                                $this->endTransaction('COMMIT');
                                infoLog($_ENV['MODE'], 'Record modified from ' . $table_name . ' ID: ' . $IDRecord);
                                $this->dbRedisConnection->clearStatus($key);
                            }
                            else
                            {
                                $this->endTransaction('ROLLBACK');
                               infoLog($_ENV['MODE'], 'Modified record failed');
                            }
                        }

                    }
                break;
                case 'remove':
                    $typeKeys = array_filter($keys, "getRemoveStatus");
                    $query = 'DELETE FROM ' . $table_name . ' WHERE ID = ';
                    foreach($typeKeys as $key)
                    {
                        //Build query
                        $query .= $status[$key]['ID'];
                        $this->startTransaction();
                        $this->loadQuery($query);

                        if($this->executeQuery())
                        {
                            $this->endTransaction('COMMIT');
                            infoLog($_ENV['MODE'], 'Record removed from ' . $table_name . ' ID: ' .  $status[$key]['ID']);
                            $this->dbRedisConnection->clearStatus($key);
                        }
                        else
                        {
                            $this->endTransaction('ROLLBACK');
                            infoLog($_ENV['MODE'], 'Removed record failed');
                        }
                    }
                break;
            }
        }
        else
        {
            infoLog($_ENV['MODE'], 'Nothing changed');
        }

        
        
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