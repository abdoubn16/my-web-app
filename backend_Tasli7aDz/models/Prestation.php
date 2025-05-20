<?php
class Prestation {
    private $pdo;
    private $table = 'prestation';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ajouter une prestation (admin uniquement)
    public function ajouterPrestation($nom, $description, $categorie) {
        $sql = "INSERT INTO $this->table (nom, description, categorie)
                VALUES (:nom, :description, :categorie)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description,
            ':categorie' => $categorie
        ]);
        return $this->pdo->lastInsertId();
    }

    // Obtenir une prestation par ID
    public function getParId($id) {
        $sql = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtenir toutes les prestations
    public function getToutes() {
        $sql = "SELECT * FROM $this->table ORDER BY id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Modifier une prestation
    public function modifier($id, $nom, $description, $categorie) {
        $sql = "UPDATE $this->table
                SET nom = :nom, description = :description, categorie = :categorie
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':description' => $description,
            ':categorie' => $categorie
        ]);
    }

    // Supprimer une prestation
    public function supprimer($id) {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}