<?php
require __DIR__ . '/auth.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include 'koneksi.php';

$id_user = $_SESSION['user_id'];

$queryUser = mysqli_query($conn, "
    SELECT username, nama, level
    FROM users
    WHERE id = '$id_user'
");

$user = mysqli_fetch_assoc($queryUser);

if (isset($_POST['update_account'])) {
    requireCsrfToken();

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $password_konfirmasi = $_POST['password_konfirmasi'];

    mysqli_query($conn, "
        UPDATE users 
        SET nama = '$nama' 
        WHERE id = '$id_user'
    ");
    $_SESSION['nama'] = $nama;

    if (!empty($password_baru)) {

        $queryPass = mysqli_query($conn, "
            SELECT password FROM users WHERE id = '$id_user'
        ");
        $dataPass = mysqli_fetch_assoc($queryPass);

        if (!password_verify($password_lama, $dataPass['password'])) {
            echo "<script>
                alert('Password lama salah!');
                window.history.back();
            </script>";
            exit;
        }

        if ($password_baru !== $password_konfirmasi) {
            echo "<script>
                alert('Konfirmasi password tidak sesuai!');
                window.history.back();
            </script>";
            exit;
        }

        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

        mysqli_query($conn, "
            UPDATE users 
            SET password = '$password_hash' 
            WHERE id = '$id_user'
        ");
    }

    echo "<script>
        alert('Account berhasil diperbarui');
        window.location.href = 'dashboard.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="theme_ocean">
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title>SI-CAPAT (Sistem Informasi Cepat Analisis Pemantauan Pengawasan Terintegrasi)</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/logo_sicapat.png">
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/dataTables.bs5.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/select2-theme.min.css">
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css">
    <!--! END: Custom CSS-->
    <!--! HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries !-->
    <!--! WARNING: Respond.js doesn"t work if you view the page via file: !-->
    <!--[if lt IE 9]>
            <script src="https:oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https:oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    <style>
        /* ================= GLOBAL ================= */
        html {
            height: 100%;
            scrollbar-gutter: stable;
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
        }

        body.modal-open {
            padding-right: 0 !important;
        }

        /* ================= LAYOUT ================= */
        .nxl-container {
            min-height: calc(100vh - 64px);
            display: flex;
            flex-direction: column;
        }

        .nxl-content {
            flex: 1;
            height: calc(100vh - 64px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nxl-footer {
            flex-shrink: 0;
        }

        /* ================= LOGO ================= */
        .b-brand {
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .b-brand .logo {
            height: auto;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        .logo-lg {
            display: block;
            width: 200px;
            max-width: 100%;
            max-height: 80px;
        }

        .logo-sm {
            display: none;
            width: 36px;
            max-width: 36px;
            max-height: 36px;
        }

        .sidebar-collapsed .b-brand {
            padding: 0;
        }

        .sidebar-collapsed .logo-lg {
            display: none;
        }

        .sidebar-collapsed .logo-sm {
            display: block;
            margin: auto;
        }

        /* ================= PROGRESS ================= */
        .circle-progress-value {
            stroke-width: 6px;
            stroke-linecap: round;
        }

        .circle-progress-circle {
            stroke-width: 3px;
            stroke: #e5e7eb;
        }

        .circle-danger .circle-progress-value {
            stroke: #ef4444;
        }

        .circle-warning .circle-progress-value {
            stroke: #facc15;
        }

        .circle-primary .circle-progress-value {
            stroke: #3454d1;
        }

        .circle-success .circle-progress-value {
            stroke: #22c55e;
        }

        .sales-progress-1a .circle-progress-value {
            stroke: #3454d1;
        }

        .sales-progress-1a .circle-progress-circle {
            stroke: #e3e7f9;
        }

        /* ================= TABLE ================= */
        #projectList {
            width: 100%;
            table-layout: auto;
        }

        #projectList th,
        #projectList td {
            vertical-align: middle;
        }

        #projectList td {
            overflow-wrap: break-word;
        }

        #projectList td:nth-child(1),
        #projectList th:nth-child(1) {
            width: 50px;
        }

        #projectList td:nth-child(2),
        #projectList th:nth-child(2) {
            width: 200px;
            min-width: 160px;
            white-space: nowrap;
        }

        #projectList td:nth-child(3),
        #projectList th:nth-child(3) {
            min-width: 250px;
            max-width: 480px;
            white-space: normal;
        }

        #projectList td:nth-child(4),
        #projectList th:nth-child(4) {
            min-width: 180px;
            width: 120px;
            text-align: center;
            white-space: normal;
        }

        #projectList td:nth-child(5),
        #projectList th:nth-child(5),
        #projectList td:nth-child(6),
        #projectList th:nth-child(6) {
            width: 140px;
        }

        #projectList td:nth-child(7),
        #projectList th:nth-child(7) {
            width: 130px;
            text-align: center;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* ================= MODAL ================= */
        .modal-dialog-scrollable .modal-content {
            max-height: 90vh;
        }

        .modal-dialog-scrollable .modal-body {
            max-height: calc(90vh - 170px);
            overflow-y: auto !important;
        }

        .modal-dialog-scrollable .modal-footer {
            position: sticky;
            bottom: 0;
            background: #fff;
            z-index: 1055;
            border-top: 1px solid #dee2e6;
        }

        /* ================= DETAIL MODAL ================= */
        #modalDetailLHP td,
        #modalDetailLHP th,
        #modalDetailLHP p,
        #modalDetailLHP li,
        #modalDetailLHP .text-muted {
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        #modalDetailLHP table {
            table-layout: fixed;
            width: 100%;
        }

        #modalDetailLHP th {
            width: 180px;
            vertical-align: top;
        }

        #modalDetailLHP .card {
            border-radius: 10px;
        }

        #modalDetailLHP .card-header {
            border-radius: 12px 12px 0 0;
        }

        #modalDetailLHP .btn-primary {
            border-radius: 6px;
        }

        #modalDetailLHP .badge {
            font-size: 0.75rem;
        }

        /* ================= FILE ================= */
        .file-download {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 14px;
            border: 1px dashed #d0d7e2;
            border-radius: 8px;
            background-color: #f8faff;
        }

        .file-download .file-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #495057;
            word-break: break-all;
        }

        .file-download .file-info i {
            font-size: 20px;
            color: #dc3545;
        }

        .file-download .btn-download {
            white-space: nowrap;
        }

        .file-name {
            font-size: 12px;
            color: #6c757d;
            word-break: break-all;
        }

        /* ================= CARD ================= */
        .detail-card,
        .rekom-card,
        .verif-card {
            border-radius: 12px;
            background: #f8fafc;
            transition: all .2s ease;
        }

        .file-box {
            background: #eef2ff;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .detail-header {
            border-bottom: 2px solid #e9ecef;
        }

        .detail-body-line {
            border-top: 1px solid #e9ecef;
        }

        /* ================= COUNTDOWN ================= */
        @keyframes blinkRed {

            0%,
            100% {
                background-color: #f8d7da;
            }

            50% {
                background-color: #f1aeb5;
            }
        }

        .countdown {
            white-space: nowrap;
            display: inline-block;
            font-weight: 500;
        }

        .countdown.alert-deadline,
        .countdown.blink {
            animation: blinkRed 1s infinite;
        }

        .countdown-box {
            display: inline-block;
            min-width: 130px;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
        }

        .alert-deadline {
            background: #f8d7da;
        }

        /* ================= UTIL ================= */
        .wrap-text {
            word-break: break-word;
        }

        .form-select {
            font-family: var(--bs-body-font-family);
            font-size: var(--bs-body-font-size);
            font-weight: var(--bs-body-font-weight);
        }

        /* ================= DROPDOWN ================= */
        .nxl-user-dropdown,
        .dropdown-menu {
            pointer-events: auto;
        }

        .dropdown-menu {
            margin-top: 0;
        }

        #userDropdownMenu {
            display: none;
        }

        #userDropdownMenu.show {
            display: block;
        }

        /* ================= EFFECT ================= */
        .rek-group .bg-light,
        .detail-card .file-box,
        #detail-wrapper .bg-light {
            transition: all .2s ease;
        }

        .rek-group:hover .bg-light {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        #detail-wrapper .bg-light:hover {
            background-color: #f1f5ff !important;
        }

        .temuan-item,
        .rek-group {
            will-change: transform, opacity;
        }

        /* ================= PROGRESS BAR ================= */
        .progress {
            background-color: #f1f3f5;
        }

        .progress-bar {
            transition: width .4s ease;
        }

        td .badge {
            font-size: 11px;
            letter-spacing: .3px;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 768px) {

            #projectList th,
            #projectList td {
                font-size: 12px;
                padding: 8px;
            }

            #projectList td:nth-child(3) {
                min-width: 200px;
            }

            #projectList td:nth-child(4) {
                min-width: 140px;
            }

            #projectList td:last-child {
                min-width: 120px;
            }

            .progress {
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <!--! ================================================================ !-->
    <!--! [Start] Navigation Manu !-->
    <!--! ================================================================ !-->
    <nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="dashboard.php" class="b-brand">
                    <!-- ========   change your logo here   ============ -->
                    <img src="assets/images/logo_sicapat.png" alt="" class="logo logo-lg" />
                    <img src="assets/images/logo_sicapat.png" alt="" class="logo logo-sm" />
                </a>
            </div>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Navigation</label>
                    </li>
                    <li class="nxl-item">
                        <a href="dashboard.php" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <a href="lhp.php" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Laporan Hasil Pengawasan</span>
                        </a>
                    </li>
                    <?php if ($_SESSION['level'] === 'admin'): ?>
                        <li class="nxl-item">
                            <a href="akun.php" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-users"></i></span>
                                <span class="nxl-mtext">Accounts</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!--! ================================================================ !-->
    <!--! [End]  Navigation Manu !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! [Start] Header !-->
    <!--! ================================================================ !-->
    <header class="nxl-header">
        <div class="header-wrapper">
            <!--! [Start] Header Left !-->
            <div class="header-left d-flex align-items-center gap-4">
                <!--! [Start] nxl-head-mobile-toggler !-->
                <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
                <!--! [Start] nxl-head-mobile-toggler !-->
                <!--! [Start] nxl-navigation-toggle !-->
                <div class="nxl-navigation-toggle">
                    <a href="javascript:void(0);" id="menu-mini-button">
                        <i class="feather-align-left"></i>
                    </a>
                    <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                        <i class="feather-arrow-right"></i>
                    </a>
                </div>
                <!--! [End] nxl-navigation-toggle !-->
                <!--! [Start] nxl-lavel-mega-menu-toggle !-->
                <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                    <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open">
                        <i class="feather-align-left"></i>
                    </a>
                </div>
                <!--! [End] nxl-lavel-mega-menu-toggle !-->
                <!--! [Start] nxl-lavel-mega-menu !-->
                <div class="nxl-drp-link nxl-lavel-mega-menu">
                    <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                        <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    <!--! [Start] nxl-lavel-mega-menu-wrapper !-->
                    <div class="nxl-lavel-mega-menu-wrapper d-flex gap-3">
                    </div>
                    <!--! [End] nxl-lavel-mega-menu-wrapper !-->
                </div>
                <!--! [End] nxl-lavel-mega-menu !-->
            </div>
            <!--! [End] Header Left !-->
            <!--! [Start] Header Right !-->
            <div class="header-right ms-auto">
                <div class="d-flex align-items-center">
                    <div class="nxl-h-item d-none d-sm-flex">
                        <div class="full-screen-switcher">
                            <a href="javascript:void(0);" class="nxl-head-link me-0"
                                onclick="$('body').fullScreenHelper('toggle');">
                                <i class="feather-maximize maximize"></i>
                                <i class="feather-minimize minimize"></i>
                            </a>
                        </div>
                    </div>
                    <div class="nxl-h-item dark-light-theme">
                        <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                            <i class="feather-moon"></i>
                        </a>
                        <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                            <i class="feather-sun"></i>
                        </a>
                    </div>
                    <div class="dropdown nxl-h-item user-dropdown-click">
                        <a href="javascript:void(0);" id="userDropdownToggle">
                            <img src="assets/images/logo_insp_abbr.png" alt="user-image"
                                class="img-fluid user-avtar me-0" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown"
                            id="userDropdownMenu">
                            <div class="dropdown-header">
                                <div class="d-flex align-items-center">
                                    <img src="assets/images/logo_insp_abbr.png" alt="user-image"
                                        class="img-fluid user-avtar" />
                                    <div>
                                        <h6 class="text-dark mb-0">
                                            <?php echo htmlspecialchars($_SESSION['nama']); ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#modalAccountDetail">
                                <i class="feather-user"></i>
                                <span>Account Details</span>
                            </a>
                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#modalAccountSetting">
                                <i class="feather-settings"></i>
                                <span>Account Settings</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item">
                                <i class="feather-log-out"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!--! [End] Header Right !-->
        </div>
    </header>
    <!--! ================================================================ !-->
    <!--! [End] Header !-->
    <!--! ================================================================ !-->
    <!-- ModalAccountDetail -->
    <div class="modal fade" id="modalAccountDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="feather-user me-2"></i>Account Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="140">Nama</th>
                            <td>: <?= htmlspecialchars($user['nama']); ?></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>: <?= htmlspecialchars($user['username']); ?></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td class="text-capitalize">: <?= $user['level']; ?></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- ModalAccountSetting -->
    <div class="modal fade" id="modalAccountSetting" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="feather-settings me-2"></i>Account Settings
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control"
                                value="<?= htmlspecialchars($user['nama']); ?>" required>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="password_lama" class="form-control"
                                placeholder="Masukkan password lama (kosongkan jika tidak diubah)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control"
                                placeholder="Kosongkan jika tidak diubah">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_konfirmasi" class="form-control"
                                placeholder="Ulangi password baru">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" name="update_account" class="btn btn-primary">
                            <i class="feather-save me-2"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('userDropdownToggle');
            const menu = document.getElementById('userDropdownMenu');

            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('show');
            });

            document.addEventListener('click', function() {
                menu.classList.remove('show');
            });

            menu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>