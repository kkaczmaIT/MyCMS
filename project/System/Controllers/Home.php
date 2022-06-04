<?php
namespace System\Controllers;
use System\MainController;

    class Home extends MainController
    {
        public function index()
        {
            $this->view('home');
        }
    }

?>