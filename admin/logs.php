<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/header.php';

// Fetch logs. Since admins and users are in different tables, we handle user display carefully
$logs_query = "SELECT l.*, u.name as user_name, u.email as user_email 
               FROM activity_logs l 
               LEFT JOIN users u ON l.user_id = u.id AND l.role != 'admin'
               ORDER BY l.created_at DESC LIMIT 100";
$logs = $conn->query($logs_query);
?>

<div style="padding: 2rem;">
    <h1 style="color: var(--primary-gold); margin-bottom: 2rem;">System Activity Logs</h1>
    
    <div class="card" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid var(--primary-gold);">
                    <th style="padding: 1rem;">Time</th>
                    <th style="padding: 1rem;">Role</th>
                    <th style="padding: 1rem;">User & Action</th>
                    <th style="padding: 1rem;">Description</th>
                    <th style="padding: 1rem;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($logs && $logs->num_rows > 0): ?>
                    <?php while($row = $logs->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.9em;">
                                <?php echo htmlspecialchars($row['created_at']); ?>
                            </td>
                            <td style="padding: 1rem; font-weight: bold; text-transform: capitalize; color: var(--primary-gold);">
                                <?php echo htmlspecialchars($row['role']); ?>
                            </td>
                            <td style="padding: 1rem;">
                                <strong><?php echo htmlspecialchars($row['action']); ?></strong><br>
                                <span style="font-size: 0.85em; color: var(--text-muted);">
                                <?php 
                                    if ($row['user_name']) {
                                        echo htmlspecialchars($row['user_name']) . " (" . htmlspecialchars($row['user_email']) . ")";
                                    } elseif ($row['role'] == 'admin') {
                                        echo "Admin User (ID: " . $row['user_id'] . ")";
                                    } else {
                                        echo "Guest / System";
                                    }
                                ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.9em;">
                                <?php echo htmlspecialchars($row['ip_address']); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 2rem; text-align: center;">No activity logs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
