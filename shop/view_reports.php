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

<div class="dashboard-header">
    <h2>Reports Against Me</h2>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <h3 style="color: var(--primary-gold); margin-bottom: 1rem;">Submitted Reports</h3>
    <div class="table-responsive">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Report ID</th>
                    <th style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Reporter</th>
                    <th style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Subject</th>
                    <th style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Description</th>
                    <th style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Date</th>
                    <th style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT r.id, r.subject, r.description, r.status, r.created_at, 
                                 c.name as reporter_name, c.role as reporter_role
                          FROM reports r 
                          JOIN users c ON r.customer_id = c.id 
                          WHERE r.shop_id = $user_id
                          ORDER BY r.created_at DESC";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td style='padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);'>#" . $row['id'] . "</td>";
                        echo "<td style='padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);'>" . htmlspecialchars($row['reporter_name']) . " <br><small style='color: var(--primary-gold);'>(" . ucfirst($row['reporter_role']) . ")</small></td>";
                        echo "<td style='padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);'>" . htmlspecialchars($row['subject']) . "</td>";
                        echo "<td style='padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05); max-width: 250px; overflow: hidden; text-overflow: ellipsis;'>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td style='padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);'>" . date('Y-m-d H:i', strtotime($row['created_at'])) . "</td>";
                        
                        $status_color = $row['status'] == 'resolved' ? '#d4edda' : '#fff3cd';
                        $status_text_color = $row['status'] == 'resolved' ? '#155724' : '#856404';
                        
                        echo "<td style='padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);'><span style='padding: 0.3rem 0.6rem; border-radius: 4px; background: {$status_color}; color: {$status_text_color};'>" . ucfirst($row['status']) . "</span></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='padding: 1rem; text-align: center;'>No reports found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
