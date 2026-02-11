<?php

/**
 * UserController
 * Gestion des opérations liées aux utilisateurs
 */

namespace app\controllers;
use app\models\User;
use Flight;
class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

//sing-in fonction :    
    public function login()
    {
        $data = Flight::request()->data->getData();

        if (empty($data)) {
            Flight::json(['error' => 'No data received']);
            Flight::redirect('/');
            return;
        }

        $email = $data['Email'] ?? '';
        $pwd = $data['password'] ?? '';


        // Voir si le user existe deja
        $existingUser = $this->userModel->getByEmail($email);
        if ($existingUser) {
            // Mettre à jour la dernière activité
            $this->userModel->updateStatus($existingUser['id_user'], 'active');
            if (password_verify($pwd, $existingUser['pwd'])) {
                // Créer la session
                $_SESSION['user_id'] = $existingUser['id_user'];
                $_SESSION['user_name'] = $existingUser['name'];
                $_SESSION['user_phone'] = $existingUser['phone'];
                $_SESSION['user_email'] = $existingUser['email'];
                $_SESSION['user_role'] = $existingUser['role'];
                $_SESSION['login_time'] = time();
                Flight::redirect('/profile');
                return;
            }
            Flight::redirect('/');
            return;

        }

        Flight::redirect('/');
        return;
    }

//sign-up fonction :
    public function register()
    {
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            Flight::json(['error' => 'No data received']);
            Flight::redirect('/sign-up');
            return;
        }
        $email = $data['email'] ?? '';
        $pwd = $data['password'] ?? '';
        $name = $data['name'] ?? '';
        $phone = $data['phone'] ?? '';

        if ($this->userModel->emailExists($email)) {
            Flight::json(['error' => 'Email already exists']);
            Flight::redirect('/sign-up');
            return;
        } else {
            $userId = $this->userModel->signUp(['name' => $name, 'email' => $email, 'phone' => $phone, 'password' => $pwd]);
            if ($userId) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_phone'] = $phone;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';
                $_SESSION['login_time'] = time();
                Flight::redirect('/profile');
                return;
            } else {
                Flight::json(['error' => 'Failed to create user']);
                Flight::redirect('/sign-up');
                return;
            }

        }
    }

//get the count of users :
    public function getCountUser()
    {
        return $this->userModel->getCountUser();
    } 

//get the count of Admins :
    public function getCountAdmin()
    {
        return $this->userModel->getCountAdmin();
    } 


    public function logout($id)
    {
        $result = $this->userModel->updateStatus($id, 'inactive');

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        @session_regenerate_id(true);
        return $result;
    }

    public function checkEmailExists($email)
    {
        return $this->userModel->emailExists($email);
    }

    public function getAll()
    {
        $args = func_get_args();
        $options = [];
        if (isset($args[0]) && is_array($args[0])) {
            $options = $args[0];
        }
        return $this->userModel->getAll($options);
    }

    public function getById($id_user)
    {
        return $this->userModel->getById($id_user);
    }
}
