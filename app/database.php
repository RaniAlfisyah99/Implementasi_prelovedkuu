<?php
// Penyimpanan data sederhana berbasis file JSON (tanpa MySQL/database server).
// Variabel $pdo dipertahankan sebagai adaptor agar halaman lama tetap berjalan.
require_once __DIR__ . '/file_store.php';
$pdo = new PrelovedFileStore();
