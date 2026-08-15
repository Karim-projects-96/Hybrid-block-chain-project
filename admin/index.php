<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../includes/header.php';

// Handle Deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_jewellery'])) {
        $id = (int)$_POST['delete_jewellery'];
        $conn->query("DELETE FROM transactions WHERE jewellery_id = $id");
        $conn->query("DELETE FROM jewellery WHERE id = $id");
        echo "<script>window.location.href='index.php';</script>";
    }
    if (isset($_POST['delete_user'])) {
        $id = (int)$_POST['delete_user'];
        // Nullify references to avoid foreign key constraint errors
        $conn->query("UPDATE jewellery SET manufacturer_id = NULL WHERE manufacturer_id = $id");
        $conn->query("UPDATE jewellery SET current_owner_id = NULL WHERE current_owner_id = $id");
        $conn->query("UPDATE transactions SET from_user_id = NULL WHERE from_user_id = $id");
        $conn->query("UPDATE transactions SET to_user_id = NULL WHERE to_user_id = $id");
        
        // Now safely delete the user
        $conn->query("DELETE FROM users WHERE id = $id");
        echo "<script>window.location.href='index.php';</script>";
    }
}

$jCount = $conn->query("SELECT count(*) as c FROM jewellery")->fetch_assoc()['c'];
$uCount = $conn->query("SELECT count(*) as c FROM users")->fetch_assoc()['c'];
?>
<div class="dashboard-header">
    <h2>Super Admin Dashboard</h2>
</div>
<div class="grid-2" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <h3>Total Assets Minted</h3>
        <div class="value"><?php echo $jCount; ?></div>
    </div>
    <div class="stat-card">
        <h3>Registered Users</h3>
        <div class="value"><?php echo $uCount; ?></div>
    </div>
</div>
<div class="grid-2">
    <div class="card">
        <h3 style="color: var(--primary-gold); margin-bottom: 1rem;">Quick Actions</h3>
        <ul style="list-style: none;">
            <li style="margin-bottom: 1rem;"><a href="add_jewellery.php" class="btn btn-primary" style="width: 100%; text-align: center;">Mint New Jewellery</a></li>
            <li style="margin-bottom: 1rem;"><a href="manage_users.php" class="btn btn-outline" style="width: 100%; text-align: center;">Manage Users</a></li>
            <li style="margin-bottom: 1rem;"><a href="generate_qr.php" class="btn btn-outline" style="width: 100%; text-align: center;">QR Generator Tool</a></li>
            <li style="margin-bottom: 1rem;"><a href="view_reports.php" class="btn btn-outline" style="width: 100%; text-align: center;">View Customer Reports</a></li>
        </ul>
    </div>
    <div class="card">
        <h3 style="color: var(--primary-gold); margin-bottom: 1rem;">Recent Activity</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM jewellery ORDER BY id DESC LIMIT 5");
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>#" . $row['token_id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>
                            <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this product?\");'>
                                <input type='hidden' name='delete_jewellery' value='".$row['id']."'>
                                <button type='submit' class='btn' style='background: #ff4444; color: white; padding: 5px 10px; font-size: 12px;'>Delete</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-top: 2rem;">
    <div class="card">
        <h3 style="color: var(--primary-gold); margin-bottom: 1rem;">Administrators</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM admins ORDER BY id DESC");
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card">
        <h3 style="color: var(--primary-gold); margin-bottom: 1rem;">Users & Manufacturers</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC LIMIT 10");
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td><span style='text-transform: capitalize;'>" . htmlspecialchars($row['role']) . "</span></td>";
                        echo "<td>
                            <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to remove this user?\");'>
                                <input type='hidden' name='delete_user' value='".$row['id']."'>
                                <button type='submit' class='btn' style='background: #ff4444; color: white; padding: 5px 10px; font-size: 12px;'>Remove</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
