<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
// Base URL for links
$base_url = "/GitHub/Hybrid block chain project";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxBlock - Hybrid Blockchain Jewellery</title>
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <!-- Web3 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <a href="<?php echo $base_url; ?>/index.php">LuxBlock</a>
        </div>
        <ul class="nav-links">
            <li><a href="<?php echo $base_url; ?>/index.php">Home</a></li>
            <li><a href="<?php echo $base_url; ?>/verify.php">Verify Authenticity</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <li><a href="<?php echo $base_url; ?>/admin/index.php">Admin Panel</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base_url; ?>/user/dashboard.php">Dashboard</a></li>
                <?php endif; ?>
                <li><a href="<?php echo $base_url; ?>/profile.php">Profile</a></li>
                <li><a href="<?php echo $base_url; ?>/logout.php" class="btn btn-outline">Logout</a></li>
            <?php else: ?>
                <li><a href="<?php echo $base_url; ?>/login.php" class="btn btn-primary">Login</a></li>
            <?php endif; ?>
            <li><button id="connectWalletBtn" class="btn btn-gold">Connect Wallet</button></li>
        </ul>
    </nav>
    <main class="main-content">
