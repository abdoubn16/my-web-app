<?php
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/UtilisateurController.php';

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();
$pdo = $db->getConnection();
$controller = new UtilisateurController($pdo);

$data = json_decode(file_get_contents("php://input"), true) ?? [];
$action = $_GET['action'] ?? null;

try {
    switch ($_SERVER['REQUEST_METHOD']) {

        // --------- POST: inscription ou login ---------
        case 'POST':
            if ($action === 'inscription') {
                $champs = ['nom', 'prenom', 'email', 'mot_de_passe', 'telephone', 'adresse', 'role'];
                foreach ($champs as $champ) {
                    if (empty($data[$champ])) {
                        throw new Exception("Champ manquant : $champ");
                    }
                }

                if ($data['role'] === 'Administrateur') {
                    throw new Exception("Impossible de s'inscrire comme administrateur.");
                }

                $id_prestation = $data['id_prestation'] ?? null;

                $id = $controller->inscrire(
                    $data['nom'], $data['prenom'], $data['email'], $data['mot_de_passe'],
                    $data['telephone'], $data['adresse'], $data['role'], $id_prestation
                );
                echo json_encode(["message" => "Inscription réussie", "id_utilisateur" => $id]);

            } elseif ($action === 'login') {
                if (empty($data['email']) || empty($data['mot_de_passe'])) {
                    throw new Exception("Email et mot de passe requis");
                }
                $user = $controller->login($data['email'], $data['mot_de_passe']);
                echo json_encode(["message" => "Connexion réussie", "utilisateur" => $user]);

            } else {
                throw new Exception("Action POST invalide");
            }
            break;

        // --------- PUT: modifier ou changer mot de passe ---------
        case 'PUT':
            if ($action === 'modifier') {
                if (empty($data['id']) || empty($data['nom']) || empty($data['prenom']) || empty($data['telephone']) || empty($data['adresse'])) {
                    throw new Exception("Champs manquants pour la modification");
                }
                $ok = $controller->modifier($data['id'], $data['nom'], $data['prenom'], $data['telephone'], $data['adresse']);
                echo json_encode(["message" => $ok ? "Modification réussie" : "Aucune modification effectuée"]);

            } elseif ($action === 'changer_mot_de_passe') {
                if (empty($data['id']) || empty($data['ancien']) || empty($data['nouveau'])) {
                    throw new Exception("Champs manquants pour changer le mot de passe");
                }
                $ok = $controller->changerMotDePasse($data['id'], $data['ancien'], $data['nouveau']);
                echo json_encode(["message" => $ok ? "Mot de passe modifié avec succès" : "Échec de la modification"]);

            } else {
                throw new Exception("Action PUT invalide");
            }
            break;

        // --------- GET: get, get_all, logout ---------
        case 'GET':
            if ($action === 'logout') {
                session_unset();
                session_destroy();
                echo json_encode(["message" => "Déconnexion réussie."]);

            } elseif ($action === 'get') {
                if (empty($_GET['id'])) {
                    throw new Exception("ID manquant");
                }
                $user = $controller->getParId($_GET['id']);
                echo json_encode($user ?: ["message" => "Utilisateur non trouvé"]);

            } elseif ($action === 'get_all') {
                if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'Administrateur') {
                    http_response_code(403);
                    echo json_encode(["message" => "Accès refusé. Administrateur requis."]);
                    exit;
                }

                $users = $controller->getTous();
                echo json_encode($users);

            } else {
                throw new Exception("Action GET invalide");
            }
            break;

        // --------- DELETE: supprimer ---------
        case 'DELETE':
            if ($action === 'supprimer') {
                if (empty($_GET['id'])) {
                    throw new Exception("ID utilisateur requis pour suppression");
                }

                $idCible = (int) $_GET['id'];

                if (!isset($_SESSION['utilisateur'])) {
                    throw new Exception("Utilisateur non connecté.");
                }

                $idActuel = (int) $_SESSION['utilisateur']['id'];
                $roleActuel = $_SESSION['utilisateur']['role'];

                // Vérifier permission
                if ($idActuel !== $idCible && $roleActuel !== 'Administrateur') {
                    http_response_code(403);
                    echo json_encode(["message" => "Vous n'avez pas la permission de supprimer cet utilisateur."]);
                    exit;
                }

                $ok = $controller->supprimer($idCible);
                echo json_encode(["message" => $ok ? "Utilisateur supprimé" : "Échec de la suppression"]);

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