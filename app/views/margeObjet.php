<!--
=========================================================
* Soft UI Dashboard 3 - v1.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="<?= BASE_URL ?>/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/favicon.png">
  <title>
    Soft UI Dashboard 3 by Creative Tim
  </title>
  <link href="#" rel="stylesheet" />
  <link href="#" rel="stylesheet" />
  <link href="#" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link id="pagestyle" href="<?= BASE_URL ?>/assets/css/soft-ui-dashboard.css?v=1.1.0" rel="stylesheet" />
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
  <style>
    .objects-scroll {
      overflow-x: auto;
      overflow-y: hidden;
      -webkit-overflow-scrolling: touch;
      scroll-snap-type: x mandatory;
    }
    .objects-scroll-item {
      flex: 0 0 520px;
      scroll-snap-align: start;
    }
    .objects-scroll-item img {
      width: 100%;
      height: 280px;
      object-fit: cover;
    }
    .objects-scroll-item h5 {
      font-size: 1.25rem;
      line-height: 1.3;
    }
    .objects-scroll-item .text-sm {
      font-size: 1rem;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">

  <?php include __DIR__ . "/inc/menu.php"; ?>

  <div class="main-content position-relative max-height-vh-100 h-100">
    <?php $page = "Resultat " . ($_GET['marge'] ?? '0') . "%"; ?>
    <?php include __DIR__ . "/inc/header.php"; ?>

    <div class="container-fluid py-4">
        <!-- Affiche information de l'objet -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Objet de référence</h6>
                                <p class="text-sm mb-0">Informations sur l'objet utilisé pour la recherche</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div id="info-objet-reference">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <p class="mt-2">Chargement des informations de l'objet...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Affiche le resultat de marge  -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Objets similaires trouvés</h6>
                                <p class="text-sm mb-0">Objets dans la marge de prix sélectionnée</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div id="liste-marge">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <p class="mt-2">Recherche des objets similaires...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      <?php include __DIR__ . "/inc/footer.php"; ?>

    </div>
  </div>

  <script src="<?= BASE_URL ?>/assets/js/core/popper.min.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/core/bootstrap.min.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <!-- Id_objet -->
  <meta name="objet-id" content="<?= htmlspecialchars($_GET['id']) ?>">
  <meta name="marge" content="<?= htmlspecialchars($_GET['marge']) ?>">

  

  <!-- Mes scripts -->
  <script src="<?= BASE_URL ?>/traitement-js/methodes/met_objet.js"></script>

  <script src="<?= BASE_URL ?>/traitement-js/margeObjet/margeObjet.js"></script>


  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/soft-ui-dashboard.min.js?v=1.1.0"></script>

</body>

</html>
