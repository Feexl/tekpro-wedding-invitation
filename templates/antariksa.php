<?php
$tanggal = $wedding_data['tanggal_acara']; // "Jumat, 09 Januari 2026"
// Hapus hari
$tanggal = preg_replace('/^[^,]+,\s*/', '', $tanggal);
// sekarang "09 Januari 2026"

// Ubah nama bulan Indonesia → Inggris
$bulan = [
    'Januari' => 'January',
    'Februari' => 'February',
    'Maret' => 'March',
    'April' => 'April',
    'Mei' => 'May',
    'Juni' => 'June',
    'Juli' => 'July',
    'Agustus' => 'August',
    'September' => 'September',
    'Oktober' => 'October',
    'November' => 'November',
    'Desember' => 'December'
];

$tanggal = str_replace(array_keys($bulan), array_values($bulan), $tanggal);

$jamRaw = $wedding_data['jam_acara_pemberkatan'] ?? '10:00:00';
// Ambil hanya bagian sebelum tanda '-'
$jamParts = explode('-', $jamRaw);
$jam = trim($jamParts[0]); // "09:00"
// Jika jam tidak lengkap (misal tanpa detik), tambahkan ":00"
if (preg_match('/^\d{2}:\d{2}$/', $jam)) {
    $jam .= ':00';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Wedding of <?php echo htmlspecialchars($wedding_data['nama_mempelai_pria'] ?? ''); ?> & <?php echo htmlspecialchars($wedding_data['nama_mempelai_wanita'] ?? ''); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Poppins:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            overflow-x: hidden;
        }

        /* Background Animation */
        .bg-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0b2e 40%, #3d2463 100%);
        }

        .stars {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .star {
            position: absolute;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            animation: twinkle 3s infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }
        }

        /* Splash Screen */
        .splash {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #0a0a0a, #1a0b2e);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.8s ease;
        }

        .splash.hide {
            opacity: 0;
            pointer-events: none;
        }

        .splash-box {
            text-align: center;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .symbol {
            font-size: 80px;
            margin-bottom: 30px;
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .splash h1 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            margin: 20px 0;
            background: linear-gradient(135deg, #b794f4, #d4af37);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .splash p {
            color: #b794f4;
            letter-spacing: 3px;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .open-btn {
            padding: 15px 50px;
            background: linear-gradient(135deg, #7a4bab, #3d2463);
            border: 2px solid #b794f4;
            color: white;
            font-size: 14px;
            letter-spacing: 2px;
            cursor: pointer;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .open-btn:hover {
            background: linear-gradient(135deg, #b794f4, #7a4bab);
            box-shadow: 0 0 30px rgba(183, 148, 244, 0.5);
            transform: translateY(-2px);
        }

        /* Main Content */
        .main {
            opacity: 0;
            transition: opacity 0.8s ease;
        }

        .main.show {
            opacity: 1;
        }

        /* Music Button */
        .music-btn {
            position: fixed;
            top: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: rgba(61, 36, 99, 0.8);
            border: 2px solid #b794f4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s;
            font-size: 24px;
        }

        .music-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(183, 148, 244, 0.5);
        }

        .music-btn.active {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(183, 148, 244, 0.3);
            }

            50% {
                box-shadow: 0 0 40px rgba(183, 148, 244, 0.8);
            }
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }

        .hero-content h2 {
            font-size: 16px;
            color: #b794f4;
            letter-spacing: 4px;
            margin-bottom: 20px;
        }

        .hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 80px;
            font-weight: 900;
            line-height: 1.2;
            background: linear-gradient(135deg, #ffffff, #b794f4, #d4af37);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .ampersand {
            font-size: 60px;
            font-style: italic;
            color: #d4af37;
            display: block;
            margin: 20px 0;
        }

        .hero-quote {
            font-size: 18px;
            font-style: italic;
            color: #b794f4;
            max-width: 600px;
            margin: 30px auto;
            line-height: 1.8;
        }

        .date-box {
            display: inline-block;
            padding: 15px 40px;
            background: rgba(61, 36, 99, 0.6);
            border: 2px solid #b794f4;
            border-radius: 50px;
            margin-top: 30px;
            font-size: 18px;
            letter-spacing: 2px;
        }

        /* Section */
        section {
            padding: 80px 20px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            text-align: center;
            margin-bottom: 60px;
            background: linear-gradient(135deg, #ffffff, #b794f4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Couple Cards */
        .couple-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }

        .couple-card {
            background: linear-gradient(135deg, rgba(61, 36, 99, 0.4), rgba(26, 11, 46, 0.6));
            padding: 40px;
            border-radius: 30px;
            border: 2px solid #b794f4;
            text-align: center;
            transition: all 0.3s;
        }

        .couple-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(183, 148, 244, 0.3);
        }

        .avatar {
            width: 150px;
            height: 150px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #7a4bab, #3d2463);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            border: 4px solid #b794f4;
        }

        .avatar img {
            width: 100%;
            border-radius: 50%;
        }

        .couple-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .couple-card .role {
            color: #b794f4;
            letter-spacing: 2px;
            font-size: 14px;
            margin: 15px 0;
        }

        .couple-card .parents {
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            line-height: 1.8;
            margin-top: 20px;
        }

        /* Countdown */
        .countdown {
            background: linear-gradient(135deg, rgba(61, 36, 99, 0.6), rgba(26, 11, 46, 0.8));
            padding: 50px;
            border-radius: 30px;
            border: 2px solid #b794f4;
            text-align: center;
        }

        .countdown h3 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin-bottom: 40px;
        }

        .countdown-grid {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .time-box {
            background: rgba(183, 148, 244, 0.1);
            padding: 30px 20px;
            border-radius: 20px;
            min-width: 120px;
            border: 1px solid #b794f4;
        }

        .time-value {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 700;
            color: #b794f4;
            display: block;
        }

        .time-label {
            font-size: 14px;
            letter-spacing: 2px;
            opacity: 0.8;
            margin-top: 10px;
        }

        /* Event Cards */
        .event-card {
            background: linear-gradient(135deg, rgba(61, 36, 99, 0.4), rgba(26, 11, 46, 0.6));
            padding: 40px;
            border-radius: 30px;
            border-left: 5px solid #b794f4;
            margin-bottom: 30px;
            transition: all 0.3s;
        }

        .event-card:hover {
            transform: translateX(10px);
            box-shadow: 0 15px 40px rgba(183, 148, 244, 0.3);
        }

        .event-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .event-icon {
            font-size: 48px;
        }

        .event-info {
            padding-left: 70px;
        }

        .event-info p {
            font-size: 18px;
            margin: 10px 0;
            color: rgba(255, 255, 255, 0.9);
        }

        .dresscode {
            background: rgba(183, 148, 244, 0.2);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            margin: 40px 0;
        }

        .dresscode strong {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: #b794f4;
        }

        .map-btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #7a4bab, #3d2463);
            border: 2px solid #b794f4;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            margin-top: 30px;
            letter-spacing: 2px;
            transition: all 0.3s;
        }

        .map-btn:hover {
            background: linear-gradient(135deg, #b794f4, #7a4bab);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(183, 148, 244, 0.5);
        }

        /* Gallery */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .gallery-item {
            aspect-ratio: 1;
            background: linear-gradient(135deg, rgba(61, 36, 99, 0.6), rgba(122, 75, 171, 0.4));
            border-radius: 20px;
            border: 2px solid #b794f4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            cursor: pointer;
            transition: all 0.3s;
            overflow: hidden;
        }

        .gallery-item:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(183, 148, 244, 0.5);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-note {
            text-align: center;
            font-style: italic;
            color: #b794f4;
            margin-top: 40px;
            font-size: 18px;
        }

        /* RSVP Form */
        .rsvp-box {
            max-width: 700px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, rgba(61, 36, 99, 0.4), rgba(26, 11, 46, 0.6));
            padding: 50px;
            border-radius: 30px;
            border: 2px solid #b794f4;
        }

        .form-group {
            margin-bottom: 30px;
        }

        .form-group label {
            display: block;
            color: #b794f4;
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            background: rgba(26, 11, 46, 0.6);
            border: 2px solid #b794f4;
            border-radius: 15px;
            color: white;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #7a4bab, #3d2463);
            border: 2px solid #b794f4;
            color: white;
            font-size: 16px;
            letter-spacing: 3px;
            cursor: pointer;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #b794f4, #7a4bab);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(183, 148, 244, 0.5);
        }

        .success-msg {
            display: none;
            background: rgba(122, 75, 171, 0.4);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            margin-top: 30px;
        }

        .success-msg.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .success-msg .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .rsvp-note {
            text-align: center;
            font-style: italic;
            color: #b794f4;
            margin-top: 40px;
            font-size: 18px;
            line-height: 1.8;
        }

        /* Footer */
        footer {
            padding: 60px 20px;
            text-align: center;
            border-top: 1px solid #b794f4;
        }

        footer .icon {
            font-size: 48px;
            color: #b794f4;
            margin-bottom: 20px;
        }

        footer p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.8);
            margin: 15px 0;
        }

        footer h4 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            margin: 20px 0;
            background: linear-gradient(135deg, #b794f4, #d4af37);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Comments Section */
        .comments-container {
            max-width: 800px;
            margin: 50px auto 0;
        }

        .comment-item {
            background: rgba(183, 148, 244, 0.1);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            border: 1px solid #b794f4;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .comment-author {
            font-size: 18px;
            font-weight: 600;
            color: #b794f4;
        }

        .comment-date {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        .comment-text {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.8;
        }

        /* Floating Notification */
        .floating-notification {
            position: fixed;
            top: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(-150px);
            background-color: #2d2d2d;
            color: white;
            padding: 18px 30px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transition: all 0.5s ease;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .floating-notification.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .floating-notification.success {
            background-color: #28a745;
        }

        .floating-notification.error {
            background-color: #dc3545;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 48px;
            }

            .ampersand {
                font-size: 40px;
            }

            .section-title {
                font-size: 36px;
            }

            .event-card {
                padding: 30px 20px;
            }

            .event-card h3 {
                font-size: 28px;
                flex-direction: column;
            }

            .event-info {
                padding-left: 0;
            }

            .rsvp-box {
                padding: 30px 20px;
            }

            .music-btn {
                width: 50px;
                height: 50px;
                top: 20px;
                right: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Background -->
    <div class="bg-wrapper">
        <div class="stars" id="stars"></div>
    </div>

    <!-- Splash Screen -->
    <div class="splash" id="splash">
        <div class="splash-box">
            <div class="symbol">◆</div>
            <p>YOU ARE INVITED TO</p>
            <h1><?php echo $wedding_data['nama_panggilan']; ?></h1>
            <button class="open-btn" onclick="openInvitation()">REVEAL THE MYSTERY</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main" id="main">
        <!-- Music Button -->
        <!-- <div class="music-btn" id="musicBtn" onclick="toggleMusic()">
            <span>♫</span>
        </div> -->

        <!-- Hidden audio: ganti src dengan file musik Anda -->
        <!-- <audio id="bgMusic" src="../audio/antariksa.mp3" loop crossorigin="anonymous"></audio> -->

        <!-- Hero -->
        <section class="hero">
            <div class="hero-content">
                <h2>THE UNION OF TWO SOULS</h2>
                <?php
                $nama = explode('&amp;', $wedding_data['nama_panggilan'] ?? 'Alexander & Victoria');
                $nama_panggilan_pria = trim($nama[0] ?? 'Alexander');
                $nama_panggilan_wanita = trim($nama[1] ?? 'Victoria');
                ?>
                <h1>
                    <?php echo htmlspecialchars($nama_panggilan_pria); ?>
                    <span class="ampersand">&amp;</span>
                    <?php echo htmlspecialchars($nama_panggilan_wanita); ?>
                </h1>
                <p class="hero-quote">
                    "In the darkness, we found each other's light. In mystery, we discovered eternal love."
                </p>
                <div class="date-box"><?php echo date('d F Y', strtotime($tanggal)); ?></div>
            </div>
        </section>

        <!-- Couple Section -->
        <section class="container">
            <h2 class="section-title">The Destined Pair</h2>

            <div class="couple-grid">
                <div class="couple-card">
                    <div class="avatar">
                        <?php if (!empty($wedding_data['foto_mempelai_pria_url'])): ?>
                            <img src="<?php echo htmlspecialchars($wedding_data['foto_mempelai_pria_url']); ?>"
                                alt="<?php echo htmlspecialchars($wedding_data['nama_mempelai_pria']); ?>"
                                class="avatar-img">
                        <?php else: ?>
                            <div class="avatar-placeholder">👑</div>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo htmlspecialchars($wedding_data['nama_mempelai_pria'] ?? 'Alexander'); ?></h3>
                    <p class="role">THE DARK PRINCE</p>
                    <p class="parents">
                        Son of<br>
                        <strong><?php echo htmlspecialchars($wedding_data['ayah_pria'] ?? 'Mr. Jonathan'); ?> & <?php echo htmlspecialchars($wedding_data['ibu_pria'] ?? 'Mrs. Elizabeth'); ?></strong>
                    </p>
                </div>

                <div class="couple-card">
                    <div class="avatar">
                        <?php if (!empty($wedding_data['foto_mempelai_wanita_url'])): ?>
                            <img src="<?php echo htmlspecialchars($wedding_data['foto_mempelai_wanita_url']); ?>"
                                alt="<?php echo htmlspecialchars($wedding_data['nama_mempelai_wanita']); ?>"
                                class="avatar-img">
                        <?php else: ?>
                            <div class="avatar-placeholder">✨</div>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo htmlspecialchars($wedding_data['nama_mempelai_wanita'] ?? 'Victoria'); ?></h3>
                    <p class="role">THE ENCHANTRESS</p>
                    <p class="parents">
                        Daughter of<br>
                        <strong><?php echo htmlspecialchars($wedding_data['ayah_wanita'] ?? 'Mr. Richard'); ?> & <?php echo htmlspecialchars($wedding_data['ibu_wanita'] ?? 'Mrs. Catherine'); ?></strong>
                    </p>
                </div>
            </div>

            <div class="countdown">
                <h3>The Mystical Hour Approaches</h3>
                <div class="countdown-grid">
                    <div class="time-box">
                        <span class="time-value" id="days">0</span>
                        <span class="time-label">DAYS</span>
                    </div>
                    <div class="time-box">
                        <span class="time-value" id="hours">0</span>
                        <span class="time-label">HOURS</span>
                    </div>
                    <div class="time-box">
                        <span class="time-value" id="minutes">0</span>
                        <span class="time-label">MINUTES</span>
                    </div>
                    <div class="time-box">
                        <span class="time-value" id="seconds">0</span>
                        <span class="time-label">SECONDS</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Event Section -->
        <section class="container">
            <h2 class="section-title">When & Where</h2>

            <?php if (!empty($wedding_data['jam_acara_pemberkatan'])): ?>
                <div class="event-card">
                    <h3>
                        <span class="event-icon">⚜️</span>
                        The Vow Ceremony
                    </h3>
                    <div class="event-info">
                        <p>📅 <?php echo htmlspecialchars($wedding_data['tanggal_acara'] ?? ''); ?></p>
                        <p>🕐 <?php echo htmlspecialchars($wedding_data['jam_acara_pemberkatan'] ?? ''); ?></p>
                        <p>📍 <strong><?php echo htmlspecialchars($wedding_data['lokasi_acara_pemberkatan'] ?? 'Grand Cathedral'); ?></strong></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($wedding_data['jam_acara_resepsi'])): ?>
                <div class="event-card">
                    <h3>
                        <span class="event-icon">🏰</span>
                        The Grand Reception
                    </h3>
                    <div class="event-info">
                        <p>📅 <?php echo htmlspecialchars($wedding_data['tanggal_acara'] ?? ''); ?></p>
                        <p>🕐 <?php echo htmlspecialchars($wedding_data['jam_acara_resepsi'] ?? ''); ?></p>
                        <p>📍 <strong><?php echo htmlspecialchars($wedding_data['lokasi_acara_resepsi'] ?? 'Royal Ballroom'); ?></strong></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="dresscode">
                <strong>Dress Code</strong><br>
                <?php echo htmlspecialchars($wedding_data['dress_code'] ?? 'Formal / Evening Attire'); ?>
            </div>

            <?php if (!empty($wedding_data['google_maps'])): ?>
                <center>
                    <a href="<?php echo htmlspecialchars($wedding_data['google_maps']); ?>" target="_blank" class="map-btn">🗺️ VIEW LOCATION</a>
                </center>
            <?php endif; ?>
        </section>

        <!-- Gallery -->
        <section class="container">
            <h2 class="section-title">Our Gallery</h2>

            <div class="gallery-grid">
                <?php if (!empty($wedding_data['galeri_foto'])): ?>
                    <?php foreach ($wedding_data['galeri_foto'] as $foto): ?>
                        <div class="gallery-item">
                            <img src="<?php echo htmlspecialchars($foto['url']); ?>"
                                alt="Gallery Photo"
                                class="gallery-img">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <p class="gallery-note">
                "Every picture tells a chapter of our mysterious love story"
            </p>
        </section>

        <!-- RSVP -->
        <section class="container">
            <h2 class="section-title">RSVP</h2>

            <!-- RSVP Form -->
            <div class="rsvp-box">
                <form id="rsvpForm" method="POST">
                    <input type="hidden" name="wedding_id" value="<?php echo $wedding_data['wedding_id']; ?>">
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="nama_tamu" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label>Number of Guests *</label>
                        <select name="jumlah_tamu" required>
                            <option value="">Choose Number of Guests</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5+</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Will You Attend? *</label>
                        <select name="status" required>
                            <option value="">Choose your answer</option>
                            <option value="hadir">Yes, I will attend</option>
                            <option value="tidak_hadir">Unfortunately, I cannot</option>
                            <option value="belum_pasti">I am not sure yet</option>
                        </select>
                    </div>

                    <button type="submit" class="submit-btn">CONFIRM YOUR PRESENCE</button>
                </form>
            </div>

            <!-- Comment Form -->
            <div class="rsvp-box">
                <form id="commentForm" method="POST">
                    <input type="hidden" name="wedding_id" value="<?php echo $wedding_data['wedding_id']; ?>">
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="nama_tamu" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label>Your Blessing *</label>
                        <textarea name="komentar" placeholder="Share your wishes for the couple..." required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">SEND YOUR BLESSING</button>
                </form>
            </div>

            <p class="rsvp-note">
                "Your presence is the greatest gift we could receive.<br>
                We look forward to celebrating this mystical union with you."
            </p>

            <!-- Comments List -->
            <div class="comments-container" id="commentsContainer">
                <p style="text-align: center; color: #b794f4; padding: 40px; font-style: italic;">Loading blessings...</p>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="icon">◆</div>
            <p>Thank you for being part of our mysterious journey</p>
            <h4><?php echo htmlspecialchars($nama_panggilan_pria); ?> & <?php echo htmlspecialchars($nama_panggilan_wanita); ?></h4>
            <p style="color: #b794f4; letter-spacing: 3px;"><?php echo date('d • m • Y', strtotime($tanggal)); ?></p>
        </footer>

        <!-- Floating Notification -->
        <div class="floating-notification" id="notification">
            <span id="notificationText"></span>
        </div>
    </div>

    <script>
        // Constants
        const WEDDING_ID = <?php echo $wedding_data['wedding_id']; ?>;
        const WEDDING_DATE = new Date('<?php echo date('Y-m-d H:i:s', strtotime($tanggal . ' ' . $jam)); ?>');

        // Generate stars
        function generateStars() {
            const stars = document.getElementById('stars');
            for (let i = 0; i < 100; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.animationDelay = Math.random() * 3 + 's';
                stars.appendChild(star);
            }
        }

        // Open invitation
        function openInvitation() {
            document.getElementById('splash').classList.add('hide');
            setTimeout(() => {
                document.getElementById('main').classList.add('show');
                startCountdown();
                loadComments();
            }, 500);
        }

        // Toggle music
        function toggleMusic() {
            const btn = document.getElementById('musicBtn');
            const isActive = btn.classList.contains('active');

            if (isActive) {
                btn.classList.remove('active');
            } else {
                btn.classList.add('active');
            }
        }

        // Countdown function
        function startCountdown() {
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = WEDDING_DATE.getTime() - now;

            if (distance > 0) {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('days').textContent = days.toString().padStart(2, '0');
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            } else {
                document.getElementById('days').textContent = '00';
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notificationText');

            notificationText.textContent = message;
            notification.className = 'floating-notification ' + type;
            notification.classList.add('show');

            setTimeout(() => {
                notification.classList.remove('show');
            }, 4000);
        }

        // Escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.toString().replace(/[&<>"']/g, m => map[m]);
        }

        // Load comments from API - FIXED VERSION
        async function loadComments() {
            const container = document.getElementById('commentsContainer');

            try {
                // Gunakan path yang relatif
                const apiUrl = `./api/get_comments.php?wedding_id=${WEDDING_ID}`;

                const response = await fetch(apiUrl);
                const result = await response.json();

                if (result.success === true && result.comments) {
                    if (result.comments.length > 0) {
                        container.innerHTML = result.comments.map((comment, index) => `
                        <div class="comment-item" style="animation-delay: ${index * 0.1}s">
                            <div class="comment-header">
                                <span class="comment-author">${escapeHtml(comment.nama)}</span>
                                <span class="comment-date">${escapeHtml(comment.tanggal)}</span>
                            </div>
                            <p class="comment-text">${escapeHtml(comment.komentar)}</p>
                        </div>
                    `).join('');
                    } else {
                        container.innerHTML = '<p style="text-align: center; color: #b794f4; padding: 40px; font-style: italic;">No blessings yet. Be the first to send your wishes! 💝</p>';
                    }
                } else {
                    container.innerHTML = '<p style="text-align: center; color: #b794f4; padding: 40px; font-style: italic;">No comments available</p>';
                }
            } catch (error) {
                container.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 40px;">⚠️ Failed to load blessings</p>';
            }
        }

        // Handle RSVP form submission - FIXED VERSION
        const rsvpForm = document.getElementById('rsvpForm');
        if (rsvpForm) {
            rsvpForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('.submit-btn');
                const originalText = submitBtn.textContent;

                submitBtn.textContent = 'SENDING...';
                submitBtn.disabled = true;

                try {
                    const response = await fetch('./api/save_rsvp.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        showNotification('✅ RSVP submitted successfully! Thank you', 'success');
                        this.reset();
                        this.querySelector('[name="wedding_id"]').value = WEDDING_ID;
                    } else {
                        showNotification('❌ ' + (result.message || 'Failed to submit RSVP'), 'error');
                    }
                } catch (error) {
                    showNotification('❌ Connection error. Please try again.', 'error');
                } finally {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });
        }

        // Handle comment form submission - FIXED VERSION
        const commentForm = document.getElementById('commentForm');
        if (commentForm) {
            commentForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('.submit-btn');
                const originalText = submitBtn.textContent;

                submitBtn.textContent = 'SENDING...';
                submitBtn.disabled = true;

                try {
                    const response = await fetch('./api/save_comment.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        showNotification('✅ Blessing sent successfully!', 'success');
                        this.reset();
                        this.querySelector('[name="wedding_id"]').value = WEDDING_ID;
                        // Reload comments
                        setTimeout(() => loadComments(), 1000);
                    } else {
                        showNotification('❌ ' + (result.message || 'Failed to send blessing'), 'error');
                    }
                } catch (error) {
                    showNotification('❌ Connection error. Please try again.', 'error');
                } finally {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });
        }

        // Initialize
        generateStars();
    </script>
</body>

</html>