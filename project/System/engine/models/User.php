<?php
    namespace System\Engine\Models;

    use System\RedisInstance;
    use System\Database;
    class User
    {
        private $db;
        private $dbSql;
        private $dbRedis;

        public function __construct() 
        {
            $this->db = new Database;
            $this->dbSql = $this->db->getConnectionSql();
            $this->dbRedis = $this->db->getConnectionRedis();
        }

        
    }
?>