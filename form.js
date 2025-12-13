function updatePrice() {
    const select = document.getElementById('template_id');
    const option = select.options[select.selectedIndex];
    const harga = option.getAttribute('data-harga');

    if (harga) {
        document.getElementById('harga_input').value = harga;
        document.getElementById('harga_display').textContent =
            'Rp ' + parseInt(harga).toLocaleString('id-ID');
    }
}

// Show notification from URL
const urlParams = new URLSearchParams(window.location.search);
const statusParam = urlParams.get('status');
const message = urlParams.get('message');

if (statusParam && message) {
    const notification = document.getElementById('notification');
    const notificationText = document.getElementById('notificationText');

    notificationText.textContent = message;
    notification.classList.add('show', statusParam);

    setTimeout(() => {
        notification.classList.remove('show');
    }, 4000);
}