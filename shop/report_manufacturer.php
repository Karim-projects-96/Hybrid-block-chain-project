<?php
$base_url = "/GitHub/Hybrid block chain project";
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shop') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reported_id = intval($_POST['reported_id']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $description = $conn->real_escape_string($_POST['description']);

    if (empty($reported_id) || empty($subject) || empty($description)) {
        $error = "All fields are required.";
    } else {
        // customer_id serves as the reporter, shop_id serves as the reported party
        $stmt = $conn->prepare("INSERT INTO reports (customer_id, shop_id, subject, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $reported_id, $subject, $description);
        
        if ($stmt->execute()) {
            $success = "Report submitted successfully.";
        } else {
            $error = "Error submitting report.";
        }
        $stmt->close();
    }
}

require_once '../includes/header.php';
?>

<div class="card" style="max-width: 600px; margin: 2rem auto;">
    <h2 style="color: var(--primary-gold); margin-bottom: 1.5rem; text-align: center;">Report a Manufacturer</h2>
    
    <?php if($error): ?>
        <div class="alert alert-danger" style="color: red; margin-bottom: 1rem; text-align: center;"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="alert alert-success" style="color: green; margin-bottom: 1rem; text-align: center;"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="reported_id" style="display: block; margin-bottom: 0.5rem; color: #a0a0a0;">Select Manufacturer</label>
            <select name="reported_id" id="reported_id" class="form-control" required>
                <option value="">-- Select a Manufacturer --</option>
                <?php
                $result = $conn->query("SELECT id, name FROM users WHERE role = 'manufacturer' ORDER BY name ASC");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="subject" style="display: block; margin-bottom: 0.5rem; color: #a0a0a0;">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control" required>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label for="description" style="display: block; margin-bottom: 0.5rem; color: #a0a0a0;">Description</label>
            <textarea name="description" id="description" rows="5" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; border: none; border-radius: 8px; cursor: pointer;">Submit Report</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
