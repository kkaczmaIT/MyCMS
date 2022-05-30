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
            $_SESSION['user_id'] = '1';
            $_SESSION['user_login'] = 'Test';
            $this->users($ID);
        }


        /**
         * Register new user. Add new user to database.
         * Require user model
         * Permission is constant. Do not use permission in project for now.
         * @return void
         */
        public function registerUser()
        {
            $data = [
                'login' => '',
                'password' => '',
                'confirm_password' => '',
                'login_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                // Init data
                $data = [
                    'login' => trim($_POST['login']),
                    'password' => trim($_POST['password']),
                    'confirm_password' => trim($_POST['confirm_password']),
                    'login_err' => '',
                    'password_err' => '',
                    'confirm_password_err' => ''
                ];

                if(empty($data['login']))
                {
                    $data['login_err'] = 'Please enter login';
                }
                else
                {
                    if($this->userModel->getUserDataByLogin($data['login']))
                    {
                        $data['login_err'] = 'Login is taken';
                    }
                }

                if (empty($_POST['password']))
                {
                    $data['password_err'] = 'Please enter password';
                }
                elseif(strlen($_POST['password']) < 8)
                {
                    $data['password_err'] = 'Password must contain at least 8 characters';
                }

                if (empty($_POST['confirm_password']))
                {
                    $data['confirm_password_err'] = 'Please enter password';
                }
                elseif(strcmp($_POST['password'], $_POST['confirm_password']))
                {
                    $data['confirm_password_err'] = 'Passwords are not the same';
                }

                if(empty($data['login_err'] && empty($data['password_err']) && empty($data['confirm_password_err'])))
                {
                    if($this->userModel->registerUser($_POST['login'], $_POST['password'], 8))
                    {
                        if($user = $this->userModel->getUserDataByLogin($_POST['login']))
                        {
                            $info = null;
                            if(system('mkdir ' . getenv('STORAGE_PATH') . $user['home_directory'], $info))
                            {
                                infoLog(getenv('MODE'), 'User home directory cretaed');
                            }
                            else
                            {
                                infoLog(getenv('MODE'), 'User home directory not created');
                            }
                            infoLog(getenv('MODE'), $info);
                           
                        }
                        else
                        {
                            die('User\'s home directory not created. Please contact with Us');
                        }
                        flash('register_success', 'User registered. Go to Login page');
                        $this->view('register', $data);
                    }
                    else
                    {
                        die('Something went wrong');
                    }
                }
            }
            else
            {
                $this->view('users/register', $data);
            }
        }

        public function users($ID = 'all')
        {
            $userArr = array();
            $userArr['status'] = 'pending';
            if(isLogged())
            {
                echo $ID;
                    if($userArr['data'] = $this->userModel->getUsersLogin($ID))
                    {
                        $userArr['status'] = 'success';
                        echo json_encode($userArr);
                    }
                    else
                    {
                        $userArr['status'] = 'failed';
                        $userArr['data'] = ' User not found';
                        echo json_encode($userArr);
                    }
            }
            else
            {
                $data = [
                    'login' => '',
                    'password' => '',
                    'login_err' => '',
                    'password_err' => ''
                ];
                $userArr['status'] = 'failed';
                $userArr['data'] = 'You have to register login';
                $this->view('users/login', $data);
                
            }
        }

    }
?>