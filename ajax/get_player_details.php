<?php
require_once __DIR__ . '/../includes/db.php';

if (!defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');
}

// Utiliser la connexion PDO de test si elle existe
if (defined('PHPUNIT_RUNNING')) {
    global $pdo;
}

// Valider l'ID du joueur
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
    $player = $stmt->fetch();

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

    echo json_encode($player);
} catch (PDOException $e) {
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(500);
        exit;
    } else {
        echo json_encode(['error' => 'Erreur lors de la récupération des détails du joueur']);
        return;
    }
} 