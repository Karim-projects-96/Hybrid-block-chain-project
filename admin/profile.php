<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

// Ensure the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($password)) {
        $hashed_password = md5($password);
        $sql = "UPDATE admins SET name = '$name', address = '$address', password = '$hashed_password' WHERE id = $user_id";
    } else {
        $sql = "UPDATE admins SET name = '$name', address = '$address' WHERE id = $user_id";
    }
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
$result = $conn->query("SELECT * FROM admins WHERE id = $user_id");
$user = $result->fetch_assoc();

require_once '../includes/header.php';
?>

<div class="card" style="max-width: 600px; margin: 4rem auto;">
    <h2 style="text-align: center; color: var(--primary-gold); margin-bottom: 2rem;">Admin Profile</h2>
    
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
            <input type="text" class="form-control" value="Admin" disabled style="background-color: #f5f5f5; cursor: not-allowed; width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px;">
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px;">
        </div>


        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Physical Address</label>
            <textarea name="address" class="form-control" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px; resize: vertical;"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control" placeholder="Enter new password" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px;">
        </div>

        <button type="submit" class="btn btn-gold" style="width: 100%;">Save Changes</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
