<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db_connect.php';
require_once 'includes/logger.php';

if (!isset($_SESSION['verified_reset_email']) || !isset($_SESSION['verified_user_type']) || !isset($_SESSION['verified_user_id'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['verified_reset_email'];
$user_type = $_SESSION['verified_user_type'];
$user_id = $_SESSION['verified_user_id'];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        $hashed_password = md5($password);
        
        $sql = "UPDATE $user_type SET password = '$hashed_password' WHERE id = $user_id";
        if ($conn->query($sql) === TRUE) {
            
            // Log the action
            $role_for_log = ($user_type === 'admins') ? 'admin' : 'user';
            // We can fetch the specific role for users if we want, but 'user' or 'admin' is fine for logging password reset
            log_action($conn, $user_id, $role_for_log, 'password_reset', 'Password was successfully reset via OTP');

            // Clear session data
            unset($_SESSION['verified_reset_email']);
            unset($_SESSION['verified_user_type']);
            unset($_SESSION['verified_user_id']);
            
            $success = 'Your password has been reset successfully. You can now login with your new password.';
        } else {
            $error = 'An error occurred while updating your password. Please try again.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="card" style="max-width: 400px; margin: 4rem auto;">
    <h2 style="text-align: center; color: var(--primary-gold); margin-bottom: 2rem;">Reset Password</h2>
    
    <?php if($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
        <a href="login.php" class="btn btn-gold" style="width: 100%; text-align: center; display: inline-block; margin-top: 1rem;">Go to Login</a>
    <?php else: ?>
        <p style="text-align: center; margin-bottom: 1.5rem; color: var(--text-muted);">Enter your new password for <?php echo htmlspecialchars($email); ?>.</p>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimum 6 characters">
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required placeholder="Confirm your new password">
            </div>
            <button type="submit" class="btn btn-gold" style="width: 100%; margin-top: 1rem;">Reset Password</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
