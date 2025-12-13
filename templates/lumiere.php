<!-- Template undangan lumiere - FIXED VERSION -->
<?php
$tanggal = $wedding_data['tanggal_acara']; // "Jumat, 09 Januari 2026"
// Hapus hari
$tanggal = preg_replace('/^[^,]+, /', '', $tanggal);
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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>The Wedding of <?php echo htmlspecialchars($wedding_data['nama_mempelai_pria']); ?> & <?php echo htmlspecialchars($wedding_data['nama_mempelai_wanita']); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        :root {
            --sage: #9caf88;
            --sage-dark: #7a9068;
            --sage-light: #c8d5b9;
            --white: #ffffff;
            --cream: #faf9f6;
            --gold: #d4af37;
            --text-dark: #2d3e2d;
        }

        body {
            font-family: 'Cormorant Garamond', serif;
            background: var(--cream);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        .fantasy-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.05;
            pointer-events: none;
        }

        /* Floating Particles */
        .particle {
            position: fixed;
            width: 4px;
            height: 4px;
            background: var(--sage);
            border-radius: 50%;
            pointer-events: none;
            opacity: 0.3;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) translateX(0);
            }

            25% {
                transform: translateY(-100px) translateX(50px);
            }

            50% {
                transform: translateY(-200px) translateX(-30px);
            }

            75% {
                transform: translateY(-150px) translateX(80px);
            }
        }

        /* Splash Screen */
        .splash-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, var(--sage-dark) 0%, var(--sage) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.8s ease, visibility 0.8s ease;
        }

        .splash-screen.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .splash-content {
            text-align: center;
            color: var(--white);
            animation: fadeInUp 1s ease;
        }

        .magic-circle {
            width: 200px;
            height: 200px;
            margin: 0 auto 2rem;
            position: relative;
            animation: rotate 20s linear infinite;
        }

        .magic-circle::before,
        .magic-circle::after {
            content: '';
            position: absolute;
            border: 2px solid var(--white);
            border-radius: 50%;
            opacity: 0.3;
        }

        .magic-circle::before {
            width: 100%;
            height: 100%;
            animation: pulse 2s ease infinite;
        }

        .magic-circle::after {
            width: 80%;
            height: 80%;
            top: 10%;
            left: 10%;
            animation: pulse 2s ease infinite 0.5s;
        }

        .magic-symbol {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 4rem;
            color: var(--white);
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.3;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.6;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .splash-title {
            font-family: 'Cinzel', serif;
            font-size: 1rem;
            letter-spacing: 4px;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .splash-names {
            font-family: 'Cinzel', serif;
            font-size: 3.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .open-btn {
            background: var(--white);
            color: var(--sage-dark);
            border: none;
            padding: 1rem 3rem;
            font-family: 'Cinzel', serif;
            font-size: 1rem;
            letter-spacing: 2px;
            cursor: pointer;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .open-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);
        }

        /* Main Content */
        .main-content {
            opacity: 0;
            transition: opacity 0.8s ease;
        }

        .main-content.visible {
            opacity: 1;
        }

        /* Music Toggle */
        .music-toggle {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 1000;
            width: 50px;
            height: 50px;
            background: var(--white);
            border: 2px solid var(--sage);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .music-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .music-toggle.playing {
            background: var(--sage);
            color: var(--white);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: linear-gradient(to bottom, var(--cream), var(--white));
        }

        .hero-content {
            text-align: center;
            padding: 2rem;
            max-width: 800px;
        }

        .enchanted-text {
            font-size: 1rem;
            letter-spacing: 3px;
            color: var(--sage-dark);
            margin-bottom: 1rem;
            text-transform: uppercase;
            font-weight: 400;
        }

        .couple-names {
            font-family: 'Cinzel', serif;
            font-size: 5rem;
            font-weight: 600;
            color: var(--sage-dark);
            margin: 1rem 0;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.05);
        }

        .couple-names .ampersand {
            font-size: 4rem;
            color: var(--gold);
            font-style: italic;
            display: block;
            margin: 0.5rem 0;
        }

        .quote {
            font-size: 1.3rem;
            font-style: italic;
            color: var(--text-dark);
            max-width: 600px;
            margin: 2rem auto;
            line-height: 1.8;
        }

        .date-badge {
            display: inline-block;
            padding: 1rem 2rem;
            background: var(--sage-light);
            border-radius: 50px;
            margin-top: 2rem;
            color: var(--sage-dark);
            font-size: 1.2rem;
            font-weight: 500;
            letter-spacing: 2px;
        }

        /* Decorative Elements */
        .leaf-decoration {
            position: absolute;
            opacity: 0.15;
            pointer-events: none;
            font-size: 3rem;
        }

        .leaf-decoration.top-left {
            top: 10%;
            left: 5%;
            transform: rotate(-30deg);
        }

        .leaf-decoration.top-right {
            top: 15%;
            right: 5%;
            transform: rotate(30deg);
        }

        .leaf-decoration.bottom-left {
            bottom: 10%;
            left: 8%;
            transform: rotate(45deg);
        }

        .leaf-decoration.bottom-right {
            bottom: 15%;
            right: 8%;
            transform: rotate(-45deg);
        }

        /* Section */
        section {
            padding: 5rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            font-family: 'Cinzel', serif;
            font-size: 3rem;
            text-align: center;
            color: var(--sage-dark);
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title::after {
            content: '❋';
            display: block;
            font-size: 1.5rem;
            color: var(--gold);
            margin-top: 1rem;
        }

        /* Couple Info */
        .couple-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-bottom: 4rem;
        }

        .couple-card {
            background: var(--white);
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 2px solid var(--sage-light);
            transition: all 0.3s ease;
        }

        .couple-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .avatar {
            width: 150px;
            height: 150px;
            margin: 0 auto 2rem;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sage-light), var(--sage));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: var(--white);
            border: 4px solid var(--white);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .avatar img {
            width: 100%;
            border-radius: 50%;
        }
        .couple-name {
            font-family: 'Cinzel', serif;
            font-size: 2rem;
            color: var(--sage-dark);
            margin-bottom: 0.5rem;
        }

        .couple-label {
            color: var(--sage);
            margin: 1rem 0;
            font-size: 1.1rem;
        }

        .parent-names {
            color: var(--text-dark);
            font-size: 1.1rem;
            line-height: 1.6;
        }

        /* Countdown */
        .countdown-container {
            background: linear-gradient(135deg, var(--sage), var(--sage-dark));
            padding: 3rem;
            border-radius: 20px;
            color: var(--white);
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .countdown-title {
            font-family: 'Cinzel', serif;
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .time-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 15px;
            min-width: 100px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .time-value {
            font-size: 3rem;
            font-weight: 600;
            display: block;
        }

        .time-label {
            font-size: 1rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Event Details */
        .event-grid {
            display: grid;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .event-card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 20px;
            border-left: 5px solid var(--sage);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            display: flex;
            gap: 2rem;
            align-items: start;
        }

        .event-icon {
            font-size: 3rem;
            color: var(--sage);
            min-width: 60px;
        }

        .event-details h3 {
            font-family: 'Cinzel', serif;
            font-size: 2rem;
            color: var(--sage-dark);
            margin-bottom: 1rem;
        }

        .event-info {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            font-size: 1.1rem;
            color: var(--text-dark);
        }

        .event-info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .event-info-item::before {
            content: '✦';
            color: var(--gold);
            font-size: 1rem;
        }

        .dress-code {
            background: var(--sage-light);
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            margin-top: 2rem;
        }

        .dress-code strong {
            color: var(--sage-dark);
            font-family: 'Cinzel', serif;
            font-size: 1.2rem;
        }

        .map-btn {
            display: inline-block;
            margin-top: 2rem;
            padding: 1rem 2.5rem;
            background: var(--sage);
            color: var(--white);
            text-decoration: none;
            border-radius: 50px;
            font-family: 'Cinzel', serif;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .map-btn:hover {
            background: var(--sage-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }

        /* Gallery */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .gallery-item {
            aspect-ratio: 1;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--sage-light), var(--sage));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--white);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            overflow: hidden;
        }

        .gallery-item:hover {
            transform: scale(1.05) rotate(2deg);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        .gallery-caption {
            text-align: center;
            font-style: italic;
            color: var(--sage);
            margin-top: 2rem;
            font-size: 1.2rem;
        }

        /* RSVP & Comments */
        .rsvp-container,
        .comment-container {
            max-width: 600px;
            margin: 0 auto 30px;
        }

        .rsvp-form,
        .comment-form {
            background: var(--white);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 2px solid var(--sage-light);
        }

        .form-group {
            margin-bottom: 2rem;
        }

        .form-group label {
            display: block;
            color: var(--sage-dark);
            font-family: 'Cinzel', serif;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--sage-light);
            border-radius: 10px;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background: var(--cream);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--sage);
            box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            width: 100%;
            padding: 1.2rem;
            background: var(--sage);
            color: var(--white);
            border: none;
            border-radius: 50px;
            font-family: 'Cinzel', serif;
            font-size: 1.1rem;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .submit-btn:hover {
            background: var(--sage-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .rsvp-note {
            text-align: center;
            font-style: italic;
            color: var(--sage);
            margin-top: 3rem;
            font-size: 1.2rem;
        }

        /* Comments Section */
        .comments-container {
            max-width: 800px;
            margin: 50px auto 0;
        }

        .comment-item {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
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
            color: var(--sage-dark);
        }

        .comment-date {
            font-size: 14px;
            color: #999;
        }

        .comment-text {
            font-size: 16px;
            color: #666;
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

        /* Footer */
        footer {
            background: linear-gradient(to top, var(--sage-dark), var(--sage));
            color: var(--white);
            padding: 3rem 2rem;
            text-align: center;
        }

        .footer-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .footer-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .footer-text {
            font-size: 1.2rem;
            margin: 1rem 0;
            line-height: 1.6;
        }

        .footer-names {
            font-family: 'Cinzel', serif;
            font-size: 1.8rem;
            margin: 1rem 0;
        }

        .footer-date {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .couple-names {
                font-size: 3rem;
            }

            .couple-names .ampersand {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2.5rem;
            }

            .event-card {
                flex-direction: column;
                text-align: center;
            }

            .countdown-timer {
                gap: 1rem;
            }

            .time-box {
                min-width: 80px;
                padding: 1rem;
            }

            .time-value {
                font-size: 2rem;
            }

            .rsvp-form,
            .comment-form {
                padding: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Floating Particles -->
    <div class="particles-container"></div>

    <!-- Splash Screen -->
    <div class="splash-screen" id="splashScreen">
        <div class="splash-content">
            <div class="magic-circle">
                <div class="magic-symbol">✧</div>
            </div>
            <p class="splash-title">A FANTASY TALE BEGINS</p>
            <h1 class="splash-names"><?php echo ($wedding_data['nama_panggilan']); ?></h1>
            <button class="open-btn" id="openBtn">ENTER THE REALM</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent" aria-hidden="true">
        <!-- Music Toggle -->
        <!-- <div class="music-toggle" id="musicToggle" title="Toggle music">
            <span id="musicIcon">♪</span>
        </div> -->

        <!-- Hidden audio: ganti src dengan file musik Anda -->
                <!-- <audio id="bgMusic" loop crossorigin="anonymous" preload="auto">
            <source src="../audio/lumiere.mp3" type="audio/mpeg">
        </audio> -->

        <!-- Hero Section -->
        <section class="hero">
            <div class="leaf-decoration top-left">🍃</div>
            <div class="leaf-decoration top-right">🍃</div>
            <div class="hero-content">
                <p class="enchanted-text"><?php echo htmlspecialchars($wedding_data['kalimat_pembuka'] ?? 'An Enchanted Union'); ?></p>
                <h1 class="couple-names">
                    <?php
                    $nama = explode('&amp;', $wedding_data['nama_panggilan']);
                    $nama_panggilan_pria = trim($nama[0] ?? '');
                    $nama_panggilan_wanita = trim($nama[1] ?? '');
                    ?>
                    <?php echo $nama_panggilan_pria; ?>
                    <span class="ampersand">&amp;</span>
                    <?php echo $nama_panggilan_wanita; ?>
                </h1>
                <p class="quote">"In a realm where magic meets love, two souls intertwine to create their own fairy tale"</p>
                <div class="date-badge"><?php echo date('d F Y', strtotime($tanggal)); ?></div>
            </div>
            <div class="leaf-decoration bottom-left">🍃</div>
            <div class="leaf-decoration bottom-right">🍃</div>
        </section>

        <!-- Couple Info -->
        <section>
            <h2 class="section-title">The Betrothed</h2>
            <div class="couple-container">
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
                    <h3 class="couple-name"><?php echo htmlspecialchars($wedding_data['nama_mempelai_pria']); ?></h3>
                    <p class="couple-label">The Valiant Knight</p>
                    <p class="parent-names">
                        Putra <?php echo htmlspecialchars($wedding_data['anak_ke_pria'] ?? ''); ?> dari<br>
                        <strong><?php echo htmlspecialchars($wedding_data['ayah_pria']); ?> & <?php echo htmlspecialchars($wedding_data['ibu_pria']); ?></strong>
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
                    <h3 class="couple-name"><?php echo htmlspecialchars($wedding_data['nama_mempelai_wanita']); ?></h3>
                    <p class="couple-label">The Enchantress</p>
                    <p class="parent-names">
                        Putri <?php echo htmlspecialchars($wedding_data['anak_ke_wanita'] ?? ''); ?> dari<br>
                        <strong><?php echo htmlspecialchars($wedding_data['ayah_wanita']); ?> & <?php echo htmlspecialchars($wedding_data['ibu_wanita']); ?></strong>
                    </p>
                </div>
            </div>

            <div class="countdown-container">
                <h3 class="countdown-title">The Magic Unfolds In</h3>
                <div class="countdown-timer">
                    <div class="time-box">
                        <span class="time-value" id="days">0</span>
                        <span class="time-label">Days</span>
                    </div>
                    <div class="time-box">
                        <span class="time-value" id="hours">0</span>
                        <span class="time-label">Hours</span>
                    </div>
                    <div class="time-box">
                        <span class="time-value" id="minutes">0</span>
                        <span class="time-label">Minutes</span>
                    </div>
                    <div class="time-box">
                        <span class="time-value" id="seconds">0</span>
                        <span class="time-label">Seconds</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Event Details -->
        <section>
            <h2 class="section-title">The Sacred Ceremony</h2>
            <div class="event-grid">
                <?php if (!empty($wedding_data['jam_acara_pemberkatan'])): ?>
                    <div class="event-card">
                        <div class="event-icon">⚔️</div>
                        <div class="event-details">
                            <h3>The Vow Ritual</h3>
                            <div class="event-info">
                                <div class="event-info-item"><?php echo $wedding_data['tanggal_acara']; ?></div>
                                <div class="event-info-item"><?php echo htmlspecialchars($wedding_data['jam_acara_pemberkatan']); ?> WIB</div>
                                <div class="event-info-item">
                                    <strong><?php echo htmlspecialchars($wedding_data['lokasi_acara_pemberkatan']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($wedding_data['jam_acara_resepsi'])): ?>
                    <div class="event-card">
                        <div class="event-icon">🏰</div>
                        <div class="event-details">
                            <h3>The Grand Celebration</h3>
                            <div class="event-info">
                                <div class="event-info-item"><?php echo $wedding_data['tanggal_acara']; ?></div>
                                <div class="event-info-item"><?php echo htmlspecialchars($wedding_data['jam_acara_resepsi']); ?> WIB</div>
                                <div class="event-info-item">
                                    <strong><?php echo htmlspecialchars($wedding_data['lokasi_acara_resepsi']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="dress-code">
                <strong>Royal Attire</strong><br>
                <?php echo htmlspecialchars($wedding_data['dress_code']) ?>
            </div>

            <?php if (!empty($wedding_data['google_maps'])): ?>
                <center>
                    <a href="<?php echo htmlspecialchars($wedding_data['google_maps']); ?>" target="_blank" class="map-btn">📍 VIEW THE REALM MAP</a>
                </center>
            <?php endif; ?>
        </section>

        <!-- Gallery -->
        <section>
            <h2 class="section-title">Chronicles of Love</h2>
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
            <p class="gallery-caption">"Every moment is a page in our magical story"</p>
        </section>

        <!-- RSVP -->
        <section>
            <h2 class="section-title">Join Our Quest</h2>
            <div class="rsvp-container">
                <form class="rsvp-form" id="rsvpForm">
                    <input type="hidden" name="wedding_id" value="<?php echo $wedding_data['wedding_id']; ?>">
                    <div class="form-group">
                        <label>Your Noble Name *</label>
                        <input type="text" name="nama_tamu" placeholder="Enter your name" required>
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
                            <option value="">Choose your destiny</option>
                            <option value="hadir">Yes, I shall attend</option>
                            <option value="tidak_hadir">Alas, I cannot make it</option>
                            <option value="belum_pasti">I am undecided</option>
                        </select>
                    </div>
                    <button type="submit" class="submit-btn">CONFIRM YOUR PRESENCE</button>
                </form>
            </div>

            <div class="comment-container">
                <form class="comment-form" id="commentForm">
                    <input type="hidden" name="wedding_id" value="<?php echo $wedding_data['wedding_id']; ?>">
                    <div class="form-group">
                        <label>Your Noble Name *</label>
                        <input type="text" name="nama_tamu" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label>Your Blessing *</label>
                        <textarea name="komentar" placeholder="Share your wishes for the couple..." required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">SEND YOUR BLESSING</button>
                </form>
            </div>

            <p class="rsvp-note">Kami menantikan kehadiran dan doa restu Anda.</p>

            <!-- Comments List -->
            <div class="comments-container" id="commentsContainer">
                <p style="text-align: center; color: #999; padding: 40px;">Memuat ucapan...</p>
            </div>
        </section>

        <footer>
            <div class="footer-content">
                <div class="footer-icon">✦</div>
                <div class="footer-text">With love and magic, we await our day.</div>
                <div class="footer-names"><?php echo $wedding_data['nama_panggilan']; ?></div>
                <div class="footer-date"><?php echo date('d F Y', strtotime($tanggal)); ?></div>
            </div>
        </footer>

        <!-- Floating Notification -->
        <div class="floating-notification" id="notification">
            <span id="notificationText"></span>
        </div>
    </div>

    <script>
        // ============================================
        // WEDDING INVITATION SCRIPT - FIXED VERSION
        // ============================================

        <?php
        $jamRaw = $wedding_data['jam_acara_pemberkatan'] ?? '10:00:00';

        // Ambil hanya bagian sebelum tanda '-'
        $jamParts = explode('-', $jamRaw);
        $jam = trim($jamParts[0]); // "09:00"

        // Jika jam tidak lengkap (misal tanpa detik), tambahkan ":00"
        if (preg_match('/^\d{2}:\d{2}$/', $jam)) {
            $jam .= ':00';
        }

        ?>

            (function() {
                const WEDDING_ID = <?php echo $wedding_data['wedding_id']; ?>;
                const WEDDING_DATE = new Date('<?php echo date('Y-m-d H:i:s', strtotime($tanggal . ' ' . $jam)); ?>');

                const splash = document.getElementById('splashScreen');
                const main = document.getElementById('mainContent');
                const openBtn = document.getElementById('openBtn');
                const musicToggle = document.getElementById('musicToggle');
                const musicIcon = document.getElementById('musicIcon');
                const bgMusic = document.getElementById('bgMusic');
                const particlesContainer = document.querySelector('.particles-container');

                // ========== OPEN INVITATION ==========
                function openInvitation() {
                    splash.classList.add('hidden');
                    main.classList.add('visible');
                    main.setAttribute('aria-hidden', 'false');

                    // Try to play music
                    if (bgMusic && bgMusic.src) {
                        bgMusic.play().then(() => {
                            musicToggle.classList.add('playing');
                            musicIcon.textContent = '♫';
                        }).catch(() => {
                            musicToggle.classList.remove('playing');
                            musicIcon.textContent = '♪';
                        });
                    }

                    // Load comments after opening
                    setTimeout(() => {
                        loadComments();
                    }, 800);
                }

                // ========== MUSIC TOGGLE ==========
                function toggleMusic() {
                    if (!bgMusic) return;
                    if (bgMusic.paused) {
                        bgMusic.play();
                        musicToggle.classList.add('playing');
                        musicIcon.textContent = '♫';
                    } else {
                        bgMusic.pause();
                        musicToggle.classList.remove('playing');
                        musicIcon.textContent = '♪';
                    }
                }

                // ========== COUNTDOWN ==========
                function updateCountdown() {
                    const now = new Date();
                    const diff = WEDDING_DATE - now;

                    const daysEl = document.getElementById('days');
                    const hoursEl = document.getElementById('hours');
                    const minutesEl = document.getElementById('minutes');
                    const secondsEl = document.getElementById('seconds');

                    if (diff <= 0) {
                        if (daysEl) daysEl.textContent = '0';
                        if (hoursEl) hoursEl.textContent = '0';
                        if (minutesEl) minutesEl.textContent = '0';
                        if (secondsEl) secondsEl.textContent = '0';
                        return;
                    }

                    const seconds = Math.floor((diff / 1000) % 60);
                    const minutes = Math.floor((diff / (1000 * 60)) % 60);
                    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

                    if (daysEl) daysEl.textContent = days;
                    if (hoursEl) hoursEl.textContent = hours;
                    if (minutesEl) minutesEl.textContent = minutes;
                    if (secondsEl) secondsEl.textContent = seconds;
                }

                // ========== FLOATING PARTICLES ==========
                function createParticles(count = 30) {
                    particlesContainer.innerHTML = '';
                    const w = window.innerWidth;
                    const h = window.innerHeight;

                    for (let i = 0; i < count; i++) {
                        const p = document.createElement('div');
                        p.className = 'particle';
                        const size = (Math.random() * 3) + 2;
                        p.style.width = `${size}px`;
                        p.style.height = `${size}px`;
                        p.style.left = (Math.random() * w) + 'px';
                        p.style.top = (Math.random() * h) + 'px';
                        p.style.opacity = (Math.random() * 0.5) + 0.1;

                        const colors = ['var(--sage)', 'var(--gold)', 'rgba(156,175,136,0.8)'];
                        p.style.background = colors[Math.floor(Math.random() * colors.length)];

                        const duration = 10 + Math.random() * 20;
                        p.style.animationDuration = duration + 's';
                        p.style.animationDelay = (Math.random() * 5) + 's';

                        particlesContainer.appendChild(p);
                    }
                }

                // ========== RSVP FORM HANDLER ==========
                document.getElementById('rsvpForm').addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('.submit-btn');
                    const originalText = submitBtn.textContent;

                    submitBtn.textContent = '⏳ Mengirim...';
                    submitBtn.disabled = true;

                    try {
                        const response = await fetch('api/save_rsvp.php', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            showNotification('✅ RSVP berhasil dikirim! Terima kasih', 'success');
                            this.reset();
                            // Set kembali wedding_id
                            this.querySelector('[name="wedding_id"]').value = WEDDING_ID;
                        } else {
                            showNotification('❌ ' + (result.message || 'RSVP gagal dikirim'), 'error');
                        }
                    } catch (error) {
                        console.error('RSVP Error:', error);
                        showNotification('❌ Terjadi kesalahan koneksi', 'error');
                    } finally {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                    }
                });

                // ========== COMMENT FORM HANDLER ==========
                document.getElementById('commentForm').addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('.submit-btn');
                    const originalText = submitBtn.textContent;

                    submitBtn.textContent = '⏳ Mengirim...';
                    submitBtn.disabled = true;

                    try {
                        const response = await fetch('api/save_comment.php', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            showNotification('✅ Ucapan berhasil dikirim!', 'success');
                            this.reset();
                            // Set kembali wedding_id
                            this.querySelector('[name="wedding_id"]').value = WEDDING_ID;
                            // Reload comments
                            loadComments();
                        } else {
                            showNotification('❌ ' + (result.message || 'Ucapan gagal dikirim'), 'error');
                        }
                    } catch (error) {
                        console.error('Comment Error:', error);
                        showNotification('❌ Terjadi kesalahan koneksi', 'error');
                    } finally {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                    }
                });

                // ========== LOAD COMMENTS dengan AJAX ==========
                async function loadComments() {
                    const container = document.getElementById('commentsContainer');

                    try {
                        const response = await fetch(`api/get_comments.php?wedding_id=${WEDDING_ID}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            const comments = result.comments || [];

                            if (comments.length > 0) {
                                container.innerHTML = comments.map((comment, index) => `
                                <div class="comment-item" style="animation-delay: ${index * 0.1}s">
                                    <div class="comment-header">
                                        <span class="comment-author">${escapeHtml(comment.nama)}</span>
                                        <span class="comment-date">${escapeHtml(comment.tanggal)}</span>
                                    </div>
                                    <p class="comment-text">${escapeHtml(comment.komentar)}</p>
                                </div>
                            `).join('');
                            } else {
                                container.innerHTML = '<p style="text-align: center; color: #999; padding: 40px;">Belum ada ucapan. Jadilah yang pertama! 💝</p>';
                            }
                        } else {
                            throw new Error(result.message || 'Gagal memuat data');
                        }
                    } catch (error) {
                        console.error('Error loading comments:', error);
                        container.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 40px;">⚠️ Gagal memuat ucapan.</p>';
                    }
                }

                // ========== SHOW NOTIFICATION ==========
                function showNotification(message, type) {
                    const notification = document.getElementById('notification');
                    const notificationText = document.getElementById('notificationText');

                    notificationText.textContent = message;
                    notification.className = 'floating-notification show ' + type;

                    setTimeout(() => {
                        notification.classList.remove('show');
                    }, 4000);
                }

                // ========== ESCAPE HTML ==========
                function escapeHtml(text) {
                    const map = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    };
                    return text.replace(/[&<>"']/g, m => map[m]);
                }

                // ========== EVENT LISTENERS ==========
                openBtn.addEventListener('click', openInvitation);
                musicToggle.addEventListener('click', toggleMusic);

                // ========== INITIALIZATION ==========
                updateCountdown();
                setInterval(updateCountdown, 1000);
                

                createParticles(35);
                window.addEventListener('resize', () => createParticles(35));

                console.log('✅ Wedding System Initialized');
                console.log('Wedding ID:', WEDDING_ID);
                console.log('Wedding Date:', WEDDING_DATE);
            })();
    </script>
</body>

</html>