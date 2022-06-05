<?php
    namespace System\Models;
    use System\Database;
    
    class User
    {
        private $db;
        private $dbSql;
        private $dbRedis;
        private $ID_users  = array();
        private $redisTableName;
        public function __construct($tableName) 
        {
            $this->db = new Database;
            $this->dbSql = $this->db->getConnectionSql();
            $this->dbRedis = $this->db->getConnectionRedis();
            $this->redisTableName = $tableName;
            $this->ID_users = $this->loadTableUsers();
            
        }

        /**
         * load data from SQL table to Redis
         *
         * @return void
         */
        private function loadTableUsers()
        {
            $this->db->loadQuery('SELECT ID, loginU, password, is_active, home_directory, permission, created_at FROM USERS');
            $this->db->executeQuery();
            $users = $this->db->resultSet();
            $IDRedisUsers = $this->db->dataTableLoadToRedis($this->redisTableName, $users);
            return $IDRedisUsers;
        }

        private function addIDRedis($ID)
        {
            array_push($this->ID_users, $ID);
        }
        
        /**
         * Return last ID in Redis table
         *
         * @return void
         */
        private function getLastID($separator)
        {
            $ID_last = 0;
            $users = $this->ID_users;
            foreach($users as $user)
            {
                $ID = explode($separator, $user);
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

        /**
         * Add new user to Redis and register, hash password and create homeDir
         *
         * @param [string] $loginU - user name
         * @param [string] $password - password user. It will be hash
         * @param [type] $permission - permission in thiis version unnecessary
         * @return [bool] true/false
         */
        public function registerUser($loginU, $password, $permission)
        {
            if(!count_chars($loginU, 1) || !count_chars($password, 1) || !count_chars($permission))
            {
                infoLog($_ENV['MODE'], 'Empty value. Please check field');
                return false; //for now              
            }

            if(count_chars($loginU, 1) >= 3)
            {
                $partLogin = substr($loginU, 0, 3);
            }
            else
            {
                $partLogin = 'salt';
            }


            if($loginArray = $this->getColumnsFromRedisID($this->ID_users, ['loginU']))
            {
                if(checkRedundantPhrase($loginU, $loginArray, 'loginU'))
                {
                    infoLog($_ENV['MODE'], 'Login is taken. Try with different login');
                    return false;
                }
               
            }

            $ID = (int)$this->getLastID('_:') + 1;
            $dataUser = array(
                'ID' => $ID,
                'loginU' => $loginU,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'is_active' => 1,
                'home_directory' => $loginU . md5(($partLogin[0])),
                'permission' => $permission,
                'created_at' => date('d.m.y H:i:s')
            );
            if($this->dbRedis->saveRecord($this->redisTableName . $ID, $dataUser))
            {
                infoLog($_ENV['MODE'], 'User registered');
                $this->addIDRedis($this->redisTableName . $ID);
                $this->forceUpdateSQLDatabase();
                return true;
            }
            else
            {
                infoLog($_ENV['MODE'], 'Something went wrong. User not registered.');
                return false;
            }
        }


        /**
         * Check user data with data from database. Check if login is in database. Verify password if login is.
         *
         * @param [string] $login - login user
         * @param [string] $password - password user
         * @return [array] $user/false - in success case array with data and in falied case bool value
         */
        public function login($login, $password)
        {
            if($loginUsers = $this->getColumnsFromRedisID($this->ID_users, ['loginU']))
            {
                if($ID = checkRedundantPhraseGetID($login, $loginUsers, 'loginU'))
                {
                    $user = $this->dbRedis->getRecord($this->ID_users[$ID], ['ID', 'loginU', 'password', 'is_active', 'home_directory', 'permission', 'created_at']);
                    if($user['is_active'])
                    {
                        if(password_verify($password, $user['password']))
                        {
                            $userWithID = ['ID_redis' => $this->ID_users[$ID], ...$user];
                            infoLog($_ENV['MODE'], 'User logged in');
                            //return record of user data from database
                            return $userWithID;
                        }
                        else
                        {
                            infoLog($_ENV['MODE'], 'Password incorrect. User not logged in');
                            return false;
                        }
                    }
                    else
                    {
                        infoLog($_ENV['MODE'], 'User is not active');
                        return false;
                    }
        
                }
                else
                {
                    infoLog($_ENV['MODE'], 'Login not found');
                    return false;
                }
            }
            else
            {
                infoLog($_ENV['MODE'], 'Something went wrong.');
                return false;
            }
        }

        /**
         * Update data of user. Can change at least one of parameters or more. Update current object which logged in
         *
         * @param [string] $user_login - ID sql record 
         * @param [string] $password - new password
         * @param [string] $is_active - change status
         * @param [string] $permission - change permisson - useless for now
         * @return [bool] modified user or false
         */
        public function updateUser($user_login, $password ="", $is_active = "", $permission = "")
        {
            $logins = $this->getColumnsFromRedisID($this->ID_users, ['loginU']);
            $ID_user = $this->ID_users[checkRedundantPhraseGetID($user_login, $logins, 'loginU')];
            if(isset($password) || isset($is_active) || isset($permission))
            {
                if(isset($password))
                {
                    if($this->dbRedis->updateRecord($ID_user, ['password' => password_hash($password, PASSWORD_BCRYPT)]))
                    {
                        infoLog($_ENV['MODE'], 'User\'s password updated');
                    }
                    else
                    {
                        infoLog($_ENV['MODE'], 'User\'s password not updated. Something went wrong');
                        return false;
                    }
                }
                if(isset($is_active) && ($is_active == 0 || $is_active == 1))
                {
                    if($this->dbRedis->updateRecord($ID_user, ['is_active' => $is_active]))
                    {
                        infoLog($_ENV['MODE'], 'User\'s status updated');
                    }
                    else
                    {
                        infoLog($_ENV['MODE'], 'User\'s status not updated. Something went wrong');
                        return false;
                    }
                }
                if(isset($permission) && is_numeric((int)$permission) && ($permission == 0 || $permission == 1 || $permission == 2 || $permission == 4 || $permission == 8))
                {
                    if($this->dbRedis->updateRecord($ID_user, ['permission' => $permission]))
                    {
                        infoLog($_ENV['MODE'], 'User\'s permissions updated');
                    }
                    else
                    {
                        infoLog($_ENV['MODE'], 'User\'s permissions not updated. Something went wrong');
                        return false;
                    }
                }
                return true;
            }
            else
            {
                infoLog($_ENV['MODE'], 'Command\'s parameters was empty');
                return false;
            }
        }

        /**
         * Find user by ID in SQL record
         *
         * @param [type] $ID_user - ID of SQL record
         * @return [array/bool] - in success case return user's data and in failed case return false
         */
        public function getUserDataByID($ID_user)
        {
            foreach($this->ID_users as $ID_redis)
            {
                if($user = $this->dbRedis->getRecord($ID_redis, ['ID', 'loginU', 'password', 'is_active', 'home_directory', 'permission', 'created_at']))
                {
                    if($ID_user == $user['ID'])
                    {
                        infoLog($_ENV['MODE'], 'User found by ID');
                        return $user;
                    }
                }
                else
                {
                    infoLog($_ENV['MODE'], 'Something went wrong');
                    return false;
                }
            }
            infoLog($_ENV['MODE'], 'User not found');
            return false;
        }

        /**
         * Find user by login
         *
         * @param [string] $login - user login
         * @return [array/false] - return in success case user data or in failed case false 
         */
        public function getUserDataByLogin($login)
        {

            foreach($this->ID_users as $ID_redis)
            {
                if($user = $this->dbRedis->getRecord($ID_redis, ['ID', 'loginU', 'password', 'is_active', 'home_directory', 'permission', 'created_at']))
                {
                    if($login == $user['loginU'])
                    {
                       infoLog($_ENV['MODE'], 'User found'); 
                        return $user;
                    }
                }
                else
                {
                    infoLog($_ENV['MODE'], 'Something went wrong');
                    return false;
                }
            }
            infoLog($_ENV['MODE'], 'User not found');
            return false; 
        }

        /**
         * Change Status
         *
         * @param [string] $login_user - user_login
         * @param [bool] $status - user/'s status
         * @return void
         */
        public function changeStatusUser($user_login, $status)
        {
            $logins = $this->getColumnsFromRedisID($this->ID_users, ['loginU']);
            $ID_user = $this->ID_users[checkRedundantPhraseGetID($user_login, $logins, 'loginU')];
            if($this->updateUser($ID_user, null, $status, null))
            {
                infoLog($_ENV['MODE'], 'User\'s status changed');
                return true;
            }
            return false;
        }

        // Update sql database and integrity with redis database
        public function forceUpdateSQLDatabase()
        {
            $this->db->saveRecordActivity('create', 'USERS');
            $this->db->saveRecordActivity('modified', 'USERS');
            $this->db->saveRecordActivity('remove', 'USERS');

        }
        
        /**
         * Prepare user data to JSON response.
         * User data is login and ID
         *
         * @return false/array
         */
        public function getUsersLogin($ID = 'all')
        {
            $userArr = array();
            $user = array();
            if($ID === 'all')
            {
                if($user = $this->getColumnsFromRedisID($this->ID_users, ['ID', 'loginU']))
                {
                    array_push($userArr, $user);
                }
                else
                {
                    infoLog(getenv('MODE'), 'Error User login loaded');
                    return false;
                }
            }
            elseif(is_numeric($ID))
            {
                if($user = $this->getUserDataByID($ID))
                {
                    //'ID', 'loginU', 'password', 'is_active', 'home_directory', 'permission', 'created_at']
                    unset($user['password']);
                    unset($user['home_directory']);   
                    unset($user['is_active']);   
                    unset($user['permission']);
                    unset($user['created_at']);
                    return $user;      
                }
                else
                {
                    infoLog(getenv('MODE'), 'Error User login loaded');
                    return false;
                }
            }
            else
            {
                infoLog($_ENV['MODE'], 'Wrong parameter');
                return false;
            }
            return $userArr;
        }
    }
?>