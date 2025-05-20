<?php
class Utilisateur {
    private $pdo;
    private $table = 'utilisateur';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ajouter un nouvel utilisateur (mot de passe déjà hashé depuis le controller)
    public function ajouter($nom, $prenom, $email, $mot_de_passe, $telephone, $adresse, $role) {
        $sql = "INSERT INTO $this->table (nom, prenom, email, mot_de_passe, telephone, adresse, role)
                VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :adresse, :role)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':mot_de_passe' => $mot_de_passe,  
            ':telephone' => $telephone,
            ':adresse' => $adresse,
            ':role' => $role
        ]);
        return $this->pdo->lastInsertId();
    }

    // Récupérer un utilisateur par ID
    public function getParId($id) {
        $sql = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par email
    public function getParEmail($email) {
        $sql = "SELECT * FROM $this->table WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les utilisateurs
    public function getTous() {
        $sql = "SELECT * FROM $this->table ORDER BY id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vérifier si un email existe
    public function existeEmail($email) {
        $sql = "SELECT id FROM $this->table WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Modifier un utilisateur
    public function modifier($id, $nom, $prenom, $telephone, $adresse) {
        $sql = "UPDATE $this->table
                SET nom = :nom, prenom = :prenom, telephone = :telephone, adresse = :adresse
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':telephone' => $telephone,
            ':adresse' => $adresse
        ]);
    }

    // Supprimer un utilisateur
    public function supprimer($id) {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}