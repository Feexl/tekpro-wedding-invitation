<?php
require_once '../config.php';

// Get template slug from URL
$template_slug = $_GET['template'] ?? '';

if (empty($template_slug)) {
    die("Template tidak ditemukan!");
}

// Get database connection
$conn = getConnection();

if (!$conn) {
    die("Database connection failed.");
}

// Get template data
$stmt = $conn->prepare("SELECT * FROM templates WHERE slug = ?");
$stmt->bind_param("s", $template_slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Template tidak ditemukan atau tidak aktif!");
}

$template = $result->fetch_assoc();
$conn->close();

// Check if preview file exists
$preview_file = __DIR__ . '/' . $template_slug . '.html';

if (!file_exists($preview_file)) {
    // Fallback to default preview
    $preview_file = __DIR__ . '/default.html';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: <?php echo htmlspecialchars($template['nama_template']); ?></title>
    
    <style>
        /* Preview Banner */
        body {
            font-family: Inter, system-ui, Arial, sans-serif;
        }
        .preview-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #e47691 0%, #d04d6d 100%);
            color: white;
            padding: 15px 20px;
            text-align: center;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .preview-banner strong {
            font-size: 1.1em;
            margin-right: 10px;
        }
        
        .preview-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .preview-btn {
            background: white;
            color: #d04d6d;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .preview-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255,255,255,0.3);
        }
        
        .preview-btn.close {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        /* Preview iframe container */
        .preview-container {
            margin-top: 120px;
            height: calc(100vh - 120px);
        }
        
        .preview-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        @media (max-width: 768px) {
            .preview-container {
                margin-top: 140px;
                height: calc(100vh - 140px);
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; overflow: hidden;">
    <!-- Preview Banner -->
    <div class="preview-banner">
        <div>
            <strong>MODE PREVIEW</strong>
            <span>Ini adalah tampilan demo template "<?php echo htmlspecialchars($template['nama_template']); ?>"</span>
        </div>
        <div class="preview-actions">
            <a href="../form.php?template=<?php echo $template_slug; ?>" class="preview-btn">
                🛒 Pesan Template Ini (Rp <?php echo number_format($template['harga'], 0, ',', '.'); ?>)
            </a>
            <a href="../index.php#katalog" class="preview-btn close">
                ← Kembali ke Katalog
            </a>
        </div>
    </div>

    <!-- Preview Content -->
    <div class="preview-container">
        <iframe src="<?php echo basename($preview_file); ?>" class="preview-iframe"></iframe>
    </div>
</body>
</html>