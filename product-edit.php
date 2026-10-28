<?php
// Memastikan pengguna hanya bisa mengedit listing miliknya sendiri.
require_once __DIR__ . '/app/bootstrap.php';
require_login();

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$productId, $_SESSION['user']['id']]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('error', 'Barang tidak ditemukan atau bukan milikmu.');
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_INT);
    $category = trim($_POST['category'] ?? '');
    $condition = trim($_POST['condition_label'] ?? '');
    $status = $_POST['status'] ?? 'available';

    if ($title === '' || $description === '' || $price === false || $price === null || $price < 0) {
        $error = 'Lengkapi nama, deskripsi, dan harga dengan benar.';
    } else {
        try {
            $newImage = save_product_image($_FILES['image'] ?? []);
            $image = $newImage ?: $product['image'];

            $update = $pdo->prepare(
                'UPDATE products
                 SET title = ?, description = ?, price = ?, category = ?,
                     condition_label = ?, image = ?, status = ?
                 WHERE id = ? AND user_id = ?'
            );
            $update->execute([
                $title,
                $description,
                $price,
                $category,
                $condition,
                $image,
                in_array($status, ['available', 'sold'], true) ? $status : 'available',
                $productId,
                $_SESSION['user']['id'],
            ]);

            if ($newImage) {
                delete_product_image($product['image']);
            }

            set_flash('success', 'Perubahan barang berhasil disimpan.');
            header('Location: dashboard.php');
            exit;
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }
}

$pageTitle = 'Edit Barang — PrelovedKu';
$heading = 'Rapikan listing-mu';
$subheading = 'Perbarui informasi agar calon pembeli mendapat detail yang tepat.';
$submitLabel = 'Simpan perubahan';
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="page-shell editor-shell">
    <a class="back-link" href="dashboard.php">← Kembali ke dashboard</a>
    <div class="editor-heading"><span class="eyebrow">EDIT YOUR LISTING</span><h1><?= e($heading) ?><span class="heading-dot">.</span></h1><p><?= e($subheading) ?></p></div>
    <?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
    <?php require __DIR__ . '/product-form.php'; ?>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
