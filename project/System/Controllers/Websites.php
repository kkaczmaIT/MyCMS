<?php
    namespace System\Controllers;

use System\MainController;


class Websites extends MainController
{
    public function __construct()
    {
        $this->websiteModel  = $this->model('Website', ['websites_:']);
        $this->settingModel = $this->model('Setting', ['settings_:']);
    }

    public function index($ID = 'all')
    {
        $this->websites($ID);
        //$this->settingModel->updateSetting(1, 1048576, 'infopomoc@domena.com');
        // $userHomeDir = $this->websiteModel->get
        //$ID_setting = $this->settingModel->createSetting(10485760, 'kontakt@domena.pl');
        //$this->websiteModel->createWebsite('test', getenv('STORAGE_URL') . $_SESSION['home_directory'] . '/img/shortcut_icon.png', $_SESSION['user_id'], $ID_setting);
        // $this->websiteModel->deleteWebsite(7);
        // $this->settingModel->deleteSetting(2);
        //$websites = $this->websiteModel->getWebsitesByUserID();
         //var_dump($websites);
        // if($this->websiteModel->updateWebsite(5, 'Osobisty Blog', getenv('STORAGE_URL') . $_SESSION['home_directory'] . '/img/shortcut_icon2.jpg'))
        // {
        //     infoLog(getenv('MODE'), 'Webiste updated');
        //     $websites = $this->websiteModel->getWebsitesByUserID();
        //     var_dump($websites);
        //     if($this->websiteModel->changeStatusWebsite(5, 0))
        //     {
        //         infoLog(getenv('MODE'), 'Webiste status changed');
        //         $websites = $this->websiteModel->getWebsitesByUserID();
        //         var_dump($websites);
        //     }
        //     else
        //     {
        //         infoLog(getenv('MODE'), 'Webiste status not updated');
        //     }
        // }
        // else
        // {
        //     infoLog(getenv('MODE'), 'Webiste not updated');
        // }

        // if($this->websiteModel->deleteWebsite(6))
        // {
        //     infoLog(getenv('MODE'), 'Delete ok');
        // }
        // else
        // {
        //     infoLog(getenv('MODE'), 'Delete failed');
        // }
    }

    public function websitespanel($ID = null)
    {
        $data = ['ID' => $ID];
        $this->view('websites/index', $data);
    }

    public function websites($ID = 'all')
    {
        $websitesArr = array();
        $websitesArr['status'] = 'pending';
        if(isLogged())
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET')
            {
                if($websiteArr['data'] = $this->websiteModel->getWebsitesByUserID($ID))
                {
                    $websiteArr['status'] = 'success';
                    echo json_encode($websiteArr);
                }
            }
        }
        else
        {
            $websiteArr['status'] = 'failed';
            $websiteArr['data']['message'] = 'Musisz się zalogować';
            echo json_encode($websiteArr);
        }
        
    }
}