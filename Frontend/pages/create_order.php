<?php
session_start();
require_once 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Render the sidebar for the user
renderSidebar($_SESSION['role']);

// Fetch available photos for ordering
$photos_query = "SELECT * FROM photos_og";
$photos_result = $conn->query($photos_query);

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $total_amount = 0;
    $order_items = [];

    foreach ($_POST['photos'] as $photo_id => $quantity) {
        if ($quantity > 0) {
            // Fetch photo details
            $photo_query = "SELECT * FROM photos_og WHERE photo_id = ?";
            $stmt = $conn->prepare($photo_query);
            $stmt->bind_param("i", $photo_id);
            $stmt->execute();
            $photo_result = $stmt->get_result();
            $photo = $photo_result->fetch_assoc();

            // Calculate total amount
            $item_price = $photo['is_purchasable'] ? $photo['digital_price'] : 0; // Assuming digital price for simplicity
            $total_amount += $item_price * $quantity;

            // Prepare order items
            $order_items[] = [
                'photo_id' => $photo_id,
                'quantity' => $quantity,
                'item_price' => $item_price,
            ];
        }
    }

    // Insert order into the database
    $order_query = "INSERT INTO orders_og (user_id, total_amount, payment_method, status) VALUES (?, ?, 'credit_card', 'pending')";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("id", $user_id, $total_amount);
    
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id; // Get the last inserted order ID

        // Insert order items
        foreach ($order_items as $item) {
            $order_item_query = "INSERT INTO order_items_og (order_id, photo_id, quantity, item_price) VALUES (?, ?, ?, ?)";
            $item_stmt = $conn->prepare($order_item_query);
            $item_stmt->bind_param("iiid", $order_id, $item['photo_id'], $item['quantity'], $item['item_price']);
            $item_stmt->execute();
        }

        // Redirect to payment form with order details
        header("Location: payment_form.php?order_id=$order_id&amount=$total_amount");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to create order.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <link rel="stylesheet" href="user_global.css">
</head>
<body>
<main>
<div class="main-content">
    <h1>Create Order</h1>

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

    <form action="" method="POST">
        <h2>Select Photos to Order</h2>
        <?php while ($photo = $photos_result->fetch_assoc()): ?>
            <div class="photo-item">
                <img src="<?= htmlspecialchars($photo['file_path']) ?>" alt="<?= htmlspecialchars($photo['title']) ?>" style="width: 100px;">
                <p><?= htmlspecialchars($photo['title']) ?> - $<?= number_format($photo['digital_price'], 2) ?></p>
                <label for="quantity_<?= $photo['photo_id'] ?>">Quantity:</label>
                <input type="number" name="photos[<?= $photo['photo_id'] ?>]" id="quantity_<?= $photo['photo_id'] ?>" min="0" value="0">
            </div>
        <?php endwhile; ?>
        <button type="submit">Place Order</button>
    </form>
        </div>
</main>

</body>
</html> 