<?php
class Message {
    private $pdo;
    private $table = 'message';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Envoyer un message
    public function envoyer($contenu, $id_expediteur, $id_destinataire) {
        $sql = "INSERT INTO $this->table (contenu, id_expediteur, id_destinataire)
                VALUES (:contenu, :id_expediteur, :id_destinataire)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':contenu' => $contenu,
            ':id_expediteur' => $id_expediteur,
            ':id_destinataire' => $id_destinataire
        ]);
        return $this->pdo->lastInsertId();
    }

    // Récupérer les messages entre deux utilisateurs
    public function getConversation($id1, $id2) {
        $sql = "SELECT * FROM $this->table
                WHERE (id_expediteur = :id1 AND id_destinataire = :id2)
                   OR (id_expediteur = :id2 AND id_destinataire = :id1)
                ORDER BY date_envoi ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id1' => $id1,
            ':id2' => $id2
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprimer un message
    public function supprimer($id) {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}