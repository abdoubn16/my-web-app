<?php
require_once __DIR__ . '/../models/Notification.php';

class NotificationController {
    private $pdo;
    private $notificationModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->notificationModel = new Notification($pdo);
    }

    // Créer une notification
    public function creer($type, $contenu, $id_utilisateur) {
        return $this->notificationModel->creer($type, $contenu, $id_utilisateur);
    }

    // Obtenir toutes les notifications d’un utilisateur
    public function getParUtilisateur($id_utilisateur) {
        return $this->notificationModel->getParUtilisateur($id_utilisateur);
    }

    // Obtenir uniquement les notifications non lues
    public function getNonLues($id_utilisateur) {
        return $this->notificationModel->getNonLues($id_utilisateur);
    }

    // Marquer une notification comme lue
    public function marquerCommeLue($id) {
        return $this->notificationModel->marquerCommeLue($id);
    }

    // Supprimer une notification
    public function supprimer($id) {
        return $this->notificationModel->supprimer($id);
    }
}