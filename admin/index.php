<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../includes/header.php';

$jCount = $conn->query("SELECT count(*) as c FROM jewellery")->fetch_assoc()['c'];
$uCount = $conn->query("SELECT count(*) as c FROM users")->fetch_assoc()['c'];
?>
<div class="dashboard-header">
    <h2>Super Admin Dashboard</h2>
</div>
<div class="grid-3" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <h3>Total Assets Minted</h3>
        <div class="value"><?php echo $jCount; ?></div>
    </div>
    <div class="stat-card">
        <h3>Registered Users</h3>
        <div class="value"><?php echo $uCount; ?></div>
    </div>
    <div class="stat-card">
        <h3>Ethereum Network</h3>
        <div class="value" style="color: #4CAF50; font-size: 1.5rem;">Connected (MetaMask)</div>
    </div>
</div>
<div class="grid-2">
    <div class="card">
        <h3 style="color: var(--primary-gold); margin-bottom: 1rem;">Quick Actions</h3>
        <ul style="list-style: none;">
            <li style="margin-bottom: 1rem;"><a href="add_jewellery.php" class="btn btn-primary" style="width: 100%; text-align: center;">Mint New Jewellery</a></li>
            <li style="margin-bottom: 1rem;"><a href="manage_users.php" class="btn btn-outline" style="width: 100%; text-align: center;">Manage Users</a></li>
            <li style="margin-bottom: 1rem;"><a href="generate_qr.php" class="btn btn-outline" style="width: 100%; text-align: center;">QR Generator Tool</a></li>
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
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
