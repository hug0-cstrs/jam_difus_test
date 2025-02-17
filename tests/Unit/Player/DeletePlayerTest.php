<?php

require_once __DIR__ . '/../../TestCase.php';

class DeletePlayerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Réinitialiser les variables globales
        $_POST = [];
        $_GET = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $this->pdo->exec("DROP TEMPORARY TABLE IF EXISTS players");
        $this->pdo->exec("
            CREATE TEMPORARY TABLE players (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                team VARCHAR(255),
                position VARCHAR(50),
                age INT,
                nationality VARCHAR(100),
                goals_scored INT,
                image_url VARCHAR(255)
            )
        ");

        $this->pdo->exec("
            INSERT INTO players (name, team, position, age, nationality, goals_scored, image_url) VALUES
            ('John Doe', 'Real Madrid', 'Attaquant', 25, 'France', 10, 'https://cdn.pixabay.com/photo/2017/11/10/05/48/user-2935527_1280.png')
        ");
    }

    public function testDeleteValidPlayer()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->setInputStream(json_encode(['id' => 1]));

        ob_start();
        include BASE_PATH . '/ajax/delete_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertTrue($result['success']);

        // Vérifier que le joueur a été supprimé
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM players WHERE id = 1");
        $count = $stmt->fetchColumn();
        $this->assertEquals(0, $count);
    }

    public function testDeleteInvalidPlayer()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->setInputStream(json_encode(['id' => 999]));

        ob_start();
        include BASE_PATH . '/ajax/delete_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertFalse($result['success']);
        $this->assertEquals('Joueur non trouvé', $result['error']);
    }

    public function testDeleteWithMissingId()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->setInputStream(json_encode([]));

        ob_start();
        include BASE_PATH . '/ajax/delete_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertFalse($result['success']);
        $this->assertEquals('ID du joueur invalide', $result['error']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DROP TEMPORARY TABLE IF EXISTS players");
        $this->pdo = null;
        parent::tearDown();
    }
} 