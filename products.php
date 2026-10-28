<?php
// Menampilkan katalog dengan pencarian, kategori, kondisi, dan pengurutan.
require_once __DIR__ . '/app/bootstrap.php';

$search = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$condition = trim($_GET['condition'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

$allowedSorts = [
    'newest' => 'products.created_at DESC',
    'price_low' => 'products.price ASC',
    'price_high' => 'products.price DESC',
    'name' => 'products.title ASC',
];
$orderBy = $allowedSorts[$sort] ?? $allowedSorts['newest'];

$sql = "SELECT products.*, users.name AS seller_name
        FROM products
        JOIN users ON users.id = products.user_id
        WHERE products.status = 'available'";
$params = [];

if ($search !== '') {
    $sql .= ' AND (products.title LIKE ? OR products.description LIKE ? OR products.category LIKE ?)';
    $term = '%' . $search . '%';
    array_push($params, $term, $term, $term);
}

if ($category !== '') {
    $sql .= ' AND products.category = ?';
    $params[] = $category;
}

if ($condition !== '') {
    $sql .= ' AND products.condition_label = ?';
    $params[] = $condition;
}

$sql .= ' ORDER BY ' . $orderBy;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = ['Fashion', 'Rumah & Dekor', 'Elektronik', 'Aksesori', 'Buku', 'Lainnya'];
$conditions = ['Baru', 'Seperti Baru', 'Sangat Baik', 'Baik'];

$pageTitle = 'Jelajahi Barang — PrelovedKu';
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="page-shell">
    <section class="catalog-intro">
        <span class="eyebrow">THE PRELOVED EDIT</span>
        <h1>Barang bagus,<br><span>cerita berlanjut.</span></h1>
        <p>Temukan harta kecil yang menunggu pemilik berikutnya.</p>
    </section>
    <form class="filter-panel" method="get" action="products.php">
        <label class="search-field">
            <span>⌕</span>
            <input type="search" name="q" placeholder="Cari baju, tas, dekor..." value="<?= e($search) ?>">
        </label>
        <label class="filter-select"><span>Kategori</span>
            <select name="category">
                <option value="">Semua kategori</option>
                <?php foreach ($categories as $item): ?>
                    <option value="<?= e($item) ?>" <?= $category === $item ? 'selected' : '' ?>><?= e($item) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="filter-select"><span>Kondisi</span>
            <select name="condition">
                <option value="">Semua kondisi</option>
                <?php foreach ($conditions as $item): ?>
                    <option value="<?= e($item) ?>" <?= $condition === $item ? 'selected' : '' ?>><?= e($item) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="filter-select"><span>Urutkan</span>
            <select name="sort">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Terbaru</option>
                <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Harga terendah</option>
                <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Harga tertinggi</option>
                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Nama A–Z</option>
            </select>
        </label>
        <button class="button button-dark" type="submit">Terapkan</button>
    </form>
    <div class="catalog-meta"><span><strong><?= count($products) ?></strong> barang ditemukan</span>
        <?php if ($search || $category || $condition): ?><a href="products.php">Hapus filter ×</a><?php endif; ?>
    </div>
    <div class="product-grid catalog-grid">
        <?php if (!$products): ?>
            <div class="empty-state">
                <span>⌕</span><h3>Belum ketemu yang cocok</h3>
                <p>Coba kata kunci atau filter yang berbeda.</p>
                <a class="button button-primary" href="products.php">Lihat semua barang</a>
            </div>
        <?php endif; ?>
        <?php foreach ($products as $product): ?>
            <?php require __DIR__ . '/app/views/partials/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
