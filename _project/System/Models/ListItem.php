<?php
    namespace System\Models;
    use System\Database;
    
    class ListItem
    {
        private $db;
        private $dbSql;
        private $dbRedis;
        private $ID_listitems  = array();
        private $redisTableName;
        public function __construct($tableName) 
        {
            $this->db = new Database;
            $this->dbSql = $this->db->getConnectionSql();
            $this->dbRedis = $this->db->getConnectionRedis();
            $this->redisTableName = $tableName;
            $this->ID_listitems = $this->loadTableListItem();
        }

        /**
         * load data from SQL table to Redis
         *
         * @return void
         */
        private function loadTableListItem()
        {
            $this->db->loadQuery('SELECT ID, ID_menu,text_link, href, depth, order_item FROM LISTITEM');
            $this->db->executeQuery();
            $listItems = $this->db->resultSet();
            $IDRedisListItem = $this->db->dataTableLoadToRedis($this->redisTableName, $listItems);
            return $IDRedisListItem;
        }


        private function addIDRedis($ID)
        {
            array_push($this->ID_listitems, $ID);
        }
        
        /**
         * Return last ID in Redis table
         *
         * @return void
         */
        private function getLastID($separator)
        {
            $ID_last = 0;
            $listItems = $this->ID_listitems;
            foreach($listItems as $listItem)
            {
                $ID = explode($separator, $listItem);
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
            $this->db->saveRecordActivity('create', 'LISTITEM');
            $this->db->saveRecordActivity('modified', 'LISTITEM');
            $this->db->saveRecordActivity('remove', 'LISTITEM');
        }
        
        /**
         * Create new menu position as record in database
         *
         * @param [type] $text_link - visible text on page
         * @param [type] $href - link to direct page
         * @param [type] $depth -  level menu. main menu or submenu
         * @param [type] $order_item - position in set
         * @return void
         */
        public function createListItem($text_link, $href, $depth, $order_item, $ID_menu)
        {
            $IDNewRecord = (int)$this->getLastID('_:') + 1;
            $dataListItem = array(
                'ID' => $IDNewRecord,
                'ID_menu' => $ID_menu,
                'text_link' => $text_link,
                'href' => $href,
                'depth' => $depth,
                'order_item' => $order_item
            );

            if($this->dbRedis->saveRecord($this->redisTableName . $IDNewRecord, $dataListItem))
            {
                infoLog($_ENV['MODE'], 'List item created');
                $this->addIDRedis($this->redisTableName . $IDNewRecord);
                $this->forceUpdateSQLDatabase();
                return true;
            }
            else
            {
                infoLog($_ENV['MODE'], 'Something went wrong. List item not created.');
                return false;
            }
        }
        
        /**
         * Return list of user's websites
         *
         * @return void
         */
        public function getListItemsByMenuID($ID)
        {
            $menuItems = array();
            if($listItems = $this->getColumnsFromRedisID($this->ID_listitems, ['ID', 'ID_menu', 'text_link', 'href', 'depth', 'order_item']))
            {
                foreach($listItems as $listItem)
                {
                    if( $ID == $listItem['ID_menu'])
                    {
                        array_push($menuItems, $listItem);
                    }
                }
                return $menuItems;
            }
            else
            {
                infoLog(getenv('MODE'), 'Something went wrong.');
                return false;
            }

        }

/**
         * Return list of user's websites
         *
         * @return void
         */
        public function getListItemByID($ID)
        {
            $item = array();
            if($listItems = $this->getColumnsFromRedisID($this->ID_listitems, ['ID', 'ID_menu', 'text_link', 'href', 'depth', 'order_item']))
            {
                foreach($listItems as $listItem)
                {
                    if( $ID == $listItem['ID'])
                    {
                        array_push($item, $listItem);
                    }
                }
                return $item;
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
        public function updateListItem($ID, $text_link, $href, $depth, $order_item)
        {
            $IDSQL_listitem = $this->getColumnsFromRedisID($this->ID_listitems, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_listitem, 'ID');
            if($index)
            {
                if(isset($text_link) || isset($href) || isset($depth) || isset($order_item))
                {
                    if(isset($text_link))
                    {
                        if($this->dbRedis->updateRecord($this->ID_listitems[$index], ['text_link' => $text_link]))
                        {
                            infoLog(getenv('MODE'), 'List item title updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'List item title not updated');
                            return false;
                        }
                    }

                    if(isset($href))
                    {
                        if($this->dbRedis->updateRecord($this->ID_listitems[$index], ['href' => $href]))
                        {
                            infoLog(getenv('MODE'), 'Href updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Href not updated');
                            return false;
                        }
                    }

                    if(isset($depth))
                    {
                        if($this->dbRedis->updateRecord($this->ID_listitems[$index], ['depth' => $depth]))
                        {
                            infoLog(getenv('MODE'), 'Depth updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Depth not updated');
                            return false;
                        }
                    }

                    if(isset($order_item))
                    {
                        if($this->dbRedis->updateRecord($this->ID_listitems[$index], ['order_item' => $order_item]))
                        {
                            infoLog(getenv('MODE'), 'Order updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Order not updated');
                            return false;
                        }

                    }
                    $this->forceUpdateSQLDatabase();
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
                infoLog(getenv('MODE'), 'ID List Item not found');
                return false;
            }
    
        }


        public function deleteListItem($ID)
        {
            $IDSQL_listitem = $this->getColumnsFromRedisID($this->ID_listitems, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_listitem, 'ID');
            if($index != -1)
            {
                if($this->dbRedis->clearRecord($this->ID_listitems[$index]))
                {
                    infoLog(getenv('MODE'), 'List item deleted');
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