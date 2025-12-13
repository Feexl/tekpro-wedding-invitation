<?php
session_start();
require_once '../config.php'; // Gunakan config.php

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $slug = $_POST['slug'];
    
    $conn = getConnection();
    if (!$conn) {
        die("Database connection failed");
    }
    
    $query = "SELECT w.* FROM weddings w WHERE w.email = ? AND w.slug_url = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $wedding = $result->fetch_assoc();
        
        $_SESSION['wedding_id'] = $wedding['id'];
        $_SESSION['wedding_slug'] = $wedding['slug_url'];
        $_SESSION['nama_panggilan'] = $wedding['nama_panggilan'];
        $_SESSION['logged_in'] = true;
        
        $stmt->close();
        $conn->close();
        
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Email atau URL undangan tidak valid";
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pemilik Undangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card { 
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #d04d6d 0%, #9e4057ff 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-body {
            padding: 30px;
        }
        .form-control { 
            border-radius: 8px; 
            padding: 12px;
            border: 1px solid #ddd;
        }
        .btn-login { 
            background: linear-gradient(135deg, #d04d6d 0%, #9e4057ff 100%); 
            border: none; 
            padding: 12px; 
            border-radius: 8px; 
            color: white;
            font-weight: 500;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            color: white;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }
        .form-text {
            font-size: 0.85em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h4 class="mb-2">Dashboard Undangan</h4>
            <p class="mb-0">Masuk sebagai pemilik undangan</p>
        </div>
        <div class="login-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required 
                           placeholder="contoh@gmail.com">
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Slug/URL Undangan</label>
                    <input type="text" name="slug" class="form-control" required 
                           placeholder="nama-undangan-abc123">
                    <div class="form-text">
                        Masukkan bagian akhir URL undangan Anda
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">Masuk ke Dashboard</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>