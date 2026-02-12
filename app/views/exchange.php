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
    .my-objets-scroll {
      max-height: 70vh;
      overflow-y: auto;
    }
    .selectable-objet {
      cursor: pointer;
    }
    .selectable-objet.selected {
      outline: 2px solid #cb0c9f;
      outline-offset: 2px;
      border-radius: 1rem;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">

  <?php include __DIR__ . "/inc/menu.php"; ?>

  <div class="main-content position-relative max-height-vh-100 h-100">
    <?php $page = 'Exchange'; ?>
    <?php include __DIR__ . "/inc/header.php"; ?>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0 p-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1">Proposer un échange</h6>
                  <p class="text-sm mb-0">Choisis un de tes objets pour l'échanger avec l'objet sélectionné</p>
                </div>
                <a href="<?= BASE_URL ?>/objets" class="btn btn-outline-primary btn-sm mb-0">Retour liste</a>
              </div>
            </div>
            <div class="card-body p-3">
              <div class="row align-items-stretch">
                <div class="col-12 col-lg-5 mb-3 mb-lg-0">
                  <div class="card h-100">
                    <div class="card-header pb-0">
                      <h6 class="mb-0">Objet demandé</h6>
                    </div>
                    <div class="card-body" id="target-objet">
                      <div class="text-sm text-secondary">Chargement...</div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-lg-2 d-flex align-items-center justify-content-center mb-3 mb-lg-0">
                  <div class="text-center">
                    <img src="<?= BASE_URL ?>/assets/icons/dashbord-icon/exchange.svg" alt="Exchange" style="width: 56px; height: 56px;" />
                    <div class="text-sm text-secondary mt-2">Trade</div>
                  </div>
                </div>

                <div class="col-12 col-lg-5">
                  <div class="card h-100">
                    <div class="card-header pb-0">
                      <h6 class="mb-0">Tes objets</h6>
                    </div>
                    <div class="card-body my-objets-scroll" id="my-objets"></div>
                    <div class="card-footer pt-0">
                      <button id="btn-propose" class="btn bg-gradient-primary w-100 mb-0" type="button" disabled>Proposer l'échange</button>
                      <div id="exchange-msg" class="mt-3"></div>
                    </div>
                  </div>
                </div>
              </div>

              <hr class="horizontal dark my-4">

              <div class="row">
                <div class="col-12">
                  <h6 class="mb-2">Demandes reçues</h6>
                  <div id="received-exchanges"></div>
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
  <script src="<?= BASE_URL ?>/traitement-js/methodes/met_exchange.js"></script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/soft-ui-dashboard.min.js?v=1.1.0"></script>

</body>

</html>
