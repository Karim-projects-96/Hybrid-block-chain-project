<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
require_once '../includes/logger.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manufacturer') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token_id = rand(1000, 99999);
    $name = $conn->real_escape_string($_POST['product_name']);
    $category = $conn->real_escape_string($_POST['category']);
    $weight = (float)$_POST['weight'];
    $purity = $conn->real_escape_string($_POST['purity']);
    $manufacturer_id = $user_id; 
    
    $image_url = '';
    if (isset($_FILES['offchain_data']) && $_FILES['offchain_data']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = pathinfo($_FILES["offchain_data"]["name"], PATHINFO_EXTENSION);
        $new_filename = "token_" . $token_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["offchain_data"]["tmp_name"], $target_file)) {
            $image_url = "uploads/" . $new_filename;
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }

    if (!isset($error)) {
        $hash = isset($_POST['real_blockchain_hash']) ? $conn->real_escape_string($_POST['real_blockchain_hash']) : "0x" . hash('sha256', $name . time());
        
        $qr_code = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode("http://localhost/GitHub/Hybrid block chain project/verify.php?token_id=$token_id");

        $sql = "INSERT INTO jewellery (token_id, product_name, category, weight, purity, manufacturer_id, current_owner_id, status, blockchain_hash, qr_code, image_url) 
                VALUES ($token_id, '$name', '$category', $weight, '$purity', $manufacturer_id, $manufacturer_id, 'manufactured', '$hash', '$qr_code', '$image_url')";
                
        if ($conn->query($sql) === TRUE) {
            $jewellery_id = $conn->insert_id;
            log_action($conn, $user_id, 'manufacturer', 'mint_jewellery', "Minted new jewellery: $name (Token ID: $token_id)");
            $success = "Jewellery Minted Successfully! Token ID: $token_id";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="color: var(--primary-gold); margin-bottom: 2rem;">Mint New Jewellery</h2>
    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
    <form id="mintForm" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="product_name" class="form-control" required>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" class="form-control" placeholder="e.g. Ring, Necklace">
            </div>
            <div class="form-group">
                <label>Weight (g)</label>
                <input type="number" step="0.01" name="weight" class="form-control" required>
            </div>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>Purity</label>
                <input type="text" name="purity" class="form-control" placeholder="e.g. 24K, 22K">
            </div>
            <div class="form-group">
                <label>Off-Chain Data (Design/Image)</label>
                <input type="file" name="offchain_data" class="form-control" accept="image/*,.pdf,.doc,.docx">
            </div>
        </div>
        <button type="submit" class="btn btn-gold" style="width: 100%; margin-top: 1rem;">Mint on Blockchain & Save</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>
