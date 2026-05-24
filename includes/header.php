<?php
// Ensure this is included after session_start() if possible, or session_start() is already called in session_check.php
$admin_name = isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitStay Hotel Management</title>
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/hotel/assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar will be included separately -->
        
        <div class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-left">
                    <h2>FitStay Hotel Management</h2>
                </div>
                <div class="topbar-right">
                    <span class="welcome-msg">Welcome, <?php echo $admin_name; ?></span>
                    <a href="/hotel/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </header>
            
            <div class="content-wrapper">
