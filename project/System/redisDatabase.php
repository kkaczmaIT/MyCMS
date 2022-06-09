<?php
    namespace System;
    class RedisDatabase {
        private $redis = null;
        private $host_redis = null;
        private $port_redis = null;
        private $status;
        public function __construct($host_redis, $port_redis) {
            $this->redis = new \Redis();
            $this->host_redis = $host_redis;
            $this->port_redis = $port_redis;
            $this->redis->connect($this->host_redis, $this->port_redis);
            $this->status = array();
        }

        public function getConnect() {
            return $this->redis;
        }

        /**
         * Update status to current data
         *
         * @param [type] $name - index of record
         * @param [type] $reqParam - list of data
         * @return void
         */
        public function updateStatus($name, $reqParam)
        {
            if(!isset($this->status[$name]))
                $this->status[$name] = array();
            foreach($reqParam as $key => $value)
            {
                if(array_key_exists($key, $this->status[$name]))
                {
                    $this->status[$name][$key] = $value;
                }
                else
                {
                    $this->status[$name][$key] = $value;
                }
                
            }
            return true;
        }

        /**
         * save new record to status set
         *
         * @param [type] $name - id of record
         * @param [type] $reqParam - parameters
         * @return void
         */
        public function saveStatus($name, $reqParam)
        {
            foreach($reqParam as $key => $value)
            {
                $this->status[$name][$key] = $value;
            } 
             infoLog($_ENV['MODE'], 'save Status status: success');
        }

        /**
         * Clear all data or one single record
         *
         * @param string $name - name of record or special phrase all - clear all status
         * @return void
         */
        public function clearStatus($name = "all")
        {
            if($name == 'all')
            {
                $this->status = array();
                return;
            }
            else
            {
                unset($this->status[$name]);
            }
            
            infoLog($_ENV['MODE'], 'Clear status record: success');
        }

        /**
         * return all status or specific record
         *
         * @param string $name - all or specific record
         * @return void
         */
        public function getStatus($name = 'all')
        {
            if($name == 'all')
            {
                return $this->status;
            }
            return $this->status[$name];
        }

        /**
         * save to database record of data - paramaters
         *
         * @param array $reqParam - list of query parameters assoc array
         * @param string $name - id record of list 
         * @return void
         */
        public function saveRecord($name, $reqParam)
        {
            try
            {
                if($this->redis->hMSet($name, $reqParam))
                {
                   $this->saveStatus('create_' . $name, $reqParam);
                    infoLog($_ENV['MODE'], 'save record status: success');
                    return true;
                }
                else
                {
                    infoLog($_ENV['MODE'], 'save record status: failed');
                    return false;
                }
            }
            catch(\RedisException $e)
            {
                infoLog($_ENV['MODE'], 'Error: unable to save request parameters');
                echo $e->getMessage();
            }
        }

           /**
         * save to database record of data - paramaters
         *
         * @param array $reqParamField - list of query parametrs - only field names - keys
         * @param string $name - id record of list 
         * @return void
         */
        public function getRecord($name, $reqParamField)
        {
            try
            {
               $record = $this->redis->hMGet($name, $reqParamField);
               return $record;
            }
            catch(\RedisException $e)
            {
                infoLog($_ENV['MODE'], 'Error: unable to get request parameters');
                echo $e->getMessage();
                return false;
            }
        }

        public function clearRecord($name)
        {
            $ID = $this->getRecord($name, ['ID']);
            if($this->redis->del($name))
            {
                $this->clearStatus($name);
                $this->saveStatus('remove_' . $name, ['ID' => $ID['ID']]); 
                infoLog($_ENV['MODE'], 'clear record status: successfully');
                return true;
            }
            else
            {
                infoLog($_ENV['MODE'], 'clear record status: failed');
                return false;
            }
        }

    /**
     * Update record of data. It can change current data but not to insert new key value.
     *
     * @param [type] $name - name of record
     * @param [type] $reqParam - array of key value to modified
     * @return void
     */
    public function updateRecord($name, $reqParam)
    {
        foreach($reqParam as $key => $value)
        {
            $tmp = $this->getRecord($name, array($key));
            if($tmp[$key] === false)
            {
                infoLog($_ENV['MODE'], 'key is not in list');
            }
            else
            {
                if($this->redis->hMSet($name, array($key => $value)))
                {
                    $this->updateStatus('modified_' . $name, [$key => $value]);
                    infoLog($_ENV['MODE'], 'update record status: success');
                }
                else
                {
                    infoLog($_ENV['MODE'], 'update record status: failed');
                    return null;
                }
               
            }
        }
        $this->updateStatus('modified_' . $name, $this->getRecord($name, ['ID']));
        return true;
    }


}


?>