<?php
class Evaluation {
    private $pdo;
    private $table = 'evaluation';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ajouter une évaluation
    public function ajouter($note, $commentaire, $id_client, $id_prestataire) {
        $sql = "INSERT INTO $this->table (note, commentaire, id_client, id_prestataire)
                VALUES (:note, :commentaire, :id_client, :id_prestataire)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':note' => $note,
            ':commentaire' => $commentaire,
            ':id_client' => $id_client,
            ':id_prestataire' => $id_prestataire
        ]);
        return $this->pdo->lastInsertId();
    }

    // Obtenir une évaluation par ID
    public function getParId($id) {
        $sql = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtenir toutes les évaluations d’un prestataire
    public function getParPrestataire($id_prestataire) {
        $sql = "SELECT * FROM $this->table WHERE id_prestataire = :id_prestataire ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_prestataire' => $id_prestataire]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir la note moyenne d’un prestataire
    public function getMoyenneParPrestataire($id_prestataire) {
        $sql = "SELECT AVG(note) as moyenne FROM $this->table WHERE id_prestataire = :id_prestataire";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_prestataire' => $id_prestataire]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['moyenne'] ?? 0;
    }

    // Modifier une évaluation
    public function modifier($id, $note, $commentaire) {
        $sql = "UPDATE $this->table SET note = :note, commentaire = :commentaire WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':note' => $note,
            ':commentaire' => $commentaire
        ]);
    }

    // Supprimer une évaluation
    public function supprimer($id) {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
