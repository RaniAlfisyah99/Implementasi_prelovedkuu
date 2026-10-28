<?php
// Menyiapkan judul halaman, notifikasi, dan status login.
$pageTitle = $pageTitle ?? APP_NAME;
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PrelovedKu, tempat menemukan dan menjual barang preloved dengan mudah.">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php" aria-label="PrelovedKu beranda">
        <span class="brand-mark">p.</span>
        <span>preloved<span class="brand-light">ku</span></span>
    </a>
    <nav class="main-nav" aria-label="Navigasi utama">
        <a href="index.php">Beranda</a>
        <a href="products.php">Jelajahi</a>
        <?php if (is_logged_in()): ?>
            <a href="dashboard.php">Dashboard</a>
        <?php endif; ?>
    </nav>
    <div class="header-actions">
        <?php if (is_logged_in()): ?>
            <a class="header-user" href="dashboard.php"><span class="user-avatar"><?= e(strtoupper(substr($_SESSION['user']['name'], 0, 1))) ?></span><span><?= e($_SESSION['user']['name']) ?></span></a>
            <a class="button button-dark button-small" href="logout.php">Keluar</a>
        <?php else: ?>
            <a class="login-link" href="login.php">Masuk</a>
            <a class="button button-dark button-small" href="register.php">Daftar gratis <span>↗</span></a>
        <?php endif; ?>
    </div>
</header>
<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
