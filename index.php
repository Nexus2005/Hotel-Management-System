<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: /hotel/dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/db.php';
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['username'];
                header("Location: /hotel/dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitStay Hotel Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/hotel/assets/css/style.css">
</head>
<body class="login-body">
    <div class="login-card">
        <h2>FitStay Admin Panel</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="index.php" id="loginForm" onsubmit="return validateLogin()">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </form>
    </div>

    <script>
    function validateLogin() {
        var email = document.getElementById('email').value.trim();
        var password = document.getElementById('password').value;
        
        if (email === '' || password === '') {
            alert('Please fill in all fields.');
            return false;
        }
        
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            return false;
        }
        
        if (password.length < 6) {
            alert('Password must be at least 6 characters long.');
            return false;
        }
        
        var confirmUsername = prompt("Please confirm your username to login:");
        if (confirmUsername === null || confirmUsername.trim() === "") {
            return false; // Cancelled
        }
        
        return true;
    }
    </script>
</body>
</html>
