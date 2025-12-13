<?php
require_once 'config.php';

// Get database connection
$conn = getConnection();

if (!$conn) {
    die("Database connection failed. Please check your configuration.");
}

// Ambil semua template dari database
$sql = "SELECT * FROM templates ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Invyra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css"/>
</head>

<body>
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <img src="image/logo.png" alt="invyra" class="logo-img" />
                <div class="brand">Invyra</div>
            </div>
            <nav class="nav">
                <a href="#katalog" class="nav-link">Katalog</a>
                <a href="#fitur" class="nav-link">Fitur</a>
                <a href="#kontak" class="nav-link">Kontak</a>
                <a class="order-btn" href="form.php">Order</a>
            </nav>
            <button class="hamburger">
                <i class="fas fa-bars icon-hamburger"></i>
            </button>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-inner">
                <div class="hero-text">
                    <h1>Hadirkan Keajaiban Undangan Digital Anda</h1>
                    <div class="cta">
                        <a class="btn" href="#katalog">Lihat Katalog</a>
                        <a class="btn ghost" href="form.php">Ciptakan Sekarang</a>
                    </div>
                </div>
                <div class="hero-art">
                    <div class="carousel">
                        <img src="image/hero/hero-img-1.jpeg" class="slide active">
                        <img src="image/hero/hero-img-2.jpeg" class="slide">
                        <img src="image/hero/hero-img-3.jpeg" class="slide">

                    </div>
                </div>
            </div>

            <section id="fitur" class="features container">
                <h2>Fitur Terlengkap</h2>
                <div class="feature-grid">
                    <div class="feature">Unlimited Share</div>
                    <div class="feature">Free RSVP</div>
                    <div class="feature">Ubah Warna Tema</div>
                    <div class="feature">Ucapan Selamat</div>
                    <div class="feature">Galeri Foto</div>
                </div>
            </section>

            <section id="katalog" class="catalog container">
                <h2>Katalog Tema</h2>
                <div class="template-grid">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($template = $result->fetch_assoc()) {

                            // Format harga
                            $harga = number_format($template['harga'] ?? 0, 0, ',', '.');

                            // Gambar preview - gunakan thumbnail atau preview_url
                            if (!empty($template['thumbnail'])) {
                                $preview_image = $template['thumbnail'];
                                // cek apakah file exists
                                if (file_exists($template['thumbnail'])) {
                                    $has_image = true;
                                }
                            }

                            // jika tidak ada gambar, gunakan placeholder
                            if (!$has_image) {
                                $preview_image = 'image/templates/placeholder.jpg';
                            }

                            // Tentukan badge berdasarkan kondisi tertentu (opsional)
                            $badge = '';
                            if ($template['harga'] >= 200000) {
                                $badge = 'Premium';
                            } elseif (strtotime($template['created_at']) > strtotime('-7 days')) {
                                $badge = 'New';
                            }
                    ?>
                            <div class="template-card">
                                <div class="template-preview <?= !$has_image ? 'no-image' :''; ?>"
                                <?php if ($has_image): ?>
                                    style="background-image: url('<?= htmlspecialchars($preview_image) ?>');"
                                <?php endif; ?>>

                                <?php if (!$has_image) : ?>
                                    <div style="color: white; text-align: center;">
                                        <div style="font-size: 1.2em; font-weight: bold;">
                                            <?= htmlspecialchars($template['nama_template']); ?>
                                        </div>
                                        <small>Preview Coming Soon</small>
                                    </div>
                                <?php endif; ?>
                                    <?php if (!empty($badge)): ?>
                                        <div class="badge"><?php echo htmlspecialchars($badge); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="template-info">
                                    <div class="template-name">
                                        <?php echo htmlspecialchars($template['nama_template']); ?>
                                    </div>

                                    <div class="template-price">
                                        Rp <?php echo $harga; ?>
                                    </div>

                                    <div class="template-actions">
                                        <a href="./preview/index.php?template=<?= $template['slug'] ?>"
                                            class="btn btn-preview"
                                            target="_blank">
                                            Preview
                                        </a>
                                        <a href="form.php?template=<?php echo $template['slug']; ?>"
                                            class="btn btn-order">
                                            Pesan Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="no-templates">
                            <h2>Belum Ada Template</h2>
                            <p>Template undangan akan segera tersedia.</p>
                        </div>
                    <?php
                    }
                    ?>
                    <!-- generate some items
                    <article class="card">
                        <div class="thumb">REGULER 01</div>
                        <h3>Reguler 01</h3>
                        <p class="price">Rp. 89.000</p>
                        <a class="btn-sm" href="#">Lihat Tema</a>
                    </article>

                    <article class="card">
                        <div class="thumb">REGULER 01</div>
                        <h3>Reguler 01</h3>
                        <p class="price">Rp. 89.000</p>
                        <a class="btn-sm" href="#">Lihat Tema</a>
                    </article>

                    <article class="card">
                        <div class="thumb">REGULER 01</div>
                        <h3>Reguler 01</h3>
                        <p class="price">Rp. 89.000</p>
                        <a class="btn-sm" href="#">Lihat Tema</a>
                    </article>

                    <article class="card">
                        <div class="thumb">REGULER 01</div>
                        <h3>Reguler 01</h3>
                        <p class="price">Rp. 89.000</p>
                        <a class="btn-sm" href="#">Lihat Tema</a>
                    </article>

                    <article class="card">
                        <div class="thumb">REGULER 02</div>
                        <h3>Reguler 02</h3>
                        <p class="price">Rp. 89.000</p>
                        <a class="btn-sm" href="#">Lihat Tema</a>
                    </article>

                    <article class="card">
                        <div class="thumb">PREMIUM 01</div>
                        <h3>Premium 01</h3>
                        <p class="price">Rp. 189.000</p>
                        <a class="btn-sm" href="#">Lihat Tema</a>
                    </article>

                    <article class="card">
                        <div class="thumb">PREMIUM 01</div>
                        <h3>Premium 01</h3>
                        <p class="price">Rp. 189.000</p>
                        <a class="btn-sm" href="#">Lihat Tema</a>
                    </article>

                    <article class="card">
                        <div class="thumb">PREMIUM 01</div>
                        <h3>Premium 01</h3>
                        <p class="price">Rp. 189.000</p>
                        <a class="btn-sm" href="#">Lihat Tema</a>
                    </article> -->
                </div>
            </section>

            <section id="kontak" class="contact container">
                <h2>Hubungi Kami</h2>
                <p>Butuh bantuan? Klik tombol Order untuk chat melalui WhatsApp.</p>
                <a class="btn" href="https://wa.me/6285272048989">Order via WhatsApp</a>
            </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div>© Inryra - 2025</div>
        </div>
    </footer>

    <a href="https://wa.me/6285272048989?text=Halo%20kak%20,%20saya%20tertarik%20pesan%20undangan%20digital"
        class="wa-floating" target="_blank">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
    <button class="wa-floating" onclick="openWhatsapp()">
        <i class="fa-brands fa-whatsapp"></i>
    </button>

    <script src="script.js"></script>
</body>

</html>