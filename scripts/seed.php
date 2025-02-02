<?php
require_once __DIR__ . '/../includes/db.php';

$players = [
    [
        'name' => 'Kylian Mbappé',
        'position' => 'Attaquant',
        'team' => 'Real Madrid',
        'age' => 26,
        'nationality' => 'France',
        'goals_scored' => 150,
        'image_url' => 'https://assets.goal.com/images/v3/blt7574bff101094824/Kylian%20Mbappe%20Real%20Madrid%202024-25%20(2).jpg'
    ],
    [
        'name' => 'Antoine Griezmann',
        'position' => 'Attaquant',
        'team' => 'Atlético Madrid',
        'age' => 33,
        'nationality' => 'France',
        'goals_scored' => 180,
        'image_url' => 'https://cdn-s-www.ledauphine.com/images/18DE998B-3136-4F83-9843-A29E2682F130/NW_raw/antoine-griezmann-photo-sipa-1708455708.jpg'
    ],
    [
        'name' => 'Kevin De Bruyne',
        'position' => 'Milieu',
        'team' => 'Manchester City',
        'age' => 33,
        'nationality' => 'Belgique',
        'goals_scored' => 89,
        'image_url' => 'https://assets-fr.imgfoot.com/kevin-de-bruyne-2324-659ad0d84a033.jpg'
    ],
    [
        'name' => 'Virgil van Dijk',
        'position' => 'Défenseur',
        'team' => 'Liverpool',
        'age' => 33,
        'nationality' => 'Pays-Bas',
        'goals_scored' => 20,
        'image_url' => 'https://static.onzemondial.com/8/2024/12/photo_article/930579/373433/1200-L-liverpool-excellente-nouvelle-pour-le-futur-de-virgil-van-dijk.jpg'
    ],
    [
        'name' => 'Thibaut Courtois',
        'position' => 'Gardien',
        'team' => 'Real Madrid',
        'age' => 32,
        'nationality' => 'Belgique',
        'goals_scored' => 0,
        'image_url' => 'https://www.leparisien.fr/resizer/JiI9ris-w7pGHcboCqI9N64AMO8=/932x582/cloudfront-eu-central-1.images.arcpublishing.com/leparisien/CA62RDYWXJDJ3H6A2TV7Z4ODBQ.jpg'
    ],
    [
        'name' => 'Erling Haaland',
        'position' => 'Attaquant',
        'team' => 'Manchester City',
        'age' => 24,
        'nationality' => 'Norvège',
        'goals_scored' => 130,
        'image_url' => 'https://imageio.forbes.com/specials-images/imageserve/645e171afce09061884bd1eb/Manchester-City-v-Nottingham-Forest---Premier-League/960x0.jpg?format=jpg&width=960'
    ],
    [
        'name' => 'Jude Bellingham',
        'position' => 'Milieu',
        'team' => 'Real Madrid',
        'age' => 21,
        'nationality' => 'Angleterre',
        'goals_scored' => 25,
        'image_url' => 'https://i0.wp.com/real-france.fr/wp-content/uploads/2023/09/real-madrid-cf-v-getafe-cf-laliga-ea-sports-1.jpg?fit=750%2C500&ssl=1'
    ],
    [
        'name' => 'Victor Osimhen',
        'position' => 'Attaquant',
        'team' => 'Galatasaray',
        'age' => 26,
        'nationality' => 'Nigeria',
        'goals_scored' => 85,
        'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSNGJi_PAtXjP-znOj07bPuu5N3QzXuXGfJ8Q&s'
    ],
    [
        'name' => 'Bruno Fernandes',
        'position' => 'Milieu',
        'team' => 'Manchester United',
        'age' => 30,
        'nationality' => 'Portugal',
        'goals_scored' => 65,
        'image_url' => 'https://assets.goal.com/images/v3/blt814cc46145391386/b0f36a6e8fa2a683006586af5f193216915836b5.jpg?auto=webp&format=pjpg&width=3840&quality=60'
    ],
    [
        'name' => 'Theo Hernandez',
        'position' => 'Défenseur',
        'team' => 'AC Milan',
        'age' => 27,
        'nationality' => 'France',
        'goals_scored' => 15,
        'image_url' => 'https://cdn-s-www.lalsace.fr/images/095ADAAB-6557-4062-8C47-99DC31F5EF9A/NW_raw/photo-j-e-e-sipa-1668771785.jpg'
    ],
    [
        'name' => 'Lionel Messi',
        'position' => 'Attaquant',
        'team' => 'Inter Miami',
        'age' => 36,
        'nationality' => 'Argentine',
        'goals_scored' => 700,
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/b/b4/Lionel-Messi-Argentina-2022-FIFA-World-Cup_(cropped).jpg'
    ],
    [
        'name' => 'Neymar Jr',
        'position' => 'Attaquant',
        'team' => 'Santos FC',
        'age' => 31,
        'nationality' => 'Brésil',
        'goals_scored' => 400,
        'image_url' => 'https://i.pinimg.com/736x/f2/44/da/f244da64dd7bd550d5b28786510ce3e0.jpg'
    ],
    [
        'name' => 'Robert Lewandowski',
        'position' => 'Attaquant',
        'team' => 'FC Barcelone',
        'age' => 35,
        'nationality' => 'Pologne',
        'goals_scored' => 500,
        'image_url' => 'https://imgresizer.eurosport.com/unsafe/1200x0/filters:format(jpeg)/origin-imgresizer.eurosport.com/2024/09/25/4044545-82028548-2560-1440.jpg'
    ]
];

try {
    // Vider la table avant d'insérer de nouvelles données
    $pdo->exec('TRUNCATE TABLE players');
    
    $stmt = $pdo->prepare('INSERT INTO players (name, position, team, age, nationality, goals_scored, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)');
    
    foreach ($players as $player) {
        $stmt->execute([
            $player['name'],
            $player['position'],
            $player['team'],
            $player['age'],
            $player['nationality'],
            $player['goals_scored'],
            $player['image_url']
        ]);
    }
    
    echo "Base de données peuplée avec succès !\n";
} catch (PDOException $e) {
    die("Erreur lors du peuplement de la base de données : " . $e->getMessage());
}
