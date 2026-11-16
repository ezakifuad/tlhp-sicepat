<?php include 'header.php';
?>
<!--! ================================================================ !-->
<!--! [Start] Main Content !-->
<!--! ================================================================ !-->
<main class="nxl-container">
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Laporan Hasil Pengawasan</h5>
                </div>
            </div>
        </div>
        <!-- [ page-header ] end -->
        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="row">
                <!-- [Project Remainders] start -->
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title"></h5>
                            <div class="card-header-action">
                                <div class="card-header-btn">
                                    <div data-bs-toggle="tooltip" title="Refresh">
                                        <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                            data-bs-toggle="refresh"> </a>
                                    </div>
                                    <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                                        <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                            data-bs-toggle="expand"> </a>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="avatar-text avatar-sm"
                                        data-bs-toggle="dropdown" data-bs-offset="25, 25">
                                        <div data-bs-toggle="tooltip">
                                            <i class="feather-more-vertical"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body custom-card-action p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Jumlah LHP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ([2026, 2025, 2024, 2023, 2022] as $t):

                                            $T_LHP = "lhp_$t";
                                            $T_TEM = "temuan_$t";
                                            $T_REK = "rekomendasi_$t";
                                            $total = (int)$conn->query("
            SELECT COUNT(*) as jml
            FROM $T_LHP
        ")->fetch_assoc()['jml'];
                                            $selesai = (int)$conn->query("
            SELECT COUNT(DISTINCT l.id_lhp) AS total
            FROM $T_LHP l
            WHERE EXISTS (
                SELECT 1 
                FROM $T_TEM t 
                WHERE t.id_lhp = l.id_lhp
            )
            AND NOT EXISTS (
                SELECT 1
                FROM $T_TEM t
                JOIN $T_REK r ON r.id_temuan = t.id_temuan
                WHERE t.id_lhp = l.id_lhp
                AND (
                    r.status_verifikasi != 'sesuai'
                    OR r.status_verifikasi IS NULL
                    OR r.rekomendasi = '-'
                )
            )
        ")->fetch_assoc()['total'];

                                        ?>
                                            <tr>
                                                <td class="align-middle">
                                                    <div class="hstack gap-2 align-items-center">
                                                        <span class="wd-10 ht-10 bg-gray-400 rounded-circle d-inline-block me-2"></span>

                                                        <div class="border-3 border-start rounded ps-3">
                                                            <a href="lhp_<?= $t ?>.php" class="mb-0 d-block">
                                                                <span>Laporan Hasil Pengawasan Tahun <?= $t ?></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <a>
                                                        <span><?= $selesai . "/" . $total; ?></span>
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
                <!-- [Project Remainders] end -->
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
    <?php include 'footer.php'; ?>