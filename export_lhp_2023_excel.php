<?php
require __DIR__ . '/auth.php';
requireLogin();
include 'koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan Hasil Pemeriksaan 2023.xls");
header("Pragma: no-cache");
header("Expires: 0");

$T_LHP = 'lhp_2023';
$T_TEM = 'temuan_2023';
$T_REK = 'rekomendasi_2023';

$sql = "
SELECT
    l.id_lhp,
    l.no_lhp,
    l.judul_lhp,
    l.pengelola,
    l.sasaran,
    l.perangkat_daerah,
    l.desa,
    l.jumlah_uang,
    l.tanggal_mulai,
    l.tanggal_selesai,
    t.id_temuan,
    t.temuan,
    r.rekomendasi,
    r.status_verifikasi,
    r.catatan_pemeriksa
FROM $T_LHP l
LEFT JOIN $T_TEM t ON t.id_lhp = l.id_lhp
LEFT JOIN $T_REK r ON r.id_temuan = t.id_temuan
ORDER BY 
    l.id_lhp DESC,
    t.id_temuan,
    r.id_rekomendasi
";

$res = $conn->query($sql);

$data = [];

while ($row = $res->fetch_assoc()) {

    $id = $row['id_lhp'];

    if (!isset($data[$id])) {
        $data[$id] = [
            'no_lhp' => $row['no_lhp'],
            'judul' => $row['judul_lhp'],
            'pengelola' => $row['pengelola'],
            'sasaran' => $row['sasaran'],
            'perangkat' => $row['perangkat_daerah'],
            'desa' => $row['desa'],
            'mulai' => $row['tanggal_mulai'],
            'selesai' => $row['tanggal_selesai'],
            'jumlah' => $row['jumlah_uang'],
            'temuan' => []
        ];
    }

    $temuanId = $row['id_temuan'];

    if (!isset($data[$id]['temuan'][$temuanId])) {
        $data[$id]['temuan'][$temuanId] = [
            'text' => $row['temuan'],
            'rekom' => []
        ];
    }

    if (!empty($row['rekomendasi'])) {
        $data[$id]['temuan'][$temuanId]['rekom'][] = [
            'rekom' => $row['rekomendasi'],
            'verifikasi' => $row['status_verifikasi'],
            'catatan' => $row['catatan_pemeriksa']
        ];
    }
}

$totalLHP = count($data);
$totalTemuan = 0;
$totalRekomendasi = 0;

foreach ($data as $lhp) {
    $totalTemuan += count($lhp['temuan']);
    foreach ($lhp['temuan'] as $t) {
        $totalRekomendasi += count($t['rekom']);
    }
}

$qLhp = $conn->query("SELECT COUNT(*) total FROM $T_LHP");
$totalLhpDb = $qLhp->fetch_assoc()['total'];

$qLhpStatus = $conn->query("
    SELECT
        SUM(
            CASE
                WHEN NOT EXISTS (
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
                THEN 1 ELSE 0
            END
        ) AS selesai,

        SUM(
            CASE
                WHEN EXISTS (
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
                THEN 1 ELSE 0
            END
        ) AS belum
    FROM $T_LHP l
");

$lhpStatus = $qLhpStatus->fetch_assoc();

$qTemuan = $conn->query("SELECT COUNT(*) total FROM $T_TEM");
$totalTemuanDb = $qTemuan->fetch_assoc()['total'];

$qTemuanStatus = $conn->query("
    SELECT
        SUM(
            CASE
                WHEN NOT EXISTS (
                    SELECT 1
                    FROM $T_REK r
                    WHERE r.id_temuan = t.id_temuan
                    AND (
                        r.status_verifikasi != 'sesuai'
                        OR r.status_verifikasi IS NULL
                        OR r.rekomendasi = '-'
                    )
                )
                THEN 1 ELSE 0
            END
        ) AS selesai,

        SUM(
            CASE
                WHEN EXISTS (
                    SELECT 1
                    FROM $T_REK r
                    WHERE r.id_temuan = t.id_temuan
                    AND (
                        r.status_verifikasi != 'sesuai'
                        OR r.status_verifikasi IS NULL
                        OR r.rekomendasi = '-'
                    )
                )
                THEN 1 ELSE 0
            END
        ) AS belum
    FROM $T_TEM t
");

$temuanStatus = $qTemuanStatus->fetch_assoc();

$qRekom = $conn->query("SELECT COUNT(*) total FROM $T_REK");
$totalRekomDb = $qRekom->fetch_assoc()['total'];

$qRekomStatus = $conn->query("
    SELECT
        SUM(
            CASE
                WHEN status_verifikasi = 'sesuai'
                     AND rekomendasi IS NOT NULL
                     AND rekomendasi <> '-'
                THEN 1 ELSE 0
            END
        ) AS selesai,

        SUM(
            CASE
                WHEN status_verifikasi != 'sesuai'
                     OR status_verifikasi IS NULL
                     OR rekomendasi = '-'
                THEN 1 ELSE 0
            END
        ) AS belum
    FROM $T_REK
");

$rekomStatus = $qRekomStatus->fetch_assoc();

function statusLHP($id_lhp, $conn)
{
    global $T_TEM, $T_REK;

    $q = $conn->query("
        SELECT
            COUNT(r.id_rekomendasi) total,
            SUM(
                CASE
                    WHEN r.status_verifikasi != 'sesuai'
                         OR r.status_verifikasi IS NULL
                         OR r.rekomendasi = '-'
                    THEN 1 ELSE 0
                END
            ) belum_selesai
        FROM $T_TEM t
        LEFT JOIN $T_REK r ON r.id_temuan = t.id_temuan
        WHERE t.id_lhp = $id_lhp
    ");

    $r = $q->fetch_assoc();

    if ($r['total'] > 0 && $r['belum_selesai'] == 0) {
        return 'Selesai';
    }
    return 'Belum Selesai';
}

function persen($nilai, $total)
{
    if ($total == 0)
        return '0%';
    return round(($nilai / $total) * 100, 2) . '%';
}

echo "
<table>
<tr>
<td colspan='15' style='font-size:16px;font-weight:bold;text-align:center;'>
LAPORAN HASIL PEMERIKSAAN 2023
</td>
</tr>
<tr><td colspan='15'></td></tr>
</table>
";

echo "<table border='1'>
<tr>
<th valign='top'>No</th>
<th valign='top'>No LHP</th>
<th valign='top'>Judul LHP</th>
<th valign='top'>Pengelola</th>
<th valign='top'>Sasaran</th>
<th valign='top'>Tanggal Mulai</th>
<th valign='top'>Tanggal Selesai</th>
<th valign='top'>Jumlah Uang</th>
<th valign='top'>Temuan</th>
<th valign='top'>Rekomendasi</th>
<th valign='top'>Verifikasi</th>
<th valign='top'>Catatan Pemeriksa</th>
<th valign='top'>Jumlah Temuan</th>
<th valign='top'>Jumlah Rekomendasi</th>
<th valign='top'>Rekomendasi Sesuai</th>

</tr>";

$no = 1;
$firstTemuanGlobalEver = true;

foreach ($data as $id_lhp => $lhp) {

    $status = statusLHP($id_lhp, $conn);

    $sasaranText = '-';

    if (
        $lhp['sasaran'] == 'Audit' ||
        $lhp['sasaran'] == 'Reviu' ||
        $lhp['sasaran'] == 'Evaluasi' ||
        $lhp['sasaran'] == 'Monitoring'
    ) {
        $sasaranText = $lhp['sasaran'] . ' - ' . ($lhp['perangkat'] ?? '-');
    } elseif ($lhp['sasaran'] == 'Pemeriksaan') {
        $sasaranText = 'Pemeriksaan - ' . ($lhp['desa'] ?? '-');
    }

    $jumlahTemuan = count($lhp['temuan']);
    $jumlahRekom = 0;
    foreach ($lhp['temuan'] as $temuan) {
        $jumlahRekom += count($temuan['rekom']);
    }

    $jumlahRekomSesuai = 0;
    foreach ($lhp['temuan'] as $temuan) {
        foreach ($temuan['rekom'] as $r) {
            if ($r['verifikasi'] === 'sesuai') {
                $jumlahRekomSesuai++;
            }
        }
    }

    $firstLhpRow = true;

    foreach ($lhp['temuan'] as $temuan) {

        $temuanText = $temuan['text'];
        $rekomList = $temuan['rekom'];

        if (!$firstTemuanGlobalEver) {
            echo "
<tr>
    " . str_repeat("<td></td>", 15) . "
</tr>
";
        }

        if (empty($rekomList)) {
            $rekomList = [
                [
                    'rekom' => '-',
                    'verifikasi' => '',
                    'catatan' => '-'
                ]
            ];
        }

        $firstTemuanRow = true;

        foreach ($rekomList as $r) {

            echo "<tr>";

            if ($firstLhpRow) {
                echo "
    <td valign='top' align='center'>{$no}</td>
    <td valign='top'>{$lhp['no_lhp']}</td>
    <td valign='top'>{$lhp['judul']}</td>
    <td valign='top'>{$lhp['pengelola']}</td>
    <td valign='top'>{$sasaranText}</td>
    <td valign='top' align='center'>{$lhp['mulai']}</td>
    <td valign='top' align='center'>{$lhp['selesai']}</td>
    <td valign='top'>Rp. " . number_format($lhp['jumlah'], 0, ',', '.') . "</td>
    ";
            } else {
                echo str_repeat("<td></td>", 8);
            }

            $verifikasi = '-';
            if ($r['verifikasi'] === 'sesuai') {
                $verifikasi = 'Sesuai';
            } elseif ($r['verifikasi'] === 'tidak_sesuai') {
                $verifikasi = 'Tidak Sesuai';
            }

            if ($firstTemuanRow) {
                echo "<td valign='top'>{$temuanText}</td>";
                $firstTemuanRow = false;
            } else {
                echo "<td></td>";
            }

            echo "<td valign='top'>{$r['rekom']}</td>";

            echo "<td valign='top' align='center'>{$verifikasi}</td>";

            echo "<td valign='top'>{$r['catatan']}</td>";

            if ($firstLhpRow) {
                echo "<td valign='top' align='center'>{$jumlahTemuan}</td>";
                echo "<td valign='top' align='center'>{$jumlahRekom}</td>";
                echo "<td valign='top' align='center'>{$jumlahRekomSesuai}</td>";
                $firstLhpRow = false;
                $no++;
            } else {
                echo "<td></td><td></td><td></td>";
            }

            echo "</tr>";
        }

        $firstTemuanGlobalEver = false;
    }
}
echo "</table>";
echo "<br>";
echo "
<table border='1'>
<tr>
    <th colspan='2' valign='top'>Keterangan</th>
    <th valign='top'>Jumlah</th>
</tr>
<tr>
    <td colspan='2' valign='top'>Jumlah LHP</td>
    <td valign='top' align='center'><strong>{$totalLHP}</strong></td>
</tr>
<tr>
    <td colspan='2' valign='top'>Jumlah Temuan</td>
    <td valign='top' align='center'><strong>{$totalTemuan}</strong></td>
</tr>
<tr>
    <td colspan='2' valign='top'>Jumlah Rekomendasi</td>
    <td valign='top' align='center'><strong>{$totalRekomendasi}</strong></td>
</tr>
</table>";
echo "<br>";
echo "
<table border='1'>
<tr>
    <th colspan='2' valign='top'>Keterangan</th>
    <th valign='top'>Jumlah</th>
    <th valign='top'>Persentase</th>
</tr>
<tr>
    <td colspan='2' valign='top'>Jumlah LHP Selesai</td>
    <td valign='top' align='center'><strong>{$lhpStatus['selesai']}</strong></td>
    <td valign='top'><strong>" . persen($lhpStatus['selesai'], $totalLhpDb) . "</strong></td>
</tr>
<tr>
    <td colspan='2' valign='top'>Jumlah LHP Belum Selesai</td>
    <td valign='top' align='center'><strong>{$lhpStatus['belum']}</strong></td>
    <td valign='top'><strong>" . persen($lhpStatus['belum'], $totalLhpDb) . "</strong></td>
</tr>
<tr>
    <td colspan='2' valign='top'>Jumlah Temuan Selesai</td>
    <td valign='top' align='center'><strong>{$temuanStatus['selesai']}</strong></td>
    <td valign='top'><strong>" . persen($temuanStatus['selesai'], $totalTemuanDb) . "</strong></td>
</tr>
<tr>
    <td colspan='2' valign='top'>Jumlah Temuan Belum Selesai</td>
    <td valign='top' align='center'><strong>{$temuanStatus['belum']}</strong></td>
    <td valign='top'><strong>" . persen($temuanStatus['belum'], $totalTemuanDb) . "</strong></td>
</tr>
<tr>
    <td colspan='2' valign='top'>Jumlah Rekomendasi Selesai</td>
    <td valign='top' align='center'><strong>{$rekomStatus['selesai']}</strong></td>
    <td valign='top'><strong>" . persen($rekomStatus['selesai'], $totalRekomDb) . "</strong></td>
</tr>
<tr>
    <td colspan='2' valign='top'>Jumlah Rekomendasi Belum Selesai</td>
    <td valign='top' align='center'><strong>{$rekomStatus['belum']}</strong></td>
    <td valign='top'><strong>" . persen($rekomStatus['belum'], $totalRekomDb) . "</strong></td>
</tr>
</table>";
exit;
