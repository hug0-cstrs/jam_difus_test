<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/template_manager.php';
require_once __DIR__ . '/../includes/models/Player.php';

if (!defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');
}

// Utiliser la connexion PDO de test si elle existe
if (defined('PHPUNIT_RUNNING')) {
    global $pdo;
}

$templateManager = TemplateManager::getInstance();

// Valider l'ID du joueur
if (!isset($_GET['id']) || 
    !is_numeric($_GET['id']) || 
    intval($_GET['id']) != $_GET['id'] || 
    $_GET['id'] <= 0) {
    $response = ['success' => false, 'error' => 'ID du joueur manquant ou invalide'];
    echo json_encode($response);
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(400);
        exit;
    }
    return;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $playerData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification si le joueur existe dans la base de données
    if (!$playerData) {
        $response = ['success' => false, 'error' => 'Joueur non trouvé'];
        echo json_encode($response);
        if (!defined('PHPUNIT_RUNNING')) {
            http_response_code(404);
            exit;
        }
        return;
    }

    // Créer l'objet Player
    $player = new Player($playerData);

    // Retourner les données du joueur
    echo json_encode([
        'success' => true
    ] + $player->toArray());

} catch (PDOException $e) {
    $response = ['success' => false, 'error' => 'Erreur lors de la récupération des détails du joueur'];
    echo json_encode($response);
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(500);
        exit;
    }
    return;
} 