<?php
require_once __DIR__ . '/../includes/db.php';

// Utiliser la connexion PDO de test si elle existe
if (defined('PHPUNIT_RUNNING')) {
    global $pdo;
}

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(405);
        exit;
    } else {
        echo json_encode(['error' => 'Méthode non autorisée']);
        return;
    }
}

// Récupérer les données du formulaire
$input = file_get_contents('php://input');
error_log('Données reçues : ' . $input);

$data = json_decode($input, true);

if (!$data) {
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(400);
        exit;
    } else {
        echo json_encode(['error' => 'Données invalides']);
        return;
    }
}

// Vérifier les champs requis
$required_fields = ['name', 'position', 'team', 'age', 'nationality', 'goals_scored'];
foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        if (!defined('PHPUNIT_RUNNING')) {
            http_response_code(400);
            exit;
        } else {
            echo json_encode(['error' => 'Données invalides : champs manquants']);
            return;
        }
    }
}

// Valider l'âge
if (!is_numeric($data['age']) || $data['age'] < 0 || $data['age'] > 100) {
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(400);
        exit;
    } else {
        echo json_encode(['error' => 'Âge invalide']);
        return;
    }
}

// Valider la position
$valid_positions = ['Attaquant', 'Milieu', 'Défenseur', 'Gardien'];
if (!in_array($data['position'], $valid_positions)) {
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(400);
        exit;
    } else {
        echo json_encode(['error' => 'Position invalide']);
        return;
    }
}

// Valider le nombre de buts
if (!is_numeric($data['goals_scored']) || $data['goals_scored'] < 0) {
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(400);
        exit;
    } else {
        echo json_encode(['error' => 'Nombre de buts invalide']);
        return;
    }
}

// Assurer que image_url est null si non fournie
if (!isset($data['image_url']) || empty($data['image_url'])) {
    $data['image_url'] = null;
}

try {
    $stmt = $pdo->prepare('INSERT INTO players (name, position, team, age, nationality, goals_scored, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)');
    
    $params = [
        $data['name'],
        $data['position'],
        $data['team'],
        $data['age'],
        $data['nationality'],
        $data['goals_scored'],
        $data['image_url']
    ];
    
    error_log('Paramètres SQL : ' . print_r($params, true));
    
    $stmt->execute($params);
    
    // Récupérer l'ID du joueur nouvellement créé
    $playerId = $pdo->lastInsertId();
    error_log('Nouveau joueur ID : ' . $playerId);
    
    // Retourner le joueur créé
    $stmt = $pdo->prepare('SELECT * FROM players WHERE id = ?');
    $stmt->execute([$playerId]);
    $player = $stmt->fetch();
    
    if (!defined('PHPUNIT_RUNNING')) {
        header('Content-Type: application/json');
    }
    echo json_encode($player);
    
} catch (PDOException $e) {
    error_log('Erreur SQL : ' . $e->getMessage());
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(500);
        exit;
    } else {
        echo json_encode(['error' => 'Erreur lors de l\'ajout du joueur : ' . $e->getMessage()]);
        return;
    }
} catch (Exception $e) {
    error_log('Erreur générale : ' . $e->getMessage());
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(500);
        exit;
    } else {
        echo json_encode(['error' => 'Erreur inattendue : ' . $e->getMessage()]);
        return;
    }
} 