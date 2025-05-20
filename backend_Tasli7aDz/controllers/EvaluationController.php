<?php
require_once __DIR__ . '/../models/Evaluation.php';

class EvaluationController {
    private $pdo;
    private $evaluationModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->evaluationModel = new Evaluation($pdo);
    }

    // Ajouter une évaluation
    public function ajouter($note, $commentaire, $id_client, $id_prestataire) {
        return $this->evaluationModel->ajouter($note, $commentaire, $id_client, $id_prestataire);
    }

    // Modifier une évaluation
    public function modifier($id, $note, $commentaire) {
        return $this->evaluationModel->modifier($id, $note, $commentaire);
    }

    // Supprimer une évaluation
    public function supprimer($id) {
        return $this->evaluationModel->supprimer($id);
    }

    // Obtenir une évaluation par ID
    public function getParId($id) {
        return $this->evaluationModel->getParId($id);
    }

    // Obtenir toutes les évaluations d’un prestataire
    public function getParPrestataire($id_prestataire) {
        return $this->evaluationModel->getParPrestataire($id_prestataire);
    }

    // Obtenir la moyenne des notes d’un prestataire
    public function getMoyenneParPrestataire($id_prestataire) {
        return $this->evaluationModel->getMoyenneParPrestataire($id_prestataire);
    }
}