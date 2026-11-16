<?php
require '/var/www/html/koneksi.php';

foreach (['users', 'lhp_2022', 'lhp_2023', 'lhp_2024', 'lhp_2025', 'lhp_2026'] as $table) {
    $conn->query("SELECT 1 FROM `$table` LIMIT 1");
}
