<?php
// Mengamankan teks sebelum ditampilkan ke HTML.
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Mengecek apakah ada pengguna yang sedang login.
function is_logged_in(): bool
{
    return isset($_SESSION['user']['id']);
}

// Melindungi halaman yang hanya boleh dibuka setelah login.
function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Silakan login terlebih dahulu.');
        header('Location: login.php');
        exit;
    }
}

// Menyimpan notifikasi sementara ke sesi.
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

// Mengambil notifikasi sementara satu kali.
function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

// Membuat token CSRF sederhana untuk melindungi form.
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Memastikan token form sesuai dengan sesi.
function verify_csrf(): void
{
    $submittedToken = $_POST['csrf_token'] ?? '';
    $savedToken = $_SESSION['csrf_token'] ?? '';

    if (!$savedToken || !hash_equals($savedToken, $submittedToken)) {
        http_response_code(419);
        exit('Sesi formulir tidak valid. Silakan kembali dan coba lagi.');
    }
}

// Memformat harga menjadi mata uang Rupiah.
function rupiah($amount): string
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

// Menentukan URL gambar produk, dengan gambar cadangan jika belum ada.
function product_image(?string $image): string
{
    if ($image && is_file(UPLOAD_DIR . basename($image))) {
        return 'uploads/' . rawurlencode(basename($image));
    }

    return 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=900&q=85';
}

// Menyimpan foto yang diunggah dan mengembalikan nama filenya.
function save_product_image(array $file): ?string
{
    if (empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Foto gagal diunggah. Coba pilih foto lain.');
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        throw new RuntimeException('Ukuran foto maksimal 3 MB.');
    }

    // Memeriksa tipe file berdasarkan isi file, bukan hanya ekstensi.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowedTypes[$mime])) {
        throw new RuntimeException('Foto harus berformat JPG, PNG, atau WEBP.');
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    // Nama acak mencegah nama file pengguna saling bertabrakan.
    $fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
    $destination = UPLOAD_DIR . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Foto tidak dapat disimpan ke folder uploads.');
    }

    return $fileName;
}

// Menghapus foto lokal bila foto lama diganti atau barang dihapus.
function delete_product_image(?string $image): void
{
    if (!$image) {
        return;
    }

    $path = UPLOAD_DIR . basename($image);

    if (is_file($path)) {
        unlink($path);
    }
}
