<?php
// Menampilkan detail produk berdasarkan ID yang ada di URL.
require_once __DIR__ . '/app/bootstrap.php';

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$productId) {
    http_response_code(404);
    exit('Barang tidak ditemukan.');
}

$stmt = $pdo->prepare(
    "SELECT products.*, users.name AS seller_name, users.email AS seller_email
     FROM products
     JOIN users ON users.id = products.user_id
     WHERE products.id = ? LIMIT 1"
);
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    exit('Barang tidak ditemukan.');
}

$pageTitle = $product['title'] . ' — PrelovedKu';
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="page-shell detail-shell">
    <a class="back-link" href="products.php">← Kembali ke katalog</a>
    <section class="detail-layout">
        <div class="detail-image">
            <img src="<?= e(product_image($product['image'])) ?>" alt="<?= e($product['title']) ?>">
            <span class="product-condition"><?= e($product['condition_label']) ?></span>
        </div>
        <div class="detail-content">
            <span class="eyebrow"><?= e($product['category']) ?></span>
            <h1><?= e($product['title']) ?></h1>
            <div class="detail-price"><?= rupiah($product['price']) ?></div>
            <div class="detail-divider"></div>
            <h3>Tentang barang ini</h3>
            <p class="detail-description"><?= nl2br(e($product['description'])) ?></p>
            <div class="seller-box">
                <span class="seller-avatar"><?= e(strtoupper(substr($product['seller_name'], 0, 1))) ?></span>
                <div><small>Dijual oleh</small><strong><?= e($product['seller_name']) ?></strong></div>
            </div>
            <?php if ($product['status'] !== 'available'): ?>
                <div class="form-error">Barang ini sudah tidak tersedia.</div>
            <?php elseif (is_logged_in() && (int) $_SESSION['user']['id'] === (int) $product['user_id']): ?>
                <a class="button button-primary button-full" href="product-edit.php?id=<?= (int) $product['id'] ?>">Edit barang milikmu</a>
            <?php else: ?>
                <a class="button button-primary button-full" href="mailto:<?= e($product['seller_email']) ?>?subject=Minat%20dengan%20<?= rawurlencode($product['title']) ?>">Hubungi penjual <span>↗</span></a>
                <p class="detail-hint">Hubungi penjual melalui email untuk menanyakan ketersediaan barang.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
