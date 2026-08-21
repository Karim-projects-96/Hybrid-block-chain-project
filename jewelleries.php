<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db_connect.php';
require_once 'includes/header.php';
?>

<!-- Products Section -->
<section style="padding: 4rem 2rem; background-color: var(--bg-light); min-height: calc(100vh - 200px);">
    <div style="max-width: 1200px; margin: 0 auto;">
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
                    if (!empty($row['image_url'])) {
                        echo '<img src="' . htmlspecialchars($row['image_url']) . '" alt="Jewellery" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">';
                    } else {
                        echo '<div style="width: 100%; height: 200px; background: #eee; border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; color: var(--text-muted);">No Image Available</div>';
                    }
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
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
