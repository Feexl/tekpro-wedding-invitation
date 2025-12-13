<?php
// save_rsvp.php
require_once '../config.php';
$conn = getConnection();

header('Content-Type: application/json');

// Pastikan wedding_id ada
if (!isset($_POST['wedding_id'])) {
    echo json_encode(['success' => false, 'message' => 'wedding_id tidak ditemukan']);
    exit;
}

$wedding_id = intval($_POST['wedding_id']);
$nama = $_POST['nama_tamu'] ?? '';
$jumlah = $_POST['jumlah_tamu'] ?? 1;
$status = $_POST['status'] ?? '';
$pesan = $_POST['pesan'] ?? '';

$sql = "INSERT INTO rsvp (wedding_id, nama_tamu, jumlah_tamu, status, pesan)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('isiss', $wedding_id, $nama, $jumlah, $status, $pesan);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
