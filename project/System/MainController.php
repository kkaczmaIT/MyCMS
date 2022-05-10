<?php
    namespace System;
    /**
    * Base Controller - must be    extend to direct controller
    * Load models and views
    */
    class MainController
    {
        // Load model
        // Model - model name
        public function model($model) {
            // Require mode file
            require_once '../engine/models/' . $model . '.php';

            //Instatiate model
            return new $model();
        }

        /**
         * Load view
         *  @param string $view - view name
         *  @param array $data - list of data to view 
         */
        public function view($view, $data = [])
        {
            // Check for view file
            if(file_exists('../engine/views/' . $view . '.php'))
            {
                require_once '../engine/views/' . $view . '.php';
            }
            else
            {
                // View does not exist
                exit('View does not exist');
            }
        }
    }

 ?>