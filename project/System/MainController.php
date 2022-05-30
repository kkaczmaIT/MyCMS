<?php
    namespace System;
    use System\Models;
    /**
    * Base Controller - must be    extend to direct controller
    * Load models and views
    * $params - list of arguments to constructor
    */
    class MainController
    {
        // Load model
        // Model - model name
        public function model($model, $params = []) {
            // Require mode file
            require_once 'Models\\' . $model . '.php';

            $model = 'System\Models\\' . $model;
            //Instatiate model
            return new $model(...$params);
        }

        /**
         * Load view
         *  @param string $view - view name
         *  @param array $data - list of data to view 
         */
        public function view($view, $data = [])
        {
            // Check for view file
            if(file_exists(__DIR__ . '\views\\' . $view . '.php'))
            {
                require_once __DIR__ . '\views\\' . $view . '.php';
            }
            else
            {
                // View does not exist
                exit('View does not exist');
            }
        }
    }

 ?>