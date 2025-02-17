<?php

require_once __DIR__ . '/../../TestCase.php';

class GetPlayerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer une table de test et insérer des données de test
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

        // Insérer des données de test
        $this->pdo->exec("
            INSERT INTO players (name, team, position, age, nationality, goals_scored, image_url) VALUES
            ('John Doe', 'Real Madrid', 'Attaquant', 25, 'France', 10, 'https://cdn.pixabay.com/photo/2017/11/10/05/48/user-2935527_1280.png')
        ");
    }

    public function testGetValidPlayerDetails()
    {
        $_GET = ['id' => 1];
        ob_start();
        include __DIR__ . '/../../../ajax/get_player_details.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertEquals('John Doe', $result['name']);
        $this->assertEquals('Real Madrid', $result['team']);
        $this->assertEquals('Attaquant', $result['position']);
        $this->assertEquals(25, $result['age']);
        $this->assertEquals('France', $result['nationality']);
        $this->assertEquals(10, $result['goals_scored']);
        $this->assertEquals('https://cdn.pixabay.com/photo/2017/11/10/05/48/user-2935527_1280.png', $result['image_url']);
    }

    public function testGetInvalidPlayerId()
    {
        $_GET = ['id' => 999];
        ob_start();
        include __DIR__ . '/../../../ajax/get_player_details.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Joueur non trouvé', $result['error']);
    }

    public function testMissingPlayerId()
    {
        $_GET = [];
        ob_start();
        include __DIR__ . '/../../../ajax/get_player_details.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('ID du joueur manquant ou invalide', $result['error']);
    }

    public function testInvalidPlayerIdFormat()
    {
        $_GET = ['id' => 'abc'];
        ob_start();
        include __DIR__ . '/../../../ajax/get_player_details.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('ID du joueur manquant ou invalide', $result['error']);
    }

    public function testNegativePlayerId()
    {
        $_GET = ['id' => -1];
        ob_start();
        include __DIR__ . '/../../../ajax/get_player_details.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('ID du joueur manquant ou invalide', $result['error']);
    }

    public function testZeroPlayerId()
    {
        $_GET = ['id' => 0];
        ob_start();
        include __DIR__ . '/../../../ajax/get_player_details.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('ID du joueur manquant ou invalide', $result['error']);
    }

    public function testFloatPlayerId()
    {
        $_GET = ['id' => 1.5];
        ob_start();
        include __DIR__ . '/../../../ajax/get_player_details.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('ID du joueur manquant ou invalide', $result['error']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DROP TEMPORARY TABLE IF EXISTS players");
        $this->pdo = null;
        parent::tearDown();
    }
} 