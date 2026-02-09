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
        
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $pwd = $data['password'] ?? '';
        $phone = $data['telephone'] ?? '';

        // Voir si le user existe deja
        $existingUser = $this->userModel->getByEmail($email);
        if($existingUser){
            // Mettre à jour la dernière activité
            $this->userModel->updateLastActive($existingUser['id_user']);
            if ($pwd == $existingUser['pwd'] && $phone == $existingUser['phone']) {
                // Créer la session
                $_SESSION['user_id'] = $existingUser['id_user'];
                $_SESSION['user_name'] = $existingUser['name'];
                $_SESSION['user_phone'] = $existingUser['phone'];
                $_SESSION['user_email'] = $existingUser['email'];
                $_SESSION['user_role'] = $existingUser['role'];
                $_SESSION['login_time'] = time();
                Flight::redirect('/home');
                return;
            } 
            Flight::redirect('/');
            return;
            
        } else {
            // Creer un nouveau user
            $newUserId = $this->userModel->create([
                'name' => $name,
                'email' => $email,
                'pwd' => $pwd,
                'phone' => $phone
            ]);
            if($newUserId){
                // Connexion automatique apres inscription
                $user = $this->userModel->getByEmail($email);
                if ($user) {
                    $_SESSION['user_id'] = $user['id_user'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['login_time'] = time();
                    Flight::redirect('/home');
                    return;
                } else {
                    Flight::redirect('/');
                    return;
                }
            } else {
                Flight::redirect('/');
                return;
            }
        }

        Flight::redirect('/');
        return;
    }

    public function logout(){
        // Détruire la session
        session_unset();
        session_destroy();
        Flight::redirect('/');
    }

    public function getAll(){
        return $this->userModel->getAll();
    }

    public function getAllJson(){
        $users = $this->userModel->getAll();
        Flight::json($users);
    }

}
