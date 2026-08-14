<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token_id = intval($_POST['token_id']);
    $action = $_POST['action']; // 'stolen' or 'safe'

    // Check if token is owned by user
    $check_item = $conn->query("SELECT id FROM jewellery WHERE token_id = $token_id AND current_owner_id = $user_id");
    
    if ($check_item->num_rows > 0) {
        $status = ($action === 'stolen') ? 'stolen' : 'sold'; // using 'sold' or default safe state as a recovery from stolen
        $status_sql = ($action === 'stolen') ? 'stolen' : 'manufactured'; // simplified

        $sql = "UPDATE jewellery SET status = '$status_sql' WHERE token_id = $token_id";
        if ($conn->query($sql) === TRUE) {
            $success = "Item status updated on Database.";
            // Blockchain update
            
        } else {
            $error = "Error updating status.";
        }
    } else {
        $error = "You do not own an item with that Token ID.";
    }
}
?>
<div class="card" style="max-width: 500px; margin: 2rem auto;">
    <h2 style="color: var(--primary-gold); margin-bottom: 2rem;">Report Stolen Asset</h2>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Flagging an item as stolen prevents any future ownership transfers on the blockchain.</p>
    
    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Select Item</label>
            <select name="token_id" class="form-control" required>
                <option value="">-- Choose Item --</option>
                <?php
                $res = $conn->query("SELECT token_id, product_name, status FROM jewellery WHERE current_owner_id = $user_id");
                while($row = $res->fetch_assoc()) {
                    echo "<option value='{$row['token_id']}'>#{$row['token_id']} - {$row['product_name']} ({$row['status']})</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Action</label>
            <select name="action" class="form-control" required>
                <option value="stolen">Mark as STOLEN</option>
                <option value="safe">Mark as SAFE (Recovered)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Update Status & Sign</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>
