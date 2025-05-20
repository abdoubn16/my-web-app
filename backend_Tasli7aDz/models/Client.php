<?php
class Client {
    private $pdo;
    private $table = 'client';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ajouter un client
    public function ajouter($id_utilisateur) {
        $sql = "INSERT INTO $this->table (id_utilisateur) VALUES (:id_utilisateur)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);
        return true;
    }

    // Récupérer un client par ID utilisateur
    public function getParId($id_utilisateur) {
        $sql = "SELECT c.*, u.nom, u.prenom, u.email, u.telephone, u.adresse
                FROM $this->table c
                JOIN utilisateur u ON c.id_utilisateur = u.id
                WHERE c.id_utilisateur = :id_utilisateur";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les clients
    public function getTous() {
        $sql = "SELECT c.*, u.nom, u.prenom, u.email, u.telephone, u.adresse
                FROM $this->table c
                JOIN utilisateur u ON c.id_utilisateur = u.id
                ORDER BY c.id_utilisateur DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Modifier les infos d'un client
    public function modifier($id_utilisateur, $telephone, $adresse) {
        $sql = "UPDATE utilisateur
                SET telephone = :telephone, adresse = :adresse
                WHERE id = :id_utilisateur";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_utilisateur' => $id_utilisateur,
            ':telephone' => $telephone,
            ':adresse' => $adresse
        ]);
    }

    // Supprimer un client
    public function supprimer($id_utilisateur) {
        // Suppression logique : supprimer de la table client uniquement
        $sql = "DELETE FROM $this->table WHERE id_utilisateur = :id_utilisateur";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_utilisateur' => $id_utilisateur]);
    }
}