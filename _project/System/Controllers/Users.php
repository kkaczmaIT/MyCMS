<?php
    namespace System\Controllers;

use System\MainController;


    class Users extends MainController
    {
        
        public function __construct()
        {
            $this->userModel = $this->model('User', ['users_:']);
        }

        public function index($ID = 'all')
        {
            $this->users($ID);
        }


        /**
         * Register new user. Add new user to database.
         * Require user model
         * Permission is constant. Do not use permission in project for now.
         * Receive POST request with json
         * @return void
         */
        public function registerUser()
        {
            header('Access-Control-Allow-Origin: *');
             //header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: POST');
            header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
            $data = [
                'login' => '',
                'password' => '',
                'confirm_password' => ''
            ];

            $dataFeedback = [
                'message' => '',
                'login_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $dataJson = file_get_contents("php://input");
                $dataJson = json_decode($dataJson);
                if(isset($dataJson->data->login))
                {
                    $newLogin = trim(htmlspecialchars($dataJson->data->login));
                    if(isset($dataJson->data->password))
                    {
                        $newPassword = trim(htmlspecialchars($dataJson->data->password));
                        if(isset($dataJson->data->password_confirm))
                        {
                            $newConfirmPassword = trim(htmlspecialchars($dataJson->data->password_confirm));
                        }
                        else
                        {
                            die('Pojawi?? si?? b????d');
                        }
                    }
                    else
                    {
                        die('Pojawi?? si?? b????d');
                    }
                }
                else
                {
                    die('Pojawi?? si?? b????d');
                }
                
                
                // Init data
                $data = [
                    'login' => $newLogin,
                    'password' => $newPassword,
                    'confirm_password' => $newConfirmPassword,
                
                ];

                if(empty($data['login']))
                {
                    $dataFeedback['login_err'] = 'Prosz?? wprowad?? login u??ytkownika';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }
                else
                {
                    if($this->userModel->getUserDataByLogin($data['login']))
                    {
                        $dataFeedback['login_err'] = 'Login jest u??yty przez innego u??ytkownika';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        //$this->view('users/register', $data);
                        die();
                    }
                }

                if (empty($data['password']))
                {
                    $dataFeedback['password_err'] = 'Prosz?? wprowad?? has??o';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }
                if(strlen($data['password']) < 8)
                {
                    $dataFeedback['password_err'] = 'Has??o musi zawiera?? co najmniej osiem znak??w';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }

                if (empty($data['confirm_password']))
                {
                    $dataFeedback['confirm_password_err'] = 'Prosz?? wprowad?? has??o';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }
                if(strcmp($data['password'], $data['confirm_password']))
                {
                    $dataFeedback['confirm_password_err'] = 'Has??a si?? r????ni??';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }

                if(empty($dataFeedback['login_err'] && empty($dataFeedback['password_err']) && empty($dataFeedback['confirm_password_err'])))
                {
                    if($this->userModel->registerUser($data['login'], $data['password'], 8))
                    {
                        if($user = $this->userModel->getUserDataByLogin($data['login']))
                        {
                            $info = null;
                            $infoimg = null;
                            $infothemes = null;
                            $infowebsites = null;
                            system('mkdir ' . getenv('STORAGE_PATH') . $user['home_directory'], $info);
                            system('mkdir ' . getenv('STORAGE_PATH') . $user['home_directory'] . '\img', $infoimg);
                            system('mkdir ' . getenv('STORAGE_PATH') . $user['home_directory'] . '\themes',  $infothemes);
                            system('mkdir ' . getenv('STORAGE_PATH') . $user['home_directory'] . '\themes\modules', $infothemes);
                            system('mkdir ' . getenv('STORAGE_PATH') . $user['home_directory'] . '\websites', $infowebsites);
                            if(!$info && !$infoimg && !$infothemes && !$infowebsites)
                            {
                                infoLog(getenv('MODE'), 'User home directory created');
                            }
                            else
                            {
                                infoLog(getenv('MODE'), 'User home directory not created');
                                infoLog(getenv('MODE'), $info);
                                $dataFeedback['message'] = 'Katalog u??ytkownika nie zosta?? poprawnie stworzony. Prosz?? skontaktuj si?? z nami.';
                                http_response_code(507);
                                $dataJson = json_encode($dataFeedback);
                                echo $dataJson;
                                die();
                            }
                            infoLog(getenv('MODE'), $info);
                           
                        }
                        else
                        {
                            $dataFeedback['message'] = 'Katalog u??ytkownika nie zosta?? poprawnie stworzony. Prosz?? skontaktuj si?? z nami.';
                            http_response_code(507);
                            $dataJson = json_encode($dataFeedback);
                            echo $dataJson;
                            die();
                        }
                        //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                        $dataFeedback['message'] = 'Zosta??e?? poprawnie zarejestrowany. Przejd?? do strony logowania aby si?? zalogowa??';
                        http_response_code(201);
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        //$this->view('users/register', $dataErr);
                    }
                    else
                    {
                        die('Pojawi?? si?? b????d');
                    }
                }
            }
            else
            {
                $this->view('users/register', $dataFeedback);
            }
        }

        public function editUser()
        {
            header('Access-Control-Allow-Origin: *');
             //header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: POST');
            header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
            $data = [
                'password' => '',
                'confirm_password' => ''
            ];

            $dataFeedback = [
                'message' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'status' => 'pending'
            ];
  
            if($_SERVER['REQUEST_METHOD'] == 'PUT' && isLogged())
            {
                $dataJson = file_get_contents("php://input");
                $dataJson = json_decode($dataJson);
                    if(isset($dataJson->data->password))
                    {
                        $newPassword = trim(htmlspecialchars($dataJson->data->password));
                        if(isset($dataJson->data->password_confirm))
                        {
                            $newConfirmPassword = trim(htmlspecialchars($dataJson->data->password_confirm));
                        }
                        else
                        {              
                            $dataFeedback['message'] = 'Pojawi?? si?? b????d. odczyt has??a';
                            $dataFeedback['status'] = 'failed';
                            $dataJson = json_encode($dataFeedback);
                            echo $dataJson;
                            die('Pojawi?? si?? b????d');
                        }
                    }
                    else
                    {
                        $dataFeedback['message'] = 'Pojawi?? si?? b????d. odczyt ponownego wprowadzenia has??a';
                        $dataFeedback['status'] = 'failed';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        die('Pojawi?? si?? b????d');
                    }
                
                
                // Init data
                $data = [
                    'password' => $newPassword,
                    'confirm_password' => $newConfirmPassword,
                ];

                if (empty($data['password']))
                {
                    $dataFeedback['password_err'] = 'Prosz?? wprowad?? has??o';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }
                if(strlen($data['password']) < 8)
                {
                    $dataFeedback['password_err'] = 'Has??o musi zawiera?? co najmniej osiem znak??w';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }

                if (empty($data['confirm_password']))
                {
                    $dataFeedback['confirm_password_err'] = 'Prosz?? wprowad?? has??o';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }
                if(strcmp($data['password'], $data['confirm_password']))
                {
                    $dataFeedback['confirm_password_err'] = 'Has??a si?? r????ni??';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }

                if(empty($dataFeedback['password_err']) && empty($dataFeedback['confirm_password_err']))
                {

                    if($this->userModel->updateUser($_SESSION['user_login'], $data['password']))
                    {
                        
                        infoLog(getenv('MODE'), 'User\'s data changed correctly');
                        //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                        $dataFeedback['message'] = 'Dane u??ytkownika zosta??y poprawnie zmodyfikowane';
                        $dataFeedback['status'] = 'success';
                        http_response_code(201);
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        //$this->view('users/register', $dataErr);
                    }
                    else
                    {
                        $dataFeedback['message'] = 'Pojawi?? si?? b????d';
                        $dataFeedback['status'] = 'failed';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        die();
                    }
            }
            else
            {
                $dataFeedback['message'] = 'Pojawi?? si?? b????d';
                $dataFeedback['status'] = 'failed';
                $dataJson = json_encode($dataFeedback);
                echo $dataJson;
                die();
                //$this->view('users/login', $dataFeedback);
            }
        }
        else
        {
            die('Aby kontynuowa?? musisz by?? zalogowany');
            //$this->view('users/login', $dataFeedback);
        }
    }

    public function deleteUser()
    {
        header('Access-Control-Allow-Origin: *');
         //header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
        $data = [
            'user_login' => ''
        ];

        $dataFeedback = [
            'message' => '',
            'status' => 'pending'
        ];


        if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isLogged())
        {
            $dataJson = file_get_contents("php://input");
            $dataJson = json_decode($dataJson);
                
            
            // Init data
            $data = [
                'user_login' => $_SESSION['user_login']
            ];

            if($this->userModel->changeStatusUser($data['user_login'], 0))
            {
                    
                    infoLog(getenv('MODE'), 'User\'s account closed');
                    //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                    $dataFeedback['message'] = 'Konto u??ytkownika zosta??o zablokowane. Prosz?? si?? wylogowa??.';
                    $dataFeedback['status'] = 'success';
                    $this->userModel->forceUpdateSQLDatabase();
                    http_response_code(200);
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $dataErr);
                    die();
            }
            else
            {
                    $dataFeedback['message'] = 'Pojawi?? si?? b????d';
                    $dataFeedback['status'] = 'failed';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    die();
            }
            $dataJson = json_encode($dataFeedback);
            echo $dataJson;
        }
        else
        {
            die('Aby kontynuowa?? musisz by?? zalogowany');
            //$this->view('users/login', $dataFeedback);
        }

}

    public function settings()
    {
        $this->view('users/settings');
    }

        public function userslist($ID = null)
        {
            $this->view('users/index', ['ID' => $ID]);
        }

        public function users($ID = 'all')
        {
            $userArr = array();
            $userArr['status'] = 'pending';
            if(isLogged())
            {
                if($_SERVER['REQUEST_METHOD'] == 'GET')
                {
                    if($userArr['data'] = $this->userModel->getUsersLogin($ID))
                    {
                        $userArr['status'] = 'success';
                        echo json_encode($userArr);
                    }
                    else
                    {
                        $userArr['status'] = 'failed';
                        $userArr['data'] = ' U??ytkownik nie znaleziony';
                        echo json_encode($userArr);
                    }
                }
            }
            else
            {
                $data = [
                    'login_err' => '',
                    'password_err' => ''
                ];
                $userArr['status'] = 'failed';
                $userArr['data'] = 'Musisz si?? zalogowa??';
                $this->view('users/login', $data);
            }
        }

        public function login()
        {
            header('Access-Control-Allow-Origin: *');
             //header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: POST');
            header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
            $data = [
                'login' => '',
                'password' => '',
            ];

            $dataFeedback = [
                'message' => '',
                'login_err' => '',
                'password_err' => '',
            ];
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $dataJson = file_get_contents("php://input");
                $dataJson = json_decode($dataJson);
                if(isset($dataJson->data->login))
                {
                    $login = trim(htmlspecialchars($dataJson->data->login));
                    if(isset($dataJson->data->password))
                    {
                        $password = trim(htmlspecialchars($dataJson->data->password));
                    }
                    else
                    {
                        die('Pojawi?? si?? b????d');
                    }
                }
                else
                {
                    die('Pojawi?? si?? b????d');
                }
                
                
                // Init data
                $data = [
                    'login' => $login,
                    'password' => $password
                ];

                if(empty($data['login']))
                {
                    $dataFeedback['login_err'] = 'Prosz?? wprowad?? login u??ytkownika';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }

                if (empty($data['password']))
                {
                    $dataFeedback['password_err'] = 'Prosz?? wprowad?? has??o';
                    $dataJson = json_encode($dataFeedback);
                    echo $dataJson;
                    //$this->view('users/register', $data);
                    die();
                }

                if(empty($dataFeedback['login_err'] && empty($dataFeedback['password_err'])))
                {
                    if($user = $this->userModel->login($data['login'], $data['password']))
                    {
                        $this->createUserSession($user['ID'], $user['loginU'], $user['is_active'], $user['home_directory'], $user['permission']);
                        //flash('register_success', 'User registered. Go to Login page', 'bg-success');
                        http_response_code(200);
                        $dataFeedback['message'] = 'Pomy??lnie zalogowano do systemu. Trwa przekierowanie do panelu.';
                        $dataFeedback['type'] = 'success';
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        //$this->view('home');
                    }
                    else
                    {
                        $dataFeedback['message'] = 'Nieprawid??owy login lub has??o. Konto mo??e by?? r??wnie?? zablokowane';
                        http_response_code(200);
                        $dataJson = json_encode($dataFeedback);
                        echo $dataJson;
                        die();
                    }
                }
            }
            else
            {
                $this->view('users/login', $dataFeedback);
            }
        }
        

        /**
         * Create session status of user
         *
         * @param [type] $id - ID of user
         * @param [type] $login - login user
         * @param [type] $is_active - status user
         * @param [type] $home_direcotry - home directory user
         * @param [type] $permission = user's permission
         * @return void
         */
        private function createUserSession($ID, $login, $is_active, $home_directory, $permission)
        {
            $_SESSION['user_id'] = $ID;
            $_SESSION['user_login'] = $login;
            $_SESSION['is_active'] = $is_active;
            $_SESSION['home_directory'] = $home_directory;
            $_SESSION['permission'] = $permission;
        }

        /**
         * remove users data and destroy session
         *
         * @return void
         */
        private function destroyUserSession()
        {
            unset($_SESSION['user_id']);
            unset($_SESSION['user_login']);
            unset($_SESSION['is_active']);
            unset($_SESSION['home_directory']);
            unset($_SESSION['permission']);
            session_destroy();
        }

        public function logout()
        {
            $this->userModel->forceUpdateSQLDatabase();
            $this->destroyUserSession();
            $this->view('users/logout');
        }
    }
?>