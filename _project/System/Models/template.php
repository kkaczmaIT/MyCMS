<?php
    namespace System\Models;
    use System\Database;
    
    class Website
    {
        private $db;
        private $dbSql;
        private $dbRedis;
        private $ID_websites  = array();
        private $redisTableName;
        public function __construct($tableName) 
        {
            $this->db = new Database;
            $this->dbSql = $this->db->getConnectionSql();
            $this->dbRedis = $this->db->getConnectionRedis();
            $this->redisTableName = $tableName;
            $this->ID_websites = $this->loadTableWebsites();
            var_dump($this->ID_websites);
        }

        /**
         * load data from SQL table to Redis
         *
         * @return void
         */
        private function loadTableWebsites()
        {
            $this->db->loadQuery('SELECT ID_website, title_website, shortcut_icon_path, IS_user, is_active, created_at FROM WEBSITES');
            $this->db->executeQuery();
            $users = $this->db->resultSet();
            $IDRedisUsers = $this->db->dataTableLoadToRedis($this->redisTableName, $users);
            return $IDRedisUsers;
        }

        private function addIDRedis($ID)
        {
            array_push($this->ID_websites, $ID);
        }
        
        /**
         * Return last ID in Redis table
         *
         * @return void
         */
        private function getLastID($separator)
        {
            $ID_last = 0;
            $websites = $this->ID_websites;
            foreach($websites as $website)
            {
                $ID = explode($separator, $website);
                if($ID_last < $ID[1])
                {
                    $ID_last = $ID[1];
                }
            }
            return $ID_last;
        }

        /**
         * Load data of specific column
         *
         * @param [array] $ID_redis - ID existing 
         * @param [array] $columns_name - key fields to fetch in each record by ID
         * @return void
         */
        private function getColumnsFromRedisID($ID_redis, $columns_name)
        {
            $recordsArray = array();
            foreach($ID_redis as $ID)
            {
                $record = $this->dbRedis->getRecord($ID, $columns_name);
                array_push($recordsArray, $record);
            }
            return $recordsArray;
        }

        // Update sql database and integrity with redis database
        public function forceUpdateSQLDatabase()
        {
            $this->db->saveRecordActivity('create', 'WEBSITES');
            $this->db->saveRecordActivity('modified', 'WEBSITES');
            $this->db->saveRecordActivity('remove', 'WEBSITES');

        }
        
        

    }
?>