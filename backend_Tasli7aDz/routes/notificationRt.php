<?php
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/NotificationController.php';

session_start();

$db = new Database();
$pdo = $db->getConnection();
$controller = new NotificationController($pdo);

$data = json_decode(file_get_contents("php://input"), true) ?? [];
$action = $_GET['action'] ?? null;

try {
    switch ($_SERVER['REQUEST_METHOD']) {

        // -------- POST: créer une notification --------
        case 'POST':
            if ($action === 'creer') {
                $champs = ['type', 'contenu', 'id_utilisateur'];
                foreach ($champs as $champ) {
                    if (empty($data[$champ])) {
                        throw new Exception("Champ manquant : $champ");
                    }
                }

                $id = $controller->creer(
                    $data['type'],
                    $data['contenu'],
                    $data['id_utilisateur']
                );

                echo json_encode([
                    "message" => "Notification créée",
                    "id_notification" => $id
                ]);

            } else {
                throw new Exception("Action POST invalide");
            }
            break;

        // -------- PUT: marquer comme lue (propriétaire uniquement) --------
        case 'PUT':
            if ($action === 'marquer_lue') {
                if (empty($data['id'])) {
                    throw new Exception("ID notification requis");
                }

                $idNotification = (int) $data['id'];
                $idActuel = $_SESSION['utilisateur']['id'] ?? null;

                $toutes = $controller->getParUtilisateur($idActuel);
                $trouve = false;
                foreach ($toutes as $notif) {
                    if ($notif['id'] == $idNotification) {
                        $trouve = true;
                        break;
                    }
                }

                if (!$trouve) {
                    http_response_code(403);
                    echo json_encode(["message" => "Vous ne pouvez pas modifier cette notification."]);
                    exit;
                }

                $ok = $controller->marquerCommeLue($idNotification);
                echo json_encode([
                    "message" => $ok ? "Notification marquée comme lue" : "Échec de la mise à jour"
                ]);

            } else {
                throw new Exception("Action PUT invalide");
            }
            break;

        // -------- GET: get notifications --------
        case 'GET':
            if ($action === 'get_par_utilisateur') {
                if (empty($_GET['id_utilisateur'])) {
                    throw new Exception("ID utilisateur requis");
                }

                $idDemande = (int) $_GET['id_utilisateur'];
                $idActuel = $_SESSION['utilisateur']['id'] ?? null;
                $roleActuel = $_SESSION['utilisateur']['role'] ?? null;

                if ($idActuel !== $idDemande && $roleActuel !== 'Administrateur') {
                    http_response_code(403);
                    echo json_encode(["message" => "Accès refusé à ces notifications."]);
                    exit;
                }

                echo json_encode($controller->getParUtilisateur($idDemande));

            } elseif ($action === 'get_non_lues') {
                if (empty($_GET['id_utilisateur'])) {
                    throw new Exception("ID utilisateur requis");
                }

                $idDemande = (int) $_GET['id_utilisateur'];
                $idActuel = $_SESSION['utilisateur']['id'] ?? null;
                $roleActuel = $_SESSION['utilisateur']['role'] ?? null;

                if ($idActuel !== $idDemande && $roleActuel !== 'Administrateur') {
                    http_response_code(403);
                    echo json_encode(["message" => "Accès refusé à ces notifications."]);
                    exit;
                }

                echo json_encode($controller->getNonLues($idDemande));

            } else {
                throw new Exception("Action GET invalide");
            }
            break;

        // -------- DELETE: supprimer une notification (propriétaire ou admin) --------
        case 'DELETE':
            if ($action === 'supprimer') {
                if (empty($_GET['id'])) {
                    throw new Exception("ID notification requis");
                }

                $idNotification = (int) $_GET['id'];
                $idActuel = $_SESSION['utilisateur']['id'] ?? null;
                $roleActuel = $_SESSION['utilisateur']['role'] ?? null;

                $notifs = $controller->getParUtilisateur($idActuel);
                $trouve = false;
                foreach ($notifs as $notif) {
                    if ($notif['id'] == $idNotification) {
                        $trouve = true;
                        break;
                    }
                }

                if (!$trouve && $roleActuel !== 'Administrateur') {
                    http_response_code(403);
                    echo json_encode(["message" => "Vous ne pouvez pas supprimer cette notification."]);
                    exit;
                }

                $ok = $controller->supprimer($idNotification);
                echo json_encode(["message" => $ok ? "Notification supprimée" : "Échec de la suppression"]);

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