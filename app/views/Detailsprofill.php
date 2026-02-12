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
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Soft UI Dashboard 3 by Creative Tim
  </title>
  <link href="#" rel="stylesheet" />
  <link href="#" rel="stylesheet" />
  <link href="#" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.1.0" rel="stylesheet" />
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
  <style>
    .objets-scroll {
      display: flex;
      gap: 1rem;
      overflow-x: auto;
      padding-bottom: 0.75rem;
      scroll-snap-type: x mandatory;
    }
    .objets-scroll::-webkit-scrollbar {
      height: 8px;
    }
    .objets-scroll::-webkit-scrollbar-thumb {
      background: #d1d5db;
      border-radius: 99px;
    }
    .objet-card {
      min-width: 280px;
      max-width: 280px;
      scroll-snap-align: start;
    }
    .objet-img {
      width: 100%;
      height: 170px;
      object-fit: cover;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">

  <?php include __DIR__ . "/inc/menu.php"; ?>

  <div class="main-content position-relative max-height-vh-100 h-100">
    <?php $page = 'Details Profil'; ?>
    <?php include __DIR__ . "/inc/header.php"; ?>

    <?php
      $user = $user_profile ?? null;
      $objets = $objets ?? [];
      $received = $received_stats ?? [];
      $sent = $sent_stats ?? [];

      $userId = (int)($user['id_user'] ?? 0);
      $name = (string)($user['name'] ?? '');
      $email = (string)($user['email'] ?? '');
      $phone = (string)($user['phone'] ?? '');
      $role = (string)($user['role'] ?? 'user');
      $status = strtolower((string)($user['status'] ?? 'inactive'));
      $joinDate = (string)($user['join_date'] ?? '');

      $badgeClass = $status === 'active' ? 'bg-gradient-success' : 'bg-gradient-secondary';
      $statusLabel = $status === 'active' ? 'Online' : 'Offline';

      $totalObjets = is_array($objets) ? count($objets) : 0;

      $rTotal = (int)($received['total_demandes'] ?? 0);
      $rAcc = (int)($received['total_accepter'] ?? 0);
      $rRef = (int)($received['total_refuser'] ?? 0);
      $rNon = (int)($received['total_non_reponse'] ?? 0);

      $sTotal = (int)($sent['total_demandes'] ?? 0);
      $sAcc = (int)($sent['total_accepter'] ?? 0);
      $sRef = (int)($sent['total_refuser'] ?? 0);
      $sNon = (int)($sent['total_non_reponse'] ?? 0);

      $calcRate = function($num, $den) {
        $den = (int)$den;
        if ($den <= 0) return 0;
        return (int)round(((int)$num * 100) / $den);
      };

      $rAccRate = $calcRate($rAcc, $rTotal);
      $rRefRate = $calcRate($rRef, $rTotal);
      $rNonRate = $calcRate($rNon, $rTotal);

      $sAccRate = $calcRate($sAcc, $sTotal);
      $sRefRate = $calcRate($sRef, $sTotal);
      $sNonRate = $calcRate($sNon, $sTotal);

      $defaultImg = '../assets/img/home-decor-1.jpg';
    ?>

    <div class="container-fluid py-4">

      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                  <div class="d-flex align-items-center">
                    <h5 class="mb-0 me-3">
                      <i class="fa fa-user me-2"></i>
                      <?= htmlspecialchars($name !== '' ? $name : ('User #' . (string)$userId)) ?>
                    </h5>
                    <span class="badge badge-sm <?= htmlspecialchars($badgeClass) ?>">
                      <i class="fa fa-circle-dot me-1"></i>
                      <?= htmlspecialchars($statusLabel) ?>
                    </span>
                  </div>
                  <div class="text-sm text-secondary mt-2">
                    <div class="mb-1"><i class="fa fa-envelope me-2"></i><?= htmlspecialchars($email !== '' ? $email : '—') ?></div>
                    <div class="mb-1"><i class="fa fa-phone me-2"></i><?= htmlspecialchars($phone !== '' ? $phone : '—') ?></div>
                    <div class="mb-1"><i class="fa fa-id-badge me-2"></i><?= htmlspecialchars($role) ?></div>
                    <div><i class="fa fa-calendar me-2"></i><?= htmlspecialchars($joinDate !== '' ? $joinDate : '—') ?></div>
                  </div>
                </div>

                <div class="text-end">
                  <a href="<?= BASE_URL ?>/tables" class="btn btn-outline-primary btn-sm mb-0">
                    <i class="fa fa-arrow-left me-1"></i>
                    Retour
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-header pb-0">
              <h6 class="mb-0"><i class="fa fa-boxes-stacked me-2"></i>Objets</h6>
            </div>
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-sm text-secondary">Total</div>
                  <div class="h4 mb-0"><?= htmlspecialchars((string)$totalObjets) ?></div>
                </div>
                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                  <i class="fa fa-cube text-white opacity-10" aria-hidden="true"></i>
                </div>
              </div>
              <hr class="horizontal dark my-3">
              <div class="text-xs text-secondary">Tu peux défiler les objets plus bas et proposer un échange.</div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-8 mb-4">
          <div class="card h-100">
            <div class="card-header pb-0">
              <h6 class="mb-0"><i class="fa fa-right-left me-2"></i>Demandes d'échange</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-md-6 mb-3">
                  <div class="card bg-gray-100 shadow-none mb-0">
                    <div class="card-body p-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <div class="text-sm text-secondary">Reçues</div>
                          <div class="h5 mb-0"><?= htmlspecialchars((string)$rTotal) ?></div>
                        </div>
                        <i class="fa fa-inbox text-secondary"></i>
                      </div>
                      <hr class="horizontal dark my-3">
                      <div class="row">
                        <div class="col-4 text-center">
                          <div class="text-xs text-secondary">Accept</div>
                          <div class="text-sm font-weight-bold"><?= htmlspecialchars((string)$rAccRate) ?>%</div>
                        </div>
                        <div class="col-4 text-center">
                          <div class="text-xs text-secondary">Refus</div>
                          <div class="text-sm font-weight-bold"><?= htmlspecialchars((string)$rRefRate) ?>%</div>
                        </div>
                        <div class="col-4 text-center">
                          <div class="text-xs text-secondary">NR</div>
                          <div class="text-sm font-weight-bold"><?= htmlspecialchars((string)$rNonRate) ?>%</div>
                        </div>
                      </div>
                      <div class="text-xs text-secondary mt-2">
                        <span class="me-2"><i class="fa fa-check text-success me-1"></i><?= htmlspecialchars((string)$rAcc) ?></span>
                        <span class="me-2"><i class="fa fa-xmark text-danger me-1"></i><?= htmlspecialchars((string)$rRef) ?></span>
                        <span><i class="fa fa-clock text-info me-1"></i><?= htmlspecialchars((string)$rNon) ?></span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-6 mb-3">
                  <div class="card bg-gray-100 shadow-none mb-0">
                    <div class="card-body p-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <div class="text-sm text-secondary">Envoyées</div>
                          <div class="h5 mb-0"><?= htmlspecialchars((string)$sTotal) ?></div>
                        </div>
                        <i class="fa fa-paper-plane text-secondary"></i>
                      </div>
                      <hr class="horizontal dark my-3">
                      <div class="row">
                        <div class="col-4 text-center">
                          <div class="text-xs text-secondary">Accept</div>
                          <div class="text-sm font-weight-bold"><?= htmlspecialchars((string)$sAccRate) ?>%</div>
                        </div>
                        <div class="col-4 text-center">
                          <div class="text-xs text-secondary">Refus</div>
                          <div class="text-sm font-weight-bold"><?= htmlspecialchars((string)$sRefRate) ?>%</div>
                        </div>
                        <div class="col-4 text-center">
                          <div class="text-xs text-secondary">NR</div>
                          <div class="text-sm font-weight-bold"><?= htmlspecialchars((string)$sNonRate) ?>%</div>
                        </div>
                      </div>
                      <div class="text-xs text-secondary mt-2">
                        <span class="me-2"><i class="fa fa-check text-success me-1"></i><?= htmlspecialchars((string)$sAcc) ?></span>
                        <span class="me-2"><i class="fa fa-xmark text-danger me-1"></i><?= htmlspecialchars((string)$sRef) ?></span>
                        <span><i class="fa fa-clock text-info me-1"></i><?= htmlspecialchars((string)$sNon) ?></span>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h6 class="mb-0"><i class="fa fa-cubes me-2"></i>Objets de l'utilisateur</h6>
                <div class="text-sm text-secondary">Défile horizontal</div>
              </div>
            </div>
            <div class="card-body">
              <?php if (!is_array($objets) || count($objets) === 0): ?>
                <div class="text-sm text-secondary">Aucun objet.</div>
              <?php else: ?>
                <div class="objets-scroll">
                  <?php foreach ($objets as $o): ?>
                    <?php
                      $oid = (int)($o['id_objet'] ?? 0);
                      $title = (string)($o['title'] ?? '');
                      $desc = (string)($o['description'] ?? '');
                      $cat = (string)($o['nom_categorie'] ?? '');
                      $prix = $o['prix_estime'] ?? null;
                      $img = (string)($o['image'] ?? '');
                      $imgUrl = $img !== '' ? ('/uploads/objets/' . ltrim($img, '/\\')) : $defaultImg;
                      $modalId = 'objetModal' . $oid;
                    ?>
                    <div class="card objet-card">
                      <div class="position-relative">
                        <img src="<?= htmlspecialchars($imgUrl) ?>" class="border-radius-lg objet-img" alt="objet<?= htmlspecialchars((string)$oid) ?>">
                      </div>
                      <div class="card-body p-3">
                        <p class="text-xs text-secondary mb-1"><?= htmlspecialchars($cat !== '' ? $cat : '—') ?></p>
                        <h6 class="mb-1"><?= htmlspecialchars($title !== '' ? $title : ('Objet #' . (string)$oid)) ?></h6>
                        <p class="text-sm text-secondary mb-2" style="min-height: 42px;">
                          <?= htmlspecialchars($desc !== '' ? $desc : '—') ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                          <div class="text-sm font-weight-bold">
                            <i class="fa fa-tag me-1"></i>
                            <?= htmlspecialchars($prix !== null && $prix !== '' ? ((string)$prix . ' Ar') : '—') ?>
                          </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                          <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#<?= htmlspecialchars($modalId) ?>">
                            <i class="fa fa-eye me-1"></i>
                            Voir
                          </button>
                          <a class="btn bg-gradient-primary btn-sm mb-0" href="<?= BASE_URL ?>/exchange?target=<?= htmlspecialchars((string)$oid) ?>">
                            <i class="fa fa-right-left me-1"></i>
                            Échanger
                          </a>
                        </div>
                      </div>
                    </div>

                    <div class="modal fade" id="<?= htmlspecialchars($modalId) ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h6 class="modal-title">
                              <i class="fa fa-cube me-2"></i>
                              <?= htmlspecialchars($title !== '' ? $title : ('Objet #' . (string)$oid)) ?>
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <img src="<?= htmlspecialchars($imgUrl) ?>" class="img-fluid border-radius-lg shadow mb-3" alt="objet<?= htmlspecialchars((string)$oid) ?>">
                            <div class="text-sm text-secondary mb-2"><i class="fa fa-layer-group me-2"></i><?= htmlspecialchars($cat !== '' ? $cat : '—') ?></div>
                            <div class="text-sm mb-2"><?= htmlspecialchars($desc !== '' ? $desc : '—') ?></div>
                            <div class="text-sm font-weight-bold"><i class="fa fa-tag me-2"></i><?= htmlspecialchars($prix !== null && $prix !== '' ? ((string)$prix . ' Ar') : '—') ?></div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Fermer</button>
                            <a class="btn bg-gradient-primary btn-sm mb-0" href="<?= BASE_URL ?>/exchange?target=<?= htmlspecialchars((string)$oid) ?>">
                              <i class="fa fa-right-left me-1"></i>
                              Échanger
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>

                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <?php include __DIR__ . "/inc/footer.php"; ?>

    </div>
  </div>

  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="../assets/js/soft-ui-dashboard.min.js?v=1.1.0"></script>

</body>

</html>
