<?php
require_once __DIR__ . '/../includes/db.php';

if (!defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');
}

if (defined('PHPUNIT_RUNNING')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    global $pdo;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = ['success' => false, 'error' => 'Méthode non autorisée'];
    echo json_encode($response);
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(405);
        exit;
    }
    return;
}

try {
    // Récupérer les données (soit de $_POST soit du corps JSON)
    $data = $_POST;
    if (empty($_POST)) {
        $input = file_get_contents('php://input');
        $jsonData = json_decode($input, true);
        if ($jsonData !== null) {
            $data = $jsonData;
        }
    }

    // Vérifier si l'ID est fourni
    if (!isset($data['id']) || !is_numeric($data['id'])) {
        throw new Exception('ID du joueur invalide');
    }

    $playerId = intval($data['id']);

    // Vérifier si le joueur existe
    $checkStmt = $pdo->prepare("SELECT id FROM players WHERE id = ?");
    $checkStmt->execute([$playerId]);
    if (!$checkStmt->fetch()) {
        throw new Exception('Joueur non trouvé');
    }

    // Supprimer le joueur
    $stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
    $success = $stmt->execute([$playerId]);

    if (!$success) {
        throw new Exception('Erreur lors de la suppression du joueur');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Joueur supprimé avec succès'
    ]);

} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
    echo json_encode($response);
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(500);
        exit;
    }
    return;
}