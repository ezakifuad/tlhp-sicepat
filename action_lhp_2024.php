<?php
require __DIR__ . '/auth.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
}
include 'koneksi.php';

$uploadDir = rtrim(getenv('APP_UPLOAD_DIR') ?: __DIR__ . '/uploads', '/') . '/lhp_2024/';

if (isset($_GET['action']) && $_GET['action'] === 'download') {

    $requestedFile = $_GET['file'] ?? '';
    $file = is_string($requestedFile) ? basename($requestedFile) : '';
    if ($file === '' || $file !== $requestedFile || preg_match('/[\x00-\x1f\x7f]/', $file)) {
        http_response_code(404);
        exit('File tidak ditemukan.');
    }
    $path = $uploadDir . $file;

    if (is_file($path) && !is_link($path)) {

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($file));
        header('X-Content-Type-Options: nosniff');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, no-store');
        header('Pragma: no-cache');

        readfile($path);
        exit;
    }

    http_response_code(404);
    exit("File tidak ditemukan");
}

$T_LHP = 'lhp_2024';
$T_TEM = 'temuan_2024';
$T_REK = 'rekomendasi_2024';

$action = $_POST['action'] ?? null;

function cleanRupiah($rupiah)
{
    if (!$rupiah) {
        return 0;
    }

    return (int) str_replace(['Rp. ', 'Rp', '.', ','], '', $rupiah);
}

if ($action === 'simpan_rekomendasi') {

    $id_lhp = $_POST['id_lhp'] ?? null;
    $rekomendasi = $_POST['rekomendasi'] ?? [];

    if (!$id_lhp) {
        $_SESSION['success'] = "Data tidak valid";
        header("Location: lhp_2024.php");
        exit;
    }

    $stmtInsert = $conn->prepare(
        "INSERT INTO $T_REK (id_temuan, rekomendasi)
                        VALUES (?, ?)"
    );

    $stmtUpdate = $conn->prepare(
        "UPDATE $T_REK
                        SET rekomendasi = ?
                        WHERE id_rekomendasi = ?"
    );

    foreach ($rekomendasi as $id_temuan => $items) {
        foreach ($items as $uid => $data) {

            $text = trim($data['text'] ?? '');
            if ($text === '') {
                continue;
            }

            $idRek = !empty($data['id_rek']) ? (int) $data['id_rek'] : null;

            if ($idRek) {
                $stmtUpdate->bind_param("si", $text, $idRek);
                $stmtUpdate->execute();
            } else {
                $stmtInsert->bind_param("is", $id_temuan, $text);
                $stmtInsert->execute();
            }
        }
    }

    $_SESSION['success'] = "Rekomendasi berhasil disimpan";
    header("Location: lhp_2024.php");
    exit;
}

if ($action === 'get_temuan') {

    $id = $_POST['id_lhp'] ?? null;
    if (!$id) {
        echo json_encode([]);
        exit;
    }

    $stmt = $conn->prepare(
        "SELECT id_temuan, temuan
                        FROM $T_TEM
                        WHERE id_lhp = ?
                        ORDER BY id_temuan"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($action === 'get_temuan_rekomendasi_edit') {

    $id = $_POST['id_lhp'] ?? null;
    if (!$id) {
        exit;
    }

    $stmt = $conn->prepare(
        "SELECT 
    t.id_temuan,
    t.temuan,
    r.id_rekomendasi,
    r.rekomendasi,
    r.catatan_pemeriksa,
    r.file_path,
    l.file_lhp
FROM $T_TEM t
LEFT JOIN $T_REK r ON r.id_temuan = t.id_temuan
JOIN $T_LHP l ON l.id_lhp = t.id_lhp
WHERE t.id_lhp = ?
                    ORDER BY t.id_temuan, r.id_rekomendasi"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $res = $stmt->get_result();
    $data = [];

    while ($row = $res->fetch_assoc()) {

        $id_temuan = $row['id_temuan'];

        if (!isset($data[$id_temuan])) {
            $data[$id_temuan] = [
                'id_temuan' => $id_temuan,
                'temuan' => $row['temuan'],
                'rekomendasi' => []
            ];
        }

        if ($row['id_rekomendasi']) {
            $data[$id_temuan]['rekomendasi'][] = [
                'id_rekomendasi' => $row['id_rekomendasi'],
                'rekomendasi' => $row['rekomendasi'],
                'catatan_pemeriksa' => $row['catatan_pemeriksa'],
                'file' => $row['file_path']
            ];
        }
    }

    header('Content-Type: application/json');
    $data_lhp = $conn->query("
    SELECT file_lhp 
    FROM $T_LHP 
    WHERE id_lhp = $id
")->fetch_assoc();
    echo json_encode([
        'temuan' => array_values($data),
        'file_lhp' => $data_lhp['file_lhp'] ?? null
    ]);
    exit;
}

if ($action === 'tambah') {

    $no_lhp = $_POST['no_lhp'] ?? null;
    $judul = $_POST['judul_lhp'] ?? null;
    $pengelola = $_POST['pengelola'] ?? null;
    $sasaran = $_POST['sasaran'] ?? null;

    $perangkat = null;
    $desa = null;

    if (
        $sasaran === 'Audit' ||
        $sasaran === 'Reviu' ||
        $sasaran === 'Evaluasi' ||
        $sasaran === 'Monitoring'
    ) {
        $perangkat = $_POST['perangkat_daerah'] ?? null;
    }

    if ($sasaran === 'Pemeriksaan') {
        $desa = $_POST['desa'] ?? null;
    }
    $mulai = $_POST['tanggal_mulai'] ?? null;
    $selesai = $_POST['tanggal_selesai'] ?? null;
    $temuanArr = $_POST['temuan'] ?? [];
    $jumlah_uang = cleanRupiah($_POST['jumlah_uang'] ?? null);

    if (!$no_lhp || !$judul || !$mulai || !$selesai) {
        die("Data wajib belum lengkap");
    }

    $stmt = $conn->prepare(
        "INSERT INTO $T_LHP 
    (no_lhp, judul_lhp, pengelola, sasaran, perangkat_daerah, desa, jumlah_uang, tanggal_mulai, tanggal_selesai)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssssssiss",
        $no_lhp,
        $judul,
        $pengelola,
        $sasaran,
        $perangkat,
        $desa,
        $jumlah_uang,
        $mulai,
        $selesai
    );
    $stmt->execute();

    $id_lhp = $conn->insert_id;

    $fileLhpName = null;

    if (!empty($_FILES['file_lhp']['name'])) {

        $originalName = basename($_FILES['file_lhp']['name']);

        $fileLhpName = time() . '_' . $originalName;

        $targetPath = $uploadDir . $fileLhpName;

        move_uploaded_file(
            $_FILES['file_lhp']['tmp_name'],
            $targetPath
        );

        $stmtFile = $conn->prepare(
            "UPDATE $T_LHP SET file_lhp = ? WHERE id_lhp = ?"
        );

        $stmtFile->bind_param("si", $fileLhpName, $id_lhp);
        $stmtFile->execute();
    }

    if (!empty($temuanArr)) {

        $stmtTemuan = $conn->prepare(
            "INSERT INTO $T_TEM (id_lhp, temuan)
                            VALUES (?, ?)"
        );

        $temuanIds = [];

        foreach ($temuanArr as $t) {

            $t = trim($t);
            if ($t === '')
                continue;

            $stmtTemuan->bind_param("is", $id_lhp, $t);
            $stmtTemuan->execute();

            $temuanIds[] = $conn->insert_id;
        }
    }

    $rekomendasi = $_POST['rekomendasi'] ?? [];
    $files = $_FILES['file_rekomendasi'] ?? [];

    $stmtRek = $conn->prepare(
        "INSERT INTO $T_REK (id_temuan, rekomendasi, file_path)
     VALUES (?, ?, ?)"
    );

    foreach ($rekomendasi as $index => $items) {

        $posisi = (int) $index - 1;

        if (!isset($temuanIds[$posisi])) {
            continue;
        }

        $id_temuan = $temuanIds[$posisi];

        foreach ($items as $uid => $data) {

            $text = trim($data['text'] ?? '');
            if ($text === '')
                continue;

            $fileName = null;

            if (!empty($files['name'][$index][$uid])) {

                $originalName = basename($files['name'][$index][$uid]);
                $fileName = time() . '_' . $originalName;
                $targetPath = $uploadDir . $fileName;

                move_uploaded_file(
                    $files['tmp_name'][$index][$uid],
                    $targetPath
                );
            }

            $stmtRek->bind_param("iss", $id_temuan, $text, $fileName);
            $stmtRek->execute();
        }
    }

    $_SESSION['success'] = "Data berhasil ditambahkan";
} elseif ($action === 'update') {

    $newTemuanMap = [];
    $id = $_POST['id_lhp'];
    $no_lhp = $_POST['no_lhp'];
    $judul = $_POST['judul_lhp'];
    $pengelola = $_POST['pengelola'];
    $sasaran = $_POST['sasaran'];

    $perangkat = null;
    $desa = null;

    if (
        $sasaran === 'Audit' ||
        $sasaran === 'Reviu' ||
        $sasaran === 'Evaluasi' ||
        $sasaran === 'Monitoring'
    ) {
        $perangkat = $_POST['perangkat_daerah'] ?? null;
    }

    if ($sasaran === 'Pemeriksaan') {
        $desa = $_POST['desa'] ?? null;
    }
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];
    $jumlah_uang = cleanRupiah($_POST['jumlah_uang']);

    $idTemuanArr = $_POST['id_temuan'] ?? [];
    $temuanArr = $_POST['temuan'] ?? [];

    $stmt = $conn->prepare(
        "UPDATE $T_LHP SET
        no_lhp = ?,
        judul_lhp = ?,
        pengelola = ?,
        sasaran = ?,
        perangkat_daerah = ?,
        desa = ?,
        jumlah_uang = ?,
        tanggal_mulai = ?,
        tanggal_selesai = ?
    WHERE id_lhp = ?"
    );

    $stmt->bind_param(
        "ssssssissi",
        $no_lhp,
        $judul,
        $pengelola,
        $sasaran,
        $perangkat,
        $desa,
        $jumlah_uang,
        $mulai,
        $selesai,
        $id
    );
    $stmt->execute();

    if (!empty($_FILES['file_lhp']['name'])) {

        $resFile = $conn->query("
        SELECT file_lhp 
        FROM $T_LHP 
        WHERE id_lhp = $id
    ");

        if ($rowFile = $resFile->fetch_assoc()) {

            if (!empty($rowFile['file_lhp'])) {

                $oldPath = $uploadDir . $rowFile['file_lhp'];

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
        }

        $originalName = basename($_FILES['file_lhp']['name']);
        $fileLhpName = time() . '_' . $originalName;
        $targetPath = $uploadDir . $fileLhpName;

        move_uploaded_file(
            $_FILES['file_lhp']['tmp_name'],
            $targetPath
        );

        $stmtFile = $conn->prepare(
            "UPDATE $T_LHP SET file_lhp = ? WHERE id_lhp = ?"
        );

        $stmtFile->bind_param("si", $fileLhpName, $id);
        $stmtFile->execute();
    }

    $old = [];
    $res = $conn->query("SELECT id_temuan FROM $T_TEM WHERE id_lhp = $id");

    while ($r = $res->fetch_assoc()) {
        $old[] = $r['id_temuan'];
    }

    $used = [];

    for ($i = 0; $i < count($temuanArr); $i++) {

        $isi = trim($temuanArr[$i]);
        if ($isi === '') {
            continue;
        }

        if (!empty($idTemuanArr[$i])) {

            $stmt = $conn->prepare(
                "UPDATE $T_TEM SET temuan = ? WHERE id_temuan = ?"
            );

            $stmt->bind_param("si", $isi, $idTemuanArr[$i]);
            $stmt->execute();

            $used[] = $idTemuanArr[$i];
        } else {

            $stmt = $conn->prepare(
                "INSERT INTO $T_TEM (id_lhp, temuan)
                        VALUES (?, ?)"
            );

            $stmt->bind_param("is", $id, $isi);
            $stmt->execute();

            $newId = $conn->insert_id;
            $used[] = $newId;

            $newKey = "new_" . ($i + 1);
            $newTemuanMap[$newKey] = $newId;
        }
    }

    $hapusRek = $_POST['hapus_rekomendasi'] ?? [];

    if (!empty($hapusRek)) {

        foreach ($hapusRek as $idRek) {

            $idRek = (int) $idRek;

            $res = $conn->query("
            SELECT file_path 
            FROM $T_REK 
            WHERE id_rekomendasi = $idRek
        ");

            if ($row = $res->fetch_assoc()) {

                if (!empty($row['file_path'])) {

                    $filePath = $uploadDir . $row['file_path'];

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            $conn->query("DELETE FROM $T_REK WHERE id_rekomendasi = $idRek");
        }
    }

    $toDelete = array_diff($old, $used);

    if ($toDelete) {
        $ids = implode(',', $toDelete);
        $conn->query("DELETE FROM $T_TEM WHERE id_temuan IN ($ids)");
    }

    $_SESSION['success'] = "Data berhasil diperbarui";


    $rekomendasi = $_POST['rekomendasi'] ?? [];

    $oldRek = [];
    $res = $conn->query("
                    SELECT r.id_rekomendasi
                    FROM $T_REK r
                    JOIN $T_TEM t ON t.id_temuan = r.id_temuan
                    WHERE t.id_lhp = $id
                ");
    while ($r = $res->fetch_assoc()) {
        $oldRek[] = $r['id_rekomendasi'];
    }

    $usedRek = [];

    $stmtInsert = $conn->prepare(
        "INSERT INTO $T_REK (id_temuan, rekomendasi)
                    VALUES (?, ?)"
    );

    $stmtUpdate = $conn->prepare(
        "UPDATE $T_REK
                    SET rekomendasi = ?
                    WHERE id_rekomendasi = ?"
    );

    $files = $_FILES['file_rekomendasi'] ?? [];

    foreach ($rekomendasi as $key => $items) {

        if (strpos($key, 'new_') === 0) {
            if (!isset($newTemuanMap[$key]))
                continue;
            $id_temuan = $newTemuanMap[$key];
        } else {
            $id_temuan = (int) $key;
        }

        foreach ($items as $uid => $data) {

            $text = trim($data['text'] ?? '');
            if ($text === '')
                continue;

            $idRek = !empty($data['id_rek']) ? (int) $data['id_rek'] : null;

            $fileName = null;

            if (!empty($files['name'][$key][$uid])) {

                $originalName = basename($files['name'][$key][$uid]);
                $fileName = time() . '_' . $originalName;
                $targetPath = $uploadDir . $fileName;

                move_uploaded_file(
                    $files['tmp_name'][$key][$uid],
                    $targetPath
                );
            }

            if ($idRek) {

                if ($fileName) {
                    $stmt = $conn->prepare(
                        "UPDATE $T_REK
                     SET rekomendasi = ?, file_path = ?
                     WHERE id_rekomendasi = ?"
                    );
                    $stmt->bind_param("ssi", $text, $fileName, $idRek);
                } else {
                    $stmt = $conn->prepare(
                        "UPDATE $T_REK
                     SET rekomendasi = ?
                     WHERE id_rekomendasi = ?"
                    );
                    $stmt->bind_param("si", $text, $idRek);
                }

                $stmt->execute();
                $usedRek[] = $idRek;
            } else {

                $stmt = $conn->prepare(
                    "INSERT INTO $T_REK (id_temuan, rekomendasi, file_path)
                 VALUES (?, ?, ?)"
                );

                $stmt->bind_param("iss", $id_temuan, $text, $fileName);
                $stmt->execute();
                $usedRek[] = $conn->insert_id;
            }
        }
    }

    $toDelete = array_diff($oldRek, $usedRek);
    if ($toDelete) {
        $ids = implode(',', array_map('intval', $toDelete));
        $res = $conn->query("
    SELECT file_path 
    FROM $T_REK 
    WHERE id_rekomendasi IN ($ids)
");

        while ($row = $res->fetch_assoc()) {

            if (!empty($row['file_path'])) {

                $filePath = $uploadDir . $row['file_path'];

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $conn->query("DELETE FROM $T_REK WHERE id_rekomendasi IN ($ids)");
    }
} elseif ($action === 'delete') {

    $id = $_POST['id_lhp'] ?? null;
    if (!$id)
        die("ID tidak ditemukan");

    $stmt = $conn->prepare("DELETE FROM $T_LHP WHERE id_lhp = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $_SESSION['success'] = "Data berhasil dihapus";
    header("Location: lhp_2024.php");
    exit;
}

if ($action === 'get_temuan_rekomendasi') {

    $id = $_POST['id_lhp'] ?? null;
    if (!$id) {
        exit;
    }

    $stmt = $conn->prepare(
        "SELECT id_temuan, temuan
                        FROM $T_TEM
                        WHERE id_lhp = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $res = $stmt->get_result();
    $data = [];

    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($action === 'get_detail') {

    $id = $_POST['id_lhp'] ?? null;
    if (!$id) {
        exit;
    }

    $stmt = $conn->prepare(
        "SELECT id_lhp, no_lhp, judul_lhp, pengelola, 
       sasaran, perangkat_daerah, desa,
       jumlah_uang, tanggal_mulai, tanggal_selesai, file_lhp
FROM $T_LHP
WHERE id_lhp = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $lhp = $stmt->get_result()->fetch_assoc();

    $stmt = $conn->prepare(
        "SELECT
                        t.id_temuan,
                        t.temuan,
                        r.rekomendasi,
                        r.status_verifikasi,
                        r.catatan_pemeriksa,
                        r.file_path
                    FROM $T_TEM t
                    LEFT JOIN $T_REK r ON r.id_temuan = t.id_temuan
                    WHERE t.id_lhp = ?
                    ORDER BY t.id_temuan, r.id_rekomendasi"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $res = $stmt->get_result();

    $temuan = [];
    $totalTemuan = 0;
    $temuanPunyaRekom = [];
    $rekomendasiTidakLengkap = false;

    while ($row = $res->fetch_assoc()) {

        $id_temuan = $row['id_temuan'];

        if (!isset($temuan[$id_temuan])) {
            $temuan[$id_temuan] = [
                'temuan' => $row['temuan'],
                'rekomendasi' => []
            ];
            $totalTemuan++;
        }

        if (!empty($row['rekomendasi'])) {

            $temuanPunyaRekom[$id_temuan] = true;

            $temuan[$id_temuan]['rekomendasi'][] = [
                'text' => $row['rekomendasi'],
                'status' => $row['status_verifikasi'],
                'catatan' => $row['catatan_pemeriksa'],
                'file' => $row['file_path']
            ];

            if (
                $row['rekomendasi'] === '-' ||
                $row['status_verifikasi'] === null ||
                $row['status_verifikasi'] === ''
            ) {
                $rekomendasiTidakLengkap = true;
            }
        }
    }

    $status = (
        $totalTemuan > 0 &&
        count($temuanPunyaRekom) === $totalTemuan &&
        $rekomendasiTidakLengkap === false
    )
        ? 'Complete'
        : 'Incomplete';

    header('Content-Type: application/json');
    echo json_encode([
        'lhp' => $lhp,
        'temuan' => array_values($temuan),
        'status' => $status
    ]);
    exit;
}

if ($action === 'get_verifikasi') {

    $id = $_POST['id_lhp'];

    $lhp = $conn->query("
    SELECT
        no_lhp,
        judul_lhp,
        pengelola,
        sasaran,
        perangkat_daerah,
        desa,
        jumlah_uang,
        tanggal_mulai,
        tanggal_selesai,
        file_lhp
    FROM $T_LHP
    WHERE id_lhp = $id
")->fetch_assoc();

    $res = $conn->query("
                        SELECT
                            t.id_temuan,
                            t.temuan,
                            r.id_rekomendasi,
                            r.rekomendasi,
                            r.status_verifikasi,
                            r.catatan_pemeriksa,
                            r.file_path
                        FROM $T_TEM t
                        LEFT JOIN $T_REK r ON r.id_temuan = t.id_temuan
                        WHERE t.id_lhp = $id
                        ORDER BY t.id_temuan, r.id_rekomendasi
                    ");

    $temuan = [];
    $totalTemuan = 0;
    $temuanPunyaRekom = [];
    $rekomendasiTidakLengkap = false;

    while ($row = $res->fetch_assoc()) {

        $id_temuan = $row['id_temuan'];

        if (!isset($temuan[$id_temuan])) {
            $temuan[$id_temuan] = [
                'temuan' => $row['temuan'],
                'rekomendasi' => []
            ];
            $totalTemuan++;
        }

        if (!empty($row['rekomendasi'])) {

            $temuanPunyaRekom[$id_temuan] = true;

            $temuan[$id_temuan]['rekomendasi'][] = [
                'id_rekomendasi' => $row['id_rekomendasi'],
                'text' => $row['rekomendasi'],
                'status' => $row['status_verifikasi'],
                'catatan' => $row['catatan_pemeriksa'],
                'file' => $row['file_path']
            ];

            if (
                $row['rekomendasi'] === '-' ||
                $row['status_verifikasi'] === null ||
                $row['status_verifikasi'] === ''
            ) {
                $rekomendasiTidakLengkap = true;
            }
        }
    }

    $status = (
        $totalTemuan > 0 &&
        count($temuanPunyaRekom) === $totalTemuan &&
        $rekomendasiTidakLengkap === false
    )
        ? 'Complete'
        : 'Incomplete';

    echo json_encode([
        'lhp' => $lhp,
        'temuan' => array_values($temuan),
        'status' => $status
    ]);
    exit;
}

if ($action === 'verifikasi_rekomendasi') {

    $status = $_POST['status'] ?? [];
    $catatan = $_POST['catatan'] ?? [];

    $stmt = $conn->prepare(
        "UPDATE $T_REK
                        SET status_verifikasi = ?, catatan_pemeriksa = ?
                        WHERE id_rekomendasi = ?"
    );

    foreach ($status as $id_rek => $val) {

        $note = trim($catatan[$id_rek] ?? '');

        $stmt->bind_param("ssi", $val, $note, $id_rek);
        $stmt->execute();
    }

    $_SESSION['success'] = "Verifikasi & catatan berhasil disimpan";
    header("Location: lhp_2024.php");
    exit;
}

header("Location: lhp_2024.php");
exit;
