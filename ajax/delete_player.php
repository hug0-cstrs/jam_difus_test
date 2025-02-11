<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/phpquery_adapter.php';

if (!defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

try {
    // Vérifier si l'ID est fourni
    $data = json_decode(file_get_contents('php://input'), true);
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
    error_log('Erreur lors de la suppression : ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}