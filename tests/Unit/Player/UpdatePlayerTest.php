<?php

require_once __DIR__ . '/../../TestCase.php';

class UpdatePlayerTest extends TestCase
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

    public function testUpdateValidPlayer()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->setInputStream(json_encode([
            'id' => 1,
            'name' => 'John Updated',
            'team' => 'Barcelona',
            'position' => 'Milieu',
            'age' => 26,
            'nationality' => 'Espagne',
            'goals_scored' => 15,
            'image_url' => 'https://fileinfo.com/img/ss/xl/jpg_44-2.jpg'
        ]));

        ob_start();
        include BASE_PATH . '/ajax/update_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertTrue($result['success']);

        // Vérifier que les données ont été mises à jour
        $stmt = $this->pdo->query("SELECT * FROM players WHERE id = 1");
        $player = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals('John Updated', $player['name']);
        $this->assertEquals('Barcelona', $player['team']);
        $this->assertEquals('Milieu', $player['position']);
        $this->assertEquals(26, $player['age']);
        $this->assertEquals('Espagne', $player['nationality']);
        $this->assertEquals(15, $player['goals_scored']);
        $this->assertEquals('https://fileinfo.com/img/ss/xl/jpg_44-2.jpg', $player['image_url']);
    }

    public function testUpdateInvalidPlayer()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->setInputStream(json_encode([
            'id' => 999,
            'name' => 'Invalid Player'
        ]));

        ob_start();
        include BASE_PATH . '/ajax/update_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertFalse($result['success']);
        $this->assertEquals('Joueur non trouvé', $result['error']);
    }

    public function testUpdateWithMissingRequiredFields()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->setInputStream(json_encode([
            'id' => 1
        ]));

        ob_start();
        include BASE_PATH . '/ajax/update_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertFalse($result['success']);
        $this->assertEquals('Le champ name est requis', $result['error']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DROP TEMPORARY TABLE IF EXISTS players");
        $this->pdo = null;
        parent::tearDown();
    }
} 