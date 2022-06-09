<?php
    namespace System\Models;
    use System\Database;
    
    class PagesWeb
    {
        private $db;
        private $dbSql;
        private $dbRedis;
        private $ID_pagesWebs  = array();
        private $redisTableName;
        public function __construct($tableName) 
        {
            $this->db = new Database;
            $this->dbSql = $this->db->getConnectionSql();
            $this->dbRedis = $this->db->getConnectionRedis();
            $this->redisTableName = $tableName;
            $this->ID_pagesWebs = $this->loadTablePagesWeb();
            var_dump($this->ID_pagesWebs);
        }

        /**
         * load data from SQL table to Redis
         *
         * @return void
         */
        private function loadTablePagesWeb()
        {
            $this->db->loadQuery('SELECT ID, ID_menu, ID_theme, ID_website, title, keyphrases, description_meta, content, footertext FROM PAGESWEB');
            $this->db->executeQuery();
            $pagesWebs = $this->db->resultSet();
            $IDRedisPagesWebs = $this->db->dataTableLoadToRedis($this->redisTableName, $pagesWebs);
            return $IDRedisPagesWebs;
        }

        private function addIDRedis($ID)
        {
            array_push($this->ID_pagesWebs, $ID);
        }
        
        /**
         * Return last ID in Redis table
         *
         * @return void
         */
        private function getLastID($separator)
        {
            $ID_last = 0;
            $pagesWebs = $this->ID_pagesWebs;
            foreach($pagesWebs as $pagesWeb)
            {
                $ID = explode($separator, $pagesWeb);
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
            $this->db->saveRecordActivity('create', 'PAGESWEB');
            $this->db->saveRecordActivity('modified', 'PAGESWEB');
            $this->db->saveRecordActivity('remove', 'PAGESWEB');

        }
        
       /**
        * Create new Page. Need convenient handle variable session in controller
        *
        * @param [type] $title - title page
        * @param [type] $keyphrases - meta tag content key words seo
        * @param [type] $description_meta - meta tag content seo
        * @param [type] $content - page description in panel
        * @param [type] $footer_text - text in footer
        * @return [bool] true/false
        */
        public function createPagesWeb($title, $keyphrases, $description_meta, $content, $footer_text)
        {
            $IDNewRecord = (int)$this->getLastID('_:') + 1;
            $dataPagesWeb = array(
                'ID' => $IDNewRecord,
                'ID_menu' => 1,//last id
                'ID_theme' => 1, //default,
                'ID_website' => 1, //session
                'title' => $title,
                'keyphrases' => $keyphrases,
                'description_meta' => $description_meta,
                'content' => $content,
                'footer_text' => $footer_text
            );

            if($this->dbRedis->saveRecord($this->redisTableName . $IDNewRecord, $dataPagesWeb))
            {
                infoLog($_ENV['MODE'], 'Pages created');
                $this->addIDRedis($this->redisTableName . $IDNewRecord);
                $this->forceUpdateSQLDatabase();
                return $IDNewRecord;
            }
            else
            {
                infoLog($_ENV['MODE'], 'Something went wrong. Pages not created.');
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
    }
?>