<?php
// get_comments.php
require_once '../config.php';
$conn = getConnection();

header('Content-Type: application/json');

// wedding_id HARUS dikirim via GET
if (!isset($_GET['wedding_id'])) {
    echo json_encode([
        'success' => false,
        'comments' => [],
        'message' => 'wedding_id tidak ditemukan'
    ]);
    exit;
}

$wedding_id = intval($_GET['wedding_id']);

$sql = "SELECT nama_tamu, komentar, created_at 
        FROM comments
        WHERE wedding_id = ? AND is_approved = 1
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $wedding_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = [
        'nama' => $row['nama_tamu'],
        'komentar' => $row['komentar'],
        'tanggal' => date('d M Y', strtotime($row['created_at']))
    ];
}

echo json_encode([
    'success' => true,
    'comments' => $comments
]);
