<!DOCTYPE html>
<html lang="id">
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

// Ambil jam acara
$jamRaw = $wedding_data['jam_acara_pemberkatan'] ?? '10:00:00';
$jamParts = explode('-', $jamRaw);
$jam = trim($jamParts[0]); // "09:00"
if (preg_match('/^\d{2}:\d{2}$/', $jam)) {
    $jam .= ':00';
}

// Ambil nama panggilan
$nama_panggilan = $wedding_data['nama_panggilan'] ?? 'Michael & Jessica';
$nama = explode('&amp;', $nama_panggilan);
$nama_panggilan_pria = trim($nama[0] ?? 'Michael');
$nama_panggilan_wanita = trim($nama[1] ?? 'Jessica');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Wedding of <?php echo htmlspecialchars($nama_panggilan_pria); ?> & <?php echo htmlspecialchars($nama_panggilan_wanita); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --gold: #d4af37;
            --gold-light: #f4e4c1;
            --white: #ffffff;
            --cream: #faf9f7;
            --text: #2c2c2c;
            --gray: #666666;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--cream);
            color: var(--text);
            overflow-x: hidden;
            position: relative;
        }

        /* ===== HIASAN BACKGROUND ELEGAN ===== */
        .background-ornaments {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            opacity: 0.1;
        }

        .ornament-left {
            position: absolute;
            left: 50px;
            top: 50%;
            transform: translateY(-50%);
            width: 150px;
            height: 400px;
            background-image: url("data:image/svg+xml,%3Csvg width='150' height='400' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M75,20 C85,20 90,50 75,70 C60,90 40,100 50,150 C60,200 90,220 75,260 C60,300 30,320 40,370 C50,380 70,390 75,380' fill='none' stroke='%23d4af37' stroke-width='1'/%3E%3Cpath d='M40,60 Q75,40 110,60' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3Cpath d='M60,120 Q75,100 90,120' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3Cpath d='M50,200 Q75,180 100,200' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3Cpath d='M65,300 Q75,280 85,300' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
        }

        .ornament-right {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%) scaleX(-1);
            width: 150px;
            height: 400px;
            background-image: url("data:image/svg+xml,%3Csvg width='150' height='400' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M75,20 C85,20 90,50 75,70 C60,90 40,100 50,150 C60,200 90,220 75,260 C60,300 30,320 40,370 C50,380 70,390 75,380' fill='none' stroke='%23d4af37' stroke-width='1'/%3E%3Cpath d='M40,60 Q75,40 110,60' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3Cpath d='M60,120 Q75,100 90,120' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3Cpath d='M50,200 Q75,180 100,200' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3Cpath d='M65,300 Q75,280 85,300' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
        }

        /* Corner ornaments */
        .corner-ornament {
            position: fixed;
            width: 100px;
            height: 100px;
            z-index: -1;
            opacity: 0.08;
        }

        .corner-top-left {
            top: 30px;
            left: 30px;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20,20 L80,20 L80,30 Q70,30 70,40 L40,40 Q40,30 30,30 L20,30 Z' fill='%23d4af37'/%3E%3Cpath d='M20,20 L20,80 L30,80 Q30,70 40,70 L40,40 Q30,40 30,30 Z' fill='%23d4af37'/%3E%3C/svg%3E");
        }

        .corner-top-right {
            top: 30px;
            right: 30px;
            transform: scaleX(-1);
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20,20 L80,20 L80,30 Q70,30 70,40 L40,40 Q40,30 30,30 L20,30 Z' fill='%23d4af37'/%3E%3Cpath d='M20,20 L20,80 L30,80 Q30,70 40,70 L40,40 Q30,40 30,30 Z' fill='%23d4af37'/%3E%3C/svg%3E");
        }

        .corner-bottom-left {
            bottom: 30px;
            left: 30px;
            transform: scaleY(-1);
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20,20 L80,20 L80,30 Q70,30 70,40 L40,40 Q40,30 30,30 L20,30 Z' fill='%23d4af37'/%3E%3Cpath d='M20,20 L20,80 L30,80 Q30,70 40,70 L40,40 Q30,40 30,30 Z' fill='%23d4af37'/%3E%3C/svg%3E");
        }

        .corner-bottom-right {
            bottom: 30px;
            right: 30px;
            transform: scale(-1);
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20,20 L80,20 L80,30 Q70,30 70,40 L40,40 Q40,30 30,30 L20,30 Z' fill='%23d4af37'/%3E%3Cpath d='M20,20 L20,80 L30,80 Q30,70 40,70 L40,40 Q30,40 30,30 Z' fill='%23d4af37'/%3E%3C/svg%3E");
        }

        /* Gold lines between sections */
        .section-divider {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg,
                    transparent 10%,
                    var(--gold) 30%,
                    var(--gold) 70%,
                    transparent 90%);
            margin: 60px 0;
        }

        /* Decorative frame for sections */
        .decorative-frame {
            position: relative;
            padding: 60px 40px;
        }

        .decorative-frame:before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 1px solid var(--gold-light);
            pointer-events: none;
        }

        .decorative-frame:after {
            content: '';
            position: absolute;
            top: 40px;
            left: 40px;
            right: 40px;
            bottom: 40px;
            border: 1px solid var(--gold-light);
            pointer-events: none;
        }

        /* Enhanced gold line */
        .gold-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            margin: 40px auto;
            max-width: 300px;
            position: relative;
        }

        .gold-line:before,
        .gold-line:after {
            content: '❖';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold);
            font-size: 12px;
        }

        .gold-line:before {
            left: -20px;
        }

        .gold-line:after {
            right: -20px;
        }

        .ornament {
            text-align: center;
            color: var(--gold);
            font-size: 24px;
            margin: 20px 0;
        }

        /* Enhanced ornament for section titles */
        .section-ornament {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 30px 0;
        }

        .section-ornament:before,
        .section-ornament:after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .section-ornament span {
            margin: 0 20px;
            color: var(--gold);
            font-size: 20px;
        }

        /* Decorative border for cards */
        .card-decoration {
            position: relative;
        }

        .card-decoration:before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border: 1px solid var(--gold-light);
            z-index: -1;
        }

        /* Floral border elements */
        .floral-border {
            position: absolute;
            width: 50px;
            height: 50px;
            opacity: 0.3;
        }

        .floral-tl {
            top: 10px;
            left: 10px;
            background-image: url("data:image/svg+xml,%3Csvg width='50' height='50' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10,25 Q15,10 25,10 Q35,10 40,25 Q35,40 25,40 Q15,40 10,25' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3C/svg%3E");
        }

        .floral-tr {
            top: 10px;
            right: 10px;
            transform: scaleX(-1);
            background-image: url("data:image/svg+xml,%3Csvg width='50' height='50' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10,25 Q15,10 25,10 Q35,10 40,25 Q35,40 25,40 Q15,40 10,25' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3C/svg%3E");
        }

        .floral-bl {
            bottom: 10px;
            left: 10px;
            transform: scaleY(-1);
            background-image: url("data:image/svg+xml,%3Csvg width='50' height='50' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10,25 Q15,10 25,10 Q35,10 40,25 Q35,40 25,40 Q15,40 10,25' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3C/svg%3E");
        }

        .floral-br {
            bottom: 10px;
            right: 10px;
            transform: scale(-1);
            background-image: url("data:image/svg+xml,%3Csvg width='50' height='50' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10,25 Q15,10 25,10 Q35,10 40,25 Q35,40 25,40 Q15,40 10,25' fill='none' stroke='%23d4af37' stroke-width='0.5'/%3E%3C/svg%3E");
        }

        /* Splash Screen */
        .splash {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: all 0.8s ease;
        }

        .splash.hide {
            opacity: 0;
            visibility: hidden;
        }

        .splash-content {
            text-align: center;
            animation: fadeIn 1s ease;
            position: relative;
            padding: 40px;
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

        .splash-ornament {
            font-size: 60px;
            color: var(--gold);
            margin-bottom: 30px;
        }

        .splash h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 60px;
            font-weight: 300;
            color: var(--text);
            margin: 20px 0;
            letter-spacing: 3px;
        }

        .splash p {
            font-size: 13px;
            letter-spacing: 4px;
            color: var(--gray);
            margin-bottom: 40px;
        }

        .splash-btn {
            padding: 15px 50px;
            background: var(--white);
            color: var(--text);
            border: 2px solid var(--gold);
            border-radius: 0;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 3px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .splash-btn:hover {
            background: var(--gold);
            color: var(--white);
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
            width: 50px;
            height: 50px;
            background: var(--white);
            border: 1px solid var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .music-btn:hover {
            background: var(--gold);
            color: var(--white);
        }

        .music-btn.playing {
            animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Container */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
        }

        /* Section */
        section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 80px 20px;
            position: relative;
        }

        /* Hero */
        .hero {
            text-align: center;
            background: var(--white);
            padding: 80px 40px;
            position: relative;
            overflow: hidden;
        }

        .hero:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .hero:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .hero-label {
            font-size: 12px;
            letter-spacing: 5px;
            color: var(--gold);
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }

        .hero-label:before,
        .hero-label:after {
            content: '❖';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold);
            font-size: 8px;
        }

        .hero-label:before {
            left: -20px;
        }

        .hero-label:after {
            right: -20px;
        }

        .hero-names {
            font-family: 'Cormorant Garamond', serif;
            font-size: 80px;
            font-weight: 300;
            color: var(--text);
            line-height: 1.2;
            letter-spacing: 2px;
            position: relative;
        }

        .hero-ampersand {
            font-size: 50px;
            color: var(--gold);
            font-style: italic;
            display: block;
            margin: 20px 0;
            font-family: 'Cormorant Garamond', serif;
        }

        .hero-date {
            font-size: 16px;
            color: var(--text);
            margin: 30px 0;
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
            padding: 10px 30px;
            border: 1px solid var(--gold-light);
        }

        .hero-quote {
            font-size: 16px;
            font-style: italic;
            color: var(--gray);
            max-width: 600px;
            margin: 30px auto;
            line-height: 1.8;
            position: relative;
            padding: 20px;
        }

        .hero-quote:before {
            content: '❝';
            position: absolute;
            top: -10px;
            left: -10px;
            color: var(--gold);
            font-size: 24px;
        }

        .hero-quote:after {
            content: '❞';
            position: absolute;
            bottom: -10px;
            right: -10px;
            color: var(--gold);
            font-size: 24px;
        }

        /* Section Title */
        .section-title {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .section-label {
            font-size: 11px;
            letter-spacing: 4px;
            color: var(--gold);
            margin-bottom: 15px;
        }

        .section-heading {
            font-family: 'Cormorant Garamond', serif;
            font-size: 48px;
            font-weight: 300;
            color: var(--text);
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
            padding: 0 20px;
        }

        /* Couple Cards */
        .couple-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 60px;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }

        .couple-card {
            background: var(--white);
            padding: 50px 30px;
            text-align: center;
            border: 1px solid var(--gold-light);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .couple-card:hover {
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.15);
            transform: translateY(-5px);
        }

        .couple-img {
            width: 150px;
            height: 150px;
            margin: 0 auto 30px;
            border-radius: 50%;
            background: var(--gold-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            border: 3px solid var(--gold);
            position: relative;
        }

        .couple-img img {
            width: 100%;
            border-radius: 50%;
        }

        .couple-img:before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border-radius: 50%;
            border: 1px solid var(--gold-light);
        }

        .couple-card h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            font-weight: 400;
            color: var(--text);
            margin-bottom: 10px;
        }

        .couple-card .role {
            font-size: 11px;
            letter-spacing: 3px;
            color: var(--gold);
            margin: 20px 0;
        }

        .couple-card .parents {
            font-size: 14px;
            color: var(--gray);
            line-height: 1.8;
            margin-top: 20px;
        }

        .couple-card .parents strong {
            color: var(--text);
        }

        /* Countdown */
        .countdown-box {
            background: var(--white);
            padding: 50px;
            text-align: center;
            position: relative;
            max-width: 800px;
            margin: 60px auto 0;
        }

        .countdown-box h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            font-weight: 300;
            margin-bottom: 40px;
            color: var(--text);
        }

        .countdown-grid {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .time-item {
            min-width: 100px;
            position: relative;
            padding: 20px;
        }

        .time-item:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 1px solid var(--gold-light);
        }

        .time-value {
            font-family: 'Cormorant Garamond', serif;
            font-size: 48px;
            font-weight: 300;
            color: var(--gold);
            display: block;
        }

        .time-label {
            font-size: 11px;
            letter-spacing: 2px;
            color: var(--gray);
            margin-top: 10px;
        }

        /* Event Cards */
        .event-grid {
            max-width: 700px;
            margin: 0 auto;
            display: grid;
            gap: 40px;
        }

        .event-card {
            background: var(--white);
            padding: 40px;
            border: 1px solid var(--gold-light);
            text-align: center;
            position: relative;
        }

        .event-icon {
            font-size: 50px;
            margin-bottom: 20px;
            color: var(--gold);
        }

        .event-card h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            font-weight: 400;
            color: var(--text);
            margin-bottom: 25px;
        }

        .event-detail {
            font-size: 15px;
            color: var(--gray);
            margin: 12px 0;
        }

        .event-detail strong {
            color: var(--text);
        }

        .dresscode {
            background: var(--gold-light);
            padding: 25px;
            margin-top: 40px;
            text-align: center;
            position: relative;
        }

        .dresscode strong {
            color: var(--text);
            font-size: 16px;
        }

        .map-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 40px;
            background: var(--white);
            color: var(--text);
            border: 1px solid var(--gold);
            text-decoration: none;
            font-size: 12px;
            letter-spacing: 2px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .map-btn:hover {
            background: var(--gold);
            color: var(--white);
        }

        /* Gallery */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 900px;
            margin: 0 auto;
        }

        .gallery-item {
            aspect-ratio: 1;
            background: var(--gold-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.3s;
            border: 1px solid var(--gold);
            position: relative;
        }

        .gallery-item:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.2);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Video Section */
        .video-container {
            max-width: 800px;
            margin: 60px auto 0;
            text-align: center;
            position: relative;
        }

        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border: 2px solid var(--gold);
            margin-top: 30px;
            border-radius: 10px;
            background: var(--gold-light);
        }

        .video-wrapper iframe,
        .video-wrapper video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* SPECIAL MESSAGE SECTION */
        .special-message-section {
            background: var(--white);
            padding: 80px 20px;
        }

        .special-message-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .special-message {
            font-size: 18px;
            line-height: 1.8;
            color: var(--gray);
            margin-bottom: 30px;
            font-style: italic;
            position: relative;
            padding: 0 20px;
        }

        .special-message:before {
            content: '❝';
            position: absolute;
            top: -20px;
            left: 0;
            color: var(--gold);
            font-size: 40px;
        }

        .special-message:after {
            content: '❞';
            position: absolute;
            bottom: -30px;
            right: 0;
            color: var(--gold);
            font-size: 40px;
        }

        /* RSVP Section (DIPERBESAR) */
        .rsvp-section {
            background: var(--cream);
        }

        .rsvp-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .rsvp-form {
            background: var(--white);
            padding: 60px;
            border: 1px solid var(--gold-light);
            position: relative;
        }

        .form-group {
            margin-bottom: 40px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            letter-spacing: 3px;
            color: var(--text);
            margin-bottom: 15px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 20px;
            border: 2px solid var(--gold-light);
            background: var(--cream);
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            transition: all 0.3s;
            border-radius: 5px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 20px;
            background: var(--white);
            color: var(--text);
            border: 2px solid var(--gold);
            font-size: 14px;
            letter-spacing: 4px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            border-radius: 5px;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background: var(--gold);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
        }

        .success-msg {
            display: none;
            background: var(--gold-light);
            padding: 60px;
            text-align: center;
            margin-top: 30px;
            border-radius: 10px;
        }

        .success-msg.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .success-msg .icon {
            font-size: 70px;
            margin-bottom: 30px;
            color: var(--gold);
        }

        .success-msg h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 400;
            color: var(--text);
            margin-bottom: 15px;
        }

        /* BLESSINGS/COMMENT SECTION (DIPERBESAR) */
        .blessings-section {
            background: var(--white);
            padding: 80px 20px;
        }

        .blessings-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .blessings-form {
            background: var(--cream);
            padding: 60px;
            border: 1px solid var(--gold-light);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.1);
            position: relative;
        }

        .blessings-form textarea {
            width: 100%;
            padding: 25px;
            border: 2px solid var(--gold-light);
            background: var(--white);
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            min-height: 200px;
            resize: vertical;
            transition: all 0.3s;
            border-radius: 5px;
            line-height: 1.6;
        }

        .blessings-form textarea:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .blessings-btn {
            width: 100%;
            padding: 20px;
            background: var(--white);
            color: var(--text);
            border: 2px solid var(--gold);
            font-size: 14px;
            letter-spacing: 4px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            border-radius: 5px;
            margin-top: 30px;
        }

        .blessings-btn:hover {
            background: var(--gold);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
        }

        .blessings-success {
            display: none;
            background: var(--gold-light);
            padding: 60px;
            text-align: center;
            margin-top: 30px;
            border-radius: 10px;
        }

        .blessings-success.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .blessings-success .icon {
            font-size: 70px;
            margin-bottom: 30px;
            color: var(--gold);
        }

        .blessings-success h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 400;
            color: var(--text);
            margin-bottom: 15px;
        }

        /* Comments Display Section */
        .comments-section {
            background: var(--cream);
            padding: 80px 20px;
        }

        .comments-container {
            max-width: 900px;
            margin: 40px auto 0;
        }

        .comment-list {
            display: grid;
            gap: 30px;
            margin-top: 40px;
        }

        .comment-item {
            background: var(--white);
            padding: 40px;
            border: 1px solid var(--gold-light);
            border-radius: 10px;
            position: relative;
            animation: fadeIn 0.5s ease;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--gold-light);
        }

        .comment-author {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            color: var(--text);
            font-weight: 600;
        }

        .comment-date {
            font-size: 12px;
            color: var(--gray);
            letter-spacing: 1px;
        }

        .comment-text {
            font-size: 16px;
            line-height: 1.8;
            color: var(--gray);
            margin-top: 15px;
        }

        .loading-comments {
            text-align: center;
            padding: 60px;
            color: var(--gray);
            font-style: italic;
        }

        .no-comments {
            text-align: center;
            padding: 60px;
            color: var(--gray);
            font-style: italic;
        }

        /* Footer */
        footer {
            background: var(--white);
            padding: 60px 20px;
            text-align: center;
            position: relative;
        }

        footer .ornament {
            font-size: 40px;
            color: var(--gold);
            margin-bottom: 20px;
        }

        footer h4 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 300;
            color: var(--text);
            margin: 20px 0;
        }

        footer p {
            font-size: 13px;
            color: var(--gray);
            letter-spacing: 2px;
        }

        /* Responsive */
        @media (max-width: 768px) {

            .background-ornaments,
            .corner-ornament {
                display: none;
            }

            .ornament-left,
            .ornament-right {
                display: none;
            }

            .hero-names {
                font-size: 50px;
            }

            .hero-ampersand {
                font-size: 35px;
            }

            .section-heading {
                font-size: 36px;
            }

            .music-btn {
                width: 45px;
                height: 45px;
                top: 20px;
                right: 20px;
            }

            .rsvp-form,
            .blessings-form {
                padding: 40px 25px;
            }

            .couple-card {
                padding: 40px 25px;
            }

            .event-card {
                padding: 40px 25px;
            }

            .special-message-section {
                padding: 60px 20px;
            }

            .time-item {
                min-width: 80px;
                padding: 15px;
            }

            .time-value {
                font-size: 36px;
            }

            .form-group input,
            .form-group select,
            .blessings-form textarea {
                padding: 18px;
                font-size: 15px;
            }

            .submit-btn,
            .blessings-btn {
                padding: 18px;
                font-size: 13px;
            }

            .success-msg,
            .blessings-success {
                padding: 50px 25px;
            }

            .comment-item {
                padding: 30px 25px;
            }
        }
    </style>
</head>

<body>
    <!-- Background Ornaments -->
    <div class="background-ornaments">
        <div class="ornament-left"></div>
        <div class="ornament-right"></div>
        <div class="corner-ornament corner-top-left"></div>
        <div class="corner-ornament corner-top-right"></div>
        <div class="corner-ornament corner-bottom-left"></div>
        <div class="corner-ornament corner-bottom-right"></div>
    </div>

    <!-- Splash Screen -->
    <div class="splash" id="splash">
        <div class="splash-content decorative-frame">
            <div class="floral-border floral-tl"></div>
            <div class="floral-border floral-tr"></div>
            <div class="floral-border floral-bl"></div>
            <div class="floral-border floral-br"></div>

            <div class="splash-ornament">❖</div>
            <p>THE WEDDING OF</p>
            <h1><?php echo ($nama_panggilan); ?></h1>
            <div class="gold-line"></div>
            <button class="splash-btn" onclick="openInvitation()">OPEN INVITATION</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main" id="main">
        <!-- Music Button -->
        <!-- <div class="music-btn" id="musicBtn" onclick="toggleMusic()">
            <span style="font-size: 18px; color: var(--gold);">♪</span>
        </div> -->

        <!-- Hidden audio: ganti src dengan file musik Anda -->
        <!-- <audio id="bgMusic" src="../audio/lumiere.mp3" loop crossorigin="anonymous"></audio> -->

        <!-- Hero Section -->
        <section id="home" class="hero decorative-frame">
            <div class="floral-border floral-tl"></div>
            <div class="floral-border floral-tr"></div>
            <div class="floral-border floral-bl"></div>
            <div class="floral-border floral-br"></div>

            <div>
                <p class="hero-label">THE WEDDING OF</p>
                <h1 class="hero-names">
                    <?php echo htmlspecialchars($nama_panggilan_pria); ?>
                    <span class="hero-ampersand">&</span>
                    <?php echo htmlspecialchars($nama_panggilan_wanita); ?>
                </h1>
                <div class="section-ornament">
                    <span>❖</span>
                </div>
                <p class="hero-date"><?php echo strtoupper(date('l, d F Y', strtotime($tanggal))); ?></p>
                <p class="hero-quote">
                    "Two souls with but a single thought, two hearts that beat as one"
                </p>
            </div>
        </section>

        <!-- Section Divider -->
        <div class="section-divider"></div>

        <!-- Couple Section -->
        <section id="couple" style="background: var(--cream);">
            <div class="container">
                <div class="section-title">
                    <p class="section-label">BRIDE & GROOM</p>
                    <h2 class="section-heading">Our Story</h2>
                    <div class="section-ornament">
                        <span>❖</span>
                    </div>
                </div>

                <div class="couple-grid">
                    <div class="couple-card card-decoration">
                        <div class="floral-border floral-tl"></div>
                        <div class="floral-border floral-br"></div>
                        <div class="couple-img">
                            <?php if (!empty($wedding_data['foto_mempelai_pria_url'])): ?>
                                <img src="<?php echo htmlspecialchars($wedding_data['foto_mempelai_pria_url']); ?>"
                                    alt="<?php echo htmlspecialchars($wedding_data['nama_mempelai_pria']); ?>"
                                    class="avatar-img">
                            <?php else: ?>
                                <div class="avatar-placeholder">👑</div>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($wedding_data['nama_mempelai_pria'] ?? $nama_panggilan_pria); ?></h3>
                        <p class="role">THE GROOM</p>
                        <div class="gold-line" style="max-width: 100px;"></div>
                        <p class="parents">
                            Son of<br>
                            <strong><?php echo htmlspecialchars($wedding_data['ayah_pria'] ?? 'Mr. Robert'); ?> & <?php echo htmlspecialchars($wedding_data['ibu_pria'] ?? 'Mrs. Patricia'); ?></strong>
                        </p>
                    </div>

                    <div class="couple-card card-decoration">
                        <div class="floral-border floral-tr"></div>
                        <div class="floral-border floral-bl"></div>
                        <div class="couple-img">
                            <?php if (!empty($wedding_data['foto_mempelai_wanita_url'])): ?>
                                <img src="<?php echo htmlspecialchars($wedding_data['foto_mempelai_wanita_url']); ?>"
                                    alt="<?php echo htmlspecialchars($wedding_data['nama_mempelai_wanita']); ?>"
                                    class="avatar-img">
                            <?php else: ?>
                                <div class="avatar-placeholder">✨</div>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($wedding_data['nama_mempelai_wanita'] ?? $nama_panggilan_wanita); ?></h3>
                        <p class="role">THE BRIDE</p>
                        <div class="gold-line" style="max-width: 100px;"></div>
                        <p class="parents">
                            Daughter of<br>
                            <strong><?php echo htmlspecialchars($wedding_data['ayah_wanita'] ?? 'Mr. Thomas'); ?> & <?php echo htmlspecialchars($wedding_data['ibu_wanita'] ?? 'Mrs. Jennifer'); ?></strong>
                        </p>
                    </div>
                </div>

                <div class="countdown-box card-decoration">
                    <div class="floral-border floral-tl"></div>
                    <div class="floral-border floral-tr"></div>
                    <div class="floral-border floral-bl"></div>
                    <div class="floral-border floral-br"></div>

                    <h3>Save The Date</h3>
                    <div class="section-ornament">
                        <span>❖</span>
                    </div>
                    <div class="countdown-grid">
                        <div class="time-item">
                            <span class="time-value" id="days">0</span>
                            <span class="time-label">DAYS</span>
                        </div>
                        <div class="time-item">
                            <span class="time-value" id="hours">0</span>
                            <span class="time-label">HOURS</span>
                        </div>
                        <div class="time-item">
                            <span class="time-value" id="minutes">0</span>
                            <span class="time-label">MINUTES</span>
                        </div>
                        <div class="time-item">
                            <span class="time-value" id="seconds">0</span>
                            <span class="time-label">SECONDS</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Divider -->
        <div class="section-divider"></div>

        <!-- Event Section -->
        <section id="event">
            <div class="container">
                <div class="section-title">
                    <p class="section-label">EVENT DETAILS</p>
                    <h2 class="section-heading">When & Where</h2>
                    <div class="section-ornament">
                        <span>❖</span>
                    </div>
                </div>

                <div class="event-grid">
                    <div class="event-card card-decoration">
                        <div class="floral-border floral-tl"></div>
                        <div class="event-icon">⛪</div>
                        <h3>Holy Matrimony</h3>
                        <div class="gold-line" style="max-width: 150px;"></div>
                        <p class="event-detail"><?php echo htmlspecialchars($wedding_data['tanggal_acara'] ?? ''); ?></p>
                        <p class="event-detail"><?php echo htmlspecialchars($wedding_data['jam_acara_pemberkatan'] ?? '13.00 - 14.00 WIB'); ?></p>
                        <p class="event-detail">
                            <strong><?php echo htmlspecialchars($wedding_data['lokasi_acara_pemberkatan'] ?? 'Grand Cathedral'); ?></strong><br>
                        </p>
                    </div>

                    <?php if (!empty($wedding_data['jam_acara_resepsi'])): ?>
                        <div class="event-card card-decoration">
                            <div class="floral-border floral-tr"></div>
                            <div class="event-icon">💒</div>
                            <h3>Wedding Reception</h3>
                            <div class="gold-line" style="max-width: 150px;"></div>
                            <p class="event-detail"><?php echo htmlspecialchars($wedding_data['tanggal_acara'] ?? ''); ?></p>
                            <p class="event-detail"><?php echo htmlspecialchars($wedding_data['jam_acara_resepsi']); ?></p>
                            <p class="event-detail">
                                <strong><?php echo htmlspecialchars($wedding_data['lokasi_acara_resepsi'] ?? 'The Grand Ballroom'); ?></strong><br>
                            </p>
                            <?php if (!empty($wedding_data['google_maps'])): ?>
                                <a href="<?php echo htmlspecialchars($wedding_data['google_maps']); ?>" target="_blank" class="map-btn">VIEW MAP</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="dresscode card-decoration">
                    <div class="floral-border floral-bl"></div>
                    <strong>DRESS CODE</strong><br>
                    <span style="font-size: 14px; color: var(--gray);"><?php echo htmlspecialchars($wedding_data['dress_code'] ?? 'Formal Attire'); ?></span>
                </div>
            </div>
        </section>

        <!-- Section Divider -->
        <div class="section-divider"></div>

        <!-- Gallery & Video Section -->
        <section id="gallery" style="background: var(--cream);">
            <div class="container">
                <div class="section-title">
                    <p class="section-label">OUR MEMORIES</p>
                    <h2 class="section-heading">Gallery</h2>
                    <div class="section-ornament">
                        <span>❖</span>
                    </div>
                </div>

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


            </div>
        </section>

        <!-- Section Divider -->
        <div class="section-divider"></div>

        <!-- Special Message Section -->
        <section id="special-message" class="special-message-section">
            <div class="container">
                <div class="special-message-content decorative-frame">
                    <div class="floral-border floral-tl"></div>
                    <div class="floral-border floral-tr"></div>
                    <div class="floral-border floral-bl"></div>
                    <div class="floral-border floral-br"></div>

                    <div class="section-title">
                        <p class="section-label">A SPECIAL NOTE</p>
                        <h2 class="section-heading">Your Presence is Our Gift</h2>
                        <div class="section-ornament">
                            <span>❖</span>
                        </div>
                    </div>

                    <p class="special-message">
                        The greatest gift you could give us is your presence at our wedding.
                        Your love, support, and blessings are all we could ever ask for as we
                        embark on this beautiful journey together. We are truly grateful for
                        your friendship and look forward to celebrating this special day with you.
                    </p>
                </div>
            </div>
        </section>

        <!-- Section Divider -->
        <div class="section-divider"></div>

        <!-- RSVP Section (DIPERBESAR) -->
        <section id="rsvp" class="rsvp-section">
            <div class="container">
                <div class="section-title">
                    <p class="section-label">CONFIRM ATTENDANCE</p>
                    <h2 class="section-heading">RSVP</h2>
                    <div class="section-ornament">
                        <span>❖</span>
                    </div>
                </div>

                <div class="rsvp-container">
                    <form id="rsvpForm" method="POST" class="rsvp-form card-decoration">
                        <input type="hidden" name="wedding_id" value="<?php echo $wedding_data['wedding_id']; ?>">
                        <div class="floral-border floral-tl"></div>
                        <div class="floral-border floral-br"></div>

                        <div class="form-group">
                            <label>FULL NAME *</label>
                            <input type="text" name="nama_tamu" placeholder="Enter your full name" required>
                        </div>

                        <div class="form-group">
                            <label>WILL YOU ATTEND? *</label>
                            <select name="status" required>
                                <option value="">Select your attendance</option>
                                <option value="hadir">Yes, I will attend with pleasure</option>
                                <option value="tidak_hadir">Regretfully, I cannot attend</option>
                                <option value="belum_pasti">I'm not sure yet</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>NUMBER OF GUESTS *</label>
                            <select name="jumlah_tamu" required>
                                <option value="1">1 Person (Myself)</option>
                                <option value="2">2 People</option>
                                <option value="3">3 People</option>
                                <option value="4">4 People</option>
                                <option value="5">5+ People</option>
                            </select>
                        </div>


                        <button type="submit" class="submit-btn">SUBMIT RSVP</button>
                    </form>

                    <div class="success-msg" id="successMsg">
                        <div class="icon">✓</div>
                        <h3>Thank You!</h3>
                        <p style="color: var(--gray); margin-top: 10px; font-size: 16px;">Your attendance has been confirmed. We look forward to celebrating with you!</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Divider -->
        <div class="section-divider"></div>

        <!-- BLESSINGS/COMMENT SECTION (DIPERBESAR) -->
        <section id="blessings" class="blessings-section">
            <div class="container">
                <div class="section-title">
                    <p class="section-label">SEND YOUR BLESSINGS</p>
                    <h2 class="section-heading">Words of Love</h2>
                    <div class="section-ornament">
                        <span>❖</span>
                    </div>
                </div>

                <div class="blessings-container">
                    <form id="commentForm" method="POST" class="blessings-form card-decoration">
                        <input type="hidden" name="wedding_id" value="<?php echo $wedding_data['wedding_id']; ?>">
                        <div class="floral-border floral-tl"></div>
                        <div class="floral-border floral-br"></div>

                        <div class="form-group">
                            <label>YOUR NAME *</label>
                            <input type="text" name="nama_tamu" placeholder="Enter your name" required>
                        </div>

                        <div class="form-group">
                            <label>YOUR MESSAGE *</label>
                            <textarea name="komentar" placeholder="Write your heartfelt wishes, blessings, congratulations, or advice for the happy couple..." required></textarea>
                        </div>

                        <button type="submit" class="blessings-btn">SEND YOUR BLESSING</button>
                    </form>

                    <div class="blessings-success" id="blessingsSuccess">
                        <div class="icon">♥</div>
                        <h3>Thank You For Your Blessing!</h3>
                        <p style="color: var(--gray); margin-top: 10px; font-size: 16px;">Your beautiful message has been received. <?php echo htmlspecialchars($nama_panggilan); ?> appreciate your love and support.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Divider -->
        <div class="section-divider"></div>

        <!-- COMMENTS DISPLAY SECTION -->
        <section id="comments" class="comments-section">
            <div class="container">
                <div class="section-title">
                    <p class="section-label">BLESSINGS FROM LOVED ONES</p>
                    <h2 class="section-heading">Messages of Love</h2>
                    <div class="section-ornament">
                        <span>❖</span>
                    </div>
                </div>

                <div class="comments-container">
                    <div class="comment-list" id="commentsList">
                        <div class="loading-comments">
                            <p>Loading messages...</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="decorative-frame">
            <div class="floral-border floral-tl"></div>
            <div class="floral-border floral-tr"></div>
            <div class="floral-border floral-bl"></div>
            <div class="floral-border floral-br"></div>

            <div class="ornament">❖</div>
            <p style="margin: 20px 0;">Thank you for celebrating with us</p>
            <h4><?php echo ($nama_panggilan); ?></h4>
            <div class="gold-line" style="max-width: 200px;"></div>
            <p><?php echo date('d • m • Y', strtotime($tanggal)); ?></p>
        </footer>
    </div>

    <script>
        // Constants
        const WEDDING_ID = <?php echo $wedding_data['wedding_id']; ?>;
        const WEDDING_DATE = new Date('<?php echo date('Y-m-d H:i:s', strtotime($tanggal . ' ' . $jam)); ?>');

        let musicPlaying = false;

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
            musicPlaying = !musicPlaying;
            const btn = document.getElementById('musicBtn');
            if (musicPlaying) {
                btn.classList.add('playing');
            } else {
                btn.classList.remove('playing');
            }
        }

        // Countdown timer
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

                document.getElementById('days').textContent = days;
                document.getElementById('hours').textContent = hours;
                document.getElementById('minutes').textContent = minutes;
                document.getElementById('seconds').textContent = seconds;
            } else {
                // Jika hari H sudah lewat
                document.querySelector('.countdown-box h3').textContent = 'Thank You For Celebrating With Us!';
                document.querySelector('.countdown-grid').innerHTML = `
                    <div style="font-size: 18px; color: var(--gold); padding: 20px;">
                        The celebration has begun! Thank you for being part of our special day.
                    </div>
                `;
            }
        }

        // Load comments from API
        async function loadComments() {
            const commentsList = document.getElementById('commentsList');
            if (!commentsList) return;

            commentsList.innerHTML = '<div class="loading-comments"><p>Loading messages...</p></div>';

            try {
                const response = await fetch(`api/get_comments.php?wedding_id=${WEDDING_ID}&_=${Date.now()}`);
                const result = await response.json();

                if (result.success && result.comments && result.comments.length > 0) {
                    let html = '';
                    result.comments.forEach((comment, index) => {
                        html += `
                            <div class="comment-item" style="animation-delay: ${index * 0.1}s">
                                <div class="comment-header">
                                    <div class="comment-author">${escapeHtml(comment.nama)}</div>
                                    <div class="comment-date">${escapeHtml(comment.tanggal)}</div>
                                </div>
                                <div class="comment-text">${escapeHtml(comment.komentar)}</div>
                            </div>
                        `;
                    });
                    commentsList.innerHTML = html;
                } else {
                    commentsList.innerHTML = '<div class="no-comments"><p>No messages yet. Be the first to send your blessings!</p></div>';
                }
            } catch (error) {
                commentsList.innerHTML = '<div class="no-comments"><p>Unable to load messages. Please try again later.</p></div>';
            }
        }

        // Handle RSVP form submission
        document.getElementById('rsvpForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('.submit-btn');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = 'SUBMITTING...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('api/save_rsvp.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    this.style.display = 'none';
                    document.getElementById('successMsg').classList.add('show');

                    // Reset form after 4 seconds
                    setTimeout(() => {
                        document.getElementById('successMsg').classList.remove('show');
                        this.style.display = 'block';
                        this.reset();
                        this.querySelector('[name="wedding_id"]').value = WEDDING_ID;
                    }, 4000);
                } else {
                    alert(result.message || 'Failed to submit RSVP');
                }
            } catch (error) {
                alert('Connection error. Please try again.');
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });

        // Handle comment form submission
        document.getElementById('commentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('.blessings-btn');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = 'SENDING...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('api/save_comment.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    this.style.display = 'none';
                    document.getElementById('blessingsSuccess').classList.add('show');

                    // Reset form and reload comments
                    setTimeout(() => {
                        document.getElementById('blessingsSuccess').classList.remove('show');
                        this.style.display = 'block';
                        this.reset();
                        this.querySelector('[name="wedding_id"]').value = WEDDING_ID;
                        loadComments();
                    }, 3000);
                } else {
                    alert(result.message || 'Failed to send blessing');
                }
            } catch (error) {
                alert('Connection error. Please try again.');
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });

        // Escape HTML to prevent XSS
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

        // Auto open invitation after 5 seconds
        setTimeout(() => {
            if (!document.getElementById('splash').classList.contains('hide')) {
                openInvitation();
            }
        }, 5000);
    </script>
</body>

</html>