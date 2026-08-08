<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token_id = rand(1000, 99999);
    $name = $conn->real_escape_string($_POST['product_name']);
    $category = $conn->real_escape_string($_POST['category']);
    $weight = (float)$_POST['weight'];
    $purity = $conn->real_escape_string($_POST['purity']);
    $manufacturer_id = (int)$_POST['manufacturer_id'];
    
    // Create dummy blockchain hash for simulation if not fully integrated
    $hash = "0x" . hash('sha256', $name . time());
    
    $qr_code = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode("http://localhost/GitHub/Hybrid block chain project/verify.php?token_id=$token_id");

    $sql = "INSERT INTO jewellery (token_id, product_name, category, weight, purity, manufacturer_id, current_owner_id, status, blockchain_hash, qr_code) 
            VALUES ($token_id, '$name', '$category', $weight, '$purity', $manufacturer_id, $manufacturer_id, 'manufactured', '$hash', '$qr_code')";
            
    if ($conn->query($sql) === TRUE) {
        $success = "Jewellery Minted Successfully! Token ID: $token_id";
        // Here we would ideally trigger MetaMask to send a transaction in JS
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                alert('Please sign the MetaMask transaction to deploy to Blockchain.');
                // Simulate smart contract call
            });
        </script>";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="color: var(--primary-gold); margin-bottom: 2rem;">Mint New Jewellery</h2>
    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
    <form method="POST">
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
                <label>Assign to Manufacturer</label>
                <select name="manufacturer_id" class="form-control" required>
                    <?php
                    $m_res = $conn->query("SELECT id, name FROM users WHERE role IN ('manufacturer', 'admin')");
                    while($m = $m_res->fetch_assoc()) {
                        echo "<option value='{$m['id']}'>{$m['name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-gold" style="width: 100%; margin-top: 1rem;">Mint on Blockchain & Save</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>
