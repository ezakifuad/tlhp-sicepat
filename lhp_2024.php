<?php include 'header.php';
$T_LHP = 'lhp_2024';
$T_TEM = 'temuan_2024';
$T_REK = 'rekomendasi_2024';
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
                    <h5 class="m-b-10">Laporan Hasil Pengawasan Tahun 2024</h5>
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
                    <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                        <a href="export_lhp_2024_excel.php" class="btn btn-success">
                            <i class="feather-download me-2"></i>
                            <span>Export Excel</span>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalTambahLHP">
                            <i class="feather-plus me-2"></i>
                            <span>Tambah LHP</span>
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
        <!-- [ page-header ] end -->
        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive w-100">
                                <?php
                                if (session_status() === PHP_SESSION_NONE) {
                                    session_start();
                                }
                                ?>
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
                                        <i class="feather-check-circle me-2"></i>
                                        <?= $_SESSION['success']; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php unset($_SESSION['success']); ?>
                                <?php endif; ?>
                                <table class="table table-hover align-middle mb-0" id="projectList">
                                    <?php
                                    $data = $conn->query("
    SELECT
        l.*,

        COUNT(DISTINCT t.id_temuan) AS total_temuan,

        COUNT(DISTINCT r.id_rekomendasi) AS total_rekomendasi,

        COUNT(DISTINCT CASE
            WHEN r.status_verifikasi = 'sesuai'
            THEN r.id_rekomendasi
        END) AS rekom_selesai,

        COUNT(DISTINCT CASE
            WHEN r.status_verifikasi = 'tidak_sesuai'
              OR r.rekomendasi = '-'
            THEN r.id_rekomendasi
        END) AS rekom_tidak_selesai

    FROM $T_LHP l
    LEFT JOIN $T_TEM t ON t.id_lhp = l.id_lhp
    LEFT JOIN $T_REK r ON r.id_temuan = t.id_temuan
    GROUP BY l.id_lhp
    ORDER BY l.id_lhp DESC
");
                                    $no = 1;
                                    ?>
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">No LHP</th>
                                            <th class="text-center">Judul LHP</th>
                                            <th class="text-center">Pengelola</th>
                                            <th class="text-center">Sasaran</th>
                                            <th class="text-center">Jumlah Temuan</th>
                                            <th class="text-center">Jumlah Rekomendasi</th>
                                            <th class="text-center">Rekomendasi Sesuai</th>
                                            <th class="text-center">Deadline</th>
                                            <th class="text-center">Countdown</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $data->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><?= $row['no_lhp'] ?></td>
                                                <td><?= $row['judul_lhp'] ?></td>
                                                <td><?= $row['pengelola'] ?></td>
                                                <td class="text-center">
                                                    <?php if ($row['sasaran'] === 'Audit' || $row['sasaran'] === 'Reviu'): ?>
                                                        <?= ucfirst($row['sasaran']) ?><br>
                                                        <small class="text-muted"><?= $row['perangkat_daerah'] ?></small>

                                                    <?php elseif ($row['sasaran'] === 'Pemeriksaan'): ?>
                                                        Pemeriksaan<br>
                                                        <small class="text-muted"><?= $row['desa'] ?></small>

                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= $row['total_temuan'] ?></td>
                                                <td class="text-center"><?= $row['total_rekomendasi'] ?></td>
                                                <td class="text-center align-middle">
                                                    <?php
                                                    $total = $row['total_rekomendasi'];
                                                    $selesai = $row['rekom_selesai'];
                                                    $persen = $total > 0 ? round(($selesai / $total) * 100) : 0;
                                                    $statusComplete = ($total > 0 && $selesai == $total);
                                                    if ($persen == 100) {
                                                        $color = 'success';
                                                        $icon = 'feather-check-circle';
                                                        $label = 'Selesai';
                                                    } elseif ($persen >= 50) {
                                                        $color = 'warning';
                                                        $icon = 'feather-clock';
                                                        $label = 'Proses';
                                                    } else {
                                                        $color = 'danger';
                                                        $icon = 'feather-alert-circle';
                                                        $label = 'Belum Optimal';
                                                    }
                                                    ?>
                                                    <?php if ($total > 0): ?>
                                                        <div class="text-center mb-1">
                                                            <span class="text-dark">
                                                                <?= $selesai ?> / <?= $total ?>
                                                            </span>
                                                        </div>
                                                        <div class="progress mx-auto"
                                                            style="width:60%; height:8px; border-radius:20px;">
                                                            <div class="progress-bar bg-<?= $color ?>"
                                                                style="width: <?= $persen ?>%; border-radius:20px;">
                                                            </div>
                                                        </div>
                                                        <div class="small text-dark mt-1">
                                                            <?= $persen ?>% Selesai
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">
                                                            Belum Ada Rekomendasi
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= date('d M Y', strtotime($row['tanggal_mulai'])) ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        s/d <?= date('d M Y', strtotime($row['tanggal_selesai'])) ?>
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="countdown countdown-box"
                                                        data-deadline="<?= $row['tanggal_selesai'] ?> 23:59:59"
                                                        data-status="<?= $statusComplete ? 'Selesai' : 'Belum Selesai' ?>">
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="hstack gap-1 justify-content-center">
                                                        <a href="javascript:void(0)"
                                                            class="avatar-text avatar-md btn-detail"
                                                            data-id="<?= $row['id_lhp'] ?>" data-bs-toggle="modal"
                                                            data-bs-target="#modalDetailLHP" data-bs-title="Detail">
                                                            <i class="feather feather-eye"></i>
                                                        </a>
                                                        <a href="javascript:void(0)"
                                                            class="avatar-text avatar-md text-success btn-verifikasi"
                                                            data-id="<?= $row['id_lhp'] ?>" data-bs-toggle="modal"
                                                            data-bs-target="#modalVerifikasiLHP" data-bs-title="Verifikasi">
                                                            <i class="feather feather-check-circle"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" class="avatar-text avatar-md btn-edit"
                                                            data-id="<?= $row['id_lhp'] ?>" data-no="<?= $row['no_lhp'] ?>"
                                                            data-judul="<?= $row['judul_lhp'] ?>"
                                                            data-pengelola="<?= $row['pengelola'] ?>"
                                                            data-jumlah="<?= $row['jumlah_uang'] ?>"
                                                            data-mulai="<?= $row['tanggal_mulai'] ?>"
                                                            data-selesai="<?= $row['tanggal_selesai'] ?>"
                                                            data-sasaran="<?= $row['sasaran'] ?>"
                                                            data-perangkat="<?= $row['perangkat_daerah'] ?>"
                                                            data-desa="<?= $row['desa'] ?>"
                                                            data-bs-toggle="modal" data-bs-target="#modalEditLHP"
                                                            data-bs-title="Edit">
                                                            <i class="feather feather-edit-3"></i>
                                                        </a>
                                                        <a href="#"
                                                            class="avatar-text avatar-md text-danger btn-delete btn-submit"
                                                            data-id="<?= $row['id_lhp'] ?>" data-bs-title="Hapus">
                                                            <i class="feather feather-trash-2"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <form id="formDelete" action="action_lhp_2024.php" method="post" style="display:none;">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_lhp" id="delete_id">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Modal Tambah LHP -->
    <div class="modal fade" id="modalTambahLHP" tabindex="-1" aria-labelledby="modalTambahLHPLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="action_lhp_2024.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="tambah">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLHPLabel">
                            Tambah Laporan Hasil Pengawasan Tahun 2024
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">No LHP</label>
                                <input type="text" name="no_lhp" class="form-control" placeholder="Masukan No LHP"
                                    required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Judul LHP</label>
                                <input type="text" name="judul_lhp" class="form-control"
                                    placeholder="Masukkan Judul LHP" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Pengelola</label>
                                <select name="pengelola" class="form-control" required>
                                    <option value="" disabled selected>-- Pilih Pengelola --</option>
                                    <optgroup label="Inspektorat Kabupaten Hulu Sungai Tengah">
                                        <option value="IRBAN I">IRBAN I</option>
                                        <option value="IRBAN II">IRBAN II</option>
                                        <option value="IRBAN III">IRBAN III</option>
                                        <option value="IRBAN IV">IRBAN IV</option>
                                        <option value="IRBANSUS">IRBANSUS</option>
                                        <option value="IRBANTIM">IRBANTIM</option>
                                    </optgroup>
                                    <optgroup label="Lembaga Internal">
                                        <option value="Inspektorat Jenderal Kemendagri">Inspektorat Jenderal Kemendagri</option>
                                        <option value="Inspektorat Provinsi Kalimantan Selatan">Inspektorat Provinsi
                                            Kalimantan Selatan</option>
                                        <option value="BPKP (Badan Pengawasan Keuangan dan Pembangunan)">BPKP (Badan
                                            Pengawasan Keuangan dan Pembangunan)</option>
                                    </optgroup>
                                    <optgroup label="Lembaga Eksternal">
                                        <option value="BPK (Badan Pemeriksa Keuangan)">BPK (Badan Pemeriksa Keuangan)
                                        </option>
                                        <option value="KPK (Komisi Pemberantasan Korupsi)">KPK (Komisi Pemberantasan
                                            Korupsi)</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Sasaran</label>
                                <select name="sasaran" id="sasaran" class="form-control" required>
                                    <option value="" disabled selected>-- Pilih Sasaran --</option>
                                    <option value="Audit">Audit</option>
                                    <option value="Evaluasi">Evaluasi</option>
                                    <option value="Monitoring">Monitoring</option>
                                    <option value="Reviu">Reviu</option>
                                    <option value="Pemeriksaan">Pemeriksaan (Desa)</option>
                                </select>
                            </div>

                            <div class="col-12" id="wrap-perangkat" style="display:none;">
                                <label class="form-label">Perangkat Daerah</label>
                                <select name="perangkat_daerah" id="perangkat_daerah" class="form-control">
                                    <option value="" disabled selected>-- Pilih Perangkat Daerah --</option>
                                    <option value="BKPSDMD">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDMD)</option>
                                    <option value="BAKESBANGPOL">Badan Kesatuan Bangsa dan Politik (BAKESBANGPOL)</option>
                                    <option value="BPKPD">Badan Pengelolaan Keuangan dan Pendapatan Daerah (BPKPD)</option>
                                    <option value="BPBD">Badan Penanggulangan Bencana Daerah (BPBD)</option>
                                    <option value="BAPPERIDA">Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah (BAPPERIDA)</option>
                                    <option value="DISDUKCAPIL">Dinas Kependudukan dan Pencatatan Sipil (DISDUKCAPIL)</option>
                                    <option value="DINKES">Dinas Kesehatan (DINKES)</option>
                                    <option value="DISPORABUDPAREKRAF">Dinas Pemuda, Olahraga, Kebudayaan, Pariwisata, dan Ekonomi Kreatif (DISPORABUDPAREKRAF)</option>
                                    <option value="DKPP">Dinas Ketahanan Pangan dan Perikanan (DKPP)</option>
                                    <option value="DISKOMINFOSP">Dinas Komunikasi, Informatika, Statistik dan Persandian (DISKOMINFOSP)</option>
                                    <option value="DLHP">Dinas Lingkungan Hidup (DLH)</option>
                                    <option value="DISHUB">Dinas Perhubungan (DISHUB)</option>
                                    <option value="DISDIK">Dinas Pendidikan (DISDIK)</option>
                                    <option value="DPUPR">Dinas Pekerjaan Umum dan Penataan Ruang (DPUPR)</option>
                                    <option value="PMD">Dinas Pemberdayaan Masyarakat dan Desa (PMD)</option>
                                    <option value="DPMPTSP">Dinas Penanaman Modal, Pelayanan Terpadu Satu Pintu (DPMPTSP)</option>
                                    <option value="DISPERINAKERKOP">Dinas Perindustrian, Tenaga Kerja, Koperasi, dan UKM (DISPERINAKERKOP)</option>
                                    <option value="DISDAG">Dinas Perdagangan (DISDAG)</option>
                                    <option value="DISPERSIP"> Dinas Perpustakaan dan Kearsipan (DISPERSIP)</option>
                                    <option value="DISPERKIMTAN">Dinas Perumahan Kawasan Permukiman dan Pertanahan (DISPERKIMTAN)</option>
                                    <option value="DISTAN">Dinas Pertanian (DISTAN)</option>
                                    <option value="DINSOS PPKB PP PA">Dinas Sosial, Pengendalian Penduduk, Keluarga Berencana, Pemberdayaan Perempuan dan Perlindungan Anak (DINSOS PPKB PP & PA)</option>
                                    <option value="INSPEKTORAT">Inspektorat</option>
                                    <option value="KEC BARABAI">Kecamatan Barabai</option>
                                    <option value="KEC BAS">Kecamatan Batang Alai Selatan</option>
                                    <option value="KEC BAT">Kecamatan Batang Alai Timur</option>
                                    <option value="KEC BAU">Kecamatan Batang Alai Utara</option>
                                    <option value="KEC BATU BENAWA">Kecamatan Batu Benawa</option>
                                    <option value="KEC HANTAKAN">Kecamatan Hantakan</option>
                                    <option value="KEC HARUYAN">Kecamatan Haruyan</option>
                                    <option value="KEC LAS">Kecamatan Labuan Amas Selatan</option>
                                    <option value="KEC LAU">Kecamatan Labuan Amas Utara</option>
                                    <option value="KEC LIMPASU">Kecamatan Limpasu</option>
                                    <option value="KEC PANDAWAN">Kecamatan Pandawan</option>
                                    <option value="SATPOL PP DAMKAR">Satuan Polisi Pamong Praja dan Pemadam Kebakaran (SATPOL PP & DAMKAR)</option>
                                    <option value="SETDA">Sekretariat Daerah</option>
                                    <option value="SETWAN">Sekretariat DPRD</option>
                                </select>
                            </div>
                            <div class="col-12" id="wrap-desa" style="display:none;">
                                <label class="form-label">Desa</label>
                                <input type="text" name="desa" id="desa" class="form-control"
                                    placeholder="Masukkan Nama Desa dan Kecamatan">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Upload File LHP</label>
                                <input type="file" name="file_lhp" id="file_lhp" class="form-control file-upload-lhp"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <small class="text-muted file-name-lhp d-block mt-1"></small>
                            </div>
                            <div class="col-12">
                                <div id="temuan-wrapper">
                                    <div class="temuan-item border rounded p-3 mb-4" data-index="1">
                                        <label class="form-label">Temuan 1</label>

                                        <div class="input-group mb-3">
                                            <input type="text" name="temuan[]" class="form-control"
                                                placeholder="Temuan 1">
                                            <button type="button" class="btn btn-outline-danger remove-temuan">
                                                <i class="feather-x"></i>
                                            </button>
                                        </div>
                                        <label class="form-label">Rekomendasi</label>
                                        <div class="rekomendasi-wrapper">
                                            <div class="rek-group mb-3">
                                                <div class="input-group mb-2">
                                                    <?php $uid = time(); ?>
                                                    <input type="hidden" name="rekomendasi[1][<?= $uid ?>][id_rek]"
                                                        value="">

                                                    <input type="text" name="rekomendasi[1][<?= $uid ?>][text]"
                                                        class="form-control" placeholder="Rekomendasi 1">
                                                    <button type="button" class="btn btn-outline-danger remove-rek">
                                                        <i class="feather-x"></i>
                                                    </button>
                                                </div>
                                                <input type="file" name="file_rekomendasi[1][<?= $uid ?>]"
                                                    class="form-control file-upload">
                                                <small class="text-muted file-name d-block mt-1"></small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary add-rekomendasi">
                                            <i class="feather-plus me-1"></i>Tambah Rekomendasi
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-temuan">
                                    <i class="feather-plus me-1"></i>Tambah Temuan
                                </button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Jumlah Uang (Rp.)</label>
                                <input type="text" name="jumlah_uang" id="jumlah_uang" class="form-control"
                                    placeholder="Rp. 0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Lama Waktu</label>
                                <input type="text" id="lama_hari" class="form-control" placeholder="0 hari" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="feather-save me-2"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit LHP -->
    <div class="modal fade" id="modalEditLHP" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="action_lhp_2024.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_lhp" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Laporan Hasil Pengawasan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">No LHP</label>
                                <input type="text" name="no_lhp" id="edit_no" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Judul LHP</label>
                                <input type="text" name="judul_lhp" id="edit_judul" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Pengelola</label>
                                <select name="pengelola" id="edit_pengelola" class="form-control" required>
                                    <option value="" disabled selected>-- Pilih Pengelola --</option>
                                    <optgroup label="Inspektorat Kabupaten Hulu Sungai Tengah">
                                        <option value="IRBAN I">IRBAN I</option>
                                        <option value="IRBAN II">IRBAN II</option>
                                        <option value="IRBAN III">IRBAN III</option>
                                        <option value="IRBAN IV">IRBAN IV</option>
                                        <option value="IRBANSUS">IRBANSUS</option>
                                        <option value="IRBANTIM">IRBANTIM</option>
                                    </optgroup>
                                    <optgroup label="Lembaga Internal">
                                        <option value="Inspektorat Jenderal Kemendagri">Inspektorat Jenderal Kemendagri</option>
                                        <option value="Inspektorat Provinsi Kalimantan Selatan">Inspektorat Provinsi
                                            Kalimantan Selatan</option>
                                        <option value="BPKP (Badan Pengawasan Keuangan dan Pembangunan)">BPKP (Badan
                                            Pengawasan Keuangan dan Pembangunan)</option>
                                    </optgroup>
                                    <optgroup label="Lembaga Eksternal">
                                        <option value="BPK (Badan Pemeriksa Keuangan)">BPK (Badan Pemeriksa Keuangan)
                                        </option>
                                        <option value="KPK (Komisi Pemberantasan Korupsi)">KPK (Komisi Pemberantasan
                                            Korupsi)</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Sasaran</label>
                                <select name="sasaran" id="edit_sasaran" class="form-control" required>
                                    <option value="" disabled selected>-- Pilih Sasaran --</option>
                                    <option value="Audit">Audit</option>
                                    <option value="Evaluasi">Evaluasi</option>
                                    <option value="Monitoring">Monitoring</option>
                                    <option value="Reviu">Reviu</option>
                                    <option value="Pemeriksaan">Pemeriksaan (Desa)</option>
                                </select>
                            </div>
                            <div class="col-12" id="edit-wrap-perangkat" style="display:none;">
                                <label class="form-label">Perangkat Daerah</label>
                                <select name="perangkat_daerah" id="edit_perangkat_daerah" class="form-control">
                                    <option value="" disabled selected>-- Pilih Perangkat Daerah --</option>
                                    <option value="BKPSDMD">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDMD)</option>
                                    <option value="BAKESBANGPOL">Badan Kesatuan Bangsa dan Politik (BAKESBANGPOL)</option>
                                    <option value="BPKPD">Badan Pengelolaan Keuangan dan Pendapatan Daerah (BPKPD)</option>
                                    <option value="BPBD">Badan Penanggulangan Bencana Daerah (BPBD)</option>
                                    <option value="BAPPERIDA">Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah (BAPPERIDA)</option>
                                    <option value="DISDUKCAPIL">Dinas Kependudukan dan Pencatatan Sipil (DISDUKCAPIL)</option>
                                    <option value="DINKES">Dinas Kesehatan (DINKES)</option>
                                    <option value="DISPORABUDPAREKRAF">Dinas Pemuda, Olahraga, Kebudayaan, Pariwisata, dan Ekonomi Kreatif (DISPORABUDPAREKRAF)</option>
                                    <option value="DKPP">Dinas Ketahanan Pangan dan Perikanan (DKPP)</option>
                                    <option value="DISKOMINFOSP">Dinas Komunikasi, Informatika, Statistik dan Persandian (DISKOMINFOSP)</option>
                                    <option value="DLHP">Dinas Lingkungan Hidup (DLH)</option>
                                    <option value="DISHUB">Dinas Perhubungan (DISHUB)</option>
                                    <option value="DISDIK">Dinas Pendidikan (DISDIK)</option>
                                    <option value="DPUPR">Dinas Pekerjaan Umum dan Penataan Ruang (DPUPR)</option>
                                    <option value="PMD">Dinas Pemberdayaan Masyarakat dan Desa (PMD)</option>
                                    <option value="DPMPTSP">Dinas Penanaman Modal, Pelayanan Terpadu Satu Pintu (DPMPTSP)</option>
                                    <option value="DISPERINAKERKOP">Dinas Perindustrian, Tenaga Kerja, Koperasi, dan UKM (DISPERINAKERKOP)</option>
                                    <option value="DISDAG">Dinas Perdagangan (DISDAG)</option>
                                    <option value="DISPERSIP"> Dinas Perpustakaan dan Kearsipan (DISPERSIP)</option>
                                    <option value="DISPERKIMTAN">Dinas Perumahan Kawasan Permukiman dan Pertanahan (DISPERKIMTAN)</option>
                                    <option value="DISTAN">Dinas Pertanian (DISTAN)</option>
                                    <option value="DINSOS PPKB PP PA">Dinas Sosial, Pengendalian Penduduk, Keluarga Berencana, Pemberdayaan Perempuan dan Perlindungan Anak (DINSOS PPKB PP & PA)</option>
                                    <option value="INSPEKTORAT">Inspektorat</option>
                                    <option value="KEC BARABAI">Kecamatan Barabai</option>
                                    <option value="KEC BAS">Kecamatan Batang Alai Selatan</option>
                                    <option value="KEC BAT">Kecamatan Batang Alai Timur</option>
                                    <option value="KEC BAU">Kecamatan Batang Alai Utara</option>
                                    <option value="KEC BATU BENAWA">Kecamatan Batu Benawa</option>
                                    <option value="KEC HANTAKAN">Kecamatan Hantakan</option>
                                    <option value="KEC HARUYAN">Kecamatan Haruyan</option>
                                    <option value="KEC LAS">Kecamatan Labuan Amas Selatan</option>
                                    <option value="KEC LAU">Kecamatan Labuan Amas Utara</option>
                                    <option value="KEC LIMPASU">Kecamatan Limpasu</option>
                                    <option value="KEC PANDAWAN">Kecamatan Pandawan</option>
                                    <option value="SATPOL PP DAMKAR">Satuan Polisi Pamong Praja dan Pemadam Kebakaran (SATPOL PP & DAMKAR)</option>
                                    <option value="SETDA">Sekretariat Daerah</option>
                                    <option value="SETWAN">Sekretariat DPRD</option>
                                </select>
                            </div>
                            <div class="col-12" id="edit-wrap-desa" style="display:none;">
                                <label class="form-label">Desa</label>
                                <input type="text" name="desa" id="edit_desa" class="form-control"
                                    placeholder="Masukkan Nama Desa dan Kecamatan">
                            </div>
                            <div class="col-12">
                                <label class="form-label">File LHP</label>

                                <div id="edit-file-wrapper" class="mb-2"></div>

                                <input type="file" name="file_lhp" id="edit_file_lhp" class="form-control">

                                <small class="text-muted file-name-edit d-block mt-1"></small>
                            </div>
                            <div class="col-12">
                                <div id="edit-temuan-wrapper"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="edit-add-temuan">
                                    <i class="feather-plus me-1"></i>Tambah Temuan
                                </button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Jumlah Uang (Rp.)</label>
                                <input type="text" name="jumlah_uang" id="edit_jumlah_uang" class="form-control"
                                    placeholder="Rp. 0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="edit_mulai" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="edit_selesai" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="feather-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailLHP" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Laporan Hasil Pengawasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detail-wrapper">
                    <div class="text-center text-muted py-5">
                        <span class="spinner-border"></span>
                        <div>Memuat data...</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalVerifikasiLHP" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form action="action_lhp_2024.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="verifikasi_rekomendasi">
                    <input type="hidden" name="id_lhp" id="verifikasi_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Verifikasi Laporan Hasil Pengawasan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="verifikasi-wrapper">
                        <div class="text-center text-muted py-5">
                            <span class="spinner-border"></span>
                            <div>Memuat data...</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="feather-check-circle me-2"></i>Simpan Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal -->
    <script>
        const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]');
        const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]');
        const lamaHari = document.getElementById('lama_hari');

        function hitungLamaHari() {
            if (tanggalMulai.value && tanggalSelesai.value) {
                const mulai = new Date(tanggalMulai.value);
                const selesai = new Date(tanggalSelesai.value);
                const selisih = selesai - mulai;
                const hari = Math.floor(selisih / (1000 * 60 * 60 * 24)) + 1;
                if (hari > 0) {
                    lamaHari.value = hari + " hari";
                } else {
                    lamaHari.value = "Tanggal tidak valid";
                }
            }
        }
        tanggalMulai.addEventListener('change', hitungLamaHari);
        tanggalSelesai.addEventListener('change', hitungLamaHari);
    </script>
    <script>
        function updateCountdown() {
            document.querySelectorAll('.countdown').forEach(el => {

                const status = el.dataset.status;

                el.classList.remove('alert-deadline', 'blink');
                el.style.background = 'transparent';

                if (status === 'Selesai') {
                    el.innerHTML = `00h 00j 00m 00d`;
                    return;
                }

                const deadline = new Date(el.dataset.deadline).getTime();
                const now = new Date().getTime();
                const diff = deadline - now;

                if (diff <= 0) {
                    el.innerHTML = `00h 00j 00m 00d`;
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                const pad = n => n.toString().padStart(2, '0');

                el.innerHTML =
                    pad(days) + "h " +
                    pad(hours) + "j " +
                    pad(minutes) + "m " +
                    pad(seconds) + "d";

                if (days < 3) {
                    el.classList.add('alert-deadline');
                    el.classList.add('blink');
                }
            });
        }
        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
    <script>
        function rekomendasiEditInput(index, idTemuan, value = '', idRek = '', fileName = '') {
            const uid = Date.now() + Math.floor(Math.random() * 1000);

            return `
    <div class="rek-group mb-4 border rounded p-3">

        <div class="input-group mb-2">
            <input type="hidden"
                name="rekomendasi[${idTemuan}][${uid}][id_rek]"
                value="${idRek}">

            <input type="text"
                name="rekomendasi[${idTemuan}][${uid}][text]"
                class="form-control"
                value="${escapeHtml(value)}"
                placeholder="Rekomendasi">

            <button type="button"
                class="btn btn-outline-danger remove-rek">
                <i class="feather-x"></i>
            </button>
        </div>

        <div class="border rounded p-2 bg-light">

            ${fileName ? `
            <div class="mb-2 d-flex align-items-center gap-2">
                <i class="feather-file text-primary"></i>
                <a href="action_lhp_2024.php?action=download&file=${encodeURIComponent(fileName)}"
                   
                   class="fw-semibold text-decoration-none">
                   ${escapeHtml(fileName)}
                </a>
            </div>
            ` : `
            <div class="mb-2 text-muted small">
                Belum ada file
            </div>
            `}

            <input type="file"
                name="file_rekomendasi[${idTemuan}][${uid}]"
                class="form-control file-upload">

            <small class="text-muted file-name d-block mt-1"></small>

        </div>

    </div>
    `;
        }
    </script>
    <script>
        function escapeHtml(value) {
            const element = document.createElement('span');
            element.textContent = String(value ?? '');
            return element.innerHTML.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {

                const id = this.dataset.id;

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_no').value = this.dataset.no;
                document.getElementById('edit_judul').value = this.dataset.judul;
                document.getElementById('edit_pengelola').value = this.dataset.pengelola;
                document.getElementById('edit_mulai').value = this.dataset.mulai;
                document.getElementById('edit_selesai').value = this.dataset.selesai;
                document.getElementById('edit_sasaran').value = this.dataset.sasaran;
                document.getElementById('edit_perangkat_daerah').value = this.dataset.perangkat;
                document.getElementById('edit_desa').value = this.dataset.desa;

                document.getElementById('edit_sasaran')
                    .dispatchEvent(new Event('change'));

                document.getElementById('edit_jumlah_uang').value =
                    formatRupiah(this.dataset.jumlah.toString(), 'Rp. ');

                fetch('action_lhp_2024.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-Token': '<?= csrfToken() ?>'
                        },
                        body: `action=get_temuan_rekomendasi_edit&id_lhp=${id}`
                    })
                    .then(res => res.json())
                    .then(response => {

                        const data = response.temuan;
                        const fileLhp = response.file_lhp;

                        const wrapper = document.getElementById('edit-temuan-wrapper');
                        wrapper.innerHTML = '';

                        const fileWrapper = document.getElementById('edit-file-wrapper');

                        if (fileLhp) {
                            fileWrapper.innerHTML = `
        <div class="d-flex align-items-center gap-2">
            <i class="feather-file text-primary"></i>
            <a href="action_lhp_2024.php?action=download&file=${encodeURIComponent(fileLhp)}"
               
               class="fw-semibold text-decoration-none">
               ${escapeHtml(fileLhp)}
            </a>
        </div>
    `;
                        } else {
                            fileWrapper.innerHTML = `
        <div class="text-muted small">
            Belum ada file
        </div>
    `;
                        }

                        data.forEach((t, i) => {

                            let rekHtml = '';

                            if (t.rekomendasi.length === 0) {
                                rekHtml = rekomendasiEditInput(i + 1, t.id_temuan);
                            } else {
                                t.rekomendasi.forEach(r => {
                                    rekHtml += rekomendasiEditInput(
                                        i + 1,
                                        t.id_temuan,
                                        r.rekomendasi,
                                        r.id_rekomendasi,
                                        r.file
                                    );
                                });
                            }

                            wrapper.innerHTML += `
                <div class="temuan-item border rounded p-3 mb-3" data-index="${i + 1}">
                    <input type="hidden" name="id_temuan[]" value="${t.id_temuan}">

                    <label class="form-label">Temuan ${i + 1}</label>

                    <div class="input-group mb-2">
                        <input type="text"
                               name="temuan[]"
                               class="form-control"
                               value="${escapeHtml(t.temuan)}">
                        <button type="button"
                                class="btn btn-outline-danger remove-temuan">
                            <i class="feather-x"></i>
                        </button>
                    </div>
                    <label class="form-label">Rekomendasi</label>
                    <div class="rekomendasi-wrapper">
                        ${rekHtml}
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-outline-primary add-rekomendasi">
                        <i class="feather-plus me-1"></i>Tambah Rekomendasi
                    </button>
                </div>`;
                        });
                    });
            });
        });
    </script>
    <script>
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                if (!confirm('Apakah anda yakin ingin menghapus data ini?')) return;
                this.innerHTML = `
            <span class="spinner-border spinner-border-sm"></span>
        `;
                this.classList.add('disabled');
                setTimeout(() => {
                    document.getElementById('delete_id').value = id;
                    document.getElementById('formDelete').submit();
                }, 500);
            });
        });
    </script>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.remove();
        }, 3000);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-title]').forEach(el => {
                new bootstrap.Tooltip(el, {
                    trigger: 'hover'
                });
            });
        });
    </script>
    <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-submit');
                if (!btn) return;
                e.preventDefault();
                btn.disabled = true;
                btn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            Memproses...
        `;
                setTimeout(() => {
                    this.submit();
                }, 500);
            });
        });
    </script>
    <script>
        function formatRupiah(angka, prefix) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix + rupiah;
        }
        const tambahJumlah = document.getElementById('jumlah_uang');
        if (tambahJumlah) {
            tambahJumlah.addEventListener('keyup', function() {
                this.value = formatRupiah(this.value, 'Rp. ');
            });
        }
        const editJumlah = document.getElementById('edit_jumlah_uang');
        if (editJumlah) {
            editJumlah.addEventListener('keyup', function() {
                this.value = formatRupiah(this.value, 'Rp. ');
            });
        }
    </script>

    <script>
        document.querySelectorAll('.btn-detail').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const wrapper = document.getElementById('detail-wrapper');

                wrapper.innerHTML = `
                <div class="text-center text-muted py-5">
                    <span class="spinner-border"></span>
                    <div>Memuat data...</div>
                </div>
            `;

                fetch('action_lhp_2024.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-Token': '<?= csrfToken() ?>'
                        },
                        body: `action=get_detail&id_lhp=${id}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        let html = `
                        <table class="table table-borderless mb-4">
                            <tr>
                                <th width="200">No LHP</th>
                                <td>${escapeHtml(data.lhp.no_lhp)}</td>
                            </tr>
                            <tr>
                                <th>Judul</th>
                                <td>${escapeHtml(data.lhp.judul_lhp)}</td>
                            </tr>
                            <tr>
                                <th>Pengelola</th>
                                <td>${escapeHtml(data.lhp.pengelola ?? '-')}</td>
                            </tr>
                            <tr>
                            <th>Sasaran</th>
                            <td>
        ${
            (data.lhp.sasaran === 'Audit' ||
            data.lhp.sasaran === 'Reviu' ||
            data.lhp.sasaran === 'Evaluasi' ||
            data.lhp.sasaran === 'Monitoring')
            ? `${escapeHtml(data.lhp.sasaran)} - ${escapeHtml(data.lhp.perangkat_daerah ?? '-')}`
            : data.lhp.sasaran === 'Pemeriksaan'
            ? `Pemeriksaan - ${escapeHtml(data.lhp.desa ?? '-')}`
            : '-'
        }
                            </td>
                            </tr>
                            <tr>
                                <th>Jumlah Uang</th>
                                <td>Rp. ${formatRupiahDisplay(data.lhp.jumlah_uang)}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>
                                    ${formatTanggalIndonesia(data.lhp.tanggal_mulai)}
                                    <span>
                                        s/d ${formatTanggalIndonesia(data.lhp.tanggal_selesai)}
                                    </span>
                                </td>
                            </tr>
                            ${data.lhp.file_lhp ? `
<tr>
    <th>File LHP</th>
    <td>
        <div class="d-flex justify-content-between align-items-center border rounded p-3 bg-light">

            <div class="d-flex align-items-center gap-2">
                <i class="feather-file text-primary fs-5"></i>
                <div>
                    <div class="fw-semibold text-muted">
                        ${escapeHtml(data.lhp.file_lhp)}
                    </div>
                </div>
            </div>

            <a href="action_lhp_2024.php?action=download&file=${encodeURIComponent(data.lhp.file_lhp)}"
               class="btn btn-primary btn-sm">
                <i class="feather-download me-1"></i>
                Download
            </a>

        </div>
    </td>
</tr>
` : ''}
                        </table>
                    `;

                        data.temuan.forEach((t, i) => {

                            html += `
    <div class="card shadow-sm border-0 mb-4 detail-card">

        <div class="card-header bg-white detail-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-semibold text-dark">
                    
                    Temuan ${i + 1}
                </div>
            </div>
        </div>

        <div class="card-body detail-body-line">

            <div class="mb-4 text-dark">
                ${escapeHtml(t.temuan)}
            </div>
    `;

                            if (t.rekomendasi.length > 0) {

                                t.rekomendasi.forEach((r, idx) => {

                                    html += `
            <div class="rekom-card border rounded p-3 mb-3">

                <div class="fw-semibold mb-2 text-dark">
                    Rekomendasi ${idx + 1}
                </div>

                <div class="text-dark mb-3">
                    ${escapeHtml(r.text)}
                </div>

                ${r.file ? `
<div class="file-box d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-2">
        <i class="feather-file text-primary"></i>
        <span class="fw-semibold">
            ${escapeHtml(r.file)}
        </span>
    </div>

    <a href="action_lhp_2024.php?action=download&file=${encodeURIComponent(r.file)}"
       class="btn btn-sm btn-primary">
        <i class="feather-download me-1"></i>
        Download
    </a>
</div>
` : `
<div class="text-muted small mb-3">
    Tidak ada file pendukung
</div>
`}

                <div class="mb-2">
                    ${r.status === 'sesuai'
                                            ? `<span class="badge bg-success px-3 py-2">Sesuai</span>`
                                            : r.status === 'tidak_sesuai'
                                                ? `<span class="badge bg-danger px-3 py-2">Tidak Sesuai</span>`
                                                : `<span class="badge bg-secondary px-3 py-2">Belum Diverifikasi</span>`
                                        }
                </div>

                <div class="text-dark small">
                    <strong>Catatan Pemeriksa:</strong>
                    ${escapeHtml(r.catatan && r.catatan !== '' ? r.catatan : '-')}
                </div>

            </div>
            `;
                                });

                            } else {

                                html += `
        <div class="alert alert-light border mb-0">
            Belum ada rekomendasi
        </div>
        `;
                            }

                            html += `
        </div>
    </div>
    `;
                        });

                        wrapper.innerHTML = html;
                    });
            });
        });
    </script>
    <script>
        function formatTanggalIndonesia(tanggal) {
            const bulan = [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
            ];
            const d = new Date(tanggal);
            const hari = String(d.getDate()).padStart(2, '0');
            const bln = bulan[d.getMonth()];
            const tahun = d.getFullYear();
            return `${hari} ${bln} ${tahun}`;
        }

        function formatRupiahDisplay(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }
    </script>
    <script>
        document.getElementById('add-temuan').addEventListener('click', function() {

            const wrapper = document.getElementById('temuan-wrapper');
            const index = wrapper.querySelectorAll('.temuan-item').length + 1;
            const uid = Date.now() + Math.floor(Math.random() * 1000);

            wrapper.insertAdjacentHTML('beforeend', `
        <div class="temuan-item border rounded p-3 mb-4" data-index="${index}">
            <label class="form-label">Temuan ${index}</label>

            <div class="input-group mb-3">
                <input type="text"
                       name="temuan[]"
                       class="form-control"
                       placeholder="Temuan ${index}">
                <button type="button"
                        class="btn btn-outline-danger remove-temuan">
                    <i class="feather-x"></i>
                </button>
            </div>

            <label class="form-label">Rekomendasi</label>

            <div class="rekomendasi-wrapper">
                <div class="rek-group mb-3">

                    <input type="hidden"
                        name="rekomendasi[${index}][${uid}][id_rek]"
                        value="">

                    <div class="input-group mb-2">
                        <input type="text"
                               name="rekomendasi[${index}][${uid}][text]"
                               class="form-control"
                               placeholder="Rekomendasi 1">

                        <button type="button"
                                class="btn btn-outline-danger remove-rek">
                            <i class="feather-x"></i>
                        </button>
                    </div>

                    <input type="file"
                           name="file_rekomendasi[${index}][${uid}]"
                           class="form-control file-upload">

                    <small class="text-muted file-name d-block mt-1"></small>
                </div>
            </div>

            <button type="button"
                class="btn btn-sm btn-outline-primary add-rekomendasi">
                <i class="feather-plus me-1"></i>Tambah Rekomendasi
            </button>
        </div>
    `);

        });
    </script>
    <script>
        document.getElementById('edit-add-temuan').addEventListener('click', function() {
            const wrapper = document.getElementById('edit-temuan-wrapper');
            const index = wrapper.querySelectorAll('.temuan-item').length + 1;
            const uid = Date.now() + Math.floor(Math.random() * 1000);

            wrapper.insertAdjacentHTML('beforeend', `
        <div class="temuan-item border rounded p-3 mb-4" data-index="${index}">
            <input type="hidden" name="id_temuan[]" value="">

            <label class="form-label">Temuan ${index}</label>

            <div class="input-group mb-3">
                <input type="text"
                       name="temuan[]"
                       class="form-control"
                       placeholder="Temuan ${index}">
                <button type="button"
                        class="btn btn-outline-danger remove-temuan">
                    <i class="feather-x"></i>
                </button>
            </div>

            <label class="form-label">Rekomendasi</label>

            <div class="rekomendasi-wrapper">
                <div class="rek-group mb-3">
                    <input type="hidden"
                        name="rekomendasi[new_${index}][${uid}][id_rek]"
                        value="">

                    <div class="input-group mb-2">
                        <input type="text"
                            name="rekomendasi[new_${index}][${uid}][text]"
                            class="form-control"
                            placeholder="Rekomendasi 1">
                        <button type="button"
                            class="btn btn-outline-danger remove-rek">
                            <i class="feather-x"></i>
                        </button>
                    </div>

                    <input type="file"
                        name="file_rekomendasi[new_${index}][${uid}]"
                        class="form-control file-upload">
                    <small class="text-muted file-name d-block mt-1"></small>
                </div>
            </div>

            <button type="button"
                class="btn btn-sm btn-outline-primary add-rekomendasi">
                <i class="feather-plus me-1"></i>Tambah Rekomendasi
            </button>
        </div>
    `);

            refreshTemuanEdit(wrapper);
        });
    </script>

    <script>
        function refreshTemuan() {
            const items = document.querySelectorAll('.temuan-item');

            items.forEach((item, i) => {
                const nomor = i + 1;
                item.dataset.index = nomor;

                item.querySelector('label').innerText = `Temuan ${nomor}`;
                item.querySelector('input[name="temuan[]"]').placeholder = `Temuan ${nomor}`;
            });
        }
    </script>
    <script>
        function refreshTemuanEdit(wrapper) {
            wrapper.querySelectorAll('.temuan-item').forEach((item, i) => {
                const nomor = i + 1;
                item.dataset.index = nomor;
                item.querySelector('label').innerText = `Temuan ${nomor}`;
            });
        }
    </script>
    <script>
        document.addEventListener('click', function(e) {

            const addRekBtn = e.target.closest('.add-rekomendasi');
            if (addRekBtn) {
                const temuanItem = addRekBtn.closest('.temuan-item');
                const wrapper = temuanItem.querySelector('.rekomendasi-wrapper'); // ✅ WAJIB
                const hiddenIdTemuan = temuanItem.querySelector('input[name="id_temuan[]"]');
                const index = temuanItem.dataset.index;
                const uid = Date.now() + Math.floor(Math.random() * 1000);

                let keyName;

                if (hiddenIdTemuan && hiddenIdTemuan.value) {
                    keyName = hiddenIdTemuan.value;
                } else if (hiddenIdTemuan && !hiddenIdTemuan.value) {
                    keyName = `new_${index}`;
                } else {
                    keyName = index;
                }

                wrapper.insertAdjacentHTML('beforeend', `
<div class="rek-group mb-3">

    <input type="hidden"
        name="rekomendasi[${keyName}][${uid}][id_rek]"
        value="">

    <div class="input-group mb-2">
        <input type="text"
            name="rekomendasi[${keyName}][${uid}][text]"
            class="form-control"
            placeholder="Rekomendasi">

        <button type="button"
            class="btn btn-outline-danger remove-rek">
            <i class="feather-x"></i>
        </button>
    </div>

    <input type="file"
        name="file_rekomendasi[${keyName}][${uid}]"
        class="form-control file-upload">

    <small class="text-muted file-name d-block mt-1"></small>
</div>
`);
                refreshRekomendasi(wrapper);

                return;
            }

            const removeRekBtn = e.target.closest('.remove-rek');

            if (removeRekBtn) {

                const rekGroup = removeRekBtn.closest('.rek-group');
                const wrapper = rekGroup.closest('.rekomendasi-wrapper');
                const idRekInput = rekGroup.querySelector('input[name$="[id_rek]"]');

                if (idRekInput && idRekInput.value) {

                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'hapus_rekomendasi[]';
                    hidden.value = idRekInput.value;

                    wrapper.appendChild(hidden);
                }

                rekGroup.style.transition = "all .2s ease";
                rekGroup.style.opacity = "0";
                rekGroup.style.transform = "translateX(10px)";
                setTimeout(() => {
                    rekGroup.remove();
                }, 200);

                refreshRekomendasi(wrapper);

                return;
            }

            const removeTemuanBtn = e.target.closest('.remove-temuan');

            if (removeTemuanBtn) {

                const temuanItem = removeTemuanBtn.closest('.temuan-item');
                const wrapper = temuanItem.closest('#temuan-wrapper, #edit-temuan-wrapper');
                const items = wrapper.querySelectorAll('.temuan-item');

                if (items.length <= 1) {
                    alert('Minimal harus ada 1 temuan');
                    return;
                }

                const rekGroups = temuanItem.querySelectorAll('.rek-group');

                rekGroups.forEach(group => {
                    group.style.transition = "all .2s ease";
                    group.style.opacity = "0";
                    group.style.transform = "translateX(10px)";
                });

                temuanItem.style.transition = "all .3s ease";
                temuanItem.style.opacity = "0";
                temuanItem.style.transform = "translateX(10px)";

                setTimeout(() => {
                    temuanItem.remove();

                    if (wrapper.id === 'edit-temuan-wrapper') {
                        refreshTemuanEdit(wrapper);
                    } else {
                        refreshTemuan();
                    }

                    refreshRekomendasiIndex(wrapper);

                }, 250);

                return;
            }
        });
    </script>
    <script>
        function refreshRekomendasi(wrapper) {
            const items = wrapper.querySelectorAll('.input-group');

            items.forEach((item, index) => {
                const nomor = index + 1;
                const input = item.querySelector('input[type="text"]');
                if (input) {
                    input.placeholder = `Rekomendasi ${nomor}`;
                }
            });
        }
    </script>
    <script>
        document.querySelectorAll('.btn-verifikasi').forEach(btn => {
            btn.addEventListener('click', function() {

                const id = this.dataset.id;
                document.getElementById('verifikasi_id').value = id;

                const wrapper = document.getElementById('verifikasi-wrapper');
                wrapper.innerHTML = `
            <div class="text-center text-muted py-5">
                <span class="spinner-border"></span>
                <div>Memuat data...</div>
            </div>
        `;
                fetch('action_lhp_2024.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-Token': '<?= csrfToken() ?>'
                        },
                        body: `action=get_verifikasi&id_lhp=${id}`
                    })
                    .then(res => res.json())
                    .then(data => {

                        let html = `
    <table class="table table-borderless mb-4">
        <tr>
            <th width="200">No LHP</th>
            <td>${escapeHtml(data.lhp.no_lhp)}</td>
        </tr>

        <tr>
            <th>Judul</th>
            <td>${escapeHtml(data.lhp.judul_lhp)}</td>
        </tr>

        <tr>
            <th>Pengelola</th>
            <td>${escapeHtml(data.lhp.pengelola)}</td>
        </tr>
<tr>
    <th>Sasaran</th>
    <td>
        ${
            (data.lhp.sasaran === 'Audit' ||
            data.lhp.sasaran === 'Reviu' ||
            data.lhp.sasaran === 'Evaluasi' ||
            data.lhp.sasaran === 'Monitoring')
            ? `${escapeHtml(data.lhp.sasaran)} - ${escapeHtml(data.lhp.perangkat_daerah ?? '-')}`
            : data.lhp.sasaran === 'Pemeriksaan'
            ? `Pemeriksaan - ${escapeHtml(data.lhp.desa ?? '-')}`
            : '-'
        }
    </td>
</tr>
        <tr>
            <th>Jumlah Uang</th>
            <td>Rp. ${formatRupiahDisplay(data.lhp.jumlah_uang)}</td>
        </tr>

        <tr>
            <th>Tanggal</th>
            <td>
                ${formatTanggalIndonesia(data.lhp.tanggal_mulai)}
                s/d
                ${formatTanggalIndonesia(data.lhp.tanggal_selesai)}
            </td>
        </tr>

       ${data.lhp.file_lhp ? `
<tr>
    <th>File LHP</th>
    <td>
        <div class="d-flex justify-content-between align-items-center border rounded p-3 bg-light">

            <div class="d-flex align-items-center gap-2">
                <i class="feather-file text-primary fs-5"></i>
                <div>
                    <div class="fw-semibold text-muted">
                        ${escapeHtml(data.lhp.file_lhp)}
                    </div>
                </div>
            </div>

            <a href="action_lhp_2024.php?action=download&file=${encodeURIComponent(data.lhp.file_lhp)}"
               class="btn btn-primary btn-sm">
                <i class="feather-download me-1"></i>
                Download
            </a>

        </div>
    </td>
</tr>
` : ''}

    </table>
`;
                        data.temuan.forEach((t, i) => {
                            html += `
                    <div class="card mb-3">
                        <div class="card-header bg-white">
    <strong class="text-dark">
        
        Temuan ${i + 1}
    </strong>
</div>
                        <div class="card-body detail-body-line">
                            <p class="text-dark">${escapeHtml(t.temuan)}</p>
                `;
                            t.rekomendasi.forEach((r, idx) => {

                                html += `
    <div class="verif-card border rounded p-3 mb-3">

        <input type="hidden" name="id_rekomendasi[]" value="${r.id_rekomendasi}">

        <div class="fw-semibold text-dark mb-1">
    Rekomendasi ${idx + 1}
</div>

        <div class="text-dark mb-3">
            ${escapeHtml(r.text)}
        </div>

        ${r.file ? `
        <div class="file-box d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="feather-file text-primary"></i>
                <span class="fw-semibold">
                    ${escapeHtml(r.file)}
                </span>
            </div>

            <a href="action_lhp_2024.php?action=download&file=${encodeURIComponent(r.file)}"
               
               class="btn btn-sm btn-primary">
                <i class="feather-download me-1"></i>
                Download
            </a>
        </div>
        ` : `
        <div class="text-muted small mb-3">
            Tidak ada file pendukung
        </div>
        `}

        <div class="mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="radio"
                       name="status[${r.id_rekomendasi}]"
                       value="sesuai"
                       ${r.status === 'sesuai' ? 'checked' : ''}>
                <label class="form-check-label">
                    <span class="badge bg-success">Sesuai</span>
                </label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="radio"
                       name="status[${r.id_rekomendasi}]"
                       value="tidak_sesuai"
                       ${r.status === 'tidak_sesuai' ? 'checked' : ''}>
                <label class="form-check-label">
                    <span class="badge bg-danger">Tidak Sesuai</span>
                </label>
            </div>
        </div>

        <input type="text"
            name="catatan[${r.id_rekomendasi}]"
            class="form-control"
            placeholder="Catatan pemeriksa (opsional). Apabila tidak terdapat catatan, diisi dengan tanda (-)"
            value="${escapeHtml(r.catatan ?? '')}">

    </div>
    `;
                            });

                            html += `</div></div>`;
                        });
                        wrapper.innerHTML = html;
                    });
            });
        });
    </script>
    <script>
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('file-upload')) {
                const fileInput = e.target;
                const group = fileInput.closest('.rek-group');
                const fileNameDisplay = group.querySelector('.file-name');

                if (fileInput.files.length > 0) {
                    fileNameDisplay.innerHTML =
                        '<i class="feather-file me-1"></i>' +
                        fileInput.files[0].name;
                } else {
                    fileNameDisplay.innerHTML = '';
                }
            }
        });
    </script>
    <script>
        document.getElementById('file_lhp').addEventListener('change', function() {

            const display = document.querySelector('.file-name-lhp');

            if (this.files.length > 0) {
                display.innerHTML =
                    '<i class="feather-file me-1"></i>' +
                    this.files[0].name;
            } else {
                display.innerHTML = '';
            }

        });
    </script>
    <script>
        document.getElementById('edit_file_lhp').addEventListener('change', function() {

            const display = document.querySelector('.file-name-edit');

            if (this.files.length > 0) {
                display.innerHTML =
                    '<i class="feather-file me-1"></i>' +
                    this.files[0].name;
            } else {
                display.innerHTML = '';
            }

        });
    </script>
    <script>
        function refreshRekomendasiIndex(wrapper) {

            wrapper.querySelectorAll('.temuan-item').forEach((item, i) => {

                const nomor = i + 1;
                item.dataset.index = nomor;

                item.querySelectorAll('.rek-group').forEach(group => {

                    const uidInput = group.querySelector('input[name*="[id_rek]"]');
                    const uid = uidInput.name.match(/\[(.*?)\]\[(.*?)\]/)[2];

                    uidInput.name = `rekomendasi[${nomor}][${uid}][id_rek]`;

                    group.querySelector('input[type="text"]').name =
                        `rekomendasi[${nomor}][${uid}][text]`;

                    group.querySelector('input[type="file"]').name =
                        `file_rekomendasi[${nomor}][${uid}]`;
                });
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const sasaran = document.getElementById('sasaran');

            const wrapPerangkat = document.getElementById('wrap-perangkat');
            const perangkat = document.getElementById('perangkat_daerah');

            const wrapDesa = document.getElementById('wrap-desa');
            const desa = document.getElementById('desa');

            function resetField() {
                wrapPerangkat.style.display = 'none';
                perangkat.required = false;

                wrapDesa.style.display = 'none';
                desa.required = false;
            }

            sasaran.addEventListener('change', function() {

                resetField();

                if (
                    this.value === 'Audit' ||
                    this.value === 'Reviu' ||
                    this.value === 'Evaluasi' ||
                    this.value === 'Monitoring'
                ) {
                    wrapPerangkat.style.display = 'block';
                    perangkat.required = true;
                }

                if (this.value === 'Pemeriksaan') {
                    wrapDesa.style.display = 'block';
                    desa.required = true;
                }
            });

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const sasaranEdit = document.getElementById('edit_sasaran');

            const wrapPerangkatEdit = document.getElementById('edit-wrap-perangkat');
            const perangkatEdit = document.getElementById('edit_perangkat_daerah');

            const wrapDesaEdit = document.getElementById('edit-wrap-desa');
            const desaEdit = document.getElementById('edit_desa');

            function resetEdit() {
                wrapPerangkatEdit.style.display = 'none';
                perangkatEdit.required = false;

                wrapDesaEdit.style.display = 'none';
                desaEdit.required = false;
            }

            function handleEdit() {
                resetEdit();

                if (
                    sasaranEdit.value === 'Audit' ||
                    sasaranEdit.value === 'Reviu' ||
                    sasaranEdit.value === 'Evaluasi' ||
                    sasaranEdit.value === 'Monitoring'
                ) {
                    wrapPerangkatEdit.style.display = 'block';
                    perangkatEdit.required = true;
                }

                if (sasaranEdit.value === 'Pemeriksaan') {
                    wrapDesaEdit.style.display = 'block';
                    desaEdit.required = true;
                }
            }

            sasaranEdit.addEventListener('change', handleEdit);

        });
    </script>