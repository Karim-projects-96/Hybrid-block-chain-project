<?php
require_once 'includes/db_connect.php';
require_once 'includes/logger.php';
require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = md5($_POST['password']);
    $role = $conn->real_escape_string($_POST['role']);

    $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';

    // Check if email exists
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        $sql = "INSERT INTO users (name, email, password, role, address) VALUES ('$name', '$email', '$password', '$role', '$address')";
        if ($conn->query($sql) === TRUE) {
            $new_user_id = $conn->insert_id;
            log_action($conn, $new_user_id, $role, 'register', "New user registered as $role");
            $success = "Registration successful! You can now login.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<div class="card" style="max-width: 500px; margin: 4rem auto;">
    <h2 style="text-align: center; color: var(--primary-gold); margin-bottom: 2rem;">Create Account</h2>
    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?> <a href="login.php">Login here</a></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" required placeholder="Enter your full name">
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="Enter your email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Create a password">
        </div>
        <div class="form-group">
            <label>I am a...</label>
            <select name="role" class="form-control" required>
                <option value="customer">Customer</option>
                <option value="shop">Jewellery Shop</option>
                <option value="manufacturer">Manufacturer</option>
            </select>
        </div>
        <div class="form-group">
            <label>Address (Optional)</label>
            <textarea name="address" class="form-control" placeholder="Enter your physical address"></textarea>
        </div>
        <button type="submit" class="btn btn-gold" style="width: 100%; margin-top: 1rem;">Register</button>
    </form>
    <p style="text-align: center; margin-top: 1.5rem;">Already have an account? <a href="login.php" style="color: var(--primary-gold); font-weight: bold;">Login here</a></p>
</div>
<?php require_once 'includes/footer.php'; ?>
