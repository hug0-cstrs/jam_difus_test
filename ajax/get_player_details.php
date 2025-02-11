<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/template_manager.php';

if (!defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');
}

// Utiliser la connexion PDO de test si elle existe
if (defined('PHPUNIT_RUNNING')) {
    global $pdo;
}

$templateManager = TemplateManager::getInstance();

// Valider l'ID du joueur avec phpQuery
if (!isset($_GET['id']) || 
    !is_numeric($_GET['id']) || 
    intval($_GET['id']) != $_GET['id'] || 
    $_GET['id'] <= 0) {
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(400);
        exit;
    } else {
        echo json_encode(['error' => 'ID du joueur manquant ou invalide']);
        return;
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification si le joueur existe dans la base de données
    if (!$player) {
        if (!defined('PHPUNIT_RUNNING')) {
            http_response_code(404);
            exit;
        } else {
            echo json_encode(['error' => 'Joueur non trouvé']);
            return;
        }
    }

    // Modeler les détails du joueur avec le gestionnaire de templates
    $playerDetails = $templateManager->modelPlayerDetails($player);

    // Préparer la réponse avec le HTML modelé et les données
    $response = [
        'html' => $playerDetails->html(),
        'data' => $player
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(500);
        exit;
    } else {
        echo json_encode(['error' => 'Erreur lors de la récupération des détails du joueur']);
        return;
    }
} 