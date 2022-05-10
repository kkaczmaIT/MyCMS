<?php
    namespace System;
    class RedisDatabase {
        private $redis = null;
        public function __construct() {
            $this->redis = new \Redis();
        }

        public function getConnect() {
            return $this->redis;
        }

        /**
         * save to database record of data - paramaters
         *
         * @param array $reqParam - list of query parametrs 
         * @param string $name - id record of list 
         * @return void
         */
        public function saveParamaters($name, $reqParam)
        {
            try
            {
                $this->redis->hMSet("$name", $reqParam);
            }
            catch(\RedisException $e)
            {
                echo `Error: unable to save request parameters`;
                echo $e->getMessage();
            }
        }

           /**
         * save to database record of data - paramaters
         *
         * @param array $reqParamField - list of query parametrs - only field names
         * @param string $name - id record of list 
         * @return void
         */
        public function getParamaters($name, $reqParamField)
        {
            try
            {
                $this->redis->hMGet("$name", $reqParamField);
            }
            catch(\RedisException $e)
            {
                echo `Error: unable to save request parameters`;
                echo $e->getMessage();
            }
        }
    }
    


    
?>