<?php
require_once 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: " . ($user['role'] === 'Manufacturer' ? 'dashboard.php' : 'index.php'));
            exit();
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Please fill in all fields";
    }
}
?>
<?php include 'header.php'; ?>
    <style>
        .auth-container { max-width: 400px; margin: 4rem auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .auth-container h2 { font-family: var(--font-heading); margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        .form-group input { width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 4px; }
        .auth-btn { width: 100%; margin-top: 1rem; }
        .auth-links { text-align: center; margin-top: 1rem; font-size: 0.9rem; }
        .auth-links a { color: var(--primary); text-decoration: none; }
    </style>

    <main>
        <div class="auth-container">
            <h2>Welcome Back</h2>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary auth-btn">Login</button>
            </form>
            <?php if ($error): ?>
                <div style="color: red; margin-top: 1rem; text-align: center;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <div class="auth-links">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/web3@4.2.2/dist/web3.min.js"></script>
    <script src="js/web3App.js"></script>
</body>
</html>
