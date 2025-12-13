<?php
// ==========================================
// FILE: config.php
// ==========================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wedding_system');

// Base URL Configuration
define('BASE_URL', 'http://localhost/wedding-invitation/');
define('PREVIEW_URL', BASE_URL . 'preview/');
define('INVITATION_URL', BASE_URL . 'invitation/');

function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error);
        }
        
        $conn->set_charset('utf8mb4');
        return $conn;
        
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
    }
}


function jsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function generateSlug($nama_pria, $nama_wanita) {
    $slug = strtolower($nama_pria . '-' . $nama_wanita);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    
    // Add random string for uniqueness
    $slug .= '-' . substr(md5(time()), 0, 6);
    
    return $slug;
}
?>