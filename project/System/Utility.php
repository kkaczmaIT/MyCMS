<?php
        /**
         * sort array to create record logs
         *
         * @param [type] $key - log
         * @return [string] $key
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
         * @return [string] $key
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
         * @return [string] $key
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

        /**
         * check if phrase repeat
         *
         * @param [string] $phrase - new phrase
         * @param [array] $data - array of current phrase. format array[ ['column' => phrase1], [ 'column' => phrase2] ]
         * @param [string] column - name of key in single array from $data to compare; 
         * @return [bool] true/false
         */
        function checkRedundantPhrase($phrase, $data, $column)
        {
            foreach($data as $login)
            {
               if(!strcmp($phrase, $login[$column]))
               {
                   infoLog($_ENV['MODE'], 'Phrase repated');
                    return true;
               }
            }
            infoLog($_ENV['MODE'], 'Phrase not repeated');
            return false;
        }

        /**
         * check if phrase repeat
         *
         * @param [string] $phrase - new phrase
         * @param [array] $data - array of current phrase. format array[ ['column' => phrase1], [ 'column' => phrase2] ]
         * @param [string] column - name of key in single array from $data to compare; 
         * @return [number/null] ID - actual ID in array
         */
        function checkRedundantPhraseGetID($phrase, $data, $column)
        {
            $index = 0;
            foreach($data as $login)
            {
               if(!strcmp($phrase, $login[$column]))
               {
                   infoLog($_ENV['MODE'], 'Phrase repated');
                    return $index;
               }
               $index++;
            }
            infoLog($_ENV['MODE'], 'Phrase not repeated');
            return -1;
        }

        function flash($name = '', $message = '', $class = '')
        {
            if(!empty($name))
            {
                if(!empty($message) && empty($_SESSION[$name]))
                {
                    if(!empty($_SESSION[$name]))
                    {
                        unset($_SESSION[$name]);
                    }

                    if(!empty($_SESSION[$name . '_class']))
                    {
                        unset($_SESSION[$name . '_class']);
                    }
                    $_SESSION[$name] = $message;
                    $_SESSION[$name . '_class'] = $class;
                }
                elseif(empty($message) && !empty($_SESSION[$name]))
                {
                    $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
                    echo '<div class"' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
                    unset($_SESSION[$name]);
                    unset($_SESSION[$name . '_class']); 
                }
            }
        }


        function isLogged()
        {
            if(isset($_SESSION['user_id']) && isset($_SESSION['user_login']))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        function scanUserMediaFiles($catalog)
        {
            return scandir($catalog);
        }

?>