<?php
/**
 * Automated Off-Chain Logger
 * Helper function to log activities in the activity_logs table
 */
function log_action($conn, $user_id, $role, $action, $description) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Convert false, null or empty values appropriately for binding
    $userIdVal = $user_id ? (int)$user_id : null;
    $roleVal = $role ?: 'system';
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, role, action, description, ip_address) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("issss", $userIdVal, $roleVal, $action, $description, $ip_address);
        $stmt->execute();
        $stmt->close();
    } else {
        // Fallback for errors, could write to a file or error log
        error_log("Failed to prepare statement for activity logging: " . $conn->error);
    }
}
?>
