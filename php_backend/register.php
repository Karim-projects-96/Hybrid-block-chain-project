<?php
require_once 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($name && $email && $password && $role) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hash, $role]);
            
            session_start();
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;
            
            header("Location: " . ($role === 'Manufacturer' ? 'dashboard.php' : 'index.php'));
            exit();
        } catch (PDOException $e) {
            $error = "User already exists or database error.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<?php include 'header.php'; ?>
    <style>
        .auth-container { max-width: 400px; margin: 4rem auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .auth-container h2 { font-family: var(--font-heading); margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 4px; }
        .auth-btn { width: 100%; margin-top: 1rem; }
        .auth-links { text-align: center; margin-top: 1rem; font-size: 0.9rem; }
        .auth-links a { color: var(--primary); text-decoration: none; }
    </style>

    <main>
        <div class="auth-container">
            <h2>Create Account</h2>
            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="regName">Full Name / Company Name</label>
                    <input type="text" id="regName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="regEmail">Email</label>
                    <input type="email" id="regEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="regPassword">Password</label>
                    <input type="password" id="regPassword" name="password" required>
                </div>
                <div class="form-group">
                    <label for="regRole">Role</label>
                    <select id="regRole" name="role" required>
                        <option value="Customer">Customer</option>
                        <option value="Manufacturer">Manufacturer</option>
                        <option value="Jewellery Shop">Jewellery Shop</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary auth-btn">Register</button>
            </form>
            <?php if ($error): ?>
                <div id="regError" style="color: red; margin-top: 1rem; text-align: center;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <div class="auth-links">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/web3@4.2.2/dist/web3.min.js"></script>
    <script src="js/web3App.js"></script>
</body>
</html>
