<?php
require_once 'includes/phpquery_adapter.php';
require_once 'includes/template_manager.php';

$base_url = '/jamDifus_test';
$templateManager = TemplateManager::getInstance();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Football Manager Pro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <link href="<?php echo $base_url; ?>/assets/css/style.css" rel="stylesheet" />
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link rel="icon" type="image/svg+xml" href="<?php echo $base_url; ?>/assets/favicon/soccer-ball.svg" />
</head>

<body>
  <?php 
  // Charger et modeler la navbar
  $navbar = $templateManager->loadTemplate('components/navbar.html');
  $navbar->find('.navbar-brand')->text('Football Manager Pro 2024');
  $navbar->find('#themeToggle')->attr('title', 'Changer le thème');
  echo $navbar->html();
  ?>

  <div class="container mt-4">
    <?php 
    // Charger et modeler les filtres de recherche
    $searchFilters = $templateManager->loadTemplate('components/search_filters.html');
    $searchFilters->find('#searchInput')->attr('placeholder', 'Rechercher un joueur...');
    $searchFilters->find('#teamFilter option:first')->text('Toutes les équipes');
    $searchFilters->find('#positionFilter option:first')->text('Toutes les positions');
    echo $searchFilters->html();
    ?>

    <div id="players-container" class="row g-4">
      <!-- Les cartes des joueurs seront injectées ici dynamiquement -->
    </div>
  </div>

  <?php 
  // Charger et modeler les modales
  $modals = [
    'player_details_modal',
    'delete_confirmation_modal',
    'add_player_modal',
    'edit_player_modal'
  ];

  foreach ($modals as $modalType) {
    $modal = $templateManager->modelModal($modalType);
    echo $modal->html();
  }
  ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="<?php echo $base_url; ?>/assets/js/app.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
</body>

<?php 
// Charger et modeler le footer
$footer = $templateManager->loadTemplate('components/footer.html');
$footer->find('.footer-content')->addClass('py-3');
$footer->find('.copyright')->text('© ' . date('Y') . ' Football Manager Pro. Tous droits réservés.');
echo $footer->html();
?>

</html>