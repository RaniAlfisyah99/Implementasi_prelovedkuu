<?php
// Memulai sesi agar status login dapat digunakan di semua halaman.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memuat pengaturan dan penyimpanan data berbasis file JSON (tanpa database).
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';
