<?php
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/ReservationController.php';

session_start();

$db = new Database();
$pdo = $db->getConnection();
$controller = new ReservationController($pdo);

$data = json_decode(file_get_contents("php://input"), true) ?? [];
$action = $_GET['action'] ?? null;

try {
    switch ($_SERVER['REQUEST_METHOD']) {

        // -------- POST: ajouter une réservation --------
        case 'POST':
            if ($action === 'ajouter') {
                $champs = ['date', 'statut', 'id_client', 'id_prestataire', 'id_prestation'];
                foreach ($champs as $champ) {
                    if (empty($data[$champ])) {
                        throw new Exception("Champ manquant : $champ");
                    }
                }

                $id = $controller->ajouter(
                    $data['date'],
                    $data['statut'],
                    $data['id_client'],
                    $data['id_prestataire'],
                    $data['id_prestation']
                );

                echo json_encode([
                    "message" => "Réservation créée",
                    "id_reservation" => $id
                ]);

            } else {
                throw new Exception("Action POST invalide");
            }
            break;

        // -------- PUT: modifier le statut --------
        case 'PUT':
            if ($action === 'modifier_statut') {
                if (empty($data['id']) || empty($data['statut'])) {
                    throw new Exception("Champs requis pour modifier le statut");
                }

                $ok = $controller->modifierStatut($data['id'], $data['statut']);

                echo json_encode([
                    "message" => $ok ? "Statut mis à jour" : "Échec de la mise à jour"
                ]);

            } else {
                throw new Exception("Action PUT invalide");
            }
            break;

        // -------- GET: get / get_all / par client / par prestataire --------
        case 'GET':
            if ($action === 'get') {
                if (empty($_GET['id'])) {
                    throw new Exception("ID requis");
                }

                $res = $controller->getParId($_GET['id']);
                echo json_encode($res ?: ["message" => "Réservation non trouvée"]);

            } elseif ($action === 'get_all') {
                echo json_encode($controller->getToutes());

            } elseif ($action === 'get_par_client') {
                if (empty($_GET['id_client'])) {
                    throw new Exception("ID client requis");
                }

                echo json_encode($controller->getParClient($_GET['id_client']));

            } elseif ($action === 'get_par_prestataire') {
                if (empty($_GET['id_prestataire'])) {
                    throw new Exception("ID prestataire requis");
                }

                echo json_encode($controller->getParPrestataire($_GET['id_prestataire']));

            } else {
                throw new Exception("Action GET invalide");
            }
            break;

        // -------- DELETE: supprimer une réservation (avec vérification) --------
        case 'DELETE':
            if ($action === 'supprimer') {
                if (empty($_GET['id'])) {
                    throw new Exception("ID requis pour suppression");
                }

                $idReservation = (int) $_GET['id'];
                $reservation = $controller->getParId($idReservation);
                if (!$reservation) {
                    throw new Exception("Réservation introuvable.");
                }

                $idActuel = $_SESSION['utilisateur']['id'] ?? null;
                $roleActuel = $_SESSION['utilisateur']['role'] ?? null;

                if (!$idActuel) {
                    throw new Exception("Utilisateur non connecté.");
                }

                // Autoriser uniquement le client ou l'admin
                if ($roleActuel !== 'Administrateur' && $reservation['id_client'] !== $idActuel) {
                    http_response_code(403);
                    echo json_encode(["message" => "Vous ne pouvez pas supprimer cette réservation."]);
                    exit;
                }

                $ok = $controller->supprimer($idReservation);
                echo json_encode(["message" => $ok ? "Réservation supprimée" : "Échec de la suppression"]);

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