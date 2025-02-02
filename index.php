<?php
$base_url = '/jamDifus_test';
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
  <?php include 'templates/components/navbar.html'; ?>

  <div class="container mt-4">
    <?php include 'templates/components/search_filters.html'; ?>

    <div id="players-container" class="row g-4">
      <!-- Les cartes des joueurs seront injectées ici dynamiquement -->
    </div>
  </div>

  <?php include 'templates/modals/player_details_modal.html'; ?>
  <?php include 'templates/modals/delete_confirmation_modal.html'; ?>
  <?php include 'templates/modals/add_player_modal.html'; ?>
  <?php include 'templates/modals/edit_player_modal.html'; ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="<?php echo $base_url; ?>/assets/js/app.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
</body>

<?php include 'templates/components/footer.html'; ?>

</html>