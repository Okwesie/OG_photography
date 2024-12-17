<?php
session_start();
require_once 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the admin
renderSidebar($_SESSION['role']);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get booking details from the URL
$booking_id = $_GET['booking_id'] ?? null;
$amount = $_GET['amount'];
$order_id = $_GET['order_id'] ?? null; // Optional order ID

// Handle payment processing logic here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <link rel="stylesheet" href="user_global.css">
</head>
<body>
<main>
    <div class="main-content">
        <h1>Make Payment</h1>

        <form action="paystack_payment.php" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
            <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">
            <?php if ($order_id): ?>
                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
            <?php endif; ?>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="payment_method">Select Payment Method:</label>
            <select name="payment_method" required>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
                <!-- Add other payment methods as needed -->
            </select>

            <p>Please confirm that you are making a payment for 
                <?php if ($order_id): ?>
                    your order (ID: <strong><?php echo htmlspecialchars($order_id); ?></strong>) 
                <?php endif; ?>
                <?php if ($booking_id): ?>
                    or your booking (ID: <strong><?php echo htmlspecialchars($booking_id); ?></strong>)
                <?php endif; ?>
                for the amount of <strong>$<?php echo number_format($amount, 2); ?></strong>.
            </p>

            <button type="submit">Pay Now</button>
        </form>
    </div>
</main>
</body>
</html>