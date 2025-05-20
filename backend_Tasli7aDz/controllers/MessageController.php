<?php
require_once __DIR__ . '/../models/Message.php';

class MessageController {
    private $pdo;
    private $messageModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->messageModel = new Message($pdo);
    }

    // Envoyer un message
    public function envoyer($contenu, $id_expediteur, $id_destinataire) {
        return $this->messageModel->envoyer($contenu, $id_expediteur, $id_destinataire);
    }

    // Obtenir la conversation entre deux utilisateurs
    public function getConversation($id1, $id2) {
        return $this->messageModel->getConversation($id1, $id2);
    }

    // Supprimer un message
    public function supprimer($id) {
        return $this->messageModel->supprimer($id);
    }
}