<?php
    namespace System\Controllers;

use System\MainController;


class FileRegisters extends MainController
{
    public function __construct()
    {
        $this->fileRegisterModel  = $this->model('FileRegister', ['files_:']);
    }

    public function index()
    {
        echo 'fileRegister';
        // if($this->fileRegisterModel->registerFile('obraz-1233.jpg', 'image/jpg', 4096, getenv('STORAGE_URL') . $_SESSION['home_directory'] . '/img/obraz-1233.jpg'))
        // {
        //     echo 'Success register new file';
        // }
        // else
        // {
        //     echo 'New File not register';
        // }

        if($files = $this->fileRegisterModel->getFilesByUserID())
        {
            echo 'Success<br>';
            print_r($files);
        }
        else
        {
            echo 'failed';
        }

        if($this->fileRegisterModel->deleteFile(2))
        {
            echo 'delete success<br>';
        }
        else
        {
            echo 'failed';
        }

        if($files = $this->fileRegisterModel->getFilesByUserID())
        {
            echo 'Success<br>';
            print_r($files);
        }
        else
        {
            echo 'null';
        }
    }
}