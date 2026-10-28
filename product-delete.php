<?php
// Menghapus listing dengan POST, token CSRF, dan pemeriksaan kepemilikan.
require_once __DIR__ . '/app/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

verify_csrf();

$productId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT image FROM products WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$productId, $_SESSION['user']['id']]);
$product = $stmt->fetch();

if ($product) {
    $delete = $pdo->prepare('DELETE FROM products WHERE id = ? AND user_id = ?');
    $delete->execute([$productId, $_SESSION['user']['id']]);

    if ($delete->rowCount() > 0) {
        delete_product_image($product['image']);
        set_flash('success', 'Barang berhasil dihapus.');
    }
} else {
    set_flash('error', 'Barang tidak ditemukan atau bukan milikmu.');
}

header('Location: dashboard.php');
exit;
