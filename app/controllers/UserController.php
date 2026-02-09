<?php

/**
 * UserController
 * Gestion des opérations liées aux utilisateurs
 */

namespace app\controllers;
use app\models\User;
use Flight;
class UserController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function login(){
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
        if($existingUser){
            // Mettre à jour la dernière activité
            $this->userModel->updateStatus($existingUser['id_user'],'active');
            if ($pwd == $existingUser['pwd']) {
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

    public function logout($id){
        $result = $this->userModel->updateStatus($id,'inactive');

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

    public function getAll(){
        return $this->userModel->getAll();
    }

}
