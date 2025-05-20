<?php
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Prestataire.php';

class UtilisateurController {
    private $pdo;
    private $utilisateurModel;
    private $clientModel;
    private $prestataireModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->utilisateurModel = new Utilisateur($pdo);
        $this->clientModel = new Client($pdo);
        $this->prestataireModel = new Prestataire($pdo);
    }

    // 1. Inscription
    public function inscrire($nom, $prenom, $email, $mot_de_passe, $telephone, $adresse, $role, $id_prestation = null) {
        // Interdire l'inscription en tant qu'Administrateur
        if (!in_array($role, ['Client', 'Prestataire'])) {
            throw new Exception("Rôle non autorisé pour l'inscription.");
        }

        if ($this->utilisateurModel->existeEmail($email)) {
            throw new Exception("Adresse email déjà utilisée.");
        }

        // Hasher le mot de passe
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Ajouter l'utilisateur
        $id_utilisateur = $this->utilisateurModel->ajouter($nom, $prenom, $email, $mot_de_passe_hash, $telephone, $adresse, $role);

        if ($role === 'Client') {
            $this->clientModel->ajouter($id_utilisateur);
        } elseif ($role === 'Prestataire') {
            if (!$id_prestation) {
                throw new Exception("Veuillez sélectionner une prestation.");
            }
            $this->prestataireModel->ajouter($id_utilisateur, $adresse, $id_prestation);
        }

        return $id_utilisateur;
    }

    // 2. Connexion
    public function login($email, $mot_de_passe) {
        $utilisateur = $this->utilisateurModel->getParEmail($email);

        if (!$utilisateur) {
            throw new Exception("Email introuvable.");
        }

        if (!password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            throw new Exception("Mot de passe incorrect.");
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['utilisateur'] = [
            'id' => $utilisateur['id'],
            'nom' => $utilisateur['nom'],
            'prenom' => $utilisateur['prenom'],
            'role' => $utilisateur['role']
        ];

        unset($utilisateur['mot_de_passe']);
        return $utilisateur;
    }

    // 3. Déconnexion
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return true;
    }

    // 4. Modifier les infos d’un utilisateur
    public function modifier($id, $nom, $prenom, $telephone, $adresse) {
        return $this->utilisateurModel->modifier($id, $nom, $prenom, $telephone, $adresse);
    }

    // 5. Supprimer un utilisateur
    public function supprimer($id) {
        return $this->utilisateurModel->supprimer($id);
    }

    // 6. Changer mot de passe
    public function changerMotDePasse($id, $ancien, $nouveau) {
        $utilisateur = $this->utilisateurModel->getParId($id);
        if (!$utilisateur) {
            throw new Exception("Utilisateur introuvable.");
        }

        if (!password_verify($ancien, $utilisateur['mot_de_passe'])) {
            throw new Exception("Ancien mot de passe incorrect.");
        }

        $nouveauHash = password_hash($nouveau, PASSWORD_DEFAULT);
        $sql = "UPDATE utilisateur SET mot_de_passe = :mdp WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':mdp' => $nouveauHash, ':id' => $id]);
    }

    // 7. Récupérer tous les utilisateurs (pour admin)
    public function getTous() {
        return $this->utilisateurModel->getTous();
    }

    // 8. Récupérer un utilisateur par ID
    public function getParId($id) {
        return $this->utilisateurModel->getParId($id);
    }
}