<?php
    namespace System\Controllers;

use System\MainController;


class Settings extends MainController
{
    public function __construct()
    {
        $this->settingModel = $this->model('Setting', ['settings_:']);
    }

    public function index($ID = "")
    {
       if(!empty($ID))
       {
            $this->settings($ID);
       } 
       else
       {
           die();
       }
        
    }

    /**
     * Handler get request to return setting of website
     *
     * @param string $ID - id website
     * @return void
     */
    public function settings($ID)
    {
        header('Access-Control-Allow-Origin: *');
        //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
        $settingsArr = array();
        $settingsArr['status'] = 'pending';
        if(isLogged())
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET' && isLogged())
            {
                if($settingsArr['data'] = $this->settingModel->getSettingsByID($ID))
                {
                    $settingsArr['status'] = 'success';
                    echo json_encode($settingsArr);
                    die();
                }
                else
                {
                    $settingsArr['data']['message'] = 'Ustawień nie znaleziono';
                    $settingsArr['status'] = 'failed';
                    echo json_encode($settingsArr);
                    die();
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

    /**
     * Add settings to database
     */
    public function add()
    {
        header('Access-Control-Allow-Origin: *');
        //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
        $data = [
            'limit_upload_file_size' => '',
            'contact' => ''
        ];

        $dataFeedback = [
            'message' => '',
            'limit_upload_file_size_err' => '',
            'contact_err' => ''
        ];
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isLogged())
        {
            $dataJson = file_get_contents("php://input");
            $dataJson = json_decode($dataJson);
            if(isset($dataJson->data->limit_upload_file_size))
            {
                $newlimitUploadFileSize = trim(htmlspecialchars($dataJson->data->limit_upload_file_size));
                if(isset($dataJson->data->contact))
                {
                    $newContact = trim(htmlspecialchars($dataJson->data->contact));
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
            
            
            // Init data
            $data = [
                'limit_upload_file_size' => $newlimitUploadFileSize,
                'contact' => $newContact,
            
            ];

            if(empty($data['limit_upload_file_size']))
            {
                $dataFeedback['limit_upload_file_size_err'] = 'Proszę wprowadź dozwoloną wielkość plików wgrywanych do systemu';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                //$this->view('users/register', $data);
                die();
            }

            if (empty($data['contact']))
            {
                $dataFeedback['contact_err'] = 'Proszę wprowadź kontakt';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                //$this->view('users/register', $data);
                die();
            }

            if(empty($dataFeedback['limit_upload_file_size_err'] && empty($dataFeedback['contact_err'])))
            {
                if($this->settingModel->createSetting($data['limit_upload_file_size'], $data['contact']))
                {
                    //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                    $dataFeedback['message'] = 'Ustawienia zostały stworzone';
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
        }
        else
        {
            $dataFeedback['message'] = 'Używasz niedozwolonej metody lub nie jesteś zalogowany';
            $dataFeedback['status'] = 'failed';
        }

    }


        /**
     * Update settings to database
     */
    public function edit($ID)
    {
        header('Access-Control-Allow-Origin: *');
        //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: PUT');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
        $data = [
            'limit_upload_file_size' => '',
            'contact' => ''
        ];

        $dataFeedback = [
            'message' => '',
            'limit_upload_file_size_err' => '',
            'contact_err' => ''
        ];
        if($_SERVER['REQUEST_METHOD'] == 'PUT' && isLogged())
        {
            $dataJson = file_get_contents("php://input");
            $dataJson = json_decode($dataJson);
            if(isset($dataJson->data->limit_upload_file_size))
            {
                $newlimitUploadFileSize = trim(htmlspecialchars($dataJson->data->limit_upload_file_size));
                if(isset($dataJson->data->contact))
                {
                    $newContact = trim(htmlspecialchars($dataJson->data->contact));
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
            
            
            // Init data
            $data = [
                'limit_upload_file_size' => $newlimitUploadFileSize,
                'contact' => $newContact,
            
            ];

            if(empty($data['limit_upload_file_size']))
            {
                $dataFeedback['limit_upload_file_size_err'] = 'Proszę wprowadź dozwoloną wielkość plików wgrywanych do systemu';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                //$this->view('users/register', $data);
                die();
            }

            if (empty($data['contact']))
            {
                $dataFeedback['contact_err'] = 'Proszę wprowadź kontakt';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                //$this->view('users/register', $data);
                die();
            }

            if(empty($dataFeedback['limit_upload_file_size_err'] && empty($dataFeedback['contact_err'])) && is_numeric($ID))
            {
                if($this->settingModel->updateSetting($ID, $data['limit_upload_file_size'], $data['contact']))
                {
                    //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                    $dataFeedback['message'] = 'Ustawienia zostały zmodyfikowane';
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
        }
        else
        {
            $dataFeedback['message'] = 'Używasz niedozwolonej metody lub nie jesteś zalogowany';
            $dataFeedback['status'] = 'failed';
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

                if($this->settingModel->deleteSetting($ID))
                {
                    //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                    $dataFeedback['message'] = 'Ustawienia zostały usunięte';
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
}