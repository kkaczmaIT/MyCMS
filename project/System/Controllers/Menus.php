<?php
    namespace System\Controllers;

use System\MainController;


class Menus extends MainController
{
    public function __construct()
    {
        $this->MenuModel  = $this->model('Menu', ['menus_:', 1] );
        $this->ListItemModel = $this->model('ListItem', ['listitems_:', 1]);
    }

    public function index()
    {
        echo 'menus, lisitem';
    }
}