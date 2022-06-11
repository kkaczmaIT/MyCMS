<?php
    namespace System\Models;
    use System\Database;
    
    class Menu
    {
        private $db;
        private $dbSql;
        private $dbRedis;
        private $ID_menus  = array();
        private $ID_menu;
        private $redisTableName;
        public function __construct($tableName) 
        {
            $this->db = new Database;
            $this->dbSql = $this->db->getConnectionSql();
            $this->dbRedis = $this->db->getConnectionRedis();
            $this->redisTableName = $tableName;
            $this->ID_menus = $this->loadTableMenu();
        }

        /**
         * load data from SQL table to Redis
         *
         * @return void
         */
        private function loadTableMenu()
        {
            $this->db->loadQuery('SELECT M.ID, M.level_menu FROM MENU M');
            $this->db->executeQuery();
            $menus = $this->db->resultSet();
            $IDRedisMenus = $this->db->dataTableLoadToRedis($this->redisTableName, $menus);
            return $IDRedisMenus;
        }


        private function addIDRedis($ID)
        {
            array_push($this->ID_menus, $ID);
        }
        
        /**
         * Return last ID in Redis table
         *
         * @return void
         */
        private function getLastID($separator)
        {
            $ID_last = 0;
            $menus = $this->ID_menus;
            foreach($menus as $menu)
            {
                $ID = explode($separator, $menu);
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
            $this->db->saveRecordActivity('create', 'MENU');
            $this->db->saveRecordActivity('modified', 'MENU');
            $this->db->saveRecordActivity('remove', 'MENU');
        }
        
        /**
         * Create new menu
         *
         * @param [type] $level_menu - level menu
         * @return int/false - ID new menu or false
         */
        public function createMenu($level_menu)
        {
            $IDNewRecord = (int)$this->getLastID('_:') + 1;
            $dataMenu = array(
                'ID' => $IDNewRecord,
                'level_menu' => $level_menu
            );

            if($this->dbRedis->saveRecord($this->redisTableName . $IDNewRecord, $dataMenu))
            {
                infoLog($_ENV['MODE'], 'Menu created');
                $this->addIDRedis($this->redisTableName . $IDNewRecord);
                $this->forceUpdateSQLDatabase();
                return $IDNewRecord;
            }
            else
            {
                infoLog($_ENV['MODE'], 'Something went wrong. Menu not created.');
                return false;
            }
        }
        
        /**
         * Return list of user's websites
         *
         * @return void
         */
        public function getMenuByID($ID)
        {
            $pageMenu = array();
            if($menus = $this->getColumnsFromRedisID($this->ID_menus, ['ID', 'level_menu']))
            {
                foreach($menus as $menu)
                {
                    if($ID == $menu['ID'])
                    {
                        array_push($pageMenu, $menu);
                    }
                }
                return $pageMenu;
            }
            else
            {
                infoLog(getenv('MODE'), 'Something went wrong.');
                return false;
            }

        }

        /**
         * Update level_menu 
         *
         * @param [type] $ID - id menu
         * @param [type] $level_menu - depth of menu
         * @return [bool] true/false
         */
        public function updateMenu($ID, $level_menu)
        {
            $IDSQL_menus = $this->getColumnsFromRedisID($this->ID_menus, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_menus, 'ID');
            if($index)
            {
                if(isset($level_menu))
                {
                    if(isset($level_menu))
                    {
                        if($this->dbRedis->updateRecord($this->ID_menus[$index], ['level_menu' => $level_menu]))
                        {
                            infoLog(getenv('MODE'), 'Menu updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Menu not updated');
                            return false;
                        }
                    }
                    return true;
                }
                else
                {
                    infoLog(getenv('MODE'), 'Parameters to update are empty');
                    return false;
                }
            }
            else
            {
                infoLog(getenv('MODE'), 'ID Menu not found');
                return false;
            }
    
        }


        public function deleteMenu($ID)
        {
            $IDSQL_menus = $this->getColumnsFromRedisID($this->ID_menus, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_menus, 'ID');
            if($index != -1)
            {
                if($this->dbRedis->clearRecord($this->ID_menus[$index]))
                {
                    infoLog(getenv('MODE'), 'Menu deleted');
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