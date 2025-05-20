<?php
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/EvaluationController.php';

session_start();

$db = new Database();
$pdo = $db->getConnection();
$controller = new EvaluationController($pdo);

$data = json_decode(file_get_contents("php://input"), true) ?? [];
$action = $_GET['action'] ?? null;

try {
    switch ($_SERVER['REQUEST_METHOD']) {

        // -------- POST: ajouter une évaluation --------
        case 'POST':
            if ($action === 'ajouter') {
                $champs = ['note', 'commentaire', 'id_client', 'id_prestataire'];
                foreach ($champs as $champ) {
                    if (empty($data[$champ])) {
                        throw new Exception("Champ manquant : $champ");
                    }
                }

                $id = $controller->ajouter(
                    $data['note'],
                    $data['commentaire'],
                    $data['id_client'],
                    $data['id_prestataire']
                );

                echo json_encode([
                    "message" => "Évaluation ajoutée",
                    "id_evaluation" => $id
                ]);

            } else {
                throw new Exception("Action POST invalide");
            }
            break;

        // -------- PUT: modifier une évaluation --------
        case 'PUT':
            if ($action === 'modifier') {
                if (empty($data['id']) || empty($data['note']) || empty($data['commentaire'])) {
                    throw new Exception("Champs requis pour la modification");
                }

                $idEval = $data['id'];
                $eval = $controller->getParId($idEval);
                if (!$eval) throw new Exception("Évaluation introuvable.");

                $idActuel = $_SESSION['utilisateur']['id'] ?? null;
                $roleActuel = $_SESSION['utilisateur']['role'] ?? null;

                if ($roleActuel !== 'Administrateur' && $eval['id_client'] !== $idActuel) {
                    http_response_code(403);
                    echo json_encode(["message" => "Vous ne pouvez pas modifier cette évaluation."]);
                    exit;
                }

                $ok = $controller->modifier($idEval, $data['note'], $data['commentaire']);
                echo json_encode([
                    "message" => $ok ? "Évaluation modifiée" : "Aucune modification effectuée"
                ]);

            } else {
                throw new Exception("Action PUT invalide");
            }
            break;

        // -------- GET: get / par prestataire / moyenne --------
        case 'GET':
            if ($action === 'get') {
                if (empty($_GET['id'])) {
                    throw new Exception("ID requis");
                }

                $eval = $controller->getParId($_GET['id']);
                echo json_encode($eval ?: ["message" => "Évaluation non trouvée"]);

            } elseif ($action === 'get_par_prestataire') {
                if (empty($_GET['id_prestataire'])) {
                    throw new Exception("ID prestataire requis");
                }

                echo json_encode($controller->getParPrestataire($_GET['id_prestataire']));

            } elseif ($action === 'get_moyenne_prestataire') {
                if (empty($_GET['id_prestataire'])) {
                    throw new Exception("ID prestataire requis");
                }

                $moyenne = $controller->getMoyenneParPrestataire($_GET['id_prestataire']);
                echo json_encode(["moyenne" => $moyenne]);

            } else {
                throw new Exception("Action GET invalide");
            }
            break;

        // -------- DELETE: supprimer une évaluation --------
        case 'DELETE':
            if ($action === 'supprimer') {
                if (empty($_GET['id'])) {
                    throw new Exception("ID requis pour suppression");
                }

                $idEval = (int) $_GET['id'];
                $eval = $controller->getParId($idEval);
                if (!$eval) throw new Exception("Évaluation introuvable.");

                $idActuel = $_SESSION['utilisateur']['id'] ?? null;
                $roleActuel = $_SESSION['utilisateur']['role'] ?? null;

                if ($roleActuel !== 'Administrateur' && $eval['id_client'] !== $idActuel) {
                    http_response_code(403);
                    echo json_encode(["message" => "Vous ne pouvez pas supprimer cette évaluation."]);
                    exit;
                }

                $ok = $controller->supprimer($idEval);
                echo json_encode(["message" => $ok ? "Évaluation supprimée" : "Échec de la suppression"]);

            } else {
                throw new Exception("Action DELETE invalide");
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["message" => "Méthode non autorisée"]);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["message" => $e->getMessage()]);
}