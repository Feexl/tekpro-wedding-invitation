<?php
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Simple authentication (you should use hashed passwords in production)
    // You can store admin credentials in database or config
    $valid_username = 'admin';
    $valid_password_hash = password_hash('admin123', PASSWORD_DEFAULT); // In production, store this hash
    
    // For demo, using plain text comparison
    if ($username === $valid_username && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: admin_weddings.php');
        exit();
    } else {
        $error = 'Invalid credentials';
    }
}

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_weddings.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Wedding System</title>
    <style>
        body {
            background: linear-gradient(135deg, #f9f2f4 0%, #ffffff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(220, 107, 136, 0.15);
            width: 100%;
            max-width: 420px;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #dc6b88, #e8a2b5);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-header i {
            font-size: 60px;
            color: #dc6b88;
            margin-bottom: 20px;
            display: block;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 45px 14px 15px;
            border: 2px solid #e8d4da;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #fcfcfc;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #dc6b88;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(220, 107, 136, 0.1);
        }
        
        .form-group i {
            position: absolute;
            right: 15px;
            top: 40px;
            color: #999;
            font-size: 18px;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #dc6b88, #e8a2b5);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #c95a77, #dc6b88);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 107, 136, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .error-message {
            background-color: #fee;
            color: #d9534f;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            border-left: 4px solid #d9534f;
            animation: shake 0.5s;
        }
        
        @keyframes shake {
            0%, 100% {transform: translateX(0);}
            25% {transform: translateX(-5px);}
            75% {transform: translateX(5px);}
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #888;
            font-size: 13px;
        }
        
        .login-footer a {
            color: #dc6b88;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        /* Remember me checkbox */
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .remember-me input[type="checkbox"] {
            width: auto;
            transform: scale(1.2);
        }
        
        .remember-me label {
            margin-bottom: 0;
            color: #666;
            font-weight: normal;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-heart"></i>
            <h1>Wedding Admin Panel</h1>
            <p>Manage wedding invitations and templates</p>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" id="username" name="username" required autofocus>
                <i class="fas fa-user"></i>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-lock"></i>
            </div>
            
            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login to Dashboard
            </button>
        </form>
        
        <div class="login-footer">
            <p>Wedding Invitation System v1.0 &copy; <?php echo date('Y'); ?></p>
            <p><a href="<?php echo BASE_URL; ?>">← Back to Main Website</a></p>
        </div>
    </div>
</body>
</html>