<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/models/Player.php';

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
    // Récupérer les données
    $data = $_POST;
    if (empty($_POST)) {
        $input = file_get_contents('php://input');
        $jsonData = json_decode($input, true);
        if ($jsonData !== null) {
            $data = $jsonData;
        }
    }
    
    // Validation de l'ID
    if (!isset($data['id']) || !is_numeric($data['id'])) {
        throw new Exception('ID du joueur invalide');
    }

    // Vérifier si le joueur existe
    $checkStmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
    $checkStmt->execute([$data['id']]);
    $existingPlayerData = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingPlayerData) {
        throw new Exception("Joueur non trouvé");
    }

    // Créer et valider le nouveau joueur
    $player = new Player($data);
    
    if (!$player->isValid()) {
        throw new Exception($player->getValidationError());
    }

    // Valider la position
    if (!in_array($player->getPosition(), Player::getValidPositions())) {
        throw new Exception('Position invalide');
    }

    // Préparation de la requête
    $query = "UPDATE players SET name = ?, team = ?, position = ?, age = ?, nationality = ?, goals_scored = ?, image_url = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    
    // Exécution de la requête
    $stmt->execute([
        $player->getName(),
        $player->getTeam(),
        $player->getPosition(),
        $player->getAge(),
        $player->getNationality(),
        $player->getGoalsScored(),
        $player->getImageUrl(),
        $data['id']
    ]);
    
    // Récupérer le joueur mis à jour
    $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
    $stmt->execute([$data['id']]);
    $updatedPlayerData = $stmt->fetch(PDO::FETCH_ASSOC);
    $updatedPlayer = new Player($updatedPlayerData);
    
    echo json_encode([
        'success' => true,
        'id' => $data['id']
    ] + $updatedPlayer->toArray());
    
} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
    echo json_encode($response);
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(500);
        exit;
    }
    return;
} 