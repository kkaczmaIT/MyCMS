<?php
    namespace System\Controllers;

use System\MainController;


class Menus extends MainController
{
    public function __construct()
    {
        $this->menuModel  = $this->model('Menu', ['menus_:'] );
        $this->listitemModel = $this->model('ListItem', ['listitems_:']);
    }

    public function index($ID)
    {
        if(is_numeric((int)$ID))
        {
            $_SESSION['menu_id'] = $ID;
        }
        $this->menus($ID);
    }

    public function linkitem()
    {
        $this->view('menu/linkitem');
    }

    public function getlinkitem($ID)
    {
        if(is_numeric($ID))
        {
        $listitemArr = array();
        $listitemArr['status'] = 'pending';
        if(isLogged())
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET')
            {
                if($listitemArr['data'] = $this->listitemModel->getListItemByID($ID))
                {
                        $listitemArr['status'] = 'success';
                        echo json_encode($listitemArr);
                }
                else
                {
                    $menuArr['data']['message'] = 'Pozycji menu nie znaleziono';
                    $menuArr['status'] = 'failed';
                    echo json_encode($listitemArr);
                }
            }
        }
        else
        {
            $listitemArr['status'] = 'failed';
            $listitemArr['data']['message'] = 'Musisz się zalogować';
            echo json_encode($listitemArr);
        }
        
        }
    }

    public function menupanel()
    {
        $this->view('menu/index');
    }

    public function menus($ID)
    {
        if(is_numeric($ID))
        {
        $menuArr = array();
        $menuArr['status'] = 'pending';
        if(isLogged())
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET')
            {
                if($menuArr['data'] = $this->menuModel->getMenuByID($ID))
                {
                    if($menuArr['data']['listitem'] = $this->listitemModel->getListItemsByMenuID($ID))
                    {
                        $menuArr['status'] = 'success';
                        echo json_encode($menuArr);
                    }
                    else
                    {
                        $menuArr['status'] = 'failed';
                        $menuArr['data']['message'] = 'Nie udało się wczytać listy';
                        echo json_encode($menuArr);
                    }
                }
                else
                {
                    $menuArr['data']['message'] = 'Menu nie znaleziono';
                    $menuArr['status'] = 'failed';
                    echo json_encode($menuArr);
                }
            }
        }
        else
        {
            $menuArr['status'] = 'failed';
            $menuArr['data']['message'] = 'Musisz się zalogować';
            echo json_encode($menuArr);
        }
        
        }
    }

    public function addlinkitem()
    {
        header('Access-Control-Allow-Origin: *');
        //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

        $data = [
            'text_link' => '',
            'href' => '',
            'depth' => '',
            'order_item' => ''
        ];

        $dataFeedback = [
            'message' => '',
            'text_link_err' => '',
            'href_err' => '',
            'depth_err' => '',
            'order_item_err' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] == 'POST' && isLogged())
        {
            $dataJson = file_get_contents("php://input");
            $dataJson = json_decode($dataJson);
            if(isset($dataJson->data->text_link) && !empty($dataJson->data->text_link))
            {
                $newTextLink = trim(htmlspecialchars($dataJson->data->text_link));
                if(isset($dataJson->data->href) && !empty($dataJson->data->href))
                {
                    $newHref = trim(htmlspecialchars($dataJson->data->href));
                    if(isset($dataJson->data->depth) && !empty($dataJson->data->depth))
                    {
                        $newDepth = trim(htmlspecialchars($dataJson->data->depth));
                        if(isset($dataJson->data->order_item) && !empty($dataJson->data->order_item))
                        {
                            $newOrderItem = trim(htmlspecialchars($dataJson->data->order_item));
                        }
                        else
                        {              
                            $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                            $dataFeedback['status'] = 'failed';
                            $dataFeedback['order_item_err'] = 'Proszę wpisać pozycje w menu';
                            $dataJson = json_encode($dataFeedback);
                            echo $dataJson;
                            die('');
                        }
                    }
                    else
                    {              
                        $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                        $dataFeedback['status'] = 'failed';
                        $dataFeedback['depth_err'] = 'Proszę wpisać ';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        die('');
                    }
                }
                else
                {              
                    $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                    $dataFeedback['status'] = 'failed';
                    $dataFeedback['href_err'] = 'Proszę wpisać adres linku';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    die('');
                }
            }
            else
            {
                $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                $dataFeedback['status'] = 'failed';
                $dataFeedback['text_link_err'] = 'Proszę wpisać tytuł linku widoczny na stronie';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                die('');
            }

            $data = [
                'text_link' => $newTextLink,
                'href' => $newHref,
                'depth' => $newDepth,
                'order_item' => $newOrderItem
            ];
         
                if($this->listitemModel->createListItem( $data['text_link'], $data['href'], $data['depth'], $data['order_item'], $_SESSION['menu_id']))
                {
                    //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                    $dataFeedback['message'] = 'Nowy link został utworzony';
                    $dataFeedback['status'] = 'success';
                    http_response_code(201);
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $dataErr);
                }
                else
                {
                    die('Pojawił się błąd');
                }
        }
        else
        {
            $dataFeedback['message'] = 'Używasz niedozwolonej metody lub nie jesteś zalogowany';
            $dataFeedback['status'] = 'failed';
            die();
        }   

    }
   /**
     * update link item
     */
    public function editlinkitem($ID)
    {
        header('Access-Control-Allow-Origin: *');
        //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: PUT');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

        $data = [
            'text_link' => '',
            'href' => '',
            'depth' => '',
            'order_item' => ''
        ];

        $dataFeedback = [
            'message' => '',
            'text_link_err' => '',
            'href_err' => '',
            'depth_err' => '',
            'order_item_err' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] == 'PUT' && isLogged())
        {
            $dataJson = file_get_contents("php://input");
            $dataJson = json_decode($dataJson);
            if(isset($dataJson->data->text_link) && !empty($dataJson->data->text_link))
            {
                $newTextLink = trim(htmlspecialchars($dataJson->data->text_link));
                if(isset($dataJson->data->href) && !empty($dataJson->data->href))
                {
                    $newHref = trim(htmlspecialchars($dataJson->data->href));
                    if(isset($dataJson->data->depth) && !empty($dataJson->data->depth))
                    {
                        $newDepth = trim(htmlspecialchars($dataJson->data->depth));
                        if(isset($dataJson->data->order_item) && !empty($dataJson->data->order_item))
                        {
                            $newOrderItem = trim(htmlspecialchars($dataJson->data->order_item));
                        }
                        else
                        {              
                            $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                            $dataFeedback['status'] = 'failed';
                            $dataFeedback['order_item_err'] = 'Proszę wpisać pozycje w menu';
                            $dataJson = json_encode($dataFeedback);
                            echo $dataJson;
                            die('');
                        }
                    }
                    else
                    {              
                        $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                        $dataFeedback['status'] = 'failed';
                        $dataFeedback['depth_err'] = 'Proszę wpisać czy to podmenu(2) czy menu(1)';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        die('');
                    }
                }
                else
                {              
                    $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                    $dataFeedback['status'] = 'failed';
                    $dataFeedback['href_err'] = 'Proszę wpisać adres linku';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    die('');
                }
            }
            else
            {
                $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                $dataFeedback['status'] = 'failed';
                $dataFeedback['text_link_err'] = 'Proszę wpisać tytuł linku widoczny na stronie';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                die('');
            }

            $data = [
                'text_link' => $newTextLink,
                'href' => $newHref,
                'depth' => $newDepth,
                'order_item' => $newOrderItem
            ];

            if(is_numeric((int)$ID))
            {
                if($this->listitemModel->updateListItem($ID, $data['text_link'], $data['href'], $data['depth'], $data['order_item']))
                {
                    //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                    $dataFeedback['message'] = 'Link został zaktualizowany';
                    $dataFeedback['status'] = 'success';
                    http_response_code(200);
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    die();
                    //$this->view('users/register', $dataErr);
                }
                else
                {
                    die('Pojawił się błąd');
                }
            }
            else
            {
                die('Pojawił się błąd');
            }
                
        }
        else
        {
            $dataFeedback['message'] = 'Używasz niedozwolonej metody lub nie jesteś zalogowany';
            $dataFeedback['status'] = 'failed';
            die();
        }   
    }

     /**
     * Delete linkitem from database
     */
    public function deletelistitem($ID)
    {
        header('Access-Control-Allow-Origin: *');
        //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: DELETE');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

        if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isLogged())
        {

                if($this->listitemModel->deleteListItem($ID))
                {
                    $dataFeedback['message'] = 'Pozycja w menu została usunięta';
                    $dataFeedback['status'] = 'success';
                    http_response_code(200);
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                }
                else
                {
                    die('Pojawił się błąd');
                }
        }
        else
        {
            $dataFeedback['message'] = 'Używasz niedozwolonej metody lub nie jesteś zalogowany';
            $dataFeedback['status'] = 'failed';
            die();
        }   

    }

}