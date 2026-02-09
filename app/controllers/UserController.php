<?php
// controllers/UserController.php
class UserController {

    private $userModel;

    public function __construct($db) {
        $this->userModel = new User_model($db);
    }

    public function register($postData) {
        // Validation à faire ici ou dans un helper/form
        $data = [
            'name'       => $postData['name'],
            'email'      => $postData['email'],
            'status'     => 'active',
            'phone'      => $postData['phone'] ?? null,
            'join_date'  => date('Y-m-d'),
            'pwd'        => password_hash($postData['password'], PASSWORD_DEFAULT),
            'role'       => 'user'
        ];

        if ($this->userModel->create($data)) {
            // redirect ou json response
            return ['success' => true, 'message' => 'Inscription réussie'];
        }
        return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
    }

    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);
        if ($user && password_verify($password, $user['pwd'])) {
            $this->userModel->updateLastActive($user['id_user']);
            return $user; // ou session_start() + $_SESSION['user'] = $user;
        }
        return false;
    }

    // profil, update, etc.
}