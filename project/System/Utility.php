<?php
        /**
         * sort array to create record logs
         *
         * @param [type] $key - log
         * @return void
         */
        function getCreateStatus($key)
        {
            $type = explode('_', $key);
            if($type[0] === 'create')
            {
                return $key;
            }
        }
        /**
         * sort array to modified record logs
         *
         * @param [type] $key - log
         * @return void
         */
        function getModifiedStatus($key)
        {
            $type = explode('_', $key);
            if($type[0] === 'modified')
            {
                return $key;
            }
        }
        /**
         * sort array to remove record logs
         *
         * @param [type] $key - log
         * @return void
         */
        function getRemoveStatus($key)
        {
            $type = explode('_', $key);
            if($type[0] === 'remove')
            {
                return $key;
            }
        }

        function infoLog($mode, $content)
        {
            if($mode == "TESTING")
            {
                echo '<br>' . $content . '<br>';
            }
        }
?>