<?php 
require_once 'includes/db_connect.php';
require_once 'includes/header.php'; 
?>

<!-- Hero Section -->
<section class="hero">
    <h1>The Future of Jewellery Provenance</h1>
    <p>Immutable, transparent, and secure authenticity tracking powered by Ethereum Blockchain.</p>
    <div style="margin-top: 2rem;">
        <a href="verify.php" class="btn btn-gold" style="margin-right: 1rem;">Verify Authenticity</a>
        <a href="register.php" class="btn btn-outline">Join as Partner</a>
    </div>
</section>

<!-- Features Section -->
<section style="padding: 4rem 0;">
    <h2 style="text-align: center; color: var(--primary-gold); margin-bottom: 3rem;">Why LuxBlock?</h2>
    <div class="grid-3">
        <div class="card" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">💎</div>
            <h3>Blockchain Security</h3>
            <p style="color: var(--text-muted); margin-top: 1rem;">Every piece of jewellery is minted as a unique digital asset, making forgery impossible.</p>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
            <h3>Instant Verification</h3>
            <p style="color: var(--text-muted); margin-top: 1rem;">Scan the QR code to instantly view the entire lifecycle, original manufacturer, and ownership history.</p>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🤝</div>
            <h3>Trusted Transfers</h3>
            <p style="color: var(--text-muted); margin-top: 1rem;">Seamlessly transfer ownership upon sale, updating the decentralized ledger automatically.</p>
        </div>
    </div>
</section>

<!-- Products Section -->
<section style="padding: 4rem 2rem; background-color: var(--bg-light);">
    <h2 style="text-align: center; color: var(--primary-gold); margin-bottom: 3rem;">All Registered Jewellery</h2>
    <div class="grid-3">
        <?php
        $sql = "SELECT j.*, u.name AS owner_name, u.address AS physical_address
                FROM jewellery j 
                LEFT JOIN users u ON j.current_owner_id = u.id 
                ORDER BY j.created_at DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $statusColor = 'var(--text-light)';
                if ($row['status'] === 'stolen') $statusColor = '#ff4444';
                elseif ($row['status'] === 'in_shop' || $row['status'] === 'available') $statusColor = '#00C851';
                elseif ($row['status'] === 'manufactured') $statusColor = '#33b5e5';
                
                echo '<div class="card" style="text-align: left;">';
                echo '<h3 style="color: var(--primary-gold); margin-bottom: 0.5rem;">' . htmlspecialchars($row['product_name'] ?? 'Unnamed Product') . '</h3>';
                echo '<p style="margin-bottom: 0.5rem;"><strong>Status:</strong> <span style="color: '.$statusColor.'; font-weight: bold; text-transform: uppercase;">' . htmlspecialchars($row['status'] ?? 'Unknown') . '</span></p>';
                echo '<p style="margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;"><strong>Shopkeeper / Owner:</strong> ' . (empty($row['owner_name']) ? 'Unknown' : htmlspecialchars($row['owner_name'])) . '</p>';
                echo '<p style="margin-bottom: 1.5rem; color: var(--text-muted); font-size: 0.9rem;"><strong>Physical Address:</strong> ' . (empty($row['physical_address']) ? 'Not provided' : htmlspecialchars($row['physical_address'])) . '</p>';
                echo '<a href="verify.php?token_id='.htmlspecialchars($row['token_id'] ?? '').'" class="btn btn-outline" style="width: 100%; text-align: center; display: inline-block;">View Details</a>';
                echo '</div>';
            }
        } else {
            echo '<p style="text-align: center; grid-column: span 3;">No products registered yet.</p>';
        }
        ?>
    </div>
</section>

<!-- Stats Section -->
<section style="background-color: var(--bg-black); padding: 4rem 2rem; border-radius: var(--border-radius); color: var(--text-light); text-align: center;">
    <div class="grid-4">
        <div>
            <h2 style="color: var(--primary-gold); font-size: 2.5rem;">10,000+</h2>
            <p>Assets Minted</p>
        </div>
        <div>
            <h2 style="color: var(--primary-gold); font-size: 2.5rem;">500+</h2>
            <p>Partner Shops</p>
        </div>
        <div>
            <h2 style="color: var(--primary-gold); font-size: 2.5rem;">50+</h2>
            <p>Manufacturers</p>
        </div>
        <div>
            <h2 style="color: var(--primary-gold); font-size: 2.5rem;">100%</h2>
            <p>Authenticity</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
