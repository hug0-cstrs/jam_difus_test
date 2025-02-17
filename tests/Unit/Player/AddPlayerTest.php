<?php

require_once __DIR__ . '/../../TestCase.php';

class AddPlayerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer une table de test
        $this->pdo->exec("DROP TEMPORARY TABLE IF EXISTS players");
        $this->pdo->exec("
            CREATE TEMPORARY TABLE players (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                position VARCHAR(50),
                team VARCHAR(255),
                age INT,
                nationality VARCHAR(100),
                goals_scored INT,
                image_url VARCHAR(255)
            )
        ");
    }

    public function testAddValidPlayer()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $testPlayer = [
            'name' => 'Test Player',
            'position' => 'Attaquant',
            'team' => 'Test Team',
            'age' => 25,
            'nationality' => 'French',
            'goals_scored' => 10,
            'image_url' => 'test.jpg'
        ];

        // Simuler l'entrée PHP
        $this->setInputStream(json_encode($testPlayer));

        ob_start();
        include __DIR__ . '/../../../ajax/add_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        
        // Vérifier que le joueur a été créé avec les bonnes données
        $this->assertEquals($testPlayer['name'], $result['name']);
        $this->assertEquals($testPlayer['position'], $result['position']);
        $this->assertEquals($testPlayer['team'], $result['team']);
        $this->assertEquals($testPlayer['age'], $result['age']);
        $this->assertEquals($testPlayer['nationality'], $result['nationality']);
        $this->assertEquals($testPlayer['goals_scored'], $result['goals_scored']);
        $this->assertEquals($testPlayer['image_url'], $result['image_url']);
    }

    public function testAddPlayerWithMissingFields()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $testPlayer = [
            'name' => 'Test Player',
            'position' => 'Attaquant'
            // Champs manquants
        ];

        $this->setInputStream(json_encode($testPlayer));

        ob_start();
        include __DIR__ . '/../../../ajax/add_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Le champ team est requis', $result['error']);
    }

    public function testAddPlayerWithInvalidAge()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $testPlayer = [
            'name' => 'Test Player',
            'position' => 'Attaquant',
            'team' => 'Test Team',
            'age' => -5, // Âge invalide
            'nationality' => 'French',
            'goals_scored' => 10,
            'image_url' => 'test.jpg'
        ];

        $this->setInputStream(json_encode($testPlayer));

        ob_start();
        include __DIR__ . '/../../../ajax/add_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Âge invalide', $result['error']);
    }

    public function testAddPlayerWithInvalidPosition()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $testPlayer = [
            'name' => 'Test Player',
            'position' => 'Position Invalide',
            'team' => 'Test Team',
            'age' => 25,
            'nationality' => 'French',
            'goals_scored' => 10,
            'image_url' => 'test.jpg'
        ];

        $this->setInputStream(json_encode($testPlayer));

        ob_start();
        include __DIR__ . '/../../../ajax/add_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Position invalide', $result['error']);
    }

    public function testAddPlayerWithInvalidGoalsScored()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $testPlayer = [
            'name' => 'Test Player',
            'position' => 'Attaquant',
            'team' => 'Test Team',
            'age' => 25,
            'nationality' => 'French',
            'goals_scored' => -1, // Nombre de buts invalide
            'image_url' => 'test.jpg'
        ];

        $this->setInputStream(json_encode($testPlayer));

        ob_start();
        include __DIR__ . '/../../../ajax/add_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Nombre de buts invalide', $result['error']);
    }

    public function testInvalidRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        ob_start();
        include __DIR__ . '/../../../ajax/add_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Méthode non autorisée', $result['error']);
    }

    public function testInvalidJsonData()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->setInputStream('invalid json');

        ob_start();
        include __DIR__ . '/../../../ajax/add_player.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Données invalides', $result['error']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DROP TEMPORARY TABLE IF EXISTS players");
        $this->pdo = null;
        parent::tearDown();
    }
} 