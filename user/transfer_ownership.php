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
    $new_owner_email = $conn->real_escape_string($_POST['new_owner_email']);

    // Check if token is owned by user
    $check_item = $conn->query("SELECT id, status FROM jewellery WHERE token_id = $token_id AND current_owner_id = $user_id");
    
    if ($check_item->num_rows > 0) {
        $item = $check_item->fetch_assoc();
        if ($item['status'] == 'stolen') {
            $error = "Cannot transfer a stolen item.";
        } else {
            // Check if new owner exists
            $check_user = $conn->query("SELECT id FROM users WHERE email = '$new_owner_email'");
            if ($check_user->num_rows > 0) {
                $new_owner_id = $check_user->fetch_assoc()['id'];
                
                // Update ownership
                $sql = "UPDATE jewellery SET current_owner_id = $new_owner_id WHERE token_id = $token_id";
                if ($conn->query($sql) === TRUE) {
                    $tx_hash = md5($token_id . $user_id . $new_owner_id . time());
                    $get_j = $conn->query("SELECT id FROM jewellery WHERE token_id = $token_id")->fetch_assoc();
                    $jid = $get_j['id'];
                    $conn->query("INSERT INTO transactions (jewellery_id, from_user_id, to_user_id, tx_hash) VALUES ($jid, $user_id, $new_owner_id, '$tx_hash')");
                    $success = "Ownership transferred successfully!";
                    
                } else {
                    $error = "Error updating ownership.";
                }
            } else {
                $error = "New owner email not found. They must register first.";
            }
        }
    } else {
        $error = "You do not own an item with that Token ID.";
    }
}
?>
<div class="card" style="max-width: 500px; margin: 2rem auto;">
    <h2 style="color: var(--primary-gold); margin-bottom: 2rem;">Transfer Ownership</h2>
    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Select Item to Transfer</label>
            <select name="token_id" class="form-control" required>
                <option value="">-- Choose Item --</option>
                <?php
                $res = $conn->query("SELECT token_id, product_name FROM jewellery WHERE current_owner_id = $user_id AND status != 'stolen'");
                while($row = $res->fetch_assoc()) {
                    echo "<option value='{$row['token_id']}'>#{$row['token_id']} - {$row['product_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>New Owner Email</label>
            <input type="email" name="new_owner_email" class="form-control" required placeholder="Enter buyer's email">
        </div>
        <button type="submit" class="btn btn-gold" style="width: 100%; margin-top: 1rem;">Initiate Transfer & Sign</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>
