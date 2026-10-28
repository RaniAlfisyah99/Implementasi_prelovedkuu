<?php
// Memuat kebutuhan aplikasi dan memproses form login.
require_once __DIR__ . '/app/bootstrap.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Mengambil akun berdasarkan email menggunakan prepared statement.
    $stmt = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ];

        set_flash('success', 'Berhasil masuk. Selamat datang kembali!');
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Email atau kata sandi belum sesuai.';
}

$pageTitle = 'Masuk — PrelovedKu';
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="auth-layout">
    <section class="auth-panel">
        <span class="eyebrow">WELCOME BACK</span>
        <h1>Senang melihatmu <span>lagi.</span></h1>
        <p>Masuk untuk mengelola barang jualanmu dan menemukan pilihan baru.</p>
        <?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
        <form method="post" class="form-stack">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Email
                <input type="email" name="email" placeholder="nama@email.com" required autocomplete="email" value="<?= e($_POST['email'] ?? '') ?>">
            </label>
            <label>Kata sandi
                <input type="password" name="password" placeholder="Masukkan kata sandi" required autocomplete="current-password">
            </label>
            <button class="button button-primary button-full" type="submit">Masuk ke akun <span>↗</span></button>
        </form>
        <p class="auth-switch">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
    </section>
    <aside class="auth-visual login-visual">
        <div class="auth-visual-content">
            <span class="eyebrow">LESS WASTE, MORE STYLE</span>
            <h2>Temukan hal baik<br>di tempat tak terduga.</h2>
            <p>Setiap barang punya cerita. Kamu bisa jadi bagian dari cerita berikutnya.</p>
        </div>
    </aside>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
