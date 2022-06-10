<?php
    namespace System\Models;
    use System\Database;
    
    class Setting
    {
        private $db;
        private $dbSql;
        private $dbRedis;
        private $ID_settings  = array();
        private $redisTableName;
        public function __construct($tableName) 
        {
            $this->db = new Database;
            $this->dbSql = $this->db->getConnectionSql();
            $this->dbRedis = $this->db->getConnectionRedis();
            $this->redisTableName = $tableName;
            $this->ID_settings = $this->loadTableSettings();
            //var_dump($this->ID_settings);
        }

        /**
         * load data from SQL table to Redis
         *
         * @return void
         */
        private function loadTableSettings()
        {
            $this->db->loadQuery('SELECT ID, PHP_version, limit_upload_file_size, contact FROM SETTINGS');
            $this->db->executeQuery();
            $users = $this->db->resultSet();
            $IDRedisUsers = $this->db->dataTableLoadToRedis($this->redisTableName, $users);
            return $IDRedisUsers;
        }

        private function addIDRedis($ID)
        {
            array_push($this->ID_settings, $ID);
        }
        
        /**
         * Return last ID in Redis table
         *
         * @return void
         */
        private function getLastID($separator)
        {
            $ID_last = 0;
            $settings = $this->ID_settings;
            foreach($settings as $setting)
            {
                $ID = explode($separator, $setting);
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
            $this->db->saveRecordActivity('create', 'SETTINGS');
            $this->db->saveRecordActivity('modified', 'SETTINGS');
            $this->db->saveRecordActivity('remove', 'SETTINGS');

        }
        
        /**
         * Create setting fo website
         *
         * @param [type] $limit_upload_file_size - limit size upload file 
         * @param [type] $contact - contact data 
         * @return false/new ID
         */
        public function createSetting($limit_upload_file_size, $contact)
        {
            $IDNewRecord = (int)$this->getLastID('_:') + 1;
            $dataSetting = array(
                'ID' => $IDNewRecord,
                'PHP_version' => phpversion(),
                'limit_upload_file_size' => $limit_upload_file_size,
                'contact' => $contact
            );

            if($this->dbRedis->saveRecord($this->redisTableName . $IDNewRecord, $dataSetting))
            {
                infoLog($_ENV['MODE'], 'setting created');
                $this->addIDRedis($this->redisTableName . $IDNewRecord);
                $this->forceUpdateSQLDatabase();
                return $IDNewRecord;
            }
            else
            {
                infoLog($_ENV['MODE'], 'Something went wrong. Setting not created.');
                return false;
            }
        }

      /**
       * Delete setting from database
       *
       * @param [type] $ID - ID setting
       * @return [bool] - status of operation
       */  
      public function deleteSetting($ID)
        {
            $IDSQL_settings = $this->getColumnsFromRedisID($this->ID_settings, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_settings, 'ID');
            echo 'index:' . $index;
            if($index != -1)
            {
                if($this->dbRedis->clearRecord($this->ID_settings[$index]))
                {
                    infoLog(getenv('MODE'), 'setting deleted');
                    $this->forceUpdateSQLDatabase();
                    return true;
                }
                else
                {
                    infoLog(getenv('MODE'), 'Operation failed');
                    return false;
                }
            }
            else
            {
                infoLog(getenv('MODE'), 'ID not found');
                return false;
            }
        }

        /**
         * Update setting of website
         *
         * @param [type] $ID - id setting
         * @param [type] $limit_upload_file_size - limit file size to upload
         * @param [type] $contact - contact display and use in contact section
         * @return void
         */
        public function updateSetting($ID, $limit_upload_file_size, $contact)
        {
            $IDSQL_setting = $this->getColumnsFromRedisID($this->ID_settings, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_setting, 'ID');
            if($index != -1)
            {
                if(isset($limit_upload_file_size) || isset($contact))
                {
                    if(isset($limit_upload_file_size))
                    {
                        if($this->dbRedis->updateRecord($this->ID_settings[$index], ['limit_upload_file_size' => $limit_upload_file_size]))
                        {
                            infoLog(getenv('MODE'), 'Limit file size updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'limit file size not updated');
                            return false;
                        }
                    }

                    if(isset($contact))
                    {
                        if($this->dbRedis->updateRecord($this->ID_settings[$index], ['contact' => $contact]))
                        {
                            infoLog(getenv('MODE'), 'Website contact updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Website contact not updated');
                            return false;
                        }
                        $this->forceUpdateSQLDatabase();
                        return true;
                    }
                }
                else
                {
                    infoLog(getenv('MODE'), 'Parameters to update are empty');
                    return false;
                }
            }
            else
            {
                infoLog(getenv('MODE'), 'ID setting not found');
                return false;
            }
    
        }

        public function getSettingsByID($ID)
        {
            if(is_numeric($ID))
            {
                if($settings = $this->getColumnsFromRedisID($this->ID_settings, ['ID', 'PHP_version', 'limit_upload_file_size', 'contact']))
                {
                    foreach($settings as $setting)
                    {
                        if($ID == $setting['ID'])
                        {
                            return $setting;
                        }
                    }
                }
                else
                {
                    infoLog(getenv('MODE'), 'Something went wrong.');
                    return false;
                }
            }
            else
            {
                infoLog(getenv('MODE'), 'Something went wrong.');
                return false;
            }
        }

      

        
    }
?>