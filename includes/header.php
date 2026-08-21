<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
// Base URL for links
$script_path = str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME']));
$root_path = str_replace('\\', '/', realpath(dirname(__DIR__)));
$relative_path = str_replace($root_path, '', $script_path);
$depth = substr_count(trim($relative_path, '/'), '/');
$base_url = dirname($_SERVER['SCRIPT_NAME']);
for ($i = 0; $i < $depth; $i++) {
    $base_url = dirname($base_url);
}
$base_url = str_replace('\\', '/', $base_url);
if ($base_url === '/') {
    $base_url = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxBlock - Hybrid Blockchain Jewellery</title>
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css?v=<?php echo time(); ?>">

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
                    <li><a href="<?php echo $base_url; ?>/admin/logs.php">Activity Logs</a></li>
                    <li><a href="<?php echo $base_url; ?>/admin/profile.php">Profile</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base_url; ?>/user/dashboard.php">Dashboard</a></li>
                    <li><a href="<?php echo $base_url; ?>/profile.php">Profile</a></li>
                <?php endif; ?>
                <li><a href="<?php echo $base_url; ?>/logout.php" class="btn btn-outline">Logout</a></li>
            <?php else: ?>
                <li class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-primary" style="padding: 0.6rem 1.5rem; display: inline-block;">Login &#9660;</a>
                    <div class="dropdown-content">
                        <a href="<?php echo $base_url; ?>/login.php?role=customer">Customer Login</a>
                        <a href="<?php echo $base_url; ?>/login.php?role=shop">Shop Login</a>
                        <a href="<?php echo $base_url; ?>/login.php?role=manufacturer">Manufacturer Login</a>
                        <a href="<?php echo $base_url; ?>/login.php?role=admin">Admin Login</a>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <main class="main-content">
