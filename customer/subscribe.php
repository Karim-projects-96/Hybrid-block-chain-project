<?php
session_start();
require_once "../includes/db_connect.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['shop_id'])) {
    die("Shop ID is required.");
}
$shop_id = intval($_GET['shop_id']);

$sql = "SELECT name FROM users WHERE id = ? AND role = 'shop'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Invalid shop.");
}
$shop = $result->fetch_assoc();

include "../includes/header.php";
?>
<div class="container" style="padding: 2rem; max-width: 600px; margin: 0 auto;">
    <div class="card">
        <h2>Subscribe to <?php echo htmlspecialchars($shop['name']); ?></h2>
        <p>Premium Membership: <strong>$10.00 / month</strong></p>
        <hr style="margin: 1rem 0;">
        <form action="process_subscription.php" method="POST">
            <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Card Number (Mock Payment)</label>
                <input type="text" value="4242 4242 4242 4242" class="form-control" style="width: 100%; padding: 0.5rem;" readonly>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Pay $10.00 & Subscribe</button>
        </form>
    </div>
</div>
<?php include "../includes/footer.php"; ?>
