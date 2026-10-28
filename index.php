<?php
// Memuat konfigurasi, helper, dan koneksi database.
require_once __DIR__ . '/app/bootstrap.php';

// Mengambil produk terbaru untuk ditampilkan di landing page.
$stmt = $pdo->query(
    "SELECT products.*, users.name AS seller_name
     FROM products
     JOIN users ON users.id = products.user_id
     WHERE products.status = 'available'
     ORDER BY products.created_at DESC
     LIMIT 6"
);
$products = $stmt->fetchAll();

$pageTitle = 'PrelovedKu — Temukan barang yang punya cerita';
require __DIR__ . '/app/views/partials/header.php';
?>
<main>
    <section class="hero">
        <div class="hero-copy">
            <span class="eyebrow">SECOND LIFE, BETTER STYLE</span>
            <h1>Barang lama.<br><span>Cerita baru.</span></h1>
            <p>Temukan barang preloved pilihan yang masih punya banyak cerita. Belanja lebih bijak, tampil lebih unik.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="products.php">Jelajahi barang <span>↗</span></a>
                <a class="button button-quiet" href="register.php">Mulai jual barang</a>
            </div>
            <div class="hero-note"><span class="status-dot"></span> Pilihan unik, harga lebih ramah</div>
        </div>
        <div class="hero-visual">
            <div class="hero-photo"></div>
            <div class="floating-card">
                <span class="floating-label">A LITTLE MORE CONSCIOUS</span>
                <strong>Good things<br>deserve a second life.</strong>
                <span class="floating-mark">✳</span>
            </div>
            <div class="hero-sticker">PRE-LOVED<br>PRE-LOVED<br>PRE-LOVED</div>
        </div>
    </section>

    <section class="category-strip">
        <div><span class="category-icon">✳</span><span>Fashion</span></div>
        <div><span class="category-icon">⌂</span><span>Rumah & dekor</span></div>
        <div><span class="category-icon">◉</span><span>Elektronik</span></div>
        <div><span class="category-icon">♡</span><span>Aksesori</span></div>
        <div><span class="category-icon">✦</span><span>Lainnya</span></div>
    </section>

    <section class="section product-section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">THE GOOD FINDS</span>
                <h2>Temukan favoritmu<span class="heading-dot">.</span></h2>
                <p>Barang terpilih yang siap menemukan pemilik baru.</p>
            </div>
            <a class="text-link" href="products.php">Lihat semua barang <span>↗</span></a>
        </div>
        <div class="product-grid">
            <?php if (!$products): ?>
                <div class="empty-state">
                    <span>✳</span>
                    <h3>Rak masih kosong</h3>
                    <p>Jadilah yang pertama menambahkan barang preloved.</p>
                </div>
            <?php endif; ?>
            <?php foreach ($products as $product): ?>
                <?php require __DIR__ . '/app/views/partials/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="sell-banner">
        <div class="sell-symbol">↗</div>
        <div>
            <span class="eyebrow">YOUR CLOSET, YOUR STORY</span>
            <h2>Sudah waktunya barangmu<br>punya cerita baru.</h2>
        </div>
        <a class="button button-light" href="<?= is_logged_in() ? 'product-create.php' : 'register.php' ?>">Jual barang sekarang <span>↗</span></a>
    </section>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
