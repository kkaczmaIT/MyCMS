<?php
    namespace System\Models;
    use System\Database;
    
    class Pagesweb
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
        }

        /**
         * load data from SQL table to Redis
         *
         * @return void
         */
        private function loadTablePagesWeb()
        {
            $this->db->loadQuery('SELECT ID, ID_menu, ID_theme, ID_website, title, keyphrases, description_meta, content, footer_text FROM PAGESWEB');
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
        
        public function getWebsitesByUserID($ID = 'all')
        {
            $userWebsites = array();
            if($websites = $this->getColumnsFromRedisID($this->ID_websites, ['ID', 'title_website', 'shortcut_icon_path', 'ID_user', 'is_active', 'ID_settings']))
            {
                foreach($websites as $website)
                {
                    if($_SESSION['user_id'] == $website['ID_user'])
                    {
                        array_push($userWebsites, $website);
                    }
                }
                if($ID == 'all')
                    return $userWebsites;
                elseif(is_numeric($ID))
                {
                    $index = checkRedundantPhraseGetID($ID, $userWebsites, 'ID');
                    if($index != -1)
                    {
                        return $userWebsites[$index];
                    }
                    else
                    {
                        infoLog(getenv('MODE'), 'ID website not found');
                        return false;
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

        /**
         * Get website's pages and return or specific website's page
         *
         * @param string $ID_pages - specific id or all page
         * @return void
         */
        public function getPagesWeb($ID_pages = 'all')
        {
            $pagesWebsites = array();
            if($pages = $this->getColumnsFromRedisID($this->ID_pagesWebs, ['ID', 'ID_menu', 'ID_theme', 'ID_website', 'title', 'keyphrases', 'description_meta', 'content', 'footer_text']))
            {
                foreach($pages as $page)
                {
                    if($_SESSION['website_id'] == $page['ID_website'])
                    {
                        array_push($pagesWebsites, $page);
                    }
                }
                if($ID_pages == 'all')
                    return $pagesWebsites;
                elseif(is_numeric($ID_pages))
                {
                    $index = checkRedundantPhraseGetID($ID_pages, $pages, 'ID');
                    if($index != -1)
                    {
                        return $pagesWebsites;
                    }
                    else
                    {
                        infoLog(getenv('MODE'), 'ID page not found');
                        return false;
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
        public function createPage($title, $keyphrases, $description_meta, $content, $footer_text, $ID_menu)
        {
            $IDNewRecord = (int)$this->getLastID('_:') + 1;
            $dataPagesWeb = array(
                'ID' => $IDNewRecord,
                'ID_menu' => $ID_menu,//last id
                'ID_theme' => 1, //default,
                'ID_website' => $_SESSION['website_id'], //session
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
       * Delete pages from database
       *
       * @param [type] $ID - ID setting
       * @return [bool] - status of operation
       */  
      public function deletePage($ID)
        {
            $IDSQL_page = $this->getColumnsFromRedisID($this->ID_pagesWebs, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_page, 'ID');
            if($index != -1)
            {
                if($this->dbRedis->clearRecord($this->ID_pagesWebs[$index]))
                {
                    infoLog(getenv('MODE'), 'pages deleted');
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
         * Update page data
         *
         * @param [type] $ID - id page
         * @param [type] $title - title of page
         * @param [type] $keyphrases - meta tag
         * @param [type] $description_meta - meta tag
         * @param [type] $content - content of page
         * @param [type] $footer_text - text in footer
         * @return void
         */
        public function updatePage($ID, $title, $keyphrases, $description_meta, $content, $footer_text)
        {
            $IDSQL_page = $this->getColumnsFromRedisID($this->ID_pagesWebs, ['ID']);
            $index = checkRedundantPhraseGetID($ID, $IDSQL_page, 'ID');
            if($index != -1)
            {
                if(isset($title) || isset($keyphrases) || isset($description_meta) || isset($content) || isset($footer_text))
                {
                    if(isset($title))
                    {
                        if($this->dbRedis->updateRecord($this->ID_pagesWebs[$index], ['title' => $title]))
                        {
                            infoLog(getenv('MODE'), 'Title page updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Title page not updated');
                            return false;
                        }
                    }

                    if(isset($keyphrases))
                    {
                        if($this->dbRedis->updateRecord($this->ID_pagesWebs[$index], ['keyphrases' => $keyphrases]))
                        {
                            infoLog(getenv('MODE'), 'Page keyphrases updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Page keyphrases not updated');
                            return false;
                        }
                    }

                    if(isset($description_meta))
                    {
                        if($this->dbRedis->updateRecord($this->ID_pagesWebs[$index], ['description_meta' => $description_meta]))
                        {
                            infoLog(getenv('MODE'), 'Page description meta tag updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Page description meta tag not updated');
                            return false;
                        }
                    }

                    
                    if(isset($content))
                    {
                        if($this->dbRedis->updateRecord($this->ID_pagesWebs[$index], ['content' => $content]))
                        {
                            infoLog(getenv('MODE'), 'Page content updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Page content  not updated');
                            return false;
                        }
                    }

                    if(isset($content))
                    {
                        if($this->dbRedis->updateRecord($this->ID_pagesWebs[$index], ['footer_text' => $footer_text]))
                        {
                            infoLog(getenv('MODE'), 'Page footer text updated');
                        }
                        else
                        {
                            infoLog(getenv('MODE'), 'Page footer text  not updated');
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
                infoLog(getenv('MODE'), 'ID page not found');
                return false;
            }
    
        }
    }
?>