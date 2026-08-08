<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $id AND role != 'admin'");
    header("Location: manage_users.php");
    exit();
}

require_once '../includes/header.php';
?>
<div class="dashboard-header">
    <h2>Manage Users</h2>
</div>
<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>#" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td><span style='padding: 0.2rem 0.5rem; background: #eee; border-radius: 4px; text-transform: capitalize;'>" . $row['role'] . "</span></td>";
                    echo "<td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>";
                    echo "<td>";
                    if($row['role'] !== 'admin') {
                        echo "<a href='?delete=" . $row['id'] . "' class='btn btn-outline' style='padding: 0.3rem 0.8rem; font-size: 0.8rem;' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
