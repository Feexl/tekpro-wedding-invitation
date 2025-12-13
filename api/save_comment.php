<?php
require_once '../config.php';
$conn = getConnection();

header('Content-Type: application/json');

if (!isset($_POST['wedding_id'])) {
    echo json_encode(['success' => false, 'message' => 'wedding_id tidak ditemukan']);
    exit;
}

$wedding_id = intval($_POST['wedding_id']);
$nama = $_POST['nama_tamu'] ?? '';
$komentar = $_POST['komentar'] ?? '';

$sql = "INSERT INTO comments (wedding_id, nama_tamu, komentar, is_approved)
        VALUES (?, ?, ?, 1)"; // auto-approve

$stmt = $conn->prepare($sql);
$stmt->bind_param('iss', $wedding_id, $nama, $komentar);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan komentar']);
}
