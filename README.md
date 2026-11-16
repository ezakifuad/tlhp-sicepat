# SI-CAPAT

Aplikasi PHP native untuk pengelolaan Laporan Hasil Pengawasan (LHP). Jalankan folder ini sebagai root repo GitHub khusus SI-CAPAT agar GitHub Actions mendeteksi workflow di `.github/workflows/`.

## Publikasi pertama ke GitHub

Buat repo privat kosong di GitHub. Dari folder proyek lokal `tlhp/`:

```bash
git init -b main
git add .
git diff --cached --name-only
git commit -m "Prepare SI-CAPAT container deployment"
git remote add origin git@github.com:ORGANISASI/sicapat.git
git push -u origin main
```

Periksa daftar file sebelum commit dan ganti `ORGANISASI/sicapat` dengan repo tujuan. Bila folder ini sudah punya repo sendiri, gunakan remote dan branch yang sudah disiapkan.

## Instalasi pertama

Diperlukan Docker Engine dan Docker Compose v2 dengan dukungan `--wait`.

```bash
git clone git@github.com:ORGANISASI/sicapat.git
cd sicapat
cp .env.example .env
chmod 600 .env
```

Isi `DB_PASSWORD` dan `DB_ROOT_PASSWORD` dengan dua nilai acak yang berbeda, misalnya dari dua kali menjalankan `openssl rand -hex 24`. Kemudian jalankan:

```bash
docker compose config --quiet
docker compose up -d --build --wait --wait-timeout 180
docker compose ps
```

Aplikasi tersedia di `http://127.0.0.1:8088/` pada server. Untuk aaPanel, buat reverse proxy HTTPS ke `http://127.0.0.1:8088`. Isi `SESSION_COOKIE_SECURE=1` di `.env` saat akses pengguna melalui HTTPS, lalu jalankan kembali Compose. Gunakan `SESSION_COOKIE_SECURE=0` untuk pengujian HTTP lokal.

MariaDB membuat skema kosong pada volume baru. Untuk membuat akun admin pertama dari terminal Bash di server:

```bash
read -rs -p 'Kata sandi admin baru (minimal 12 karakter): ' admin_password
printf '\n'
printf '%s\n' "$admin_password" | docker compose exec -T app php /opt/sicapat/create-admin.php admin 'Administrator'
unset admin_password
```

Untuk migrasi instalasi lama, siapkan dump dan lampiran dari waktu cadangan yang sama. Impor ke database tujuan yang sesuai dan salin folder `lhp_2022` sampai `lhp_2026` ke volume lampiran sambil mempertahankan nama berkas. Uji pemulihan pada lingkungan terpisah sebelum memakai data riil. Skema inisialisasi hanya dijalankan saat volume database baru. Perubahan struktur berikutnya memerlukan migrasi database tersendiri.

## Pembaruan melalui GitHub

Push perubahan aplikasi ke repo privat. GitHub Actions membangun dan menguji container saat push atau pull request. Server menarik perubahan secara manual dari branch yang sudah di-checkout dan dilacak. Untuk repo privat, gunakan deploy key SSH dengan akses baca.

Dari checkout SI-CAPAT di server:

```bash
sh docker/update.sh
```

Skrip menarik perubahan dengan fast-forward, membangun ulang image, dan menunggu health check. Simpan revisi sebelumnya yang dicetak skrip jika perlu rollback. Untuk rollback kode setelah mencadangkan data, checkout commit sebelumnya dengan `git switch --detach COMMIT`, lalu jalankan `docker compose up -d --build --wait --wait-timeout 180`. Kembali ke branch deploy dengan `git switch main` jika nama branch-nya `main`.

Volume bernama `sicapat_database` dan `sicapat_attachments` menyimpan database dan lampiran terpisah dari image. Cadangkan keduanya sebelum pembaruan produksi. Perintah `docker compose down -v` menghapus volume aplikasi.

## Status deployment

Gunakan staging dengan data dummy. Pemeriksaan login, peran admin pada perubahan akun, dan token CSRF sudah dipasang. Model akses LHP masih berbasis pengguna yang sudah login. Pembatasan per laporan/OPD, validasi unggah, penanganan kegagalan saat mengganti lampiran, dan uji pemulihan masih perlu dituntaskan sebelum go-live.

Sebelum push pertama, periksa daftar file staging Git. Konfigurasi lokal `.env`, dump lama `tlhp_db.sql`, lampiran `uploads/`, dan cadangan `backups/` sudah dikecualikan. Jika file tersebut pernah masuk riwayat Git, penambahan aturan ignore tidak menghapus riwayatnya.
