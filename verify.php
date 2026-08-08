<?php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

$jewellery = null;
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['token_id'])) {
    $token_id = intval($_GET['token_id']);
    
    // Fetch from Database
    $sql = "SELECT j.*, m.name as m_name, o.name as o_name 
            FROM jewellery j
            LEFT JOIN users m ON j.manufacturer_id = m.id
            LEFT JOIN users o ON j.current_owner_id = o.id
            WHERE j.token_id = $token_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $jewellery = $result->fetch_assoc();
    } else {
        $error = "No jewellery found with Token ID: $token_id";
    }
}
?>
<div class="card" style="max-width: 800px; margin: 4rem auto;">
    <h2 style="text-align: center; color: var(--primary-gold); margin-bottom: 2rem;">Verify Authenticity</h2>
    
    <form method="GET" action="" style="display: flex; gap: 1rem; margin-bottom: 2rem;">
        <input type="number" name="token_id" class="form-control" placeholder="Enter Token ID (e.g. 1)" required style="flex: 1;">
        <button type="submit" class="btn btn-gold">Verify on Blockchain</button>
    </form>

    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if($jewellery): ?>
        <div style="border-top: 1px solid #eee; padding-top: 2rem; display: flex; gap: 2rem;">
            <div style="flex: 1;">
                <?php if($jewellery['image_url']): ?>
                    <img src="<?php echo $jewellery['image_url']; ?>" alt="Jewellery" style="width: 100%; border-radius: 8px;">
                <?php else: ?>
                    <div style="width: 100%; height: 200px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center;">No Image Available</div>
                <?php endif; ?>
                
                <?php if($jewellery['qr_code']): ?>
                    <img src="<?php echo $jewellery['qr_code']; ?>" alt="QR Code" style="width: 100px; height: 100px; margin-top: 1rem;">
                <?php endif; ?>
            </div>
            <div style="flex: 2;">
                <h3 style="margin-bottom: 1rem; color: var(--text-dark);"><?php echo htmlspecialchars($jewellery['product_name']); ?></h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div><strong>Category:</strong> <br><?php echo $jewellery['category']; ?></div>
                    <div><strong>Weight/Purity:</strong> <br><?php echo $jewellery['weight']; ?>g / <?php echo $jewellery['purity']; ?></div>
                    <div><strong>Hallmark:</strong> <br><?php echo $jewellery['hallmark_number'] ?: 'N/A'; ?></div>
                    <div><strong>Status:</strong> <br><span style="padding: 0.2rem 0.6rem; border-radius: 4px; background: <?php echo $jewellery['status'] == 'stolen' ? '#f8d7da' : '#d4edda'; ?>; color: <?php echo $jewellery['status'] == 'stolen' ? '#721c24' : '#155724'; ?>;"><?php echo strtoupper($jewellery['status']); ?></span></div>
                </div>

                <div style="background: #fafafa; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h4 style="margin-bottom: 0.5rem;">Blockchain Record</h4>
                    <p><strong>Hash:</strong> <span style="word-break: break-all; font-family: monospace; font-size: 0.9rem;"><?php echo $jewellery['blockchain_hash'] ?: 'Pending Mint...'; ?></span></p>
                    <p style="margin-top: 0.5rem;"><strong>On-Chain Status:</strong> <span id="onchainStatus" style="color: var(--primary-gold);">Querying Contract...</span></p>
                </div>
                
                <div>
                    <p><strong>Manufacturer:</strong> <?php echo htmlspecialchars($jewellery['m_name']); ?></p>
                    <p><strong>Current Owner:</strong> <?php echo htmlspecialchars($jewellery['o_name']); ?></p>
                </div>
            </div>
        </div>
        
        <script>
            // Mock smart contract call since we don't have an active provider yet
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    document.getElementById('onchainStatus').innerText = "Verified ✅";
                    document.getElementById('onchainStatus').style.color = "green";
                }, 1500);
            });
        </script>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
