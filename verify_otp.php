<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db_connect.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $conn->real_escape_string($_POST['otp']);

    $user_type = null;
    $user_id = null;
    
    // Check admins
    $admin_res = $conn->query("SELECT id FROM admins WHERE email = '$email' AND reset_otp = '$otp' AND reset_otp_expiry >= NOW()");
    if ($admin_res && $admin_res->num_rows > 0) {
        $user_type = 'admins';
        $user_id = $admin_res->fetch_assoc()['id'];
    } else {
        // Check users
        $user_res = $conn->query("SELECT id FROM users WHERE email = '$email' AND reset_otp = '$otp' AND reset_otp_expiry >= NOW()");
        if ($user_res && $user_res->num_rows > 0) {
            $user_type = 'users';
            $user_id = $user_res->fetch_assoc()['id'];
        }
    }

    if ($user_type) {
        // Clear the OTP to prevent reuse
        $conn->query("UPDATE $user_type SET reset_otp = NULL, reset_otp_expiry = NULL WHERE id = $user_id");
        
        $_SESSION['verified_reset_email'] = $email;
        $_SESSION['verified_user_type'] = $user_type;
        $_SESSION['verified_user_id'] = $user_id;
        
        unset($_SESSION['reset_email']);
        unset($_SESSION['test_otp']);
        
        header("Location: reset_password.php");
        exit();
    } else {
        $error = 'Invalid or expired OTP. Please try again.';
    }
}

require_once 'includes/header.php';
?>

<div class="card" style="max-width: 400px; margin: 4rem auto;">
    <h2 style="text-align: center; color: var(--primary-gold); margin-bottom: 2rem;">Verify OTP</h2>
    <p style="text-align: center; margin-bottom: 1.5rem; color: var(--text-muted);">Enter the 6-digit code sent to <?php echo htmlspecialchars($email); ?>.</p>
    
    <?php if(isset($_SESSION['test_otp'])): ?>
        <div class="alert alert-success" style="text-align: center; margin-bottom: 1rem;">
            <strong>[Local Testing]</strong> Your OTP is: <span style="font-size: 1.2rem; letter-spacing: 2px; font-weight: bold;"><?php echo htmlspecialchars($_SESSION['test_otp']); ?></span>
        </div>
    <?php endif; ?>

    <?php if($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>6-Digit OTP</label>
            <input type="text" name="otp" class="form-control" required placeholder="123456" maxlength="6" pattern="\d{6}" style="letter-spacing: 5px; text-align: center; font-size: 1.2rem;">
        </div>
        <button type="submit" class="btn btn-gold" style="width: 100%; margin-top: 1rem;">Verify</button>
    </form>
    <p style="text-align: center; margin-top: 1.5rem;"><a href="forgot_password.php" style="color: var(--primary-gold); font-weight: bold;">Back</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>
