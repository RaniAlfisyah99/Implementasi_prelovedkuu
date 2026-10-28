<?php
// Menangani pembuatan listing baru oleh pengguna yang login.
require_once __DIR__ . '/app/bootstrap.php';
require_login();

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
            $image = save_product_image($_FILES['image'] ?? []);

            $stmt = $pdo->prepare(
                'INSERT INTO products (user_id, title, description, price, category, condition_label, image, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $_SESSION['user']['id'],
                $title,
                $description,
                $price,
                $category,
                $condition,
                $image,
                in_array($status, ['available', 'sold'], true) ? $status : 'available',
            ]);

            set_flash('success', 'Barang berhasil ditambahkan.');
            header('Location: dashboard.php');
            exit;
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }
}

$pageTitle = 'Tambah Barang — PrelovedKu';
$heading = 'Tambah barang baru';
$subheading = 'Bagikan barang baikmu kepada pemilik berikutnya.';
$submitLabel = 'Publikasikan barang';
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="page-shell editor-shell">
    <a class="back-link" href="dashboard.php">← Kembali ke dashboard</a>
    <div class="editor-heading"><span class="eyebrow">MAKE A LISTING</span><h1><?= e($heading) ?><span class="heading-dot">.</span></h1><p><?= e($subheading) ?></p></div>
    <?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
    <?php require __DIR__ . '/product-form.php'; ?>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
