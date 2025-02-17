<?php

require_once __DIR__ . '/../TestCase.php';

class PlayerManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
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
    }

    public function testCompletePlayerLifecycle()
    {
        echo "\nStarting test...\n";

        // 1. Création d'un joueur
        echo "1. Testing player creation...\n";
        $testData = [
            'name' => 'John Doe',
            'team' => 'Real Madrid',
            'position' => 'Attaquant',
            'age' => 25,
            'nationality' => 'France',
            'goals_scored' => 10,
            'image_url' => 'https://cdn.pixabay.com/photo/2017/11/10/05/48/user-2935527_1280.png'
        ];

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->setInputStream(json_encode($testData));

        ob_start();
        include BASE_PATH . '/ajax/add_player.php';
        $output = ob_get_clean();
        
        echo "Output from add_player.php: " . $output . "\n";
        
        $result = json_decode($output, true);
        if ($result === null) {
            echo "JSON decode error: " . json_last_error_msg() . "\n";
        }
        
        var_dump($result);
        
        $this->assertTrue($result['success']);
        $playerId = $result['id'];

        // 2. Récupération du joueur
        echo "\n2. Testing player retrieval...\n";
        $_GET = ['id' => $playerId];
        ob_start();
        include BASE_PATH . '/ajax/get_player_details.php';
        $output = ob_get_clean();
        
        echo "Output from get_player_details.php: " . $output . "\n";
        
        $result = json_decode($output, true);
        if ($result === null) {
            echo "JSON decode error: " . json_last_error_msg() . "\n";
        }
        
        var_dump($result);

        $this->assertEquals('John Doe', $result['name']);
        $this->assertEquals('Real Madrid', $result['team']);

        // 3. Mise à jour du joueur
        echo "\n3. Testing player update...\n";
        $_POST = [
            'id' => $playerId,
            'name' => 'John Updated',
            'team' => 'Barcelona',
            'position' => 'Milieu',
            'age' => 26,
            'nationality' => 'Espagne',
            'goals_scored' => 15,
            'image_url' => 'https://cdn.pixabay.com/photo/2017/11/10/05/48/user-2935527_1280.png'
        ];

        ob_start();
        include BASE_PATH . '/ajax/update_player.php';
        $output = ob_get_clean();
        
        echo "Output from update_player.php: " . $output . "\n";
        
        $result = json_decode($output, true);
        if ($result === null) {
            echo "JSON decode error: " . json_last_error_msg() . "\n";
        }
        
        var_dump($result);

        $this->assertTrue($result['success']);

        // 4. Vérification de la mise à jour
        echo "\n4. Testing updated player retrieval...\n";
        $_GET = ['id' => $playerId];
        ob_start();
        include BASE_PATH . '/ajax/get_player_details.php';
        $output = ob_get_clean();
        
        echo "Output from get_player_details.php: " . $output . "\n";
        
        $result = json_decode($output, true);
        if ($result === null) {
            echo "JSON decode error: " . json_last_error_msg() . "\n";
        }
        
        var_dump($result);

        $this->assertEquals('John Updated', $result['name']);
        $this->assertEquals('Barcelona', $result['team']);

        // 5. Suppression du joueur
        echo "\n5. Testing player deletion...\n";
        $_POST = ['id' => $playerId];
        ob_start();
        include BASE_PATH . '/ajax/delete_player.php';
        $output = ob_get_clean();
        
        echo "Output from delete_player.php: " . $output . "\n";
        
        $result = json_decode($output, true);
        if ($result === null) {
            echo "JSON decode error: " . json_last_error_msg() . "\n";
        }
        
        var_dump($result);

        $this->assertTrue($result['success']);

        // 6. Vérification de la suppression
        echo "\n6. Testing player deletion verification...\n";
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM players WHERE id = $playerId");
        $count = $stmt->fetchColumn();
        $this->assertEquals(0, $count);

        echo "\nTest completed successfully!\n";
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DROP TEMPORARY TABLE IF EXISTS players");
        $this->pdo = null;
        parent::tearDown();
    }
} 