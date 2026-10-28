<?php
// Memuat kebutuhan aplikasi dan memproses pendaftaran pengguna.
require_once __DIR__ . '/app/bootstrap.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (mb_strlen($name) < 2) {
        $error = 'Nama minimal terdiri dari 2 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Masukkan alamat email yang valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Kata sandi minimal 8 karakter.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Konfirmasi kata sandi belum sama.';
    } else {
        // Memeriksa email agar satu email hanya digunakan untuk satu akun.
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'Email sudah terdaftar. Silakan login.';
        } else {
            // Kata sandi disimpan dalam bentuk hash, bukan teks asli.
            $insert = $pdo->prepare(
                'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
            );
            $insert->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
            ]);

            set_flash('success', 'Akun berhasil dibuat. Silakan login.');
            header('Location: login.php');
            exit;
        }
    }
}

$pageTitle = 'Daftar — PrelovedKu';
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="auth-layout">
    <section class="auth-panel">
        <span class="eyebrow">JOIN THE CIRCLE</span>
        <h1>Mulai cerita <span>barumu.</span></h1>
        <p>Buat akun gratis dan mulai jual atau temukan barang favoritmu.</p>
        <?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
        <form method="post" class="form-stack">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Nama lengkap
                <input type="text" name="name" placeholder="Nama kamu" required minlength="2" autocomplete="name" value="<?= e($_POST['name'] ?? '') ?>">
            </label>
            <label>Email
                <input type="email" name="email" placeholder="nama@email.com" required autocomplete="email" value="<?= e($_POST['email'] ?? '') ?>">
            </label>
            <label>Kata sandi
                <input type="password" name="password" placeholder="Minimal 8 karakter" required minlength="8" autocomplete="new-password">
            </label>
            <label>Ulangi kata sandi
                <input type="password" name="confirm_password" placeholder="Ketik ulang kata sandi" required autocomplete="new-password">
            </label>
            <button class="button button-primary button-full" type="submit">Buat akun <span>↗</span></button>
        </form>
        <p class="auth-switch">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
    </section>
    <aside class="auth-visual register-visual">
        <div class="auth-visual-content">
            <span class="eyebrow">GOOD THINGS CIRCLE</span>
            <h2>Belanja dengan rasa.<br>Jual dengan tujuan.</h2>
            <p>Komunitas kecil untuk barang yang pantas mendapat kesempatan kedua.</p>
        </div>
    </aside>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
