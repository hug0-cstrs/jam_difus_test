<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/template_manager.php';

// Utiliser la connexion PDO de test si elle existe
if (defined('PHPUNIT_RUNNING')) {
    global $pdo;
}

if (!defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');
}

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

try {
    $templateManager = TemplateManager::getInstance();
    
    // Récupérer et valider les données
    $data = json_decode(file_get_contents('php://input'), true);
    $validation = $templateManager->validatePlayerForm($data);
    
    if (!$validation['isValid']) {
        $errorMessage = $templateManager->modelConfirmation(
            'error',
            'Erreur de validation',
            '<ul><li>' . implode('</li><li>', $validation['errors']) . '</li></ul>'
        );
        
        echo json_encode([
            'success' => false,
            'errors' => $validation['errors'],
            'html' => $errorMessage->html()
        ]);
        exit;
    }

    // Préparation de la requête
    $query = "INSERT INTO players (name, team, position, age, nationality, goals_scored, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    
    // Exécution de la requête
    $stmt->execute([
        $data['name'],
        $data['team'],
        $data['position'],
        $data['age'],
        $data['nationality'],
        $data['goals_scored'] ?? 0,
        $data['image_url'] ?? null
    ]);
    
    $newPlayerId = $pdo->lastInsertId();
    $data['id'] = $newPlayerId;
    
    // Modeler la carte du nouveau joueur
    $playerCard = $templateManager->modelPlayerCard($data);
    
    // Modeler le message de confirmation
    $confirmation = $templateManager->modelConfirmation(
        'success',
        'Joueur ajouté avec succès',
        'Le joueur ' . $data['name'] . ' a été ajouté à l\'équipe ' . $data['team']
    );
    
    // Préparer la réponse
    $response = [
        'success' => true,
        'message' => 'Joueur ajouté avec succès',
        'player' => $data,
        'html' => $playerCard->html(),
        'confirmation' => $confirmation->html()
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    error_log('Erreur SQL : ' . $e->getMessage());
    $errorMessage = $templateManager->modelConfirmation(
        'error',
        'Erreur lors de l\'ajout du joueur',
        'Une erreur est survenue lors de l\'enregistrement dans la base de données.'
    );
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de l\'ajout du joueur',
        'html' => $errorMessage->html()
    ]);
    exit;
} catch (Exception $e) {
    error_log('Erreur générale : ' . $e->getMessage());
    $errorMessage = $templateManager->modelConfirmation(
        'error',
        'Erreur inattendue',
        'Une erreur inattendue est survenue lors de l\'ajout du joueur.'
    );
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur inattendue',
        'html' => $errorMessage->html()
    ]);
    exit;
} 