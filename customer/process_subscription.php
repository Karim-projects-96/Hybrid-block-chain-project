<?php
session_start();
require_once "../includes/db_connect.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_id = intval($_POST['shop_id']);
    $customer_id = $_SESSION['user_id'];
    $plan_name = "Premium Gold";
    $amount = 10.00;
    
    // Calculate end date (30 days from now)
    $end_date = date('Y-m-d H:i:s', strtotime('+30 days'));

    $sql = "INSERT INTO subscriptions (customer_id, shop_id, plan_name, amount, status, end_date) VALUES (?, ?, ?, ?, 'active', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisds", $customer_id, $shop_id, $plan_name, $amount, $end_date);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Membership purchased successfully!";
        header("Location: dashboard.php");
    } else {
        $_SESSION['error_msg'] = "Error purchasing membership: " . $conn->error;
        header("Location: shops.php");
    }
    exit();
}
header("Location: shops.php");
