function copyToClipboard(text, button) {
    // Hapus semua spasi dari nomor rekening
    const cleanText = text.replace(/\s/g, '');

    // Copy ke clipboard
    navigator.clipboard.writeText(cleanText).then(() => {
        // Tampilkan toast notification
        const toast = document.getElementById('toast');
        toast.classList.add('show');

        // Ubah text button sementara
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="copy-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="white"/></svg>TERSALIN';

        // Sembunyikan toast setelah 2.5 detik
        setTimeout(() => {
            toast.classList.remove('show');
            button.innerHTML = originalText;
        }, 2500);
    }).catch(err => {
        console.error('Gagal menyalin:', err);
    });
}


// Fungsi untuk menampilkan notifikasi
function showNotification(message, type) {
    const notification = document.getElementById('notification');
    const notificationText = document.getElementById('notificationText');
    const notificationIcon = document.getElementById('notificationIcon');

    // Set text
    notificationText.textContent = message;

    // Set icon dan style berdasarkan type
    if (type === 'success') {
        notification.classList.remove('error');
        notification.classList.add('success');
        notificationIcon.classList.remove('error');
        notificationIcon.classList.add('success');
        notificationIcon.innerHTML = '✓';
    } else {
        notification.classList.remove('success');
        notification.classList.add('error');
        notificationIcon.classList.remove('success');
        notificationIcon.classList.add('error');
        notificationIcon.innerHTML = '✕';
    }

    // Tampilkan notifikasi
    notification.classList.add('show');

    // Sembunyikan setelah 4 detik
    setTimeout(() => {
        notification.classList.remove('show');
    }, 4000);
}

// Cek parameter URL untuk notifikasi
window.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');

    if (status === 'success') {
        showNotification(message || 'Data berhasil ditambahkan', 'success');
        // Bersihkan URL tanpa reload (opsional)
        const cleanUrl = window.location.pathname + '?id=' + urlParams.get('id');
        window.history.replaceState({}, document.title, cleanUrl);
    } else if (status === 'error') {
        showNotification(message || 'Data gagal ditambahkan', 'error');
    }
});

function confirmPayment() {
    // Nomor WhatsApp admin (ganti dengan nomor yang sesuai)
    const phoneNumber = '6285272048989'; // Format: kode negara + nomor tanpa 0 di depan
    const message = 'Halo, saya ingin mengonfirmasi pembayaran';
    const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');

    // const phoneNumber = '6285272048989';
    // const message = 'Halo, saya ingin menanyakan tentang undangan digital';
    // const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`
    // window.open(url, '_blank');
}