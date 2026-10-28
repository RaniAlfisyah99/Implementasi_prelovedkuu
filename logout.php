<?php
// Mengakhiri sesi login lalu mengarahkan pengguna ke halaman utama.
require_once __DIR__ . '/app/bootstrap.php';

$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;
