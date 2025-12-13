<?php
session_start();
require_once '../config.php'; // Gunakan config.php

// Cek login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Dapatkan koneksi
$conn = getConnection();
if (!$conn) {
    die("Database connection failed");
}

$wedding_id = $_SESSION['wedding_id'];

// Logout handler
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Ambil data undangan
$query_wedding = "SELECT w.*, t.nama_template FROM weddings w 
                  JOIN templates t ON w.template_id = t.id 
                  WHERE w.id = ?";
$stmt = $conn->prepare($query_wedding);
$stmt->bind_param("i", $wedding_id);
$stmt->execute();
$wedding_result = $stmt->get_result();

if ($wedding_result->num_rows === 0) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$wedding = $wedding_result->fetch_assoc();



$tanggal = $wedding['tanggal_acara']; // "Jumat, 09 Januari 2026"
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

$stmt->close();

// Ambil data RSVP dengan filter
$search_rsvp = $_GET['search_rsvp'] ?? '';
$status_filter = $_GET['status_filter'] ?? 'all';

$query_rsvp = "SELECT * FROM rsvp WHERE wedding_id = ? ";
$params = [$wedding_id];
$types = "i";

if (!empty($search_rsvp)) {
    $query_rsvp .= "AND nama_tamu LIKE ? ";
    $params[] = "%$search_rsvp%";
    $types .= "s";
}

if ($status_filter !== 'all') {
    $query_rsvp .= "AND status = ? ";
    $params[] = $status_filter;
    $types .= "s";
}

$query_rsvp .= "ORDER BY created_at DESC";
$stmt_rsvp = $conn->prepare($query_rsvp);

if (count($params) > 1) {
    $stmt_rsvp->bind_param($types, ...$params);
} else {
    $stmt_rsvp->bind_param($types, $params[0]);
}

$stmt_rsvp->execute();
$rsvps = $stmt_rsvp->get_result();

// Ambil data komentar
$search_comment = $_GET['search_comment'] ?? '';
$comment_filter = $_GET['comment_filter'] ?? 'all';

$query_comments = "SELECT * FROM comments WHERE wedding_id = ? ";
$params_comments = [$wedding_id];
$types_comments = "i";

if (!empty($search_comment)) {
    $query_comments .= "AND nama_tamu LIKE ? ";
    $params_comments[] = "%$search_comment%";
    $types_comments .= "s";
}

if ($comment_filter !== 'all') {
    $query_comments .= "AND is_approved = ? ";
    $params_comments[] = ($comment_filter == 'approved' ? 1 : 0);
    $types_comments .= "i";
}

$query_comments .= "ORDER BY created_at DESC";
$stmt_comments = $conn->prepare($query_comments);

if (count($params_comments) > 1) {
    $stmt_comments->bind_param($types_comments, ...$params_comments);
} else {
    $stmt_comments->bind_param($types_comments, $params_comments[0]);
}

$stmt_comments->execute();
$comments = $stmt_comments->get_result();

// Action untuk komentar
if (isset($_GET['action']) && isset($_GET['id'])) {
    $comment_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $stmt = $conn->prepare("UPDATE comments SET is_approved = 1 WHERE id = ? AND wedding_id = ?");
        $stmt->bind_param("ii", $comment_id, $wedding_id);
        $stmt->execute();
    } elseif ($action == 'disapprove') {
        $stmt = $conn->prepare("UPDATE comments SET is_approved = 0 WHERE id = ? AND wedding_id = ?");
        $stmt->bind_param("ii", $comment_id, $wedding_id);
        $stmt->execute();
    } elseif ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND wedding_id = ?");
        $stmt->bind_param("ii", $comment_id, $wedding_id);
        $stmt->execute();
    }
    
    $stmt->close();
    
    // Redirect ke halaman yang sama dengan filter yang aktif
    $redirect_url = "dashboard.php?";
    
    if (!empty($search_rsvp)) $redirect_url .= "search_rsvp=" . urlencode($search_rsvp) . "&";
    if (!empty($status_filter) && $status_filter != 'all') $redirect_url .= "status_filter=" . urlencode($status_filter) . "&";
    if (!empty($search_comment)) $redirect_url .= "search_comment=" . urlencode($search_comment) . "&";
    if (!empty($comment_filter) && $comment_filter != 'all') $redirect_url .= "comment_filter=" . urlencode($comment_filter) . "&";
    
    header("Location: " . rtrim($redirect_url, "&"));
    exit();
}

// Hitung statistik
$stats = [];

// Statistik RSVP
$stats_query = "SELECT 
                COUNT(*) as total_rsvp,
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'tidak_hadir' THEN 1 ELSE 0 END) as tidak_hadir,
                SUM(CASE WHEN status = 'belum_pasti' THEN 1 ELSE 0 END) as belum_pasti,
                SUM(jumlah_tamu) as total_tamu_hadir
              FROM rsvp WHERE wedding_id = ?";
$stmt = $conn->prepare($stats_query);
$stmt->bind_param("i", $wedding_id);
$stmt->execute();
$stats_result = $stmt->get_result();
$stats['rsvp'] = $stats_result->fetch_assoc();
$stmt->close();

// Statistik Komentar
$comment_stats = "SELECT 
                   COUNT(*) as total_comments,
                   SUM(CASE WHEN is_approved = 1 THEN 1 ELSE 0 END) as approved,
                   SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending
                 FROM comments WHERE wedding_id = ?";
$stmt = $conn->prepare($comment_stats);
$stmt->bind_param("i", $wedding_id);
$stmt->execute();
$stats_comment_result = $stmt->get_result();
$stats['comments'] = $stats_comment_result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo ($wedding['nama_panggilan']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary: #d04d6d;
            --primary-dark: #9e4057ff;
            --success: #070808ff;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }
        body { 
            background-color: #f8fafc;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        .stat-card.hadir { background: linear-gradient(135deg, #d1fae5, #a7f3d0); }
        .stat-card.tidak-hadir { background: linear-gradient(135deg, #fee2e2, #fecaca); }
        .stat-card.belum-pasti { background: linear-gradient(135deg, #fef3c7, #fde68a); }
        .stat-card.komentar { background: linear-gradient(135deg, #dbeafe, #bfdbfe); }
        .nav-tabs {
            border-bottom: 2px solid #e5e7eb;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #6b7280;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            margin-right: 0.5rem;
            border-radius: 8px 8px 0 0;
        }
        .nav-tabs .nav-link.active {
            color: var(--primary);
            background-color: white;
            border-bottom: 3px solid var(--primary);
        }
        .tab-content {
            background: white;
            border-radius: 0 12px 12px 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .comment-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }
        .comment-card.approved { border-left-color: var(--success); }
        .comment-card.pending { border-left-color: var(--warning); }
        .badge-status {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="bi bi-heart-fill me-2"></i>
                        Dashboard Undangan
                    </h1>
                    <p class="mb-0 opacity-75">
                        <?php echo htmlspecialchars($wedding['nama_mempelai_pria'] . ' & ' . $wedding['nama_mempelai_wanita']); ?>
                    </p>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i>
                        <?php echo ($wedding['nama_panggilan']); ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="<?php echo INVITATION_URL . '?slug=' . $wedding['slug_url']; ?>" target="_blank">
                                <i class="bi bi-eye me-2"></i>Lihat Undangan
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="?logout=1">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Info Undangan -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title mb-1">Informasi Undangan</h5>
                        <p class="card-text text-muted mb-2">
                            <i class="bi bi-calendar-event me-1"></i>
                            <?php echo date('d F Y', strtotime($tanggal)); ?>
                            •
                            <i class="bi bi-geo-alt me-1"></i>
                            <?php echo htmlspecialchars($wedding['lokasi_acara_resepsi'] ?? 'Lokasi belum diatur'); ?>
                        </p>
                        <small class="text-muted">
                            <i class="bi bi-link-45deg me-1"></i>
                            <?php echo BASE_URL . 'invitation/?slug=' . $wedding['slug_url']; ?>
                        </small>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge bg-light text-dark fs-6 p-2">
                            <i class="bi bi-palette me-1"></i>
                            <?php echo htmlspecialchars($wedding['nama_template']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card hadir">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Hadir</h6>
                            <h2 class="mb-0"><?php echo $stats['rsvp']['hadir'] ?? 0; ?></h2>
                            <small class="text-muted">Tamu</small>
                        </div>
                        <i class="bi bi-check-circle-fill fs-2 text-success opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card tidak-hadir">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Tidak Hadir</h6>
                            <h2 class="mb-0"><?php echo $stats['rsvp']['tidak_hadir'] ?? 0; ?></h2>
                            <small class="text-muted">Tamu</small>
                        </div>
                        <i class="bi bi-x-circle-fill fs-2 text-danger opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card belum-pasti">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Belum Pasti</h6>
                            <h2 class="mb-0"><?php echo $stats['rsvp']['belum_pasti'] ?? 0; ?></h2>
                            <small class="text-muted">Tamu</small>
                        </div>
                        <i class="bi bi-question-circle-fill fs-2 text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card komentar">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Komentar</h6>
                            <h2 class="mb-0"><?php echo $stats['comments']['total_comments'] ?? 0; ?></h2>
                            <small class="text-muted">
                                <?php echo ($stats['comments']['pending'] ?? 0); ?> menunggu
                            </small>
                        </div>
                        <i class="bi bi-chat-left-text-fill fs-2 text-info opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Tamu Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 border-end">
                        <h6 class="text-muted mb-1">Total RSVP</h6>
                        <h3 class="mb-0 text-primary"><?php echo $stats['rsvp']['total_rsvp'] ?? 0; ?></h3>
                        <small class="text-muted">Orang</small>
                    </div>
                    <div class="col-md-4 border-end">
                        <h6 class="text-muted mb-1">Estimasi Tamu Hadir</h6>
                        <h3 class="mb-0 text-success"><?php echo $stats['rsvp']['total_tamu_hadir'] ?? 0; ?></h3>
                        <small class="text-muted">Jumlah tamu</small>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Komentar Disetujui</h6>
                        <h3 class="mb-0 text-info"><?php echo $stats['comments']['approved'] ?? 0; ?></h3>
                        <small class="text-muted">Dari total <?php echo $stats['comments']['total_comments'] ?? 0; ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="dashboardTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="rsvp-tab" data-bs-toggle="tab" data-bs-target="#rsvp" type="button">
                    <i class="bi bi-people me-2"></i> Data RSVP
                    <span class="badge bg-primary ms-1"><?php echo $stats['rsvp']['total_rsvp'] ?? 0; ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments" type="button">
                    <i class="bi bi-chat-left-text me-2"></i> Komentar
                    <span class="badge bg-info ms-1"><?php echo $stats['comments']['total_comments'] ?? 0; ?></span>
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="dashboardTabContent">
            <!-- Tab RSVP -->
            <div class="tab-pane fade show active" id="rsvp" role="tabpanel">
                <!-- Filter RSVP -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search_rsvp" class="form-control" 
                               placeholder="Cari nama tamu..." 
                               value="<?php echo htmlspecialchars($search_rsvp); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="status_filter" class="form-select">
                            <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>Semua Status</option>
                            <option value="hadir" <?php echo $status_filter == 'hadir' ? 'selected' : ''; ?>>Hadir</option>
                            <option value="tidak_hadir" <?php echo $status_filter == 'tidak_hadir' ? 'selected' : ''; ?>>Tidak Hadir</option>
                            <option value="belum_pasti" <?php echo $status_filter == 'belum_pasti' ? 'selected' : ''; ?>>Belum Pasti</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </form>

                <!-- Tabel RSVP -->
                <div class="table-responsive">
                    <table class="table table-hover" id="rsvpTable">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="35%">Nama Tamu</th>
                                <th width="20%">Status</th>
                                <th width="20%">Jumlah Tamu</th>
                                <th width="20%">Tanggal RSVP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php while ($row = $rsvps->fetch_assoc()): 
                                $badge_class = [
                                    'hadir' => 'success',
                                    'tidak_hadir' => 'danger',
                                    'belum_pasti' => 'warning'
                                ][$row['status']] ?? 'secondary';
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2 text-muted"></i>
                                        <?php echo htmlspecialchars($row['nama_tamu']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $badge_class; ?> badge-status">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold"><?php echo $row['jumlah_tamu']; ?></span> orang
                                </td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                    <small class="d-block text-muted">
                                        <?php echo date('H:i', strtotime($row['created_at'])); ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <?php if ($rsvps->num_rows == 0): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
                                    <h6 class="text-muted mb-2">Belum ada data RSVP</h6>
                                    <p class="text-muted mb-0">Belum ada tamu yang mengisi RSVP</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Komentar -->
            <div class="tab-pane fade" id="comments" role="tabpanel">
                <!-- Filter Komentar -->
                <form method="GET" class="row g-3 mb-4">
                    <input type="hidden" name="search_rsvp" value="<?php echo htmlspecialchars($search_rsvp); ?>">
                    <input type="hidden" name="status_filter" value="<?php echo htmlspecialchars($status_filter); ?>">
                    
                    <div class="col-md-4">
                        <input type="text" name="search_comment" class="form-control" 
                               placeholder="Cari nama tamu..." 
                               value="<?php echo htmlspecialchars($search_comment); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="comment_filter" class="form-select">
                            <option value="all" <?php echo $comment_filter == 'all' ? 'selected' : ''; ?>>Semua Status</option>
                            <option value="approved" <?php echo $comment_filter == 'approved' ? 'selected' : ''; ?>>Disetujui</option>
                            <option value="pending" <?php echo $comment_filter == 'pending' ? 'selected' : ''; ?>>Menunggu</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </form>

                <!-- Daftar Komentar -->
                <?php if ($comments->num_rows > 0): ?>
                    <div class="row">
                        <?php while ($row = $comments->fetch_assoc()): 
                            $status_class = $row['is_approved'] ? 'approved' : 'pending';
                            $status_text = $row['is_approved'] ? 'Disetujui' : 'Menunggu';
                            $status_color = $row['is_approved'] ? 'success' : 'warning';
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="comment-card <?php echo $status_class; ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="bi bi-person-circle me-1"></i>
                                            <?php echo htmlspecialchars($row['nama_tamu']); ?>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge bg-<?php echo $status_color; ?> badge-status">
                                            <?php echo $status_text; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <p class="mb-3"><?php echo nl2br(htmlspecialchars($row['komentar'])); ?></p>
                                
                                <div class="d-flex justify-content-end gap-2">
                                    <?php if (!$row['is_approved']): ?>
                                        <a href="?action=approve&id=<?php echo $row['id']; ?>&search_comment=<?php echo urlencode($search_comment); ?>&comment_filter=<?php echo $comment_filter; ?>" 
                                           class="btn btn-sm btn-success">
                                            <i class="bi bi-check"></i> Setujui
                                        </a>
                                    <?php else: ?>
                                        <a href="?action=disapprove&id=<?php echo $row['id']; ?>&search_comment=<?php echo urlencode($search_comment); ?>&comment_filter=<?php echo $comment_filter; ?>" 
                                           class="btn btn-sm btn-warning">
                                            <i class="bi bi-x"></i> Batal
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="?action=delete&id=<?php echo $row['id']; ?>&search_comment=<?php echo urlencode($search_comment); ?>&comment_filter=<?php echo $comment_filter; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Hapus komentar ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-chat-left-text fs-1 text-muted d-block mb-3"></i>
                        <h6 class="text-muted mb-2">Belum ada komentar</h6>
                        <p class="text-muted mb-0">Belum ada tamu yang memberikan komentar</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5 pt-4 pb-3 border-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Dashboard aman - hanya Anda yang bisa mengakses data ini
                    </small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        &copy; <?php echo date('Y'); ?> Wedding Invitation System
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#rsvpTable').DataTable({
                "pageLength": 25,
                "order": [[4, 'desc']],
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Tidak ada data ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
            
            // Aktifkan tab berdasarkan URL hash
            const hash = window.location.hash;
            if (hash) {
                const triggerEl = document.querySelector(`#dashboardTab button[data-bs-target="${hash}"]`);
                if (triggerEl) {
                    bootstrap.Tab.getOrCreateInstance(triggerEl).show();
                }
            }
        });
    </script>
</body>
</html>

<?php
// Tutup koneksi
$stmt_rsvp->close();
$stmt_comments->close();
$conn->close();
?>