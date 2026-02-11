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
  <!--     Fonts and icons     -->
  <link href="#" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="#" rel="stylesheet" />
  <link href="#" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- CSS Files -->
  <link id="pagestyle" href="/assets/css/soft-ui-dashboard.css?v=1.1.0" rel="stylesheet" />
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>

<body class="g-sidenav-show  bg-gray-100">

  <!-- Menu -->
  <?php include __DIR__."/inc/menu.php"; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
     <?php $page = 'Tables'; ?>
    <?php include __DIR__."/inc/header.php"; ?>
    <!-- End Navbar -->

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <?php
                $filters = $filters ?? [];
                $searchVal = (string)($filters['search'] ?? '');
                $roleVal = (string)($filters['role'] ?? '');
              ?>
              <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                  <h6 class="mb-1">Tous Les Utilisateurs sur le plateforme</h6>
                  <p class="text-sm text-secondary mb-0">
                    <i class="fa fa-circle-info me-1"></i>
                    Clique sur l’avatar ou le nom pour voir le profil.
                  </p>
                </div>
                <form method="get" action="/tables" class="d-flex align-items-center gap-2" style="min-width: 320px;">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="form-control" id="users-search" name="search" placeholder="Rechercher un nom..." value="<?= htmlspecialchars($searchVal) ?>">
                  </div>
                  <select class="form-select form-select-sm" id="users-role" name="role" style="max-width: 150px;">
                    <option value="" <?= $roleVal === '' ? 'selected' : '' ?>>Tous</option>
                    <option value="user" <?= $roleVal === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $roleVal === 'admin' ? 'selected' : '' ?>>Admin</option>
                  </select>
                  <button type="submit" class="btn btn-outline-primary btn-sm mb-0">
                    <i class="fa fa-filter me-1"></i>
                    Filtrer
                  </button>
                </form>
              </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Author</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Function</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Employed</th>
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody id="users-tbody">
                    <?php $users = $users ?? []; ?>
                    <?php foreach ($users as $user): ?>
                    <?php
                      $idUser = (int)($user['id_user'] ?? 0);
                      $name = (string)($user['name'] ?? '');
                      $email = (string)($user['email'] ?? '');
                      $role = (string)($user['role'] ?? 'user');
                      $phone = (string)($user['phone'] ?? '');
                      $status = strtolower((string)($user['status'] ?? 'inactive'));
                      $joinDate = (string)($user['join_date'] ?? '');
                      $badgeClass = $status === 'active' ? 'bg-gradient-success' : 'bg-gradient-secondary';
                      $statusLabel = $status === 'active' ? 'Online' : 'Offline';
                      $teamImages = [
                        '../assets/img/team-2.jpg',
                        '../assets/img/team-3.jpg',
                        '../assets/img/team-4.jpg',
                      ];
                      $avatar = $teamImages[$idUser % count($teamImages)];
                      $formattedJoinDate = '';
                      if ($joinDate !== '') {
                        $ts = strtotime($joinDate);
                        $formattedJoinDate = $ts ? date('d/m/y', $ts) : $joinDate;
                      }
                    ?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>

                            <a href="/detailsprofill/<?= htmlspecialchars((string)$idUser) ?>" class="text-decoration-none">
                              <img src="<?= htmlspecialchars($avatar) ?>" class="avatar avatar-sm me-3" alt="user<?= htmlspecialchars((string)$idUser) ?>">
                            </a>

                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">
                              <a href="/detailsprofill/<?= htmlspecialchars((string)$idUser) ?>" class="text-dark text-decoration-none">
                                <?= htmlspecialchars($name) ?>
                              </a>
                            </h6>
                            <p class="text-xs text-secondary mb-0"><?= htmlspecialchars($email) ?></p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars(ucfirst($role)) ?></p>
                        <p class="text-xs text-secondary mb-0"><?= htmlspecialchars($phone !== '' ? $phone : '—') ?></p>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm <?= htmlspecialchars($badgeClass) ?>"><?= htmlspecialchars($statusLabel) ?></span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold"><?= htmlspecialchars($formattedJoinDate !== '' ? $formattedJoinDate : '—') ?></span>
                      </td>
                      <td class="align-middle">
                        <a href="javascript:;" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                          Edit
                        </a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Listes des demandes d'echange et leurs status</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center justify-content-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Project</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Budget</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Completion</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $exchanges = $exchanges ?? []; ?>
                    <?php foreach ($exchanges as $ex): ?>
                    <?php
                      $idExchange = (int)($ex['id_echange'] ?? 0);
                      $status = strtolower((string)($ex['status'] ?? ''));
                      $title = (string)($ex['objet_requise_title'] ?? '');
                      $prix = $ex['objet_requise_prix'] ?? null;
                      $image = (string)($ex['objet_requise_image'] ?? '');
                      $imgUrl = $image !== '' ? ('/uploads/objets/' . ltrim($image, '/\\')) : '../assets/img/home-decor-1.jpg';
                      $badgeClass = 'bg-gradient-secondary';
                      $statusLabel = $status;
                      $progressClass = 'bg-gradient-secondary';
                      $progressPct = 20;
                      if ($status === 'attente') {
                        $badgeClass = 'bg-gradient-info';
                        $statusLabel = 'attente';
                        $progressClass = 'bg-gradient-info';
                        $progressPct = 50;
                      } elseif ($status === 'accepter') {
                        $badgeClass = 'bg-gradient-success';
                        $statusLabel = 'accepter';
                        $progressClass = 'bg-gradient-success';
                        $progressPct = 100;
                      } elseif ($status === 'refuser') {
                        $badgeClass = 'bg-gradient-danger';
                        $statusLabel = 'refuser';
                        $progressClass = 'bg-gradient-danger';
                        $progressPct = 100;
                      }
                    ?>
                    <tr>
                      <td>
                        <div class="d-flex px-2">
                          <div>

                            <img src="<?= htmlspecialchars($imgUrl) ?>" class="avatar avatar-sm rounded-circle me-2" alt="exchange<?= htmlspecialchars((string)$idExchange) ?>">
                          </div>
                          <div class="my-auto">
                            <h6 class="mb-0 text-sm">
                              <i class="fa fa-right-left me-1"></i>
                              <?= htmlspecialchars($title !== '' ? $title : ('Demande #' . $idExchange)) ?>
                            </h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-sm font-weight-bold mb-0"><?= htmlspecialchars($prix !== null && $prix !== '' ? ($prix . ' Ar') : '—') ?></p>
                      </td>
                      <td>
                        <span class="badge badge-sm <?= htmlspecialchars($badgeClass) ?>">
                          <i class="fa fa-circle-dot me-1"></i>
                          <?= htmlspecialchars($statusLabel) ?>
                        </span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <span class="me-2 text-xs font-weight-bold"><?= htmlspecialchars((string)$progressPct) ?>%</span>
                          <div>
                            <div class="progress">

                              <div class="progress-bar <?= htmlspecialchars($progressClass) ?>" role="progressbar" aria-valuenow="<?= htmlspecialchars((string)$progressPct) ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= htmlspecialchars((string)$progressPct) ?>%;"></div>

                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button class="btn btn-link text-secondary mb-0">
                          <i class="fa fa-ellipsis-v text-xs"></i>
                        </button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
  <?php include __DIR__."/inc/footer.php"; ?>

    </div>
  </main>
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
  <script src="/assets/js/core/popper.min.js"></script>
  <script src="/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/soft-ui-dashboard.min.js?v=1.1.0"></script>
  <script src="/traitement-js/users_table_search.js"></script>

</body>

</html>