// Menangani konfirmasi penghapusan dan preview gambar sebelum diunggah.
document.addEventListener('DOMContentLoaded', function () {
    // Menampilkan dialog konfirmasi sebelum form penghapusan dikirim.
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const message = form.dataset.confirm || 'Lanjutkan tindakan ini?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    // Memeriksa ukuran gambar lebih awal agar pengguna mendapat umpan balik.
    const imageInput = document.querySelector('#image');

    if (imageInput) {
        imageInput.addEventListener('change', function () {
            const file = imageInput.files[0];

            if (file && file.size > 3 * 1024 * 1024) {
                window.alert('Ukuran foto maksimal 3 MB. Silakan pilih foto yang lebih kecil.');
                imageInput.value = '';
            }
        });
    }

    // Menutup notifikasi setelah beberapa detik agar tampilan tetap rapi.
    document.querySelectorAll('.flash').forEach(function (flash) {
        window.setTimeout(function () {
            flash.classList.add('flash-hidden');
        }, 4200);
    });
});
