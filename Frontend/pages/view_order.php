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
    <style>
        .main-content {
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .order-details {
            background-color: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .order-details p {
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-details strong {
            font-weight: 600;
            color: #555;
            min-width: 150px;
        }
        .status {
            font-weight: bold;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            text-transform: capitalize;
        }
        .status-pending { background-color: #ffeeba; color: #856404; }
        .status-processing { background-color: #b8daff; color: #004085; }
        .status-completed { background-color: #c3e6cb; color: #155724; }
        .status-cancelled { background-color: #f5c6cb; color: #721c24; }
        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.5rem 1rem;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
        @media (max-width: 768px) {
            .main-content {
                margin: 1rem;
                padding: 1rem;
            }
            .order-details p {
                flex-direction: column;
                align-items: flex-start;
            }
            .order-details strong {
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body>
<main>
    <div class="main-content"> 
        <h1>Order Details</h1>
        <?php if ($order): ?>
            <div class="order-details">
                <p><strong>Order ID:</strong> <span><?= htmlspecialchars($order['order_id']) ?></span></p>
                <p><strong>Customer Name:</strong> <span><?= htmlspecialchars($order['full_name']) ?></span></p>
                <p><strong>Order Date:</strong> <span><?= date('F j, Y, g:i a', strtotime($order['order_date'])) ?></span></p>
                <p><strong>Total Amount:</strong> <span>$<?= number_format($order['total_amount'], 2) ?></span></p>
                <p>
                    <strong>Status:</strong> 
                    <span class="status status-<?= strtolower($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span>
                </p>
            </div>
        <?php else: ?>
            <p>Order not found.</p>
        <?php endif; ?>
        <a href="manage_orders.php" class="back-link">Back to Manage Orders</a>
    </div>
</main>
</body>
</html>