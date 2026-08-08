<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shop') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
require_once '../includes/header.php';
?>
<div class="card" style="max-width: 500px; margin: 0 auto; text-align: center;">
    <h2 style="color: var(--primary-gold); margin-bottom: 2rem;">QR Code Generator</h2>
    <p style="margin-bottom: 2rem; color: var(--text-muted);">Select an item from your inventory to generate a scannable QR code for physical tagging.</p>
    
    <div class="form-group">
        <select id="tokenIdInput" class="form-control" style="text-align: center; font-size: 1.1rem;">
            <option value="">-- Choose Item --</option>
            <?php
            $res = $conn->query("SELECT token_id, product_name FROM jewellery WHERE current_owner_id = $user_id AND status != 'stolen'");
            while($row = $res->fetch_assoc()) {
                echo "<option value='{$row['token_id']}'>#{$row['token_id']} - {$row['product_name']}</option>";
            }
            ?>
        </select>
    </div>
    <button onclick="generateQR()" class="btn btn-primary" style="width: 100%;">Generate QR</button>
    
    <div id="qrResult" style="margin-top: 2rem; display: none;">
        <img id="qrImage" src="" alt="QR Code" style="border: 4px solid var(--primary-gold); border-radius: 8px;">
        <br><br>
        <button onclick="window.print()" class="btn btn-outline">Print Tag</button>
    </div>
</div>

<script>
function generateQR() {
    const id = document.getElementById('tokenIdInput').value;
    if(!id) return alert('Please select an item');
    
    const url = encodeURIComponent(`http://localhost/GitHub/Hybrid block chain project/verify.php?token_id=${id}`);
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${url}`;
    
    document.getElementById('qrImage').src = qrUrl;
    document.getElementById('qrResult').style.display = 'block';
}
</script>
<?php require_once '../includes/footer.php'; ?>
