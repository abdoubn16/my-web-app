<?php
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/MessageController.php';

session_start();

$db = new Database();
$pdo = $db->getConnection();
$controller = new MessageController($pdo);

$data = json_decode(file_get_contents("php://input"), true) ?? [];
$action = $_GET['action'] ?? null;

try {
    switch ($_SERVER['REQUEST_METHOD']) {

        // -------- POST: envoyer un message --------
        case 'POST':
            if ($action === 'envoyer') {
                $champs = ['contenu', 'id_expediteur', 'id_destinataire'];
                foreach ($champs as $champ) {
                    if (empty($data[$champ])) {
                        throw new Exception("Champ manquant : $champ");
                    }
                }

                $id = $controller->envoyer(
                    $data['contenu'],
                    $data['id_expediteur'],
                    $data['id_destinataire']
                );

                echo json_encode([
                    "message" => "Message envoyé",
                    "id_message" => $id
                ]);

            } else {
                throw new Exception("Action POST invalide");
            }
            break;

        // -------- GET: get_conversation --------
        case 'GET':
            if ($action === 'get_conversation') {
                if (empty($_GET['id1']) || empty($_GET['id2'])) {
                    throw new Exception("Deux IDs requis pour la conversation");
                }

                $conversation = $controller->getConversation($_GET['id1'], $_GET['id2']);
                echo json_encode($conversation);

            } else {
                throw new Exception("Action GET invalide");
            }
            break;

        // -------- DELETE: supprimer un message (avec protection) --------
        case 'DELETE':
            if ($action === 'supprimer') {
                if (empty($_GET['id'])) {
                    throw new Exception("ID requis pour suppression");
                }

                $idMessage = (int) $_GET['id'];
                $idActuel = $_SESSION['utilisateur']['id'] ?? null;
                $roleActuel = $_SESSION['utilisateur']['role'] ?? null;

                if (!$idActuel) {
                    throw new Exception("Utilisateur non connecté.");
                }

                // Charger message pour vérifier expéditeur
                $conversation = $controller->getConversation($idActuel, $idActuel); // astuce pour utiliser le model
                $trouve = false;
                foreach ($conversation as $msg) {
                    if ($msg['id'] == $idMessage && $msg['id_expediteur'] == $idActuel) {
                        $trouve = true;
                        break;
                    }
                }

                // Autoriser seulement expéditeur ou admin
                if (!$trouve && $roleActuel !== 'Administrateur') {
                    http_response_code(403);
                    echo json_encode(["message" => "Vous n'avez pas la permission de supprimer ce message."]);
                    exit;
                }

                $ok = $controller->supprimer($idMessage);
                echo json_encode(["message" => $ok ? "Message supprimé" : "Échec de la suppression"]);

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