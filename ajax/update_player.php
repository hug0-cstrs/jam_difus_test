<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        throw new Exception('ID du joueur manquant');
    }

    $stmt = $pdo->prepare('UPDATE players SET
        name = ?,
        position = ?,
        team = ?,
        age = ?,
        nationality = ?,
        goals_scored = ?,
        image_url = ?
        WHERE id = ?'
    );

    $stmt->execute([
        $data['name'],
        $data['position'],
        $data['team'],
        $data['age'],
        $data['nationality'],
        $data['goals_scored'],
        $data['image_url'],
        $data['id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Joueur mis à jour avec succès']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 