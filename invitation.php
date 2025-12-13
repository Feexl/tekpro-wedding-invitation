<?php
// ==========================================
// FILE: invitation.php (Router untuk undangan real)
// URL: invitation.php?slug=romeo-juliet-abc123
// ==========================================

require_once 'config.php';

// Get slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

$conn = getConnection();

// Get wedding data
$sql = "SELECT w.*, t.nama_template, t.slug as template_slug, t.file_name
        FROM weddings w
        LEFT JOIN templates t ON w.template_id = t.id
        WHERE w.slug_url = ? AND w.is_active = TRUE";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<!DOCTYPE html>
    <html><head><title>Not Found</title></head>
    <body style='font-family: Arial; text-align: center; padding: 100px;'>
        <h1>404 - Undangan Tidak Ditemukan</h1>
        <p>Maaf, undangan yang Anda cari tidak tersedia.</p>
    </body></html>";
    exit;
}

$wedding_data = $result->fetch_assoc();
$wedding_data['wedding_id'] = $wedding_data['id'];  // For RSVP & Comments

// **FUNGSI UNTUK MENDAPATKAN URL FOTO**
function getPhotoUrl($filename, $wedding_id)
{
    if (empty($filename)) {
        // Return default photo URL
        return BASE_URL . 'assets/images/default-photo.jpg';
    }

    // Cek apakah file benar-benar ada di server
    $filepath = 'uploads/weddings/' . $filename;

    if (file_exists($filepath)) {
        // File ada, return URL lengkap
        return BASE_URL . $filepath;
    } else {
        // File tidak ditemukan, return default
        error_log("Foto tidak ditemukan: $filepath untuk wedding ID: $wedding_id");
        return BASE_URL . 'assets/images/default-photo.jpg';
    }
}

$foto_mempelai_pria_url = getPhotoUrl($wedding_data['foto_mempelai_pria'] ?? '', $wedding_data['id']);
$foto_mempelai_wanita_url = getPhotoUrl($wedding_data['foto_mempelai_wanita'] ?? '', $wedding_data['id']);

// Galeri foto
$galeri_foto = [];
for ($i = 1; $i <= 8; $i++) {
    $field_name = "foto_$i";
    if (!empty($wedding_data[$field_name])) {
        $galeri_foto[] = [
            'url' => getPhotoUrl($wedding_data[$field_name], $wedding_data['id']),
            'filename' => $wedding_data[$field_name],
            'index' => $i
        ];
    }
}

// Tambah data foto ke wedding_data untuk digunakan di template
$wedding_data['foto_mempelai_pria_url'] = $foto_mempelai_pria_url;
$wedding_data['foto_mempelai_wanita_url'] = $foto_mempelai_wanita_url;
$wedding_data['galeri_foto'] = $galeri_foto;
$wedding_data['total_galeri_foto'] = count($galeri_foto);



$template_file = 'templates/' . $wedding_data['file_name'];


// Load the template
include $template_file;
