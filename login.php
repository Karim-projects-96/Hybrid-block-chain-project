<?php
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $admin_result = $conn->query("SELECT * FROM admins WHERE email = '$email'");
    if ($admin_result->num_rows == 1) {
        $admin = $admin_result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['role'] = 'admin';
            $_SESSION['name'] = $admin['name'];
            header("Location: admin/index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];
                if ($user['role'] == 'manufacturer') {
                    header("Location: manufacturer/dashboard.php");
                } elseif ($user['role'] == 'shop') {
                    header("Location: shop/dashboard.php");
                } else {
                    header("Location: customer/dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}

require_once 'includes/header.php';
?>
<div class="card" style="max-width: 400px; margin: 4rem auto;">
    <h2 style="text-align: center; color: var(--primary-gold); margin-bottom: 2rem;">Login to LuxBlock</h2>
    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="Enter your email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Enter your password">
        </div>
        <button type="submit" class="btn btn-gold" style="width: 100%; margin-top: 1rem;">Login</button>
    </form>
    <p style="text-align: center; margin-top: 1.5rem;">Don't have an account? <a href="register.php" style="color: var(--primary-gold); font-weight: bold;">Register here</a></p>
</div>
<?php require_once 'includes/footer.php'; ?>
