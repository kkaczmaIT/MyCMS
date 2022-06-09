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
        public function __construct($tableName, $ID_menu) 
        {
            $this->db = new Database;
            $this->dbSql = $this->db->getConnectionSql();
            $this->dbRedis = $this->db->getConnectionRedis();
            $this->redisTableName = $tableName;
            $this->ID_menus = $this->loadTableMenu();
            $this->ID_menu = $ID_menu;
        }

        /**
         * load data from SQL table to Redis
         *
         * @return void
         */
        private function loadTableMenu()
        {
            $this->db->loadQuery('SELECT M.ID, M.level_menu FROM MENU M WHERE PAGESWEB.ID_menu=:pagesweb_menu_id');
            $this->db->executeQuery(['pagesweb_menu_id' => $this->ID_menu]);
            $menus = $this->db->resultSet();
            $IDRedisMenus = $this->db->dataTableLoadToRedis($this->redisTableName, $menus);
            return $IDRedisMenus;
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
         * @return true/false
         */
        public function createListItem($level_menu)
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
                return true;
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
        public function getWebsitesByUserID()
        {
            $userWebsites = array();
            if($websites = $this->getColumnsFromRedisID($this->ID_websites, ['ID', 'title_website', 'shortcut_icon_path', 'ID_user', 'is_active']))
            {
                foreach($websites as $website)
                {
                    if($_SESSION['user_id'] == $website['ID_user'])
                    {
                        array_push($userWebsites, $website);
                    }
                }
                return $userWebsites;
            }
            else
            {
                infoLog(getenv('MODE'), 'Something went wrong.');
                return false;
            }

        }

        /**
         * Update title and icon website 
         *
         * @param [type] $ID - id website
         * @param [type] $title_website 
         * @param [type] $shortcut_icon_path
         * @return [bool] true/false
         */
        public function updateWebsite($ID, $title_website, $shortcut_icon_path)
        {
            $IDSQL_websites = $this->getColumnsFromRedisID($this->ID_websites, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_websites, 'ID');
            if($index)
            {
                if(isset($title_website) || isset($shortcut_icon_path))
                {
                    if(isset($title_website))
                    {
                        if($this->dbRedis->updateRecord($this->ID_websites[$index], ['title_website' => $title_website]))
                        {
                            infoLog(getenv('MODE'), 'Website title updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Website title not updated');
                            return false;
                        }
                    }

                    if(isset($shortcut_icon_path))
                    {
                        if($this->dbRedis->updateRecord($this->ID_websites[$index], ['shortcut_icon_path' => $shortcut_icon_path]))
                        {
                            infoLog(getenv('MODE'), 'Website icon updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Website icon not updated');
                            return false;
                        }
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
                infoLog(getenv('MODE'), 'ID Website not found');
                return false;
            }
    
        }

             /**
         * Update title and icon website 
         *
         * @param [type] $ID - id website
         * @param [type] $title_website 
         * @param [type] $shortcut_icon_path
         * @return [bool] true/false
         */
        public function changeStatusWebsite($ID, $status)
        {
            $IDSQL_websites = $this->getColumnsFromRedisID($this->ID_websites, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_websites, 'ID');
            if($index)
            {
                if(isset($status))
                {

                        if($this->dbRedis->updateRecord($this->ID_websites[$index], ['is_active' => $status]))
                        {
                            infoLog(getenv('MODE'), 'Website status changed');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Website status not changed');
                            return false;
                        }
                        return true;
                }
                else
                {
                    infoLog(getenv('MODE'), 'status is empty');
                    return false;
                }
            }
            else
            {
                infoLog(getenv('MODE'), 'ID Website not found');
                return false;
            }
    
        }

        public function deleteWebsite($ID)
        {
            $IDSQL_websites = $this->getColumnsFromRedisID($this->ID_websites, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_websites, 'ID');
            if($index != -1)
            {
                if($this->dbRedis->clearRecord($this->ID_websites[$index]))
                {
                    infoLog(getenv('MODE'), 'Website deleted');
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