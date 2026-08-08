<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manufacturer') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
require_once '../includes/header.php';

$jCount = $conn->query("SELECT count(*) as c FROM jewellery WHERE manufacturer_id = $user_id")->fetch_assoc()['c'];
?>
<div class="dashboard-header">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> (Manufacturer)</h2>
</div>
<div class="grid-3" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <h3>Total Minted Items</h3>
        <div class="value"><?php echo $jCount; ?> Items</div>
    </div>
    <div class="stat-card">
        <h3>Account Type</h3>
        <div class="value" style="text-transform: capitalize; font-size: 1.5rem; color: var(--primary-gold);"><?php echo $_SESSION['role']; ?></div>
    </div>
</div>
<div class="grid-2">
    <div class="card">
        <h3 style="color: var(--primary-gold); margin-bottom: 1rem;">Manufacturer Actions</h3>
        <ul style="list-style: none;">
            <li style="margin-bottom: 1rem;"><a href="add_jewellery.php" class="btn btn-primary" style="width: 100%; text-align: center;">Mint New Jewellery</a></li>
            <li style="margin-bottom: 1rem;"><a href="transfer_ownership.php" class="btn btn-outline" style="width: 100%; text-align: center;">Transfer to Shopkeeper</a></li>
        </ul>
    </div>
    <div class="card">
        <h3 style="color: var(--primary-gold); margin-bottom: 1rem;">My Minted Assets</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM jewellery WHERE current_owner_id = $user_id ORDER BY id DESC");
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>#" . $row['token_id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                        echo "<td><span style='padding:0.2rem; border-radius:4px; background: " . ($row['status']=='stolen'?'#f8d7da':'#d4edda') . "'>" . $row['status'] . "</span></td>";
                        echo "<td><a href='../verify.php?token_id=" . $row['token_id'] . "' class='btn btn-outline' style='padding: 0.3rem 0.6rem; font-size: 0.8rem;'>View</a></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
