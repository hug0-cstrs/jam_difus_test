<?php

require_once __DIR__ . '/TestCase.php';

class GetPlayersTest extends TestCase
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
                position VARCHAR(50)
            )
        ");

        // Insérer des données de test
        $this->pdo->exec("
            INSERT INTO players (name, team, position) VALUES
            ('John Doe', 'Real Madrid', 'Attaquant'),
            ('Jane Smith', 'Barcelona', 'Défenseur'),
            ('Bob Wilson', 'Real Madrid', 'Milieu'),
            ('Alice Brown', 'Barcelona', 'Attaquant'),
            ('Charlie Davis', 'Real Madrid', 'Défenseur')
        ");
    }

    public function testGetAllPlayers()
    {
        $_GET = [];
        ob_start();
        include __DIR__ . '/../ajax/get_players.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertCount(5, $result);
    }

    public function testSearchPlayers()
    {
        $_GET = ['search' => 'John'];
        ob_start();
        include __DIR__ . '/../ajax/get_players.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result[0]['name']);
    }

    public function testFilterByTeam()
    {
        $_GET = ['team' => 'Real Madrid'];
        ob_start();
        include __DIR__ . '/../ajax/get_players.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertCount(3, $result);
    }

    public function testFilterByPosition()
    {
        $_GET = ['position' => 'Défenseur'];
        ob_start();
        include __DIR__ . '/../ajax/get_players.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertCount(2, $result);
    }

    public function testSearchAndFilterByTeam()
    {
        $_GET = [
            'search' => 'Alice',
            'team' => 'Barcelona'
        ];
        ob_start();
        include __DIR__ . '/../ajax/get_players.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertCount(1, $result);
        $this->assertEquals('Alice Brown', $result[0]['name']);
    }

    public function testSearchAndFilterByPosition()
    {
        $_GET = [
            'search' => 'Alice',
            'position' => 'Attaquant'
        ];
        ob_start();
        include __DIR__ . '/../ajax/get_players.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertCount(1, $result);
        $this->assertEquals('Alice Brown', $result[0]['name']);
    }

    public function testFilterByTeamAndPosition()
    {
        $_GET = [
            'team' => 'Real Madrid',
            'position' => 'Défenseur'
        ];
        ob_start();
        include __DIR__ . '/../ajax/get_players.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertCount(1, $result);
        $this->assertEquals('Charlie Davis', $result[0]['name']);
    }

    public function testSearchAndFilterByTeamAndPosition()
    {
        $_GET = [
            'search' => 'Alice',
            'team' => 'Barcelona',
            'position' => 'Attaquant'
        ];
        ob_start();
        include __DIR__ . '/../ajax/get_players.php';
        $output = ob_get_clean();

        $result = json_decode($output, true);
        $this->assertCount(1, $result);
        $this->assertEquals('Alice Brown', $result[0]['name']);
    }

    public function testSearchWithNoResults()
    {
        $_GET = ['search' => 'XYZ'];
        ob_start();
        include __DIR__ . '/../ajax/get_players.php';
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