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
  <!--     Fonts and icons     -->
  <link href="#" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="#" rel="stylesheet" />
  <link href="#" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- CSS Files -->
  <link id="pagestyle" href="<?= BASE_URL ?>/assets/css/soft-ui-dashboard.css?v=1.1.0" rel="stylesheet" />
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>
<!-- Style -->
<?php include __DIR__."/inc/style.php"; ?>

<body class="g-sidenav-show bg-gray-100">

  <!-- Menu -->
  <?php include __DIR__."/inc/menu.php"; ?>

  <div class="main-content position-relative max-height-vh-100 h-100">
    <!-- Navbar -->
     <?php $page = 'Fiche Objet'?>
    <?php include __DIR__."/inc/header.php"; ?>
    <!-- End Navbar -->

    <div class="container-fluid py-4">
        <div class="row">
          <div class="col-lg-6">
            <div class="card mb-4" style="height: 90vh;">
                <!-- Information de l'objet -->
              <div class="card-header pb-0" id="info-objet">
              </div>
              <!-- Image et description de l'objet -->
              <div class="card-body" id="info-img" style="height: calc(100% - 60px); overflow-y: auto;">
              </div>
            </div>
          </div>
          
          <div class="col-lg-6">
            <!-- Information du propriétaire -->
            <div class="card mb-4" style="height: 45vh;">
              <div class="card-header pb-0">
                <h6 class="text-uppercase text-secondary text-sm font-weight-bolder">Propriétaire Actuel</h6>
              </div>
              <div class="card-body" id="info-proprio" style="height: calc(100% - 60px); overflow-y: auto;">
              </div>
            </div>
            <!-- Historique de l'objet -->
            <div class="card mb-4" style="height: 45vh;">
              <div class="card-header pb-0">
                <h6 class="text-uppercase text-secondary text-sm font-weight-bolder">Historique</h6>
              </div>
              <div class="card-body" id="info-historique" style="height: calc(100% - 60px); overflow-y: auto;">
              </div>
            </div>
          </div>
        </div>      
      <!-- Footer -->
      <?php include __DIR__."/inc/footer.php"; ?>
    </div>
  </div>
  <!-- Id_objet -->
  <meta name="objet-id" content="<?= htmlspecialchars($id_objet) ?>">
  <!--   Core JS Files   -->
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

  <!-- Mes script -->
  <script src="<?= BASE_URL ?>/traitement-js/methodes/met_objetImg.js"></script>
  <script src="<?= BASE_URL ?>/traitement-js/methodes/met_objet.js"></script>
  <script src="<?= BASE_URL ?>/traitement-js/methodes/met_objetHistory.js"></script>
  <script src="<?= BASE_URL ?>/traitement-js/methodes/met_categorie.js"></script>


  <script src="<?= BASE_URL ?>/traitement-js/ficheObjet/ficheObjet.js"></script>


  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="<?= BASE_URL ?>/assets/js/soft-ui-dashboard.min.js?v=1.1.0"></script>

  
</body>

</html>