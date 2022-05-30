<?php
    namespace System\Controllers;

use System\MainController;


    class Api extends MainController
    {
        
        public function __construct()
        {
            
        }

        public function index()
        {
            $this->view('users/login');
        }

    }
?>