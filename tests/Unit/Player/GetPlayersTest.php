<?php

require_once __DIR__ . '/../../TestCase.php';

class GetPlayersTest extends TestCase
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

        // Insérer des données de test
        $this->pdo->exec("
            INSERT INTO players (name, team, position, age, nationality, goals_scored, image_url) VALUES
            ('John Doe', 'Real Madrid', 'Attaquant', 25, 'France', 10, 'https://cdn.pixabay.com/photo/2017/11/10/05/48/user-2935527_1280.png'),
            ('Jane Smith', 'Barcelona', 'Milieu', 28, 'Espagne', 5, 'https://cdn.pixabay.com/photo/2017/11/10/05/48/user-2935527_1280.png'),
            ('Mike Johnson', 'PSG', 'Défenseur', 30, 'Brésil', 2, 'https://cdn.pixabay.com/photo/2017/11/10/05/48/user-2935527_1280.png')
        ");
    }

    public function testGetAllPlayers()
    {
        ob_start();
        include BASE_PATH . '/ajax/get_players.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertCount(3, $result);
    }

    public function testSearchPlayers()
    {
        $_GET['search'] = 'John';
        
        ob_start();
        include BASE_PATH . '/ajax/get_players.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result[0]['name']);
    }

    public function testFilterByTeam()
    {
        $_GET['team'] = 'Barcelona';
        
        ob_start();
        include BASE_PATH . '/ajax/get_players.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertCount(1, $result);
        $this->assertEquals('Barcelona', $result[0]['team']);
    }

    public function testFilterByPosition()
    {
        $_GET['position'] = 'Milieu';
        
        ob_start();
        include BASE_PATH . '/ajax/get_players.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertCount(1, $result);
        $this->assertEquals('Milieu', $result[0]['position']);
    }

    public function testSearchAndFilterByTeam()
    {
        $_GET['search'] = 'John';
        $_GET['team'] = 'Real Madrid';
        
        ob_start();
        include BASE_PATH . '/ajax/get_players.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result[0]['name']);
        $this->assertEquals('Real Madrid', $result[0]['team']);
    }

    public function testSearchAndFilterByPosition()
    {
        $_GET['search'] = 'Jane';
        $_GET['position'] = 'Milieu';
        
        ob_start();
        include BASE_PATH . '/ajax/get_players.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertCount(1, $result);
        $this->assertEquals('Jane Smith', $result[0]['name']);
        $this->assertEquals('Milieu', $result[0]['position']);
    }

    public function testFilterByTeamAndPosition()
    {
        $_GET['team'] = 'Barcelona';
        $_GET['position'] = 'Milieu';
        
        ob_start();
        include BASE_PATH . '/ajax/get_players.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertCount(1, $result);
        $this->assertEquals('Barcelona', $result[0]['team']);
        $this->assertEquals('Milieu', $result[0]['position']);
    }

    public function testSearchAndFilterByTeamAndPosition()
    {
        $_GET['search'] = 'Jane';
        $_GET['team'] = 'Barcelona';
        $_GET['position'] = 'Milieu';
        
        ob_start();
        include BASE_PATH . '/ajax/get_players.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertCount(1, $result);
        $this->assertEquals('Jane Smith', $result[0]['name']);
    }

    public function testSearchWithNoResults()
    {
        $_GET['search'] = 'NonExistentPlayer';
        
        ob_start();
        include BASE_PATH . '/ajax/get_players.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertCount(0, $result);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DROP TEMPORARY TABLE IF EXISTS players");
        $this->pdo = null;
        parent::tearDown();
    }
} 