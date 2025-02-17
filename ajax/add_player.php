<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/template_manager.php';
require_once __DIR__ . '/../includes/models/Player.php';

// Activer l'affichage des erreurs en mode test
if (defined('PHPUNIT_RUNNING')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    global $pdo;
}

if (!defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');
}

// Vérifier si la requête est de type POST
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
    $input = file_get_contents('php://input');
    if (!$input) {
        throw new Exception('Données manquantes');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Données invalides');
    }

    // Créer et valider le joueur
    $player = new Player($data);
    
    if (!$player->isValid()) {
        throw new Exception($player->getValidationError());
    }

    // Valider la position
    if (!in_array($player->getPosition(), Player::getValidPositions())) {
        throw new Exception('Position invalide');
    }

    // Préparation de la requête
    $query = "INSERT INTO players (name, team, position, age, nationality, goals_scored, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    
    // Exécution de la requête
    $stmt->execute([
        $player->getName(),
        $player->getTeam(),
        $player->getPosition(),
        $player->getAge(),
        $player->getNationality(),
        $player->getGoalsScored(),
        $player->getImageUrl()
    ]);
    
    $newPlayerId = $pdo->lastInsertId();

    // Récupérer le joueur créé pour confirmation
    $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
    $stmt->execute([$newPlayerId]);
    $newPlayerData = $stmt->fetch(PDO::FETCH_ASSOC);
    $newPlayer = new Player($newPlayerData);
    
    echo json_encode([
        'success' => true,
        'id' => $newPlayerId
    ] + $newPlayer->toArray());
    
} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
    echo json_encode($response);
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(500);
        exit;
    }
    return;
} 