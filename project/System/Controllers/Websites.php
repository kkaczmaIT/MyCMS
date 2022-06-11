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
    }

    /**
     * Main GUI dashboard 
     *
     * @param [type] $ID - id website
     * @return void
     */
    public function websitespanel($ID = null)
    {
        $data = ['ID' => $ID];
        if(is_numeric((int)$ID))
            $_SESSION['website_id'] = $ID;
        else
            unset($_SESSION['website_id']);
        $this->view('websites/index', $data);
    }

    /**
     * Handler get request to return all user website 
     *
     * @param string $ID - id website
     * @return void
     */
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
                    if(is_numeric((int)$ID))
                    $_SESSION['website_id'] = $ID;
                    else
                    unset($_SESSION['website_id']);
                    $websiteArr['status'] = 'success';
                    echo json_encode($websiteArr);
                }
                else
                {
                    $websiteArr['data']['message'] = 'Witryny nie znaleziono';
                    $websiteArr['status'] = 'failed';
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

    /**
     * edit website change title and shortcut icon
     *
     * @param [type] $ID - id website
     * @return void
     */
    public function edit($ID)
    {
        header('Access-Control-Allow-Origin: *');
         //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: PUT');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
        $data = [
            'title_website' => '',
            'shortcut_icon_path' => ''
        ];

        $dataFeedback = [
            'message' => '',
            'title_website_err' => '',
            'shortcut_icon_path_err' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] == 'PUT' && isLogged())
        {
            $dataJson = file_get_contents("php://input");
            $dataJson = json_decode($dataJson);
                if(isset($dataJson->data->title_website))
                {
                    $newTitleWebsite = trim(htmlspecialchars($dataJson->data->title_website));
                    if(isset($dataJson->data->shortcut_icon_path))
                    {
                        $newShortcutIconPath = trim(htmlspecialchars($dataJson->data->shortcut_icon_path));
                    }
                    else
                    {              
                        $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                        $dataFeedback['status'] = 'failed';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        die('Pojawił się błąd');
                    }
                }
                else
                {
                    $dataFeedback['message'] = 'Pojawił się błąd odczytu danych z formularza';
                    $dataFeedback['status'] = 'failed';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    die('Pojawił się błąd');
                }
            
            
            // Init data
            $data = [
                'title_website' => $newTitleWebsite,
                'shortcut_icon_path' => getenv('STORAGE_URL') . $_SESSION['home_directory'] . '//img/' . $newShortcutIconPath,
            ];

            if (empty($data['title_website']))
            {
                $dataFeedback['title_website_err'] = 'Proszę wprowadź nowy tytuł strony';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                //$this->view('users/register', $data);
                die();
            }

            if (empty($data['shortcut_icon_path']))
            {
                $dataFeedback['shortcut_icon_path_err'] = 'Proszę wprowadź nową ikonę witryny';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                //$this->view('users/register', $data);
                die();
            }


            if(empty($dataFeedback['title_website_err']) && empty($dataFeedback['shortcut_icon_path_err']))
            {
                if(is_numeric($ID))
                {
                    if($this->websiteModel->updateWebsite($ID,$data['title_website'], $data['shortcut_icon_path']))
                    {
                        
                        infoLog(getenv('MODE'), 'Website\'s data updated');
                        //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                        $dataFeedback['message'] = 'Dane witryny zostały poprawnie zmodyfikowane';
                        $dataFeedback['status'] = 'success';
                        http_response_code(201);
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        //$this->view('users/register', $dataErr);
                    }
                    else
                    {
                        $dataFeedback['message'] = 'Pojawił się błąd';
                        $dataFeedback['status'] = 'failed';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        die();
                    }
                }
                else
                {
                    $dataFeedback['message'] = 'Niepoprawne ID witryny';
                    $dataFeedback['status'] = 'failed';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    die();
                }
                
        }
        else
        {
            $dataFeedback['message'] = 'Pojawił się błąd';
            $dataFeedback['status'] = 'failed';
            $dataJson = json_encode($dataFeedback);
            echo $dataJson;
            die();
            //$this->view('users/login', $dataFeedback);
        }
    }
    else
    {
        $dataFeedback['message'] = 'Aby kontynuować musisz być zalogowany lub używasz niedozwolonej metody';
        $dataFeedback['status'] = 'failed';
        $dataJson = json_encode($dataFeedback);
        echo $dataJson;
        die();
        //$this->view('users/login', $dataFeedback);
    }
}

    public function editwebsite($ID)
    {
        $data = [
            'ID' => $ID,
            'list_files' => scanUserMediaFiles(getenv('STORAGE_PATH') . $_SESSION['home_directory'] . '/img')
        ];
        unset($data['list_files'][0]);
        unset($data['list_files'][1]);
        $this->view('websites/editwebsite', $data);
    }

        /**
         * Create new website. receive post request.
         * @return void
         */
        public function add()
        {
            header('Access-Control-Allow-Origin: *');
             //header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: POST');
            header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
            $data = [
                'title_website' => '',
                'shortcut_icon_path' => '',
                'contact' => ''
            ];

            $dataFeedback = [
                'message' => '',
                'title_website_err' => '',
                'shortcut_icon_path_err' => '',
                'contact_err' => ''
            ];
            if($_SERVER['REQUEST_METHOD'] == 'POST' && isLogged())
            {
                $dataJson = file_get_contents("php://input");
                $dataJson = json_decode($dataJson);
                if(isset($dataJson->data->title_website))
                {
                    $newTitleWebsite = trim(htmlspecialchars($dataJson->data->title_website));
                    if(isset($dataJson->data->shortcut_icon_path))
                    {
                        $newShortcutIconPath = trim(htmlspecialchars($dataJson->data->shortcut_icon_path));
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
                }
                else
                {
                    die('Pojawił się błąd');
                }
                
                
                // Init data
                $data = [
                    'title_website' => $newTitleWebsite,
                    'shortcut_icon_path' => getenv('STORAGE_URL') . $_SESSION['home_directory'] . '//img/' . $newShortcutIconPath,
                    'contact' => $newContact
                ];

                if(empty($data['title_website']))
                {
                    $dataFeedback['title_website_err'] = 'Proszę wprowadź tytuł witryny';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }

                if (empty($data['shortcut_icon_path']))
                {
                    $dataFeedback['shortcut_icon_path_err'] = 'Proszę wprowadź ikonę witryny';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }

                if (empty($data['contact']))
                {
                    $dataFeedback['contact_err'] = 'Proszę wprowadź adres email';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }

                if(empty($dataFeedback['title_website_err'] && empty($dataFeedback['shortcut_icon_path_err'])) && empty($dataFeedback['contact_err']))
                {
                    if($ID_settings = $this->settingModel->createSetting(getenv('LIMIT_UPLOAD_FILE_SIZE'), $data['contact']))
                    {
                        infoLog(getenv('MODE'), 'Settings of website created');
                    }
                    else
                    {
                        die('Pojawił się błąd');
                    }
                    if($this->websiteModel->createWebsite($data['title_website'], $data['shortcut_icon_path'], $_SESSION['user_id'], $ID_settings))
                    {
                        $dataFeedback['message'] = 'Witryna została poprawnie stworzona';
                        $dataFeedback['status'] = 'success';
                        http_response_code(201);
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        //$this->view('users/register', $dataErr);
                    }
                    else
                    {
                        $dataFeedback['message'] = 'Witryna nie została poprawnie stworzona';
                        $dataFeedback['status'] = 'failed';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson; 
                        die('Pojawił się błąd');
                    }
                }
            }
            else
            {
                //$this->view('website/create', $dataFeedback);
                $dataFeedback['message'] = 'Nie jesteś zalogowany lub używasz niedozowlonej metody. Aby kontynuować należy się zalogować.';
                $dataFeedback['status'] = 'failed';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                die();
            }
        }

        /**
         * GUI form to create website
         *
         * @return void
         */
        public function addwebsite()
        {
            $data = [
                'list_files' => scanUserMediaFiles(getenv('STORAGE_PATH') . $_SESSION['home_directory'] . '/img')
            ];
            unset($data['list_files'][0]);
            unset($data['list_files'][1]);
            $this->view('websites/createwebsite', $data);
        }

    /**
     * edit website change title and shortcut icon
     *
     * @param [type] $ID - id website
     * @return void
     */
    public function changestatuswebsite($ID, $status)
    {
        header('Access-Control-Allow-Origin: *');
         //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: PUT');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

        $dataFeedback = [
            'message' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] == 'PUT' && isLogged())
        {
            $dataJson = file_get_contents("php://input");
            $dataJson = json_decode($dataJson);
            $ID = trim(htmlspecialchars($ID));
            $status = trim(htmlspecialchars($status));
                if(is_numeric($ID) && is_numeric($status))
                {
                    if($this->websiteModel->changeStatusWebsite($ID,$status))
                    {
                        
                        infoLog(getenv('MODE'), 'Website\'s status updated');
                        //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                        $dataFeedback['message'] = 'Status witryny został zmieniony';
                        $dataFeedback['status'] = 'success';
                        http_response_code(201);
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        //$this->view('users/register', $dataErr);
                    }
                    else
                    {
                        $dataFeedback['message'] = 'Pojawił się błąd';
                        $dataFeedback['status'] = 'failed';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        die();
                    }
                }
                else
                {
                    $dataFeedback['message'] = 'Niepoprawne ID witryny lub niepoprawny status witryny';
                    $dataFeedback['status'] = 'failed';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    die();
                }
    }
    else
    {
        $dataFeedback['message'] = 'Aby kontynuować musisz być zalogowany lub używasz niedozwolonej metody';
        $dataFeedback['status'] = 'failed';
        $dataJson = json_encode($dataFeedback);
        echo $dataJson;
        die();
        //$this->view('users/login', $dataFeedback);
    }
    }

    public function settings($ID_settings)
    {
        $data = [
            'ID' => $ID_settings
        ];
        $this->view('websites/settings', $data);
    }

}