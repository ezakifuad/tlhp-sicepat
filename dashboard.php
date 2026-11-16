<?php include 'header.php'; ?>
<?php
$tahun_aktif = isset($_GET['tahun']) ? (int)$_GET['tahun'] : 2026;
$tahun_list = [$tahun_aktif];

// ==========================
// BUILD UNION PER TAHUN
// ==========================
function unionLHP($tahun_list)
{
    $arr = [];
    foreach ($tahun_list as $t) {
        $arr[] = "SELECT id_lhp FROM lhp_$t";
    }
    return implode(" UNION ALL ", $arr);
}

function unionTemuan($tahun_list)
{
    $arr = [];
    foreach ($tahun_list as $t) {
        $arr[] = "SELECT id_temuan, id_lhp FROM temuan_$t";
    }
    return implode(" UNION ALL ", $arr);
}

function unionRekomendasi($tahun_list)
{
    $arr = [];
    foreach ($tahun_list as $t) {
        $arr[] = "SELECT id_temuan, status_verifikasi, rekomendasi FROM rekomendasi_$t";
    }
    return implode(" UNION ALL ", $arr);
}

$LHP = "(" . unionLHP($tahun_list) . ") l";
$TEM = "(" . unionTemuan($tahun_list) . ") t";
$REK = "(" . unionRekomendasi($tahun_list) . ") r";

// ==========================
// TOTAL LHP
// ==========================
$q_total_lhp = "SELECT COUNT(*) AS total FROM $LHP";
$total_lhp = (int)$conn->query($q_total_lhp)->fetch_assoc()['total'];

// ==========================
// LHP SELESAI
// ==========================
$q_lhp_selesai = "
SELECT COUNT(DISTINCT l.id_lhp) AS total
FROM $LHP
WHERE EXISTS (
    SELECT 1 FROM $TEM
    WHERE t.id_lhp = l.id_lhp
)
AND NOT EXISTS (
    SELECT 1
    FROM $TEM
    JOIN $REK ON r.id_temuan = t.id_temuan
    WHERE t.id_lhp = l.id_lhp
    AND (
        r.status_verifikasi != 'sesuai'
        OR r.status_verifikasi IS NULL
        OR r.rekomendasi = '-'
    )
)
";
$lhp_selesai = (int)$conn->query($q_lhp_selesai)->fetch_assoc()['total'];

$lhp_belum = max(0, $total_lhp - $lhp_selesai);

// ==========================
// TEMUAN
// ==========================
$q_total_temuan = "SELECT COUNT(*) AS total FROM $TEM";
$temuan_total = (int)$conn->query($q_total_temuan)->fetch_assoc()['total'];

$q_temuan_selesai = "
SELECT COUNT(*) AS total
FROM $TEM
WHERE EXISTS (
    SELECT 1 FROM $REK
    WHERE r.id_temuan = t.id_temuan
)
AND NOT EXISTS (
    SELECT 1 FROM $REK
    WHERE r.id_temuan = t.id_temuan
    AND (
        r.status_verifikasi != 'sesuai'
        OR r.status_verifikasi IS NULL
        OR r.rekomendasi = '-'
    )
)
";
$temuan_selesai = (int)$conn->query($q_temuan_selesai)->fetch_assoc()['total'];

$temuan_belum = max(0, $temuan_total - $temuan_selesai);

// ==========================
// REKOMENDASI
// ==========================
$q_total_rek = "SELECT COUNT(*) AS total FROM $REK";
$rekomendasi_total = (int)$conn->query($q_total_rek)->fetch_assoc()['total'];

$q_rek_selesai = "
SELECT COUNT(*) AS total
FROM $REK
WHERE status_verifikasi = 'sesuai'
AND rekomendasi IS NOT NULL
AND rekomendasi <> '-'
";
$rekomendasi_selesai = (int)$conn->query($q_rek_selesai)->fetch_assoc()['total'];

$rekomendasi_belum = max(0, $rekomendasi_total - $rekomendasi_selesai);

// ==========================
// PERSENTASE
// ==========================
function persen($a, $b)
{
    return $b ? round($a / $b * 100) : 0;
}

$persen_lhp_selesai = persen($lhp_selesai, $total_lhp);
$persen_lhp_belum = persen($lhp_belum, $total_lhp);

$persen_temuan_selesai = persen($temuan_selesai, $temuan_total);
$persen_temuan_belum = persen($temuan_belum, $temuan_total);

$persen_rekomendasi_selesai = persen($rekomendasi_selesai, $rekomendasi_total);
$persen_rekomendasi_belum = persen($rekomendasi_belum, $rekomendasi_total);

// ==========================
// WARNA
// ==========================
function warnaLingkaran($persen)
{
    if ($persen <= 33) return 'circle-danger';
    elseif ($persen <= 66) return 'circle-warning';
    elseif ($persen < 100) return 'circle-primary';
    else return 'circle-success';
}

$warna_lhp_selesai = warnaLingkaran($persen_lhp_selesai);
$warna_lhp_belum = warnaLingkaran($persen_lhp_belum);

$warna_temuan_selesai = warnaLingkaran($persen_temuan_selesai);
$warna_temuan_belum = warnaLingkaran($persen_temuan_belum);

$warna_rekomendasi_selesai = warnaLingkaran($persen_rekomendasi_selesai);
$warna_rekomendasi_belum = warnaLingkaran($persen_rekomendasi_belum);
?>

<!--! ================================================================ !-->
<!--! [Start] Main Content !-->
<!--! ================================================================ !-->

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard</h5>
                </div>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <div class="d-flex d-md-none">
                        <a href="javascript:void(0)" class="page-header-right-close-toggle">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
                <div class="d-md-none d-flex align-items-center">
                    <a href="javascript:void(0)" class="page-header-right-open-toggle">
                        <i class="feather-align-right fs-20"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title"></h5>
                            <div class="card-header-action">
                                <div class="card-header-btn">
                                    <div data-bs-toggle="tooltip" title="Refresh">
                                        <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                            data-bs-toggle="refresh"></a>
                                    </div>
                                    <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                                        <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                            data-bs-toggle="expand"></a>
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
                                            <th>Nama</th>
                                            <th>Jumlah LHP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ([2026, 2025, 2024, 2023, 2022] as $t):

                                            $total = (int)$conn->query("SELECT COUNT(*) as jml FROM lhp_$t")->fetch_assoc()['jml'];

                                            $selesai = (int)$conn->query("
        SELECT COUNT(DISTINCT l.id_lhp) AS total
        FROM lhp_$t l
        WHERE EXISTS (
            SELECT 1 FROM temuan_$t t WHERE t.id_lhp = l.id_lhp
        )
        AND NOT EXISTS (
            SELECT 1
            FROM temuan_$t t
            JOIN rekomendasi_$t r ON r.id_temuan = t.id_temuan
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
                                                            <a href="?tahun=<?= $t ?>"
                                                                class="<?= ($t == $tahun_aktif) ? 'fw-bold text-primary' : '' ?>">
                                                                Laporan Hasil Pengawasan Tahun <?= $t ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><b><?= "$selesai/$total" ?></b></td>
                                            </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Ring Progress -->
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-xxl-3 col-lg-6">
                                    <div class="border border-dashed border-gray-5 p-4 rounded-3 text-center">
                                        <div class="sales-progress-1a <?= $warna_lhp_selesai ?>"></div>
                                        <div class="mt-4">
                                            <p class="fs-6 fw-bold text-dark">
                                                Jumlah <?= $lhp_selesai ?>/<?= $total_lhp ?>
                                            </p>
                                            <a class="fs-6 fw-bold text-dark">LHP <?= $tahun_aktif ?> Selesai</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-6">
                                    <div class="border border-dashed border-gray-5 p-4 rounded-3 text-center">
                                        <div class="sales-progress-2a <?= $warna_lhp_belum ?>"></div>
                                        <div class="mt-4">
                                            <p class="fs-6 fw-bold text-dark">
                                                Jumlah <?= $lhp_belum ?>/<?= $total_lhp ?>
                                            </p>
                                            <a class="fs-6 fw-bold text-dark">LHP <?= $tahun_aktif ?> Belum Selesai</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-6">
                                    <div class="border border-dashed border-gray-5 p-4 rounded-3 text-center">
                                        <div class="sales-progress-3a <?= $warna_temuan_selesai ?>"></div>
                                        <div class="mt-4">
                                            <p class="fs-6 fw-bold text-dark">
                                                Jumlah <?= $temuan_selesai ?>/<?= $temuan_total ?>
                                            </p>
                                            <a class="fs-6 fw-bold text-dark">Temuan <?= $tahun_aktif ?> Selesai</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-6">
                                    <div class="border border-dashed border-gray-5 p-4 rounded-3 text-center">
                                        <div class="sales-progress-4a <?= $warna_temuan_belum ?>"></div>
                                        <div class="mt-4">
                                            <p class="fs-6 fw-bold text-dark">
                                                Jumlah <?= $temuan_belum ?>/<?= $temuan_total ?>
                                            </p>
                                            <a class="fs-6 fw-bold text-dark">Temuan <?= $tahun_aktif ?> Belum Selesai</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-6">
                                    <div class="border border-dashed border-gray-5 p-4 rounded-3 text-center">
                                        <div class="sales-progress-5a <?= $warna_rekomendasi_selesai ?>"></div>
                                        <div class="mt-4">
                                            <p class="fs-6 fw-bold text-dark">
                                                Jumlah <?= $rekomendasi_selesai ?>/<?= $rekomendasi_total ?>
                                            </p>
                                            <a class="fs-6 fw-bold text-dark">Rekomendasi <?= $tahun_aktif ?> Selesai</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-6">
                                    <div class="border border-dashed border-gray-5 p-4 rounded-3 text-center">
                                        <div class="sales-progress-6a <?= $warna_rekomendasi_belum ?>"></div>
                                        <div class="mt-4">
                                            <p class="fs-6 fw-bold text-dark">
                                                Jumlah <?= $rekomendasi_belum ?>/<?= $rekomendasi_total ?>
                                            </p>
                                            <a class="fs-6 fw-bold text-dark">Rekomendasi <?= $tahun_aktif ?> Belum Selesai</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script>
        "use strict";
        $(document).ready(function() {

            $(".sales-progress-1a").circleProgress({
                max: 100,
                value: <?= $persen_lhp_selesai ?>,
                textFormat: () => "<?= $persen_lhp_selesai ?>%"
            });

            $(".sales-progress-2a").circleProgress({
                max: 100,
                value: <?= $persen_lhp_belum ?>,
                textFormat: () => "<?= $persen_lhp_belum ?>%"
            });

            $(".sales-progress-3a").circleProgress({
                max: 100,
                value: <?= $persen_temuan_selesai ?>,
                textFormat: () => "<?= $persen_temuan_selesai ?>%"
            });

            $(".sales-progress-4a").circleProgress({
                max: 100,
                value: <?= $persen_temuan_belum ?>,
                textFormat: () => "<?= $persen_temuan_belum ?>%"
            });

        });
    </script>
    <script>
        $(".sales-progress-5a").circleProgress({
            max: 100,
            value: <?= $persen_rekomendasi_selesai ?>,
            textFormat: () => "<?= $persen_rekomendasi_selesai ?>%"
        });

        $(".sales-progress-6a").circleProgress({
            max: 100,
            value: <?= $persen_rekomendasi_belum ?>,
            textFormat: () => "<?= $persen_rekomendasi_belum ?>%"
        });
    </script>
    </div>