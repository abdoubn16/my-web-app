<?php
class Notification {
    private $pdo;
    private $table = 'notification';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Créer une notification
    public function creer($type, $contenu, $id_utilisateur) {
        $sql = "INSERT INTO $this->table (type, contenu, id_utilisateur)
                VALUES (:type, :contenu, :id_utilisateur)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':type' => $type,
            ':contenu' => $contenu,
            ':id_utilisateur' => $id_utilisateur
        ]);
        return $this->pdo->lastInsertId();
    }

    // Récupérer les notifications d’un utilisateur
    public function getParUtilisateur($id_utilisateur) {
        $sql = "SELECT * FROM $this->table
                WHERE id_utilisateur = :id_utilisateur
                ORDER BY date_envoi DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les notifications non lues
    public function getNonLues($id_utilisateur) {
        $sql = "SELECT * FROM $this->table
                WHERE id_utilisateur = :id_utilisateur AND est_lue = 0
                ORDER BY date_envoi DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marquer une notification comme lue
    public function marquerCommeLue($id) {
        $sql = "UPDATE $this->table SET est_lue = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Supprimer une notification
    public function supprimer($id) {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}