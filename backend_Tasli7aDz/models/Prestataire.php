<?php
class Prestataire {
    private $pdo;
    private $table = 'prestataire';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ajouter un prestataire avec id_prestation
    public function ajouter($id_utilisateur, $adresse, $id_prestation, $disponibilite = 'Disponible') {
        $sql = "INSERT INTO $this->table (id_utilisateur, adresse, id_prestation, disponibilite)
                VALUES (:id_utilisateur, :adresse, :id_prestation, :disponibilite)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_utilisateur' => $id_utilisateur,
            ':adresse' => $adresse,
            ':id_prestation' => $id_prestation,
            ':disponibilite' => $disponibilite
        ]);
        return true;
    }

    // Récupérer un prestataire par ID utilisateur
    public function getParId($id_utilisateur) {
        $sql = "SELECT p.*, u.nom, u.prenom, u.email, u.telephone, pr.nom AS nom_prestation
                FROM $this->table p
                JOIN utilisateur u ON p.id_utilisateur = u.id
                JOIN prestation pr ON p.id_prestation = pr.id
                WHERE p.id_utilisateur = :id_utilisateur";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les prestataires
    public function getTous() {
        $sql = "SELECT p.*, u.nom, u.prenom, u.email, u.telephone, pr.nom AS nom_prestation
                FROM $this->table p
                JOIN utilisateur u ON p.id_utilisateur = u.id
                JOIN prestation pr ON p.id_prestation = pr.id
                ORDER BY p.id_utilisateur DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Modifier les infos d’un prestataire
    public function modifier($id_utilisateur, $adresse, $id_prestation, $disponibilite) {
        $sql = "UPDATE $this->table
                SET adresse = :adresse,
                    id_prestation = :id_prestation,
                    disponibilite = :disponibilite
                WHERE id_utilisateur = :id_utilisateur";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_utilisateur' => $id_utilisateur,
            ':adresse' => $adresse,
            ':id_prestation' => $id_prestation,
            ':disponibilite' => $disponibilite
        ]);
    }

    // Supprimer un prestataire
    public function supprimer($id_utilisateur) {
        $sql = "DELETE FROM $this->table WHERE id_utilisateur = :id_utilisateur";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_utilisateur' => $id_utilisateur]);
    }
}