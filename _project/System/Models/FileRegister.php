<?php
    namespace System\Models;
    use System\Database;
    
    class FileRegister
    {
        private $db;
        private $dbSql;
        private $dbRedis;
        private $ID_files  = array();
        private $redisTableName;
        public function __construct($tableName) 
        {
            $this->db = new Database;
            $this->dbSql = $this->db->getConnectionSql();
            $this->dbRedis = $this->db->getConnectionRedis();
            $this->redisTableName = $tableName;
            $this->ID_files = $this->loadTableFiles();
            var_dump($this->ID_files);
        }

        /**
         * load data from SQL table to Redis
         *
         * @return void
         */
        private function loadTableFiles()
        {
            $this->db->loadQuery('SELECT ID, ID_user, root_path, filenameF, type_mime, size, path_file, created_at, modified_at FROM FILEREGISTER WHERE ID_user=:id_user');
            $this->db->executeQuery(['id_user' => $_SESSION['user_id']]);
            $users = $this->db->resultSet();
            $IDRedisUsers = $this->db->dataTableLoadToRedis($this->redisTableName, $users);
            return $IDRedisUsers;
        }

        private function addIDRedis($ID)
        {
            array_push($this->ID_files, $ID);
        }
        
        /**
         * Return last ID in Redis table
         *
         * @return void
         */
        private function getLastID($separator)
        {
            $ID_last = 0;
            $files = $this->ID_files;
            foreach($files as $file)
            {
                $ID = explode($separator, $file);
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
            $this->db->saveRecordActivity('create', 'FILEREGISTER');
            $this->db->saveRecordActivity('modified', 'FILEREGISTER');
            $this->db->saveRecordActivity('remove', 'FILEREGISTER');

        }
        
        /**
         * Method to save data new file with path and mime type
         *
         * @param [type] $filenameF - filename
         * @param [type] $type_mime - format file
         * @param [type] $size - size in B
         * @param [type] $path_file - path file
         * @return void
         */
        public function registerFile($filenameF, $type_mime, $size, $path_file)
        {
            $IDNewRecord = (int)$this->getLastID('_:') + 1;
            $dataFile = array(
                'ID' => $IDNewRecord,
                'ID_user' => $_SESSION['user_id'],
                'root_path' => getenv('STORAGE_URL') . $_SESSION['home_directory'],
                'filenameF' => $filenameF,
                'type_mime' => $type_mime,
                'size' => $size,
                'path_file' => $path_file,
                'created_at' => date('d.m.y H:i:s'),
                'modified_at' => date('d.m.y H:i:s')
            );

            if($this->dbRedis->saveRecord($this->redisTableName . $IDNewRecord, $dataFile))
            {
                infoLog($_ENV['MODE'], 'File registered in database');
                $this->addIDRedis($this->redisTableName . $IDNewRecord);
                $this->forceUpdateSQLDatabase();
                return true;
            }
            else
            {
                infoLog($_ENV['MODE'], 'Something went wrong. File not registered.');
                return false;
            }
        }

        /**
         * Return collect files belong to user identified by ID
         *
         * @return [array/false]
         */
        public function getFilesByUserID()
        {
            $userFiles = array();
            if($files = $this->getColumnsFromRedisID($this->ID_files, ['ID', 'ID_user', 'root_path', 'filenameF', 'type_mime', 'size', 'path_file', 'created_at', 'modified_at']))
            {
                foreach($files as $file)
                {
                    if($_SESSION['user_id'] == $file['ID_user'])
                    {
                        array_push($userFiles, $file);
                    }
                }
                return $userFiles;
            }
            else
            {
                infoLog(getenv('MODE'), 'Something went wrong.');
                return false;
            }

        }
      /**
       * Delete file from database
       *
       * @param [type] $ID - ID file
       * @return [bool] - status of operation
       */  
      public function deleteFile($ID)
        {
            $IDSQL_files = $this->getColumnsFromRedisID($this->ID_files, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_files, 'ID');
            echo 'index:' . $index;
            if($index != -1)
            {
                if($this->dbRedis->clearRecord($this->ID_files[$index]))
                {
                    infoLog(getenv('MODE'), 'file deleted');
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
    }
?>