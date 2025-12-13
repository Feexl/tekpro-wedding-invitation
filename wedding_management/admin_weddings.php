<?php
session_start();
require_once '../config.php';

// Cek autentikasi admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

// Get database connection
$conn = getConnection();
if (!$conn) {
    die("Database connection failed");
}

// Search parameter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Query dasar dengan filter
$query = "SELECT w.*, t.nama_template as template_name 
          FROM weddings w 
          LEFT JOIN templates t ON w.template_id = t.id 
          WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (w.nama_mempelai_pria LIKE '%$search%' 
                    OR w.nama_mempelai_wanita LIKE '%$search%' 
                    OR w.slug_url LIKE '%$search%' 
                    OR w.email LIKE '%$search%')";
}

if (!empty($status_filter)) {
    $query .= " AND w.status_pembayaran = '$status_filter'";
}

$query .= " ORDER BY w.created_at DESC";

$result = $conn->query($query);

// Update status via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $wedding_id = $_POST['wedding_id'];
        $field = $_POST['field'];
        $value = $_POST['value'];

        // Validasi field yang boleh diupdate
        $allowed_fields = ['status_pembayaran', 'is_active', 'website_sent', 'website_ready'];

        if (in_array($field, $allowed_fields)) {
            if ($field === 'status_pembayaran') {
                $value = ($value == 'true') ? 'paid' : 'pending';
            } else {
                $value = ($value == 'true') ? 1 : 0;
            }

            $update_query = "UPDATE weddings SET $field = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('si', $value, $wedding_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $conn->error]);
            }
            $stmt->close();
            exit();
        }
    }

    // Handle delete action
    if ($_POST['action'] === 'delete') {
        $wedding_id = $_POST['wedding_id'];

        // Mulai transaction untuk safety
        $conn->begin_transaction();

        try {
            // Hapus data terkait terlebih dahulu (jika ada foreign key constraints)
            // Sesuaikan dengan struktur database Anda

            // Hapus dari tabel weddings
            $delete_query = "DELETE FROM weddings WHERE id = ?";
            $stmt = $conn->prepare($delete_query);
            $stmt->bind_param('i', $wedding_id);

            if ($stmt->execute()) {
                $conn->commit();
                echo json_encode([
                    'success' => true,
                    'message' => 'Wedding deleted successfully'
                ]);
            } else {
                throw new Exception('Delete failed: ' . $conn->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Wedding Management</title>
    <style>
        :root {
            --primary-color: #dc6b88;
            --primary-dark: #c95a77;
            --danger-color: #e74c3c;
            --danger-dark: #c0392b;
            --light-bg: #f9f2f4;
            --border-color: #e8d4da;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 {
            color: var(--primary-color);
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header h1 i {
            font-size: 32px;
        }

        .logout-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: var(--primary-dark);
        }

        /* Search and Filter */
        .search-filter {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
        }

        .search-box label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }

        .search-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .filter-box {
            min-width: 200px;
        }

        .filter-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        .btn-search {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn-search:hover {
            background-color: var(--primary-dark);
        }

        /* Table Container */
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: var(--primary-color);
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
            vertical-align: middle;
        }

        tbody tr {
            transition: all 0.3s;
        }

        tbody tr:hover {
            background-color: var(--light-bg);
        }

        /* Switch/Toggle Styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: var(--primary-color);
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        /* Badge Styles */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Status Cell */
        .status-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .switch-label {
            font-size: 12px;
            color: #666;
            min-width: 70px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-delete {
            background-color: #fee;
            color: var(--danger-color);
            border: 1px solid #fcc;
        }

        .btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
            border-color: var(--danger-color);
        }

        .btn-preview {
            background-color: var(--light-bg);
            color: var(--primary-color);
            border: 1px solid #e8d4da;
        }

        .btn-preview:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Responsive Table */
        @media (max-width: 1200px) {
            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 1200px;
            }
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .stat-icon {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 70px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #999;
            margin-bottom: 10px;
        }

        /* Confirmation Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: var(--danger-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-body {
            margin-bottom: 25px;
            color: #666;
            line-height: 1.6;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn-cancel {
            padding: 10px 25px;
            background-color: #f8f9fa;
            color: #666;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-cancel:hover {
            background-color: #e9ecef;
        }

        .btn-confirm-delete {
            padding: 10px 25px;
            background-color: var(--danger-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn-confirm-delete:hover {
            background-color: var(--danger-dark);
        }

        /* Loading State */
        .deleting {
            opacity: 0.5;
            pointer-events: none;
        }

        /* Notification */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: 600;
            z-index: 1001;
            animation: slideIn 0.3s ease-out;
            display: none;
        }

        .notification.success {
            background-color: #28a745;
        }

        .notification.error {
            background-color: #dc3545;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header h1 {
                font-size: 24px;
            }

            .search-filter {
                flex-direction: column;
            }

            .search-box,
            .filter-box {
                min-width: 100%;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this wedding?</p>
                <p><strong>This action cannot be undone.</strong> All wedding data including photos and settings will be permanently removed.</p>
                <p id="deleteCoupleNames" style="color: var(--primary-color); font-weight: bold; margin-top: 10px;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelDelete">Cancel</button>
                <button type="button" class="btn-confirm-delete" id="confirmDelete">Delete Permanently</button>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="notification"></div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-heart"></i>
                Wedding Management System
            </h1>
            <div>
                <span style="color: #666; margin-right: 15px;">
                    <i class="fas fa-user"></i> Admin
                </span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-container">
            <?php
            $conn = getConnection();
            // Total weddings
            $total_query = "SELECT COUNT(*) as total FROM weddings";
            $total_result = $conn->query($total_query);
            $total = $total_result->fetch_assoc()['total'];

            // Active weddings
            $active_query = "SELECT COUNT(*) as total FROM weddings WHERE is_active = 1";
            $active_result = $conn->query($active_query);
            $active = $active_result->fetch_assoc()['total'];

            // Paid weddings
            $paid_query = "SELECT COUNT(*) as total FROM weddings WHERE status_pembayaran = 'paid'";
            $paid_result = $conn->query($paid_query);
            $paid = $paid_result->fetch_assoc()['total'];

            // Pending weddings
            $pending_query = "SELECT COUNT(*) as total FROM weddings WHERE status_pembayaran = 'pending'";
            $pending_result = $conn->query($pending_query);
            $pending = $pending_result->fetch_assoc()['total'];

            $conn->close();
            ?>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total Weddings</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?php echo $active; ?></div>
                <div class="stat-label">Active Weddings</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="stat-number"><?php echo $paid; ?></div>
                <div class="stat-label">Paid Weddings</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $pending; ?></div>
                <div class="stat-label">Pending Payment</div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="search-filter">
            <div class="search-box">
                <label for="search"><i class="fas fa-search"></i> Search Weddings</label>
                <form method="GET" action="" style="display: flex; gap: 10px;">
                    <input type="text"
                        id="search"
                        name="search"
                        class="search-input"
                        placeholder="Search by names, email, or slug..."
                        value="<?php echo htmlspecialchars($search); ?>">

                    <div class="filter-box">
                        <select name="status" class="filter-select">
                            <option value="">All Payment Status</option>
                            <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $status_filter == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-search">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    <?php if (!empty($search) || !empty($status_filter)): ?>
                        <a href="admin_weddings.php" class="btn-search" style="background-color: #666;">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table id="weddingsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Couple Names</th>
                        <th>Event Date</th>
                        <th>Status</th>
                        <th>Contact Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr id="row-<?php echo $row['id']; ?>" data-id="<?php echo $row['id']; ?>">
                                <td><strong>#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['nama_mempelai_pria']); ?></strong> &
                                    <strong><?php echo htmlspecialchars($row['nama_mempelai_wanita']); ?></strong><br>
                                    <small style="color: #666;">
                                        Slug: <?php echo htmlspecialchars($row['slug_url']); ?>
                                    </small><br>
                                    <a href="<?php echo '../invitation.php?slug=' . $row['slug_url']; ?>"
                                        target="_blank"
                                        style="color: var(--primary-color); text-decoration: none; font-size: 12px; margin-top: 5px; display: inline-block;">
                                        <i class="fas fa-external-link-alt"></i> View Website
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    $tanggal = $row['tanggal_acara']; // "Jumat, 09 Januari 2026"
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
                                    ?>
                                    <?php echo date('d M Y', strtotime($tanggal)); ?><br>
                                    <small style="color: #666; font-size: 11px;">
                                        Created: <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                    </small>
                                </td>

                                <!-- Payment Status -->
                                <td class="status-cell">
                                    <?php if ($row['status_pembayaran'] == 'paid'): ?>
                                        <span class="badge badge-paid">PAID</span>
                                    <?php elseif ($row['status_pembayaran'] == 'cancelled'): ?>
                                        <span class="badge badge-cancelled">CANCELLED</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">PENDING</span>
                                    <?php endif; ?>

                                    <label class="switch">
                                        <input type="checkbox" class="status-toggle"
                                            data-field="status_pembayaran"
                                            <?php echo $row['status_pembayaran'] == 'paid' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="switch-label">
                                        <?php echo $row['status_pembayaran'] == 'paid' ? 'Paid' : 'Mark as Paid'; ?>
                                    </span>
                                </td>

                                <!-- Is Active -->
                                <td class="status-cell">
                                    <label class="switch">
                                        <input type="checkbox" class="status-toggle"
                                            data-field="is_active"
                                            <?php echo $row['is_active'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="switch-label">
                                        <?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>

                                <!-- Website Sent -->
                                <td class="status-cell">
                                    <label class="switch">
                                        <input type="checkbox" class="status-toggle"
                                            data-field="website_sent"
                                            <?php echo $row['website_sent'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="switch-label">
                                        <?php echo $row['website_sent'] ? 'Sent' : 'Not Sent'; ?>
                                    </span>
                                </td>

                                <!-- Website Ready -->
                                <td class="status-cell">
                                    <label class="switch">
                                        <input type="checkbox" class="status-toggle"
                                            data-field="website_ready"
                                            <?php echo $row['website_ready'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="switch-label">
                                        <?php echo $row['website_ready'] ? 'Ready' : 'Not Ready'; ?>
                                    </span>
                                </td>

                                <td>
                                    <div style="font-size: 12px;">
                                        <div style="margin-bottom: 5px;">
                                            <i class="fas fa-envelope"></i>
                                            <?php echo htmlspecialchars($row['email'] ?? '-'); ?>
                                        </div>
                                        <div>
                                            <i class="fas fa-phone"></i>
                                            <?php echo htmlspecialchars($row['nomor_wa'] ?? '-'); ?>
                                        </div>
                                        <?php if (!empty($row['harga'])): ?>
                                            <div style="margin-top: 5px; color: var(--primary-color); font-weight: bold;">
                                                Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Action Buttons -->
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?php echo '../invitation.php?slug=' . $row['slug_url']; ?>"
                                            target="_blank"
                                            class="btn-action btn-preview"
                                            title="Preview Website">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                        <button class="btn-action btn-delete"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-couple="<?php echo htmlspecialchars($row['nama_mempelai_pria'] . ' & ' . $row['nama_mempelai_wanita']); ?>"
                                            title="Delete Wedding">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <h3>No weddings found</h3>
                                    <p style="color: #999; margin-top: 10px;">
                                        <?php if (!empty($search) || !empty($status_filter)): ?>
                                            Try changing your search or filter criteria
                                        <?php else: ?>
                                            There are no weddings in the system yet
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Info -->
        <div style="text-align: center; color: #999; font-size: 13px; margin-top: 30px; padding: 20px;">
            <p>
                <i class="fas fa-info-circle"></i>
                Use the switches to update status. Click delete button to remove weddings.
            </p>
            <p style="margin-top: 10px;">
                Wedding Management System &copy; <?php echo date('Y'); ?> |
                Total: <?php echo $total; ?> Weddings
            </p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let weddingToDelete = null;
            let deleteButton = null;

            // Handle toggle switches
            $('.status-toggle').change(function() {
                const weddingId = $(this).closest('tr').data('id');
                const field = $(this).data('field');
                const isChecked = $(this).is(':checked');
                const $switchLabel = $(this).closest('.status-cell').find('.switch-label');
                const $badge = $(this).closest('.status-cell').find('.badge');
                const $this = $(this);

                // Show loading state
                $this.prop('disabled', true);

                // Update UI immediately for better UX
                updateUIBasedOnField(field, isChecked, $switchLabel, $badge);

                // Send AJAX request
                $.ajax({
                    url: 'admin_weddings.php',
                    method: 'POST',
                    data: {
                        action: 'update_status',
                        wedding_id: weddingId,
                        field: field,
                        value: isChecked
                    },
                    success: function(response) {
                        $this.prop('disabled', false);

                        try {
                            const result = JSON.parse(response);
                            if (!result.success) {
                                // Revert if failed
                                $this.prop('checked', !isChecked);
                                updateUIBasedOnField(field, !isChecked, $switchLabel, $badge);
                            }
                        } catch (e) {
                            $this.prop('checked', !isChecked);
                            updateUIBasedOnField(field, !isChecked, $switchLabel, $badge);
                        }
                    },
                    error: function() {
                        $this.prop('disabled', false);
                        // Revert on error
                        $this.prop('checked', !isChecked);
                        updateUIBasedOnField(field, !isChecked, $switchLabel, $badge);
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.btn-delete', function() {
                weddingToDelete = $(this).data('id');
                const coupleNames = $(this).data('couple');
                deleteButton = $(this);

                // Show couple names in modal
                $('#deleteCoupleNames').text(coupleNames);

                // Show modal
                $('#deleteModal').fadeIn(200);
            });

            // Cancel delete
            $('#cancelDelete').click(function() {
                $('#deleteModal').fadeOut(200);
                weddingToDelete = null;
                deleteButton = null;
            });

            // Confirm delete
            $('#confirmDelete').click(function() {
                if (!weddingToDelete) return;

                // Show loading on button
                $(this).prop('disabled', true).text('Deleting...');

                // Add deleting class to row
                $(`#row-${weddingToDelete}`).addClass('deleting');

                // Send delete request
                $.ajax({
                    url: 'admin_weddings.php',
                    method: 'POST',
                    data: {
                        action: 'delete',
                        wedding_id: weddingToDelete
                    },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);

                            if (result.success) {
                                // Show success notification
                                showNotification('Wedding deleted successfully!', 'success');

                                // Remove row with animation
                                $(`#row-${weddingToDelete}`).fadeOut(300, function() {
                                    $(this).remove();

                                    // Update stats if needed
                                    updateStatsAfterDelete();

                                    // Check if table is now empty
                                    if ($('#weddingsTable tbody tr').length === 1) { // Only empty state row left
                                        location.reload(); // Reload to show empty state properly
                                    }
                                });
                            } else {
                                // Show error notification
                                showNotification(result.message || 'Failed to delete wedding', 'error');

                                // Remove deleting class
                                $(`#row-${weddingToDelete}`).removeClass('deleting');
                            }
                        } catch (e) {
                            showNotification('Error processing response', 'error');
                            $(`#row-${weddingToDelete}`).removeClass('deleting');
                        }

                        // Close modal and reset
                        $('#deleteModal').fadeOut(200);
                        $('#confirmDelete').prop('disabled', false).text('Delete Permanently');
                        weddingToDelete = null;
                        deleteButton = null;
                    },
                    error: function() {
                        showNotification('Network error occurred', 'error');
                        $(`#row-${weddingToDelete}`).removeClass('deleting');
                        $('#deleteModal').fadeOut(200);
                        $('#confirmDelete').prop('disabled', false).text('Delete Permanently');
                        weddingToDelete = null;
                        deleteButton = null;
                    }
                });
            });

            function updateUIBasedOnField(field, isChecked, $switchLabel, $badge) {
                switch (field) {
                    case 'status_pembayaran':
                        if (isChecked) {
                            if ($badge.length) {
                                $badge.removeClass('badge-pending badge-cancelled').addClass('badge-paid').text('PAID');
                            }
                            $switchLabel.text('Paid');
                        } else {
                            if ($badge.length) {
                                $badge.removeClass('badge-paid badge-cancelled').addClass('badge-pending').text('PENDING');
                            }
                            $switchLabel.text('Mark as Paid');
                        }
                        break;
                    case 'is_active':
                        $switchLabel.text(isChecked ? 'Active' : 'Inactive');
                        break;
                    case 'website_sent':
                        $switchLabel.text(isChecked ? 'Sent' : 'Not Sent');
                        break;
                    case 'website_ready':
                        $switchLabel.text(isChecked ? 'Ready' : 'Not Ready');
                        break;
                }
            }

            function showNotification(message, type) {
                const $notification = $('#notification');
                $notification.removeClass('success error').addClass(type).text(message).fadeIn(300);

                setTimeout(function() {
                    $notification.fadeOut(300);
                }, 3000);
            }

            function updateStatsAfterDelete() {
                // Update total count
                const $totalStat = $('.stat-card:first-child .stat-number');
                const currentTotal = parseInt($totalStat.text());
                $totalStat.text(currentTotal - 1);

                // You could update other stats here if needed
                // For simplicity, we'll just update the total
            }

            // Close modal when clicking outside
            $(window).click(function(event) {
                if ($(event.target).is('#deleteModal')) {
                    $('#deleteModal').fadeOut(200);
                    weddingToDelete = null;
                    deleteButton = null;
                }
            });
        });
    </script>
</body>

</html>