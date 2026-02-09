<?php

/**
 * DiscussionController
 * Gestion des opérations liées aux utilisateurs
 */

namespace app\controllers;
use app\models\User;
use app\models\Discussion;
use Flight;
class DiscussionController {
    private $userModel;
    private $discussionModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->discussionModel = new Discussion();
    }
    
    public function getDiscussions($id_user){
        return $this->discussionModel->getConversations($id_user);
    }

    public function createDiscussion($data){
        return $this->discussionModel->create($data);
    }

    public function deleteDiscussion($id_discussion){
        return $this->discussionModel->delete($id_discussion);
    }

}
