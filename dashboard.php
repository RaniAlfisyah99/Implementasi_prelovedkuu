<?php
// Dashboard privat berisi ringkasan dan daftar barang milik pengguna.
require_once __DIR__ . '/app/bootstrap.php';
require_login();

$userId = (int) $_SESSION['user']['id'];

$countStmt = $pdo->prepare(
    "SELECT COUNT(*) AS total,
            SUM(status = 'available') AS available,
            SUM(status = 'sold') AS sold
     FROM products WHERE user_id = ?"
);
$countStmt->execute([$userId]);
$stats = $countStmt->fetch();

$productStmt = $pdo->prepare(
    'SELECT * FROM products WHERE user_id = ? ORDER BY created_at DESC'
);
$productStmt->execute([$userId]);
$products = $productStmt->fetchAll();

$pageTitle = 'Dashboard — PrelovedKu';
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="page-shell dashboard-shell">
    <section class="dashboard-welcome">
        <div>
            <span class="eyebrow">YOUR LITTLE CORNER</span>
            <h1>Halo, <?= e(explode(' ', $_SESSION['user']['name'])[0]) ?><span class="heading-dot">.</span></h1>
            <p>Siap memberi barangmu cerita yang baru?</p>
        </div>
        <a class="button button-primary" href="product-create.php">＋ Tambah barang</a>
    </section>
    <section class="stats-grid">
        <article class="stat-card"><span>Total barang</span><strong><?= (int) ($stats['total'] ?? 0) ?></strong><small>Semua barang yang kamu kelola</small></article>
        <article class="stat-card"><span>Masih tersedia</span><strong><?= (int) ($stats['available'] ?? 0) ?></strong><small>Siap menemukan pemilik baru</small></article>
        <article class="stat-card"><span>Terjual</span><strong><?= (int) ($stats['sold'] ?? 0) ?></strong><small>Barang yang sudah punya rumah</small></article>
    </section>
    <section class="dashboard-list">
        <div class="section-heading compact-heading">
            <div><span class="eyebrow">YOUR LISTINGS</span><h2>Barang yang kamu kelola<span class="heading-dot">.</span></h2></div>
            <a class="text-link" href="products.php">Lihat katalog ↗</a>
        </div>
        <?php if (!$products): ?>
            <div class="empty-state dashboard-empty"><span>✳</span><h3>Mulai dari satu barang</h3><p>Unggah foto dan detail barang yang ingin kamu jual.</p><a class="button button-primary" href="product-create.php">Tambah barang pertama</a></div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="listing-table">
                    <thead><tr><th>Barang</th><th>Kategori</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><div class="table-product"><img src="<?= e(product_image($product['image'])) ?>" alt=""><strong><?= e($product['title']) ?></strong></div></td>
                            <td><?= e($product['category']) ?></td>
                            <td><?= rupiah($product['price']) ?></td>
                            <td><span class="status-pill status-<?= e($product['status']) ?>"><?= $product['status'] === 'available' ? 'Tersedia' : 'Terjual' ?></span></td>
                            <td><div class="table-actions"><a href="product-edit.php?id=<?= (int) $product['id'] ?>">Edit</a><a href="product.php?id=<?= (int) $product['id'] ?>">Lihat</a><form method="post" action="product-delete.php" data-confirm="Yakin ingin menghapus barang ini?"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $product['id'] ?>"><button type="submit">Hapus</button></form></div></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
