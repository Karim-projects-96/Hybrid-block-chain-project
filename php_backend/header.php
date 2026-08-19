<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxe Blockchain</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="logo">LUXE BLOCKCHAIN</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="verify.php">Verify Asset</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'Manufacturer' || $_SESSION['role'] === 'Super Admin'): ?>
                    <a href="dashboard.php">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['name']); ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
            
            <button id="connectWalletBtn" class="btn btn-outline">Connect Wallet</button>
        </nav>
    </header>
