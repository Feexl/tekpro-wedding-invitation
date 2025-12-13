<?php
require_once 'config.php';

session_start();

// Buat direktori upload jika belum ada
$upload_dir = 'uploads/weddings/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$conn = getConnection();
$success = false;
$error_message = '';

function uploadFoto($file, $field_name, $wedding_id) {
    global $upload_dir;
    
    // Validasi file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error uploading file'];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'File type not allowed. Only JPG, PNG, GIF, WebP allowed.'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File too large. Max 5MB.'];
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'wedding_' . $wedding_id . '_' . $field_name . '_' . time() . '.' . $file_extension;
    $target_path = $upload_dir . $filename;
    
    // Pindahkan file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename, 'path' => $target_path];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}

function tambah($data, $files)
{
    global $conn;

    // Ambil data dari form
    $template_id = (int)$data["template_id"];
    $kalimat_pembuka = htmlspecialchars($data["kalimat_pembuka"]);
    $nama_panggilan = htmlspecialchars($data["nama_panggilan"]);
    $nama_mempelai_pria = htmlspecialchars($data["nama_mempelai_pria"]);
    $nama_mempelai_wanita = htmlspecialchars($data["nama_mempelai_wanita"]);
    $anak_ke_pria = htmlspecialchars($data["anak_ke_pria"]);
    $anak_ke_wanita = htmlspecialchars($data["anak_ke_wanita"]);
    $ayah_pria = htmlspecialchars($data["ayah_pria"]);
    $ibu_pria = htmlspecialchars($data["ibu_pria"]);
    $ayah_wanita = htmlspecialchars($data["ayah_wanita"]);
    $ibu_wanita = htmlspecialchars($data["ibu_wanita"]);
    $lokasi_acara_pemberkatan = htmlspecialchars($data["lokasi_acara_pemberkatan"]);
    $lokasi_acara_resepsi = htmlspecialchars($data['lokasi_acara_resepsi']);
    $jam_acara_pemberkatan = htmlspecialchars($data['jam_acara_pemberkatan']);
    $jam_acara_resepsi = htmlspecialchars($data['jam_acara_resepsi']);
    $google_maps = htmlspecialchars($data["google_maps"]);
    $tanggal_acara = htmlspecialchars($data["tanggal_acara"]);
    $email = htmlspecialchars($data["email"]);
    $nomor_wa = htmlspecialchars($data["nomor_wa"]);
    $dress_code = htmlspecialchars($data['dress_code']);
    $harga = (float)$data["harga"];

    // Generate unique slug
    $slug_url = generateSlug($nama_mempelai_pria, $nama_mempelai_wanita);

    // Check if slug exists (very rare)
    $check_slug = $conn->prepare("SELECT id FROM weddings WHERE slug_url = ?");
    $check_slug->bind_param('s', $slug_url);
    $check_slug->execute();
    if ($check_slug->get_result()->num_rows > 0) {
        $slug_url .= '-' . time();
    }

    // Insert query dengan placeholder untuk foto
    $query = "INSERT INTO weddings (
        template_id, slug_url, kalimat_pembuka, nama_panggilan,
        nama_mempelai_pria, nama_mempelai_wanita, anak_ke_pria, anak_ke_wanita,
        ayah_pria, ibu_pria, ayah_wanita, ibu_wanita,
        lokasi_acara_pemberkatan, lokasi_acara_resepsi, google_maps, tanggal_acara, 
        jam_acara_pemberkatan, jam_acara_resepsi,
        email, nomor_wa, dress_code, harga
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        'issssssssssssssssssssd',
        $template_id,
        $slug_url,
        $kalimat_pembuka,
        $nama_panggilan,
        $nama_mempelai_pria,
        $nama_mempelai_wanita,
        $anak_ke_pria,
        $anak_ke_wanita,
        $ayah_pria,
        $ibu_pria,
        $ayah_wanita,
        $ibu_wanita,
        $lokasi_acara_pemberkatan,
        $lokasi_acara_resepsi,
        $google_maps,
        $tanggal_acara,
        $jam_acara_pemberkatan,
        $jam_acara_resepsi,
        $email,
        $nomor_wa,
        $dress_code,
        $harga
    );

    if ($stmt->execute()) {
        $wedding_id = $conn->insert_id;
        
        // Upload foto jika ada
        $foto_fields = [
            'foto_mempelai_pria', 'foto_mempelai_wanita',
            'foto_1', 'foto_2', 'foto_3', 'foto_4',
            'foto_5', 'foto_6', 'foto_7', 'foto_8'
        ];
        
        $update_data = [];
        foreach ($foto_fields as $field) {
            if (isset($files[$field]) && $files[$field]['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadFoto($files[$field], $field, $wedding_id);
                if ($upload_result['success']) {
                    $update_data[$field] = $upload_result['filename'];
                }
            }
        }
        
        // Update database dengan nama file foto
        if (!empty($update_data)) {
            $update_fields = [];
            $update_values = [];
            $types = '';
            
            foreach ($update_data as $field => $filename) {
                $update_fields[] = "$field = ?";
                $update_values[] = $filename;
                $types .= 's';
            }
            
            $update_values[] = $wedding_id;
            $types .= 'i';
            
            $update_query = "UPDATE weddings SET " . implode(', ', $update_fields) . " WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param($types, ...$update_values);
            $update_stmt->execute();
        }
        
        return $wedding_id;
    }

    return false;
}


if (isset($_POST["submit"])) {
    $id = tambah($_POST, $_FILES);

    if ($id) {
        $_SESSION['wedding_id'] = $id;
        $_SESSION['owner_email'] = $_POST['email'];
        $_SESSION['couple_names'] = $_POST['nama_mempelai_pria'] . ' & ' . $_POST['nama_mempelai_wanita'];
        
        header("Location: pembayaran.php?id=" . $id . "&status=success&message=" . urlencode("Data berhasil ditambahkan"));
        exit;
    } else {
        header("Location: form.php?status=error&message=" . urlencode("Pesanan gagal ditambahkan"));
        exit;
    }
}

// Get templates for dropdown
$templates_query = "SELECT id, nama_template, harga FROM templates ORDER BY nama_template";
$templates_result = mysqli_query($conn, $templates_query);


?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="form.css">
    <title>Form Pemesanan Undangan Digital</title>
    <style>
        .upload-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .preview-container {
            position: relative;
            width: 100px;
            height: 100px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .remove-preview {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .upload-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .photo-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .photo-section h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dc6b88;
        }
        
        .required-star {
            color: #ff4444;
            margin-left: 3px;
        }
        
        .upload-requirements {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .upload-requirements ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .upload-requirements li {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Form Pemesanan</h1>
            <p>Lengkapi formulir di bawah ini untuk memesan undangan digital Anda.</p>
        </div>

        <!-- Requirements Section -->
        <div class="upload-requirements">
            <h3>📋 Persyaratan Upload Foto:</h3>
            <ul>
                <li>Format file: JPG, PNG, GIF, WebP</li>
                <li>Ukuran maksimal: 5MB per foto</li>
                <li>Resolusi minimal: 800x600 pixels</li>
                <li>Foto akan tampil di galeri undangan Anda</li>
                <li>Foto mempelai akan tampil di halaman utama</li>
            </ul>
        </div>

        <form action="" method="post" enctype="multipart/form-data">
            <!-- Pilih Template -->
            <div class="form-group">
                <label for="template_id">Pilih Template <span class="required">*</span></label>
                <select name="template_id" id="template_id" required onchange="updatePrice()">
                    <option value="">Pilih template yang ingin digunakan</option>
                    <?php while ($template = mysqli_fetch_assoc($templates_result)): ?>
                        <option value="<?= $template['id'] ?>" data-harga="<?= $template['harga'] ?>">
                            <?= $template['nama_template'] ?> - Rp <?= number_format($template['harga'], 0, ',', '.') ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Rest of your form fields... -->
            <div class="form-group">
                <label for="kalimat_pembuka">Kalimat Pembuka <span class="required">*</span></label>
                <select name="kalimat_pembuka" id="kalimat_pembuka" required>
                    <option value="">Pilih kata pembuka</option>
                    <option value="Bismillahirrahmanirrahim">Bismillahirrahmanirrahim</option>
                    <option value="Dengan memohon kasih karunia Tuhan">Dengan memohon kasih karunia Tuhan</option>
                    <option value="Om Swastyastu">Om Swastyastu</option>
                </select>
            </div>

            <div class="form-group">
                <label for="nama_panggilan">Nama Panggilan <span class="required">*</span></label>
                <input type="text" name="nama_panggilan" id="nama_panggilan" placeholder="Budi & Siti" required>
            </div>

            <!-- Nama Mempelai -->
            <div class="form-row">
                <div class="form-group">
                    <label for="nama_mempelai_pria">Nama Mempelai Pria <span class="required">*</span></label>
                    <input type="text" name="nama_mempelai_pria" required>
                </div>
                <div class="form-group">
                    <label for="nama_mempelai_wanita">Nama Mempelai Wanita <span class="required">*</span></label>
                    <input type="text" name="nama_mempelai_wanita" required>
                </div>
            </div>

            <!-- Section: Foto Mempelai -->
            <div class="photo-section">
                <h3>📸 Foto Mempelai</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Foto Mempelai Pria <span class="required-star">*</span></label>
                        <input type="file" name="foto_mempelai_pria" accept="image/*" required
                               onchange="previewImage(this, 'preview_mempelai_pria')">
                        <div class="upload-info">Foto ini akan muncul di halaman utama undangan</div>
                        <div class="upload-preview">
                            <div class="preview-container" id="preview_mempelai_pria"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Foto Mempelai Wanita <span class="required-star">*</span></label>
                        <input type="file" name="foto_mempelai_wanita" accept="image/*" required
                               onchange="previewImage(this, 'preview_mempelai_wanita')">
                        <div class="upload-info">Foto ini akan muncul di halaman utama undangan</div>
                        <div class="upload-preview">
                            <div class="preview-container" id="preview_mempelai_wanita"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Galeri Foto (8 foto) -->
            <div class="photo-section">
                <h3>🖼️ Galeri Foto Pernikahan</h3>
                <p class="upload-info">Unggah foto-foto terbaik untuk galeri undangan Anda (Maksimal 8 foto)</p>
                
                <?php for ($i = 1; $i <= 8; $i++): ?>
                <div class="form-group">
                    <label>Foto <?= $i ?> <?= $i <= 4 ? '<span class="required-star">*</span>' : '' ?></label>
                    <input type="file" name="foto_<?= $i ?>" accept="image/*" <?= $i <= 3 ? 'required' : '' ?>
                           onchange="previewImage(this, 'preview_foto_<?= $i ?>')">
                    <div class="upload-preview">
                        <div class="preview-container" id="preview_foto_<?= $i ?>"></div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <!-- Anak Ke -->
            <div class="form-row">
                <div class="form-group">
                    <label>Anak Ke (Pria) <span class="required">*</span></label>
                    <input type="text" name="anak_ke_pria" placeholder="Pertama" required>
                </div>
                <div class="form-group">
                    <label>Anak Ke (Wanita) <span class="required">*</span></label>
                    <input type="text" name="anak_ke_wanita" placeholder="Kedua" required>
                </div>
            </div>

            <!-- Orang Tua -->
            <div class="form-row">
                <div class="form-group">
                    <label>Ayah Mempelai Pria <span class="required">*</span></label>
                    <input type="text" name="ayah_pria" required>
                </div>
                <div class="form-group">
                    <label>Ayah Mempelai Wanita <span class="required">*</span></label>
                    <input type="text" name="ayah_wanita" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ibu Mempelai Pria <span class="required">*</span></label>
                    <input type="text" name="ibu_pria" required>
                </div>
                <div class="form-group">
                    <label>Ibu Mempelai Wanita <span class="required">*</span></label>
                    <input type="text" name="ibu_wanita" required>
                </div>
            </div>

            <!-- Lokasi -->
            <div class="form-row">
                <div class="form-group">
                    <label>Lokasi Acara Pemberkatan <span class="required">*</span></label>
                    <input type="text" name="lokasi_acara_pemberkatan" required>
                </div>
                <div class="form-group">
                    <label>Lokasi Acara Resepsi <span class="required">*</span></label>
                    <input type="text" name="lokasi_acara_resepsi" required>
                </div>
            </div>

            <!-- Jadwal -->
            <div class="form-row">
                <div class="form-group">
                    <label>Jam Acara Pemberkatan<span class="required">*</span></label>
                    <input type="text" name="jam_acara_pemberkatan" placeholder="10.00 - Selesai" required>
                </div>
                <div class="form-group">
                    <label>Jam Acara Resepsi <span class="required">*</span></label>
                    <input type="text" name="jam_acara_resepsi" placeholder="17.00 - Selesai" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Acara <span class="required">*</span></label>
                    <input type="text" name="tanggal_acara" placeholder="Sabtu, 05 Oktober 2019" required>
                </div>
                <div class="form-group">
                    <label>Link Google Maps <span class="required">*</span></label>
                    <input type="url" name="google_maps" required>
                </div>
            </div>

            <!-- Kontak -->
            <div class="form-row">
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Nomor WhatsApp <span class="required">*</span></label>
                    <input type="tel" name="nomor_wa" placeholder="081234567890" required>
                </div>
            </div>

            <div class="form-group">
                <label>Dress Code <span class="required">*</span></label>
                <input type="text" name="dress_code" required>
            </div>

            <!-- Hidden Price -->
            <input type="hidden" name="harga" id="harga_input" value="150000">

            <!-- Price Display -->
            <div class="price-display">
                <span>Total Harga:</span>
                <span id="harga_display">Rp 0</span>
            </div>

            <!-- Submit -->
            <div class="submit-button">
                <button type="submit" name="submit">Kirim Pesanan</button>
            </div>
        </form>
    </div>

    <div class="floating-notification" id="notification">
        <span id="notificationText"></span>
    </div>

    <script>
        function previewImage(input, previewId) {
            const previewContainer = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Clear existing content
                    previewContainer.innerHTML = '';
                    
                    // Create image element
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-image';
                    img.alt = 'Preview';
                    
                    // Create remove button
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-preview';
                    removeBtn.innerHTML = '×';
                    removeBtn.onclick = function() {
                        input.value = '';
                        previewContainer.innerHTML = '';
                    };
                    
                    previewContainer.appendChild(img);
                    previewContainer.appendChild(removeBtn);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function updatePrice() {
            const select = document.getElementById('template_id');
            const selectedOption = select.options[select.selectedIndex];
            const harga = selectedOption.getAttribute('data-harga') || 0;
            
            document.getElementById('harga_input').value = harga;
            document.getElementById('harga_display').textContent = 'Rp ' + formatNumber(harga);
        }
        
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
        
        // Initialize price on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePrice();
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFiles = [
                'foto_mempelai_pria',
                'foto_mempelai_wanita',
                'foto_1',
                'foto_2',
                'foto_3'
            ];
            
            let isValid = true;
            let errorMessage = '';
            
            requiredFiles.forEach(fieldName => {
                const input = document.querySelector(`input[name="${fieldName}"]`);
                if (!input || !input.files[0]) {
                    isValid = false;
                    errorMessage = `Harap unggah ${fieldName.replace('_', ' ')}`;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification(errorMessage, 'error');
                document.querySelector(`input[name="${fieldName}"]`).focus();
            }
        });
        
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notificationText');
            
            notificationText.textContent = message;
            notification.className = 'floating-notification ' + type;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        }
    </script>
</body>

</html>