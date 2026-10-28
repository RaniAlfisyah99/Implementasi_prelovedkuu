-- Database utama PrelovedKu.
-- Impor file ini melalui phpMyAdmin atau MySQL CLI.

CREATE DATABASE IF NOT EXISTS prelovedku
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE prelovedku;

-- Tabel akun pengguna.
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel barang yang dijual pengguna.
CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    price INT UNSIGNED NOT NULL DEFAULT 0,
    category VARCHAR(50) NOT NULL,
    condition_label VARCHAR(30) NOT NULL DEFAULT 'Sangat Baik',
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('available', 'sold') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    INDEX idx_products_category (category),
    INDEX idx_products_status (status),
    INDEX idx_products_price (price),
    INDEX idx_products_created_at (created_at)
) ENGINE=InnoDB;

-- Akun demo dibuat lewat register.php agar password tetap di-hash dengan aman.
-- Foto contoh menggunakan foto produk/fashion dari Unsplash, bukan gambar AI.
-- Data contoh akan muncul setelah akun pengguna menambahkan barang sendiri.
