<?php
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/PrestationController.php';

session_start();

$db = new Database();
$pdo = $db->getConnection();
$controller = new PrestationController($pdo);

$data = json_decode(file_get_contents("php://input"), true) ?? [];
$action = $_GET['action'] ?? null;

try {
    $roleActuel = $_SESSION['utilisateur']['role'] ?? null;

    switch ($_SERVER['REQUEST_METHOD']) {

        // -------- POST: ajouter une prestation --------
        case 'POST':
            if ($action === 'ajouter') {
                // Seul l'administrateur peut ajouter
                if ($roleActuel !== 'Administrateur') {
                    throw new Exception("Seul l'administrateur peut ajouter une prestation.");
                }

                $champs = ['nom', 'description', 'categorie'];
                foreach ($champs as $champ) {
                    if (empty($data[$champ])) {
                        throw new Exception("Champ manquant : $champ");
                    }
                }

                $id = $controller->ajouter(
                    $data['nom'],
                    $data['description'],
                    $data['categorie']
                );

                echo json_encode([
                    "message" => "Prestation ajoutée",
                    "id_prestation" => $id
                ]);

            } else {
                throw new Exception("Action POST invalide");
            }
            break;

        // -------- PUT: modifier une prestation --------
        case 'PUT':
            if ($action === 'modifier') {
                // Seul l'administrateur peut modifier
                if ($roleActuel !== 'Administrateur') {
                    throw new Exception("Seul l'administrateur peut modifier une prestation.");
                }

                if (empty($data['id']) || empty($data['nom']) || empty($data['description']) || empty($data['categorie'])) {
                    throw new Exception("Champs requis pour la modification");
                }

                $ok = $controller->modifier(
                    $data['id'],
                    $data['nom'],
                    $data['description'],
                    $data['categorie']
                );

                echo json_encode([
                    "message" => $ok ? "Prestation modifiée" : "Aucune modification effectuée"
                ]);

            } else {
                throw new Exception("Action PUT invalide");
            }
            break;

        // -------- GET: get / get_all --------
        case 'GET':
            if ($action === 'get') {
                if (empty($_GET['id'])) {
                    throw new Exception("ID requis");
                }

                $prestation = $controller->getParId($_GET['id']);
                echo json_encode($prestation ?: ["message" => "Prestation non trouvée"]);

            } elseif ($action === 'get_all') {
                $prestations = $controller->getToutes();
                echo json_encode($prestations);

            } else {
                throw new Exception("Action GET invalide");
            }
            break;

        // -------- DELETE: supprimer une prestation --------
        case 'DELETE':
            if ($action === 'supprimer') {
                // Seul l'administrateur peut supprimer
                if ($roleActuel !== 'Administrateur') {
                    throw new Exception("Seul l'administrateur peut supprimer une prestation.");
                }

                if (empty($_GET['id'])) {
                    throw new Exception("ID requis pour suppression");
                }

                $idPrestation = (int) $_GET['id'];
                $prestation = $controller->getParId($idPrestation);
                if (!$prestation) {
                    throw new Exception("Prestation introuvable.");
                }

                $ok = $controller->supprimer($idPrestation);
                echo json_encode(["message" => $ok ? "Prestation supprimée" : "Échec de la suppression"]);

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