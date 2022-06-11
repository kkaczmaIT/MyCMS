<?php
    namespace System\Controllers;

use System\MainController;


class Pageswebs extends MainController
{
    protected $ID_website;
    public function __construct()
    {
        $this->pageswebModel = $this->model('Pagesweb', ['pagewebs_:'] );
        $this->menuModel = $this->model('Menu', ['menus_:']);
        $this->ID_website = $_SESSION['website_id'];
    }

    public function index($ID = 'all')
    {
       $this->pageswebs($ID);
    }

    public function pageslist($ID = null)
    {
        $data = ['ID' => $ID];
        if(is_numeric($ID))
            $_SESSION['page_id'] = $ID;
        $this->view('pagesweb/index', $data);
    }

    public function pageswebs($ID = 'all')
    {
        $pagesArr = array();
        $pagesArr['status'] = 'pending';
        if(isLogged())
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET')
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

     /**
     * Delete settings from database
     */
    public function delete($ID)
    {
        header('Access-Control-Allow-Origin: *');
        //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: DELETE');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

        if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isLogged())
        {
            if($dataPage = $this->pageswebModel->getPagesWeb($ID))
                if($this->pageswebModel->deletePage($ID))
                {
                    
                        if($this->menuModel->deleteMenu($dataPage['ID_menu']))
                        {
                            $dataFeedback['message'] = 'Strona została usunięta';
                            $dataFeedback['status'] = 'success';
                            http_response_code(200);
                            $dataJson = json_encode($dataFeedback);
                            echo $dataJson;
                        }
                        else
                        {
                            $dataFeedback['message'] = 'Strona została usunięta lecz nie wszystkie jej elemty zostały usunięte';
                            $dataFeedback['status'] = 'failed';
                            http_response_code(200);
                            $dataJson = json_encode($dataFeedback);
                            echo $dataJson;
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
     * update page
     */
    public function edit($ID)
    {
        header('Access-Control-Allow-Origin: *');
        //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: PUT');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

        $data = [
            'title' => '',
            'keyphrases' => '',
            'description_meta' => '',
            'content' => '',
            'footer_text' => ''
        ];

        $dataFeedback = [
            'message' => '',
            'title_err' => '',
            'keyphrases_err' => '',
            'description_meta' => '',
            'content_err' => '',
            'footer_text_err' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] == 'PUT' && isLogged())
        {
            $dataJson = file_get_contents("php://input");
            $dataJson = json_decode($dataJson);
            if(isset($dataJson->data->title))
            {
                $newTitle = trim(htmlspecialchars($dataJson->data->title));
                if(isset($dataJson->data->keyphrases))
                {
                    $newkeyphrases = trim(htmlspecialchars($dataJson->data->keyphrases));
                    if(isset($dataJson->data->description_meta))
                    {
                        $newDescriptionMeta = trim(htmlspecialchars($dataJson->data->description_meta));
                        if(isset($dataJson->data->content))
                        {
                            $newContent = trim(htmlspecialchars($dataJson->data->content));
                            if(isset($dataJson->data->footer_text))
                            {
                                $newFooterText = trim(htmlspecialchars($dataJson->data->footer_text));
                            }
                            else
                            {
                                $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                                $dataFeedback['status'] = 'failed';
                                $dataFeedback['footer_text_err'] = 'Proszę wpisać zawartość stopki';
                                $dataJson = json_encode($dataFeedback);
                                echo $dataJson;
                                die('');
                            }
                        }
                        else
                        {              
                            $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                            $dataFeedback['status'] = 'failed';
                            $dataFeedback['content_err'] = 'Proszę wpisać zawartość strony';
                            $dataJson = json_encode($dataFeedback);
                            echo $dataJson;
                            die('');
                        }
                    }
                    else
                    {              
                        $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                        $dataFeedback['status'] = 'failed';
                        $dataFeedback['description_meta_err'] = 'Proszę wpisać opis strony';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        die('');
                    }
                }
                else
                {              
                    $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                    $dataFeedback['status'] = 'failed';
                    $dataFeedback['keyphrases_err'] = 'Proszę wpisać słowa kluczowe';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    die('');
                }
            }
            else
            {
                $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                $dataFeedback['status'] = 'failed';
                $dataFeedback['title_err'] = 'Proszę wpisać tytuł';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                die('');
            }

            $data = [
                'title' => $newTitle,
                'keyphrases' => $newkeyphrases,
                'description_meta' => $newDescriptionMeta,
                'content' => $newContent,
                'footer_text' => $newFooterText
            ];
                if($this->pageswebModel->updatePage($ID, $data['title'], $data['keyphrases'], $data['description_meta'], $data['content'], $data['footer_text']))
                {
                    //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                    $dataFeedback['message'] = 'Strona została zaktualizowana';
                    $dataFeedback['status'] = 'success';
                    http_response_code(200);
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

    public function add()
    {
        header('Access-Control-Allow-Origin: *');
        //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

        $data = [
            'title' => '',
            'keyphrases' => '',
            'description_meta' => '',
            'content' => '',
            'footer_text' => ''
        ];

        $dataFeedback = [
            'message' => '',
            'title_err' => '',
            'keyphrases_err' => '',
            'description_meta' => '',
            'content_err' => '',
            'footer_text_err' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] == 'POST' && isLogged())
        {
            $dataJson = file_get_contents("php://input");
            $dataJson = json_decode($dataJson);
            if(isset($dataJson->data->title) && !empty($dataJson->data->title))
            {
                $newTitle = trim(htmlspecialchars($dataJson->data->title));
                if(isset($dataJson->data->keyphrases) && !empty($dataJson->data->keyphrases))
                {
                    $newkeyphrases = trim(htmlspecialchars($dataJson->data->keyphrases));
                    if(isset($dataJson->data->description_meta) && !empty($dataJson->data->description_meta))
                    {
                        $newDescriptionMeta = trim(htmlspecialchars($dataJson->data->description_meta));
                        if(isset($dataJson->data->content) && !empty($dataJson->data->content))
                        {
                            $newContent = trim(htmlspecialchars($dataJson->data->content));
                            if(isset($dataJson->data->footer_text) && !empty($dataJson->data->footer_text))
                            {
                                $newFooterText = trim(htmlspecialchars($dataJson->data->footer_text));
                            }
                            else
                            {
                                $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                                $dataFeedback['status'] = 'failed';
                                $dataFeedback['footer_text_err'] = 'Proszę wpisać zawartość stopki';
                                $dataJson = json_encode($dataFeedback);
                                echo $dataJson;
                                die('');
                            }
                        }
                        else
                        {              
                            $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                            $dataFeedback['status'] = 'failed';
                            $dataFeedback['content_err'] = 'Proszę wpisać zawartość strony';
                            $dataJson = json_encode($dataFeedback);
                            echo $dataJson;
                            die('');
                        }
                    }
                    else
                    {              
                        $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                        $dataFeedback['status'] = 'failed';
                        $dataFeedback['description_meta_err'] = 'Proszę wpisać opis strony';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        die('');
                    }
                }
                else
                {              
                    $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                    $dataFeedback['status'] = 'failed';
                    $dataFeedback['keyphrases_err'] = 'Proszę wpisać słowa kluczowe';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    die('');
                }
            }
            else
            {
                $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                $dataFeedback['status'] = 'failed';
                $dataFeedback['title_err'] = 'Proszę wpisać tytuł';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                die('');
            }

            $data = [
                'title' => $newTitle,
                'keyphrases' => $newkeyphrases,
                'description_meta' => $newDescriptionMeta,
                'content' => $newContent,
                'footer_text' => $newFooterText
            ];
            if($ID_menu = $this->menuModel->createMenu(1))
            {
                infoLog(getenv('MODE'), 'Menu created');
                if($this->pageswebModel->createPage( $data['title'], $data['keyphrases'], $data['description_meta'], $data['content'], $data['footer_text'], $ID_menu))
                {
                    //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                    $dataFeedback['message'] = 'Strona została utworzona';
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
                infoLog(getenv('MODE'), 'Menu created not created');
                die();
            }
        }
        else
        {
            $dataFeedback['message'] = 'Używasz niedozwolonej metody lub nie jesteś zalogowany';
            $dataFeedback['status'] = 'failed';
            die();
        }   

    }

    public function addpage()
    {
        $this->view('pagesweb/createpage');
    }

}