<?php
session_start();
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the admin
renderSidebar($_SESSION['role']);

// Check if order_id and amount are set in the URL
if (!isset($_GET['order_id']) || !isset($_GET['amount'])) {
    header("Location: myorders.php"); // Redirect if not set
    exit();
}

$order_id = $_GET['order_id'];
$amount = $_GET['amount'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user_global.css">

    <title>Make a Payment</title>
</head>
<body>
    <h1>Make a Payment</h1>

    <form action="paystack_payment.php" method="POST">
        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="amount">Amount (in NGN):</label>
        <input type="number" name="amount" id="amount" value="<?= htmlspecialchars($amount) ?>" readonly required>

        <button type="submit">Pay Now</button>
    </form>
</body>
</html>
