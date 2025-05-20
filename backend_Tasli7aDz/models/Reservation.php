<?php
class Reservation {
    private $pdo;
    private $table = 'reservation';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ajouter une réservation
    public function ajouter($date, $statut, $id_client, $id_prestataire, $id_prestation) {
        $sql = "INSERT INTO $this->table (date, statut, id_client, id_prestataire, id_prestation)
                VALUES (:date, :statut, :id_client, :id_prestataire, :id_prestation)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':date' => $date,
            ':statut' => $statut,
            ':id_client' => $id_client,
            ':id_prestataire' => $id_prestataire,
            ':id_prestation' => $id_prestation
        ]);
        return $this->pdo->lastInsertId();
    }

    // Obtenir une réservation par ID
    public function getParId($id) {
        $sql = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtenir toutes les réservations
    public function getToutes() {
        $sql = "SELECT * FROM $this->table ORDER BY date DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les réservations d’un client
    public function getParClient($id_client) {
        $sql = "SELECT * FROM $this->table WHERE id_client = :id_client ORDER BY date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_client' => $id_client]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les réservations d’un prestataire
    public function getParPrestataire($id_prestataire) {
        $sql = "SELECT * FROM $this->table WHERE id_prestataire = :id_prestataire ORDER BY date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_prestataire' => $id_prestataire]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Modifier le statut d’une réservation
    public function modifierStatut($id, $statut) {
        $sql = "UPDATE $this->table SET statut = :statut WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':statut' => $statut]);
    }

    // Supprimer une réservation
    public function supprimer($id) {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}