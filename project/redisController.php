<?php
    
    class RedisInstance {
        private $redis = null;
        public function __construct() {
            $this->redis = new Redis();
        }

        public function run() {
            return $this->redis;
        }
    }
    
?>