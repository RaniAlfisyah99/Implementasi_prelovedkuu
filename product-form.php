<?php
// Komponen form bersama untuk tambah dan edit produk.
$categories = ['Fashion', 'Rumah & Dekor', 'Elektronik', 'Aksesori', 'Buku', 'Lainnya'];
$conditions = ['Baru', 'Seperti Baru', 'Sangat Baik', 'Baik'];
$product = $product ?? [
    'title' => '',
    'description' => '',
    'price' => '',
    'category' => 'Fashion',
    'condition_label' => 'Sangat Baik',
    'status' => 'available',
    'image' => null,
];
?>
<form method="post" enctype="multipart/form-data" class="editor-form">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="editor-main">
        <label>Nama barang
            <input type="text" name="title" maxlength="150" required placeholder="Contoh: Tote bag kanvas vintage" value="<?= e($product['title']) ?>">
        </label>
        <label>Deskripsi barang
            <textarea name="description" rows="6" maxlength="3000" required placeholder="Ceritakan kondisi, ukuran, warna, dan detail lainnya..."><?= e($product['description']) ?></textarea>
        </label>
        <div class="form-row">
            <label>Harga (Rupiah)
                <input type="number" name="price" min="0" max="999999999" step="1" required placeholder="85000" value="<?= e($product['price']) ?>">
            </label>
            <label>Kategori
                <select name="category" required>
                    <?php foreach ($categories as $item): ?>
                        <option value="<?= e($item) ?>" <?= $product['category'] === $item ? 'selected' : '' ?>><?= e($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div class="form-row">
            <label>Kondisi
                <select name="condition_label" required>
                    <?php foreach ($conditions as $item): ?>
                        <option value="<?= e($item) ?>" <?= $product['condition_label'] === $item ? 'selected' : '' ?>><?= e($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Status
                <select name="status" required>
                    <option value="available" <?= $product['status'] === 'available' ? 'selected' : '' ?>>Tersedia</option>
                    <option value="sold" <?= $product['status'] === 'sold' ? 'selected' : '' ?>>Terjual</option>
                </select>
            </label>
        </div>
    </div>
    <aside class="editor-aside">
        <label class="upload-box" for="image">
            <span class="upload-icon">↥</span>
            <strong>Foto barang</strong>
            <span>JPG, PNG, WEBP · Maks. 3 MB</span>
            <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        </label>
        <?php if (!empty($product['image'])): ?>
            <div class="current-image"><span>Foto saat ini</span><img src="<?= e(product_image($product['image'])) ?>" alt="Foto barang saat ini"></div>
        <?php endif; ?>
        <p class="form-tip">Tips: gunakan foto asli yang terang dan jelas supaya pembeli mudah melihat kondisi barang.</p>
        <button class="button button-primary button-full" type="submit"><?= e($submitLabel ?? 'Simpan barang') ?> <span>↗</span></button>
        <a class="cancel-link" href="dashboard.php">Batal, kembali ke dashboard</a>
    </aside>
</form>
