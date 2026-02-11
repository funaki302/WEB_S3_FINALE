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
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/assets/img/favicon.png">
  <title>
    Soft UI Dashboard 3 by Creative Tim
  </title>
  <link href="#" rel="stylesheet" />
  <link href="#" rel="stylesheet" />
  <link href="#" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link id="pagestyle" href="/assets/css/soft-ui-dashboard.css?v=1.1.0" rel="stylesheet" />
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>

<body class="g-sidenav-show bg-gray-100">

  <?php include __DIR__ . "/inc/menu.php"; ?>

  <div class="main-content position-relative max-height-vh-100 h-100">
    <?php $page = 'Objets (JSON)'; ?>
    <?php include __DIR__ . "/inc/header.php"; ?>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0 p-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1">Affichage JSON</h6>
                  <p class="text-sm mb-0">Réponse brute de l'API des objets</p>
                </div>
                <a href="/objets" class="btn btn-outline-primary btn-sm mb-0">Retour liste</a>
              </div>
            </div>
            <div class="card-body p-3">
              <pre id="json" data-endpoint="/api/objets/others" class="bg-gray-100 border-radius-lg p-3" style="max-height: 70vh; overflow: auto;"></pre>
            </div>
          </div>
        </div>
      </div>

      <?php include __DIR__ . "/inc/footer.php"; ?>

    </div>
  </div>

  <script src="/assets/js/core/popper.min.js"></script>
  <script src="/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="/traitement-js/methodes/met_objet.js"></script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="/assets/js/soft-ui-dashboard.min.js?v=1.1.0"></script>

</body>

</html>
