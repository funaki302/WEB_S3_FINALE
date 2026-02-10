<?php

/**
 * DiscussionController
 * Gestion des opérations liées aux utilisateurs
 */

namespace app\controllers;
use app\models\Message;
use Flight;
class MessageController {
    private $messageModel;
    public function __construct() {
        $this->messageModel = new Message();
    }

    public function getByDiscussion($id_discussion) {
        return $this->messageModel->getByDiscussion($id_discussion);
    }

    public function create($data) {
        return $this->messageModel->create($data);
    }
}