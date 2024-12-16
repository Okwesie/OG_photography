<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection file
require_once 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the admin
renderSidebar($_SESSION['role']);

// Get order_id from the URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Prepare and execute the query to fetch order details
    $stmt = $conn->prepare("SELECT o.order_id, o.user_id, o.order_date, o.total_amount, o.status, u.full_name 
                             FROM orders_og o 
                             JOIN users_og u ON o.user_id = u.user_id 
                             WHERE o.order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
} else {
    // Redirect if no order_id is provided
    header("Location: manage_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order</title>
    <link rel="stylesheet" href="admin_global.css">
</head>
<body>
<main>
<div class="main-content"> 
    <h1>Order Details</h1>
    <?php if ($order): ?>
        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
        <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
        <p><strong>Total Amount:</strong> <?= htmlspecialchars($order['total_amount']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
    <?php else: ?>
        <p>Order not found.</p>
    <?php endif; ?>
    <a href="manage_orders.php">Back to Manage Orders</a>
    </div>
</main>

</body>
</html> 