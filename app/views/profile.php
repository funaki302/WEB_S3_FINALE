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

<body class="g-sidenav-show bg-gray-100">

  <!-- Menu -->
  <?php include __DIR__."/inc/menu.php"; ?>

  <div class="main-content position-relative max-height-vh-100 h-100">
    <!-- Navbar -->
     <?php $page = 'Profile'; ?>
    <?php include __DIR__."/inc/header.php"; ?>
    <!-- End Navbar -->

    <div class="container-fluid">
      <div class="page-header min-height-250 border-radius-lg mt-4 d-flex flex-column justify-content-end" style="background-image: url('../assets/img/curved-images/curved14.jpg');">
        <span class="mask bg-gradient-dark opacity-6"></span>
        <div class="w-100 position-relative p-3">
          <div class="d-flex justify-content-between align-items-end">
            <div class="d-flex align-items-center">
              <div class="avatar avatar-xl position-relative me-3">
                <img src="<?= BASE_URL ?>/assets/img/avatar.svg" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
              </div>
              <div>
                <h5 class="mb-1 text-white font-weight-bolder">
                  <?= $_SESSION['user_name'] ?>
                </h5>
                <p class="mb-0 text-white text-sm">
                  <?= $_SESSION['user_email'] ?> 
                </p>
              </div>
            </div>

            <div class="d-flex gap-3 flex-wrap justify-content-end" style="min-width: 320px;">
              <div class="card shadow-sm mb-0" style="min-width: 280px; min-height: 140px; background: linear-gradient(135deg, rgba(251,207,51,0.28), rgba(253,126,20,0.18)); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.35);">
                <div class="card-body p-4" id="profile-exchange-received">
                </div>
              </div>
              <div class="card shadow-sm mb-0" style="min-width: 280px; min-height: 140px; background: linear-gradient(135deg, rgba(17,17,17,0.40), rgba(203,12,159,0.16), rgba(131,102,252,0.14)); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.22);">
                <div class="card-body p-4" id="profile-exchange-sent">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 col-xl-4">
          <div class="card h-100">
            <div class="card-header pb-0 p-3">
              <div class="row">
                <div class="col-md-8 d-flex align-items-center">
                  <h6 class="mb-0">Information</h6>
                </div>
                <div class="col-md-4 text-end">
                  <a href="javascript:;">
                    <i class="fas fa-user-edit text-secondary text-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Profile"></i>
                  </a>
                </div>
              </div>
            </div>
            <!-- Information du user -->
            <div class="card-body p-3" id="profile-information">
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-8">
          <div class="card h-100">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0">Demandes en attente </h6>
            </div>
            <!-- List des demandes -->
            <div class="card-body p-3" id="liste-demande">
            </div>
          </div>
        </div>
        <div class="col-12 mt-4">
          <div class="card mb-4">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-1">Objets</h6>
            </div>
            
            <!-- Liste des objet du user connecter -->
            <div class="card-body p-3">
              <div class="row" id="liste"> 
              </div>
            </div>
            
            <!-- Formulaire de newObjet -->
            <div class="card-body p-3" id="form-newObjet">
            </div>

          </div>
        </div>
      </div>

      <!-- Footer -->
  <?php include __DIR__."/inc/footer.php"; ?>

    </div>
  </div>
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="fa fa-cog py-2"> </i>
    </a>
    <div class="card shadow-lg ">
      <div class="card-header pb-0 pt-3 ">
        <div class="float-start">
          <h5 class="mt-3 mb-0">Soft UI Configurator</h5>
          <p>See our dashboard options.</p>
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="fa fa-close"></i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="horizontal dark my-1">
      <div class="card-body pt-sm-3 pt-0">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm">Choose between 2 different sidenav types.</p>
        </div>
        <div class="d-flex">
          <button class="btn btn-primary w-100 px-3 mb-2 active" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
          <button class="btn btn-primary w-100 px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
        <!-- Navbar Fixed -->
        <div class="mt-3">
          <h6 class="mb-0">Navbar Fixed</h6>
        </div>
        <div class="form-check form-switch ps-0">
          <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
        </div>
        <hr class="horizontal dark my-sm-4">
        <a class="btn bg-gradient-dark w-100" href="#">Free Download</a>
        <a class="btn btn-outline-dark w-100" href="#">View documentation</a>
        <div class="w-100 text-center">
          <a class="github-button" href="#" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/soft-ui-dashboard on GitHub">Star</a>
          <h6 class="mt-3">Thank you for sharing!</h6>
          <a href="#" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
          </a>
          <a href="#" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
          </a>
        </div>
      </div>
    </div>
  </div>
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
  <script src="<?= BASE_URL ?>/traitement-js/methodes/met_objet.js"></script>
  <script src="<?= BASE_URL ?>/traitement-js/methodes/met_categorie.js"></script>
  <script src="<?= BASE_URL ?>/traitement-js/methodes/met_user.js"></script>
  <script src="<?= BASE_URL ?>/traitement-js/methodes/met_exchange.js"></script>

  <script src="<?= BASE_URL ?>/traitement-js/profile/profile.js"></script>

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="<?= BASE_URL ?>/assets/js/soft-ui-dashboard.min.js?v=1.1.0"></script>

  
</body>

</html>