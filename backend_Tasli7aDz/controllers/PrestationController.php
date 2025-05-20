<?php
require_once __DIR__ . '/../models/Prestation.php';

class PrestationController {
    private $pdo;
    private $prestationModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->prestationModel = new Prestation($pdo);
    }

    // Ajouter une prestation (Admin uniquement)
    public function ajouter($nom, $description, $categorie) {
        return $this->prestationModel->ajouterPrestation(
            $nom, $description, $categorie
        );
    }

    // Modifier une prestation (Admin uniquement)
    public function modifier($id, $nom, $description, $categorie) {
        return $this->prestationModel->modifier($id, $nom, $description, $categorie);
    }

    // Supprimer une prestation (Admin uniquement)
    public function supprimer($id) {
        return $this->prestationModel->supprimer($id);
    }

    // Obtenir une prestation par son ID
    public function getParId($id) {
        return $this->prestationModel->getParId($id);
    }

    // Obtenir toutes les prestations
    public function getToutes() {
        return $this->prestationModel->getToutes();
    }
}