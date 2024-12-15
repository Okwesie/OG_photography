<?php
session_start();
require_once 'dbconnection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch orders from the database
$sql = "SELECT o.order_id, o.user_id, o.order_date, o.total_amount, o.status, u.full_name 
        FROM orders_og o 
        JOIN users_og u ON o.user_id = u.user_id 
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $update_sql = "UPDATE orders_og SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('si', $new_status, $order_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Order status updated successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update order status.";
    }
    header("Location: manage_orders.php");
    exit();
}

// Handle order deletion
if (isset($_GET['delete_order_id'])) {
    $order_id = $_GET['delete_order_id'];
    $delete_sql = "DELETE FROM orders_og WHERE order_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param('i', $order_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Order deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete the order.";
    }
    header("Location: manage_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Manage Orders</h1>
        
        <!-- Display success or error messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php 
                echo htmlspecialchars($_SESSION['success_message']); 
                unset($_SESSION['success_message']); 
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php 
                echo htmlspecialchars($_SESSION['error_message']); 
                unset($_SESSION['error_message']); 
                ?>
            </div>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                    <select name="status">
                                        <option value="pending" <?php if ($row['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="paid" <?php if ($row['status'] === 'paid') echo 'selected'; ?>>Paid</option>
                                        <option value="shipped" <?php if ($row['status'] === 'shipped') echo 'selected'; ?>>Shipped</option>
                                        <option value="completed" <?php if ($row['status'] === 'completed') echo 'selected'; ?>>Completed</option>
                                        <option value="cancelled" <?php if ($row['status'] === 'cancelled') echo 'selected'; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-update">Update</button>
                                </form>
                            </td>
                            <td>
                                <a href="view_order.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-view">View</a>
                                <a href="manage_orders.php?delete_order_id=<?php echo $row['order_id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>