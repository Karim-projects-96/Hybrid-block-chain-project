<?php
session_start();
require_once "../includes/db_connect.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}
include "../includes/header.php";

$sql = "SELECT id, name, email, address FROM users WHERE role = 'shop'";
$result = $conn->query($sql);
?>
<div class="container" style="padding: 2rem;">
    <h2>Browse Shops</h2>
    <p>Subscribe to a shop to become a premium member.</p>
    <div class="grid-3">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem;"><?php echo htmlspecialchars($row['address']); ?></p>
                    <hr style="margin: 1rem 0; border: none; border-top: 1px solid #ddd;">
                    <a href="subscribe.php?shop_id=<?php echo $row['id']; ?>" class="btn btn-primary" style="display: block; text-align: center;">Subscribe ($10/month)</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No shops found.</p>
        <?php endif; ?>
    </div>
</div>
<?php include "../includes/footer.php"; ?>
