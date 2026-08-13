<?php
require_once 'includes/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $wallet_address = $conn->real_escape_string($_POST['wallet_address']);

    $sql = "UPDATE users SET name = '$name', address = '$address', wallet_address = '$wallet_address' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        $success = "Profile updated successfully!";
        // Update session name if it's stored there
        if(isset($_SESSION['name'])) {
            $_SESSION['name'] = $name; 
        }
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}

// Fetch current user data
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

require_once 'includes/header.php';
?>

<div class="card" style="max-width: 600px; margin: 4rem auto;">
    <h2 style="text-align: center; color: var(--primary-gold); margin-bottom: 2rem;">My Profile</h2>
    
    <?php if($error): ?>
        <div class="alert alert-error" style="background: #ffcccc; color: #cc0000; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="alert alert-success" style="background: #ccffcc; color: #008800; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address (Read-only)</label>
            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background-color: #f5f5f5; cursor: not-allowed; width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px;">
            <small style="color: var(--text-muted);">Email cannot be changed.</small>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Role</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars(ucfirst($user['role'])); ?>" disabled style="background-color: #f5f5f5; cursor: not-allowed; width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px;">
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px;">
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Ethereum Wallet Address</label>
            <input type="text" name="wallet_address" class="form-control" value="<?php echo htmlspecialchars($user['wallet_address'] ?? ''); ?>" placeholder="0x..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px;">
            <small style="color: var(--text-muted);">Used for interacting with the Smart Contract.</small>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Physical Address</label>
            <textarea name="address" class="form-control" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px; resize: vertical;"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-gold" style="width: 100%;">Save Changes</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
