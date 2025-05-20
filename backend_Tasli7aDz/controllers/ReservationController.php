<?php
require_once __DIR__ . '/../models/Reservation.php';

class ReservationController {
    private $pdo;
    private $reservationModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->reservationModel = new Reservation($pdo);
    }

    // Ajouter une réservation
    public function ajouter($date, $statut, $id_client, $id_prestataire, $id_prestation) {
        return $this->reservationModel->ajouter(
            $date, $statut, $id_client, $id_prestataire, $id_prestation
        );
    }

    // Modifier le statut d'une réservation
    public function modifierStatut($id, $statut) {
        return $this->reservationModel->modifierStatut($id, $statut);
    }

    // Supprimer une réservation
    public function supprimer($id) {
        return $this->reservationModel->supprimer($id);
    }

    // Obtenir une réservation par ID
    public function getParId($id) {
        return $this->reservationModel->getParId($id);
    }

    // Obtenir toutes les réservations
    public function getToutes() {
        return $this->reservationModel->getToutes();
    }

    // Obtenir les réservations d’un client
    public function getParClient($id_client) {
        return $this->reservationModel->getParClient($id_client);
    }

    // Obtenir les réservations d’un prestataire
    public function getParPrestataire($id_prestataire) {
        return $this->reservationModel->getParPrestataire($id_prestataire);
    }
}