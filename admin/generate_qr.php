<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../includes/header.php';
?>
<div class="card" style="max-width: 500px; margin: 0 auto; text-align: center;">
    <h2 style="color: var(--primary-gold); margin-bottom: 2rem;">QR Code Generator</h2>
    <p style="margin-bottom: 2rem; color: var(--text-muted);">Enter a Token ID to generate a scannable QR code for physical tagging.</p>
    
    <div class="form-group">
        <input type="number" id="tokenIdInput" class="form-control" placeholder="Enter Token ID" style="text-align: center; font-size: 1.2rem;">
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
    if(!id) return alert('Enter a Token ID');
    
    const url = encodeURIComponent(`http://localhost/GitHub/Hybrid block chain project/verify.php?token_id=${id}`);
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${url}`;
    
    document.getElementById('qrImage').src = qrUrl;
    document.getElementById('qrResult').style.display = 'block';
}
</script>
<?php require_once '../includes/footer.php'; ?>
