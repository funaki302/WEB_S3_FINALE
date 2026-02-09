<?php

/**
 * DiscussionController
 * Gestion des opérations liées aux utilisateurs
 */

namespace app\controllers;
use app\models\User;
use app\models\Discussion;
use app\models\Message;
use Flight;
class MessageController {
    private $messageModel;
    public function __construct() {
        $this->messageModel = new Message();
    }

    public function getByDiscussion($id_discussion) {
        $messages = $this->messageModel->getAll(['id_discussion' => $id_discussion]);
        return $messages;
    }
}