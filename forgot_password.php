<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db_connect.php';
require_once 'includes/logger.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);

    // Check if email exists in users or admins
    $user_type = null;
    $user_id = null;
    
    $admin_res = $conn->query("SELECT id FROM admins WHERE email = '$email'");
    if ($admin_res && $admin_res->num_rows > 0) {
        $user_type = 'admins';
        $user_id = $admin_res->fetch_assoc()['id'];
    } else {
        $user_res = $conn->query("SELECT id FROM users WHERE email = '$email'");
        if ($user_res && $user_res->num_rows > 0) {
            $user_type = 'users';
            $user_id = $user_res->fetch_assoc()['id'];
        }
    }

    if ($user_type) {
        // Generate a 6-digit OTP
        $otp = sprintf("%06d", mt_rand(100000, 999999));
        $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Save to DB
        $conn->query("UPDATE $user_type SET reset_otp = '$otp', reset_otp_expiry = '$expiry' WHERE id = $user_id");

        // Simulate sending email by passing the OTP to the session so we can display it for local testing
        $_SESSION['reset_email'] = $email;
        $_SESSION['test_otp'] = $otp; // Remove this line in production

        header("Location: verify_otp.php");
        exit();
    } else {
        $error = 'If this email is registered, you will receive an OTP shortly.'; // Generic message for security
    }
}

require_once 'includes/header.php';
?>

<div class="card" style="max-width: 400px; margin: 4rem auto;">
    <h2 style="text-align: center; color: var(--primary-gold); margin-bottom: 2rem;">Forgot Password</h2>
    <p style="text-align: center; margin-bottom: 1.5rem; color: var(--text-muted);">Enter your email address and we'll send you an OTP to reset your password.</p>
    
    <?php if($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="Enter your email">
        </div>
        <button type="submit" class="btn btn-gold" style="width: 100%; margin-top: 1rem;">Send OTP</button>
    </form>
    <p style="text-align: center; margin-top: 1.5rem;"><a href="login.php" style="color: var(--primary-gold); font-weight: bold;">Back to Login</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>
