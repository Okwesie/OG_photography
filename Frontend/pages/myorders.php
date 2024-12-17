<?php
session_start();
require_once 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the user
renderSidebar($_SESSION['role']);

// Check if the user is logged in 
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = "My Orders";

$user_id = $_SESSION['user_id'];

// Fetch all orders with detailed items
$orders_query = "
    SELECT 
        o.order_id, 
        o.total_amount, 
        o.order_date, 
        o.payment_method,
        o.status,
        GROUP_CONCAT(
            CONCAT(
                p.title, 
                ' (', 
                oi.quantity, 
                'x ', 
                COALESCE(oi.print_size, 'Digital'), 
                ' - $', 
                FORMAT(oi.item_price, 2)
            ) 
            SEPARATOR ' | '
        ) AS order_items
    FROM orders_og o
    JOIN order_items_og oi ON o.order_id = oi.order_id
    JOIN photos_og p ON oi.photo_id = p.photo_id
    WHERE o.user_id = ? AND o.status NOT IN ('paid', 'cancelled', 'completed')
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
";
$orders_stmt = $conn->prepare($orders_query);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();

// Process order cancellation
if (isset($_GET['cancel']) && isset($_GET['order_id'])) {
    $cancel_order_id = intval($_GET['order_id']);
    
    // Update order status to cancelled if not already paid/shipped
    $cancel_query = "UPDATE orders_og SET status = 'cancelled' WHERE order_id = ? AND user_id = ? AND status IN ('pending')";
    $cancel_stmt = $conn->prepare($cancel_query);
    $cancel_stmt->bind_param("ii", $cancel_order_id, $user_id);
    $cancel_stmt->execute();
    
    if ($cancel_stmt->affected_rows > 0) {
        $success_message = "Order successfully cancelled.";
    } else {
        $error_message = "Unable to cancel order. It may have already been processed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="user_global.css">
</head>
<body>
<div class="dashboard-container">


    <div class="main-content">
        <h1>My Orders</h1>

        <!-- Button to create a new order -->
        <div class="create-order">
            <a href="create_order.php" class="btn btn-primary">Create New Order</a>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($orders_result->num_rows == 0): ?>
            <div class="no-orders">
                <p>You have no orders yet. <a href="create_order.php">Create an Order</a></p>
            </div>
        <?php else: ?>
            <div class="orders-container">
                <?php while ($order = $orders_result->fetch_assoc()): ?>
                    <div class="order-card <?php echo strtolower($order['status']); ?>">
                        <div class="order-header">
                            <h3>Order #<?php echo $order['order_id']; ?></h3>
                            <span class="order-status">
                                <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                            </span>
                        </div>
                        <div class="order-details">
                            <p>
                                <strong>Date:</strong> 
                                <?php echo date('F d, Y H:i', strtotime($order['order_date'])); ?>
                            </p>
                            <p>
                                <strong>Payment Method:</strong> 
                                <?php echo str_replace('_', ' ', ucfirst(htmlspecialchars($order['payment_method']))); ?>
                            </p>
                            <div class="order-items">
                                <strong>Items:</strong>
                                <p><?php echo htmlspecialchars($order['order_items']); ?></p>
                            </div>
                            <p class="order-total">
                                <strong>Total Amount:</strong> 
                                $<?php echo number_format($order['total_amount'], 2); ?>
                            </p>
                        </div>
                        <div class="order-actions">
                            <?php if (in_array($order['status'], ['pending'])): ?>
                                <a href="?cancel=1&order_id=<?php echo $order['order_id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to cancel this order?');">
                                    Cancel Order
                                </a>
                            <?php endif; ?>
                            <?php if ($order['status'] == 'completed'): ?>
                                <a href="create_order.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-secondary">
                                    Reorder
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$orders_stmt->close();
$conn->close();
?>
</body>
</html>