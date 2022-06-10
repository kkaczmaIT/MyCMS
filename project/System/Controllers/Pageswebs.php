<?php
    namespace System\Controllers;

use System\MainController;


class Pageswebs extends MainController
{
    protected $ID_website;
    public function __construct()
    {
        $this->pageswebModel  = $this->model('Pagesweb', ['pagewebs_:'] );
        $this->ID_website = $_SESSION['website_id'];
    }

    public function index($ID = 'all')
    {
       $this->pageswebs($ID);
    }

    public function pageslist($ID = null)
    {
        $data = ['ID' => $ID];
        $this->view('pagesweb/index', $data);
    }

    public function pageswebs($ID = 'all')
    {
        $pagesArr = array();
        $pagesArr['status'] = 'pending';
        if(isLogged())
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET' && isLogged())
            {
                if($pagesArr['data'] = $this->pageswebModel->getPagesWeb($ID))
                {
                    $pagesArr['status'] = 'success';
                    echo json_encode($pagesArr);
                }
                else
                {
                    $pagesArr['data']['message'] = 'Strony nie znaleziono';
                    $pagesArr['status'] = 'failed';
                    echo json_encode($pagesArr);
                }
            }
        }
        else
        {
            $pagesArr['status'] = 'failed';
            $pagesArr['data']['message'] = 'Musisz się zalogować';
            echo json_encode($pagesArr);
        }
        
    }

}