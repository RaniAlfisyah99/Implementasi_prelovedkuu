# PrelovedKu — Website PHP tanpa database

Versi ini menggunakan penyimpanan file JSON, jadi **tidak perlu membuat database atau mengimpor SQL** dan tidak perlu menyalakan MySQL. PHP tetap dijalankan melalui XAMPP Apache.

## Cara menjalankan
1. Ekstrak folder `prelovedku` ke `C:\xampp\htdocs\PrelovedKu_PHP_Website\` (atau folder htdocs pilihanmu).
2. Jalankan **Apache** di XAMPP. MySQL boleh tetap mati.
3. Buka `http://localhost/PrelovedKu_PHP_Website/prelovedku/`.
4. Daftar akun dari menu Daftar, lalu login untuk menambah dan mengelola barang.

Data akun dan barang tersimpan otomatis di `data/store.json`. Foto yang diunggah tersimpan di `uploads/`. Jangan hapus kedua folder itu jika ingin mempertahankan data. File SQL di folder `database/` sudah tidak diperlukan untuk menjalankan aplikasi.

## Catatan
- Gunakan PHP 7.4 atau lebih baru.
- Untuk upload foto, folder `uploads/` harus bisa ditulisi oleh Apache.
- Jika ingin mengosongkan semua data, hentikan Apache lalu kosongkan isi `data/store.json` dengan struktur JSON awal: `{"users":[],"products":[],"next_user_id":1,"next_product_id":1}`.
