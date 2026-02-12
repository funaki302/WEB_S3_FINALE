<?php

use app\models\User;

$connectedUserName = null;
$connectedUserRole = null;

if (!empty($_SESSION['user_id'])) {
    $userModel = new User();
    $connectedUser = $userModel->getById((int) $_SESSION['user_id']);
    if (!empty($connectedUser)) {
        $connectedUserName = $connectedUser['name'] ?? null;
        $connectedUserRole = $connectedUser['role'] ?? null;
    }
}

$isAdmin = ($connectedUserRole === 'admin');
$onlineDotClass = $isAdmin ? 'bg-gradient-danger' : 'bg-gradient-success';
?>

<style>
    html.dark-mode {
        color-scheme: dark;
    }
    html.dark-mode body {
        background-color: #0b0f17 !important;
        color: rgba(255, 255, 255, 0.88) !important;
    }
    body.dark-mode {
        background-color: #0b0f17 !important;
        color: rgba(255, 255, 255, 0.88) !important;
    }
    html.dark-mode .bg-gray-100 {
        background-color: #0b0f17 !important;
    }
    body.dark-mode .bg-gray-100 {
        background-color: #0b0f17 !important;
    }
    html.dark-mode .bg-white,
    html.dark-mode .bg-light {
        background-color: rgba(17, 24, 39, 0.80) !important;
    }
    body.dark-mode .bg-white,
    body.dark-mode .bg-light {
        background-color: rgba(17, 24, 39, 0.80) !important;
    }
    html.dark-mode .navbar-main {
        background: rgba(17, 24, 39, 0.60) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    body.dark-mode .navbar-main {
        background: rgba(17, 24, 39, 0.60) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    html.dark-mode .navbar-main .text-dark,
    html.dark-mode .navbar-main .text-body {
        color: rgba(255, 255, 255, 0.86) !important;
    }
    body.dark-mode .navbar-main .text-dark,
    body.dark-mode .navbar-main .text-body {
        color: rgba(255, 255, 255, 0.86) !important;
    }

    body.dark-mode .breadcrumb .breadcrumb-item,
    body.dark-mode .breadcrumb .breadcrumb-item a,
    body.dark-mode .breadcrumb .breadcrumb-item.active {
        color: rgba(255, 255, 255, 0.72) !important;
    }

    body.dark-mode h1,
    body.dark-mode h2,
    body.dark-mode h3,
    body.dark-mode h4,
    body.dark-mode h5,
    body.dark-mode h6 {
        color: rgba(255, 255, 255, 0.92) !important;
    }
    body.dark-mode .text-dark {
        color: rgba(255, 255, 255, 0.88) !important;
    }
    body.dark-mode .text-secondary {
        color: rgba(255, 255, 255, 0.60) !important;
    }
    body.dark-mode a {
        color: rgba(255, 255, 255, 0.86);
    }
    body.dark-mode a:hover {
        color: rgba(255, 255, 255, 0.96);
    }
    body.dark-mode .border,
    body.dark-mode .border-radius-lg,
    body.dark-mode .border-radius-md,
    body.dark-mode .border-radius-xl {
        border-color: rgba(255, 255, 255, 0.10) !important;
    }

    body.dark-mode .sidenav,
    body.dark-mode .navbar-vertical {
        background: rgba(17, 24, 39, 0.90) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.08);
    }
    body.dark-mode .navbar-vertical .nav-link,
    body.dark-mode .navbar-vertical .nav-link i {
        color: rgba(255, 255, 255, 0.72) !important;
    }
    body.dark-mode .navbar-vertical .nav-link.active,
    body.dark-mode .navbar-vertical .nav-link.active i {
        color: rgba(255, 255, 255, 0.92) !important;
    }
    body.dark-mode .navbar-vertical .nav-link.active {
        background: rgba(255, 255, 255, 0.06) !important;
    }

    body.dark-mode .navbar-vertical .icon,
    body.dark-mode .navbar-vertical .nav-link i,
    body.dark-mode .navbar-vertical .nav-link .ni,
    body.dark-mode .navbar-vertical .nav-link .fa {
        color: rgba(255, 255, 255, 0.82) !important;
        opacity: 0.95;
    }
    body.dark-mode .navbar-vertical .nav-link svg,
    body.dark-mode .navbar-vertical .nav-link svg * {
        fill: rgba(255, 255, 255, 0.82) !important;
        stroke: rgba(255, 255, 255, 0.82) !important;
    }
    body.dark-mode .navbar-vertical .nav-link img {
        filter: brightness(0) invert(1);
        opacity: 0.92;
    }

    body.dark-mode .navbar-vertical .icon {
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
    }
    body.dark-mode .navbar-vertical .navbar-brand span {
        color: rgba(255, 255, 255, 0.86) !important;
    }
    body.dark-mode .navbar-vertical .navbar-brand-img {
        filter: brightness(0) invert(1);
        opacity: 0.95;
    }

    body.dark-mode .card {
        background: rgba(17, 24, 39, 0.75) !important;
        color: rgba(255, 255, 255, 0.88) !important;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    body.dark-mode .card-header,
    body.dark-mode .card-footer {
        background: transparent !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    body.dark-mode .table {
        color: rgba(255, 255, 255, 0.84) !important;
    }
    body.dark-mode .table thead th {
        color: rgba(255, 255, 255, 0.60) !important;
    }
    body.dark-mode .table tbody tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }
    body.dark-mode .table td,
    body.dark-mode .table th {
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    body.dark-mode .form-control,
    body.dark-mode .form-select,
    body.dark-mode .input-group-text {
        background-color: rgba(15, 23, 42, 0.70) !important;
        border-color: rgba(255, 255, 255, 0.10) !important;
        color: rgba(255, 255, 255, 0.88) !important;
    }
    body.dark-mode .form-control::placeholder {
        color: rgba(255, 255, 255, 0.45) !important;
    }
    body.dark-mode .dropdown-menu {
        background: rgba(17, 24, 39, 0.95) !important;
        border-color: rgba(255, 255, 255, 0.10) !important;
    }
    body.dark-mode .dropdown-divider {
        border-top-color: rgba(255, 255, 255, 0.10) !important;
    }
    body.dark-mode .dropdown-item {
        color: rgba(255, 255, 255, 0.82) !important;
    }

    body.dark-mode .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.06) !important;
    }
    body.dark-mode .btn.btn-outline-primary {
        border-color: rgba(255, 255, 255, 0.22) !important;
        color: rgba(255, 255, 255, 0.84) !important;
    }

    body.dark-mode .btn.btn-outline-primary:hover {
        border-color: rgba(255, 255, 255, 0.30) !important;
        background: rgba(255, 255, 255, 0.08) !important;
    }
    body.dark-mode .nav-link.text-body,
    body.dark-mode .nav-link.text-body i {
        color: rgba(255, 255, 255, 0.82) !important;
    }
    body.dark-mode .shadow,
    body.dark-mode .shadow-sm,
    body.dark-mode .shadow-lg {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45) !important;
    }
    body.dark-mode .progress {
        background: rgba(255, 255, 255, 0.10) !important;
    }
    body.dark-mode .list-group-item {
        background: rgba(17, 24, 39, 0.70) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: rgba(255, 255, 255, 0.82) !important;
    }
    body.dark-mode .fixed-plugin .card {
        background: rgba(17, 24, 39, 0.92) !important;
    }
    body.dark-mode .page-header .mask {
        opacity: 0.75 !important;
    }

    body.dark-mode .modal-content {
        background: rgba(17, 24, 39, 0.96) !important;
        color: rgba(255, 255, 255, 0.90) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
    }
    body.dark-mode .modal-header,
    body.dark-mode .modal-footer {
        border-color: rgba(255, 255, 255, 0.10) !important;
    }
    body.dark-mode .modal-title {
        color: rgba(255, 255, 255, 0.92) !important;
    }
    body.dark-mode .modal .text-dark {
        color: rgba(255, 255, 255, 0.88) !important;
    }
    body.dark-mode .modal .text-secondary {
        color: rgba(255, 255, 255, 0.52) !important;
    }
    body.dark-mode .modal hr,
    body.dark-mode .modal .horizontal {
        border-color: rgba(255, 255, 255, 0.10) !important;
        opacity: 1;
    }
    body.dark-mode .modal .btn-close {
        filter: invert(1);
        opacity: 0.85;
    }
    body.dark-mode .modal .btn-close:hover {
        opacity: 1;
    }

    body.dark-mode .swal2-popup {
        background: rgba(17, 24, 39, 0.96) !important;
        color: rgba(255, 255, 255, 0.90) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
    }
    body.dark-mode .swal2-title {
        color: rgba(255, 255, 255, 0.92) !important;
    }
    body.dark-mode .swal2-html-container {
        color: rgba(255, 255, 255, 0.82) !important;
    }
    body.dark-mode .swal2-icon {
        filter: brightness(1.05);
    }

    #dark-mode-toggle .theme-icon {
        width: 16px;
        height: 16px;
        display: inline-block;
        vertical-align: -2px;
        filter: none;
        opacity: 0.92;
    }
    body.dark-mode #dark-mode-toggle .theme-icon {
        filter: invert(1) drop-shadow(0 1px 1px rgba(0,0,0,0.35));
        opacity: 0.95;
    }
</style>

<script>
    (function () {
        try {
            var stored = localStorage.getItem('theme');
            if (stored === 'dark') {
                document.documentElement.classList.add('dark-mode');
                if (document.body) {
                    document.body.classList.add('dark-mode');
                }
            }
        } catch (e) {
            // ignore
        }
    })();
</script>
<br>
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= $page ?></li>
            </ol>
            <h6 class="font-weight-bolder mb-0"><?= $page ?></h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <?php if (!empty($connectedUserName)) : ?>
                <div class="d-flex align-items-center mx-auto">
                    <span class="d-inline-block me-2 <?= $onlineDotClass ?>" style="width: 8px; height: 8px; border-radius: 50%;"></span>
                    <span class="text-sm text-dark font-weight-bold mb-0"><?= htmlspecialchars($connectedUserName) ?></span>
                    <span class="text-xs text-secondary ms-2">En ligne</span>
                </div>
            <?php endif; ?>
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <div class="input-group">
                   <!--  <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span> -->
               <!--      <input type="text" class="form-control" placeholder="Type here..."> -->
                </div>
            </div>
            <ul class="navbar-nav  justify-content-end">
                <li class="nav-item d-flex align-items-center">
                    <a class="btn btn-outline-primary btn-sm mb-0 me-3" target="_blank"
                        href="#">Online Builder</a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <button type="button" id="dark-mode-toggle" class="btn btn-outline-primary btn-sm mb-0 me-3" aria-label="Toggle dark mode">
                        <img src="/assets/icons/theme-icon/moon-stars-fill.svg" alt="" class="theme-icon me-1" id="dark-mode-icon" />
                        <span class="d-sm-inline d-none" id="dark-mode-label">Dark</span>
                    </button>
                </li>

                <li class="nav-item d-flex align-items-center">
                    <a class="btn btn-outline-primary btn-sm mb-0 me-3" href="/objets">Objets</a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <a href="/logout" class="nav-link text-body font-weight-bold px-0">
                        <i class="fa fa-user me-sm-1"></i>
                        <span class="d-sm-inline d-none">Sign Out</span>
                    </a>
                </li>
                <li class="nav-item px-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0">
                        <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
                    </a>
                </li>
                <li class="nav-item dropdown pe-2 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell cursor-pointer"></i>
                    </a>
                    <ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4"
                        aria-labelledby="dropdownMenuButton">
                        <li class="mb-2">
                            <a class="dropdown-item border-radius-md" href="javascript:;">
                                <div class="d-flex py-1">
                                    <div class="my-auto">
                                        <img src="/assets/img/team-2.jpg" class="avatar avatar-sm  me-3 ">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="text-sm font-weight-normal mb-1">
                                            <span class="font-weight-bold">New message</span> from Laur
                                        </h6>
                                        <p class="text-xs text-secondary mb-0 ">
                                            <i class="fa fa-clock me-1"></i>
                                            13 minutes ago
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="dropdown-item border-radius-md" href="javascript:;">
                                <div class="d-flex py-1">
                                    <div class="my-auto">
                                        <img src="/assets/img/small-logos/logo-spotify.svg"
                                            class="avatar avatar-sm bg-gradient-dark  me-3 ">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="text-sm font-weight-normal mb-1">
                                            <span class="font-weight-bold">New album</span> by Travis Scott
                                        </h6>
                                        <p class="text-xs text-secondary mb-0 ">
                                            <i class="fa fa-clock me-1"></i>
                                            1 day
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item border-radius-md" href="javascript:;">
                                <div class="d-flex py-1">
                                    <div class="avatar avatar-sm bg-gradient-secondary  me-3  my-auto">
                                        <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1"
                                            xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <title>credit-card</title>
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF"
                                                    fill-rule="nonzero">
                                                    <g transform="translate(1716.000000, 291.000000)">
                                                        <g transform="translate(453.000000, 454.000000)">
                                                            <path class="color-background"
                                                                d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z"
                                                                opacity="0.593633743"></path>
                                                            <path class="color-background"
                                                                d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z">
                                                            </path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="text-sm font-weight-normal mb-1">
                                            Payment successfully completed
                                        </h6>
                                        <p class="text-xs text-secondary mb-0 ">
                                            <i class="fa fa-clock me-1"></i>
                                            2 days
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    (function () {
        function updateToggleUi(isDark) {
            var icon = document.getElementById('dark-mode-icon');
            var label = document.getElementById('dark-mode-label');
            if (icon && icon.tagName && icon.tagName.toLowerCase() === 'img') {
                icon.setAttribute('src', isDark
                    ? '/assets/icons/theme-icon/brightness-high-fill.svg'
                    : '/assets/icons/theme-icon/moon-stars-fill.svg'
                );
            }
            if (label) {
                label.textContent = isDark ? 'Light' : 'Dark';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('dark-mode-toggle');
            if (!btn) return;

            var isDark = document.body.classList.contains('dark-mode') || document.documentElement.classList.contains('dark-mode');
            try {
                var stored = localStorage.getItem('theme');
                if (stored === 'dark') {
                    document.documentElement.classList.add('dark-mode');
                    document.body.classList.add('dark-mode');
                    isDark = true;
                }
            } catch (e) {
                // ignore
            }
            updateToggleUi(isDark);

            btn.addEventListener('click', function () {
                var nowDark = !(document.body.classList.contains('dark-mode') || document.documentElement.classList.contains('dark-mode'));
                document.documentElement.classList.toggle('dark-mode', nowDark);
                document.body.classList.toggle('dark-mode', nowDark);
                updateToggleUi(nowDark);
                try {
                    localStorage.setItem('theme', nowDark ? 'dark' : 'light');
                } catch (e) {
                    // ignore
                }
            });
        });
    })();
</script>

<!-- Les choses dont les scripts ont besoin -->
<meta name="user-id" content="<?= htmlspecialchars($_SESSION['user_id'] ?? 0) ?>">