<?php
    namespace System\Controllers;

use System\MainController;


class Websites extends MainController
{
    public function __construct()
    {
        $this->websiteModel  = $this->model('Website', ['websites_:']);
    }

    public function index()
    {
        echo 'websites';
    }
}