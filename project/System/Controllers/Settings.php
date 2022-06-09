<?php
    namespace System\Controllers;

use System\MainController;


class Settings extends MainController
{
    public function __construct()
    {
        $this->settingModel = $this->model('Setting', ['settings_:']);
    }

    public function index()
    {
        echo 'settings';
    }
}