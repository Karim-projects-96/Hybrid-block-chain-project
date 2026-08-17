<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Nullify references to avoid foreign key constraint errors
    $conn->query("UPDATE jewellery SET manufacturer_id = NULL WHERE manufacturer_id = $id");
    $conn->query("UPDATE jewellery SET current_owner_id = NULL WHERE current_owner_id = $id");
    $conn->query("UPDATE transactions SET from_user_id = NULL WHERE from_user_id = $id");
    $conn->query("UPDATE transactions SET to_user_id = NULL WHERE to_user_id = $id");

    $conn->query("DELETE FROM users WHERE id = $id AND role != 'admin'");
    header("Location: manage_users.php");
    exit();
}

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);
    $password = md5($_POST['password']);
    $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';

    $check_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check_email->num_rows > 0) {
        $error = "Email already exists in users table.";
    } else {
        $check_admin_email = $conn->query("SELECT id FROM admins WHERE email = '$email'");
        if ($check_admin_email->num_rows > 0) {
            $error = "Email already exists in admins table.";
        } else {
            $sql = "INSERT INTO users (name, email, password, role, address) VALUES ('$name', '$email', '$password', '$role', '$address')";
            if ($conn->query($sql) === TRUE) {
                $success = "User added successfully.";
            } else {
                $error = "Error adding user: " . $conn->error;
            }
        }
    }
}

require_once '../includes/header.php';
?>
<div class="dashboard-header">
    <h2>Manage Users</h2>
</div>

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

<div class="card" style="margin-bottom: 2rem;">
    <h3 style="color: var(--primary-gold); margin-bottom: 1rem;">Add New User</h3>
    <form method="POST" action="" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <input type="hidden" name="add_user" value="1">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                <option value="customer">Customer</option>
                <option value="manufacturer">Manufacturer</option>
                <option value="shop">Shop Owner</option>
            </select>
        </div>
        <div class="form-group" style="grid-column: 1 / -1;">
            <label>Physical Address (Required)</label>
            <textarea name="address" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;" placeholder="Enter physical address" required></textarea>
        </div>
        <div style="grid-column: 1 / -1;">
            <button type="submit" class="btn btn-primary">Add User</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>#" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td><span style='padding: 0.2rem 0.5rem; background: #eee; border-radius: 4px; text-transform: capitalize;'>" . $row['role'] . "</span></td>";
                    echo "<td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>";
                    echo "<td>";
                    if($row['role'] !== 'admin') {
                        echo "<a href='?delete=" . $row['id'] . "' class='btn btn-outline' style='padding: 0.3rem 0.8rem; font-size: 0.8rem;' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
