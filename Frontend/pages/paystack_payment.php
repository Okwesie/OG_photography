<?php
session_start();
require 'dbconnection.php';

// Your Paystack secret key (test key)
$secret_key = 'sk_test_912d5e0bd9c238a9aec4fee4cd7471445b121a0b';

// Initialize a payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $amount = $_POST['amount'] * 100; // Convert to kobo (for NGN)
    $booking_id = $_POST['booking_id'] ?? null; // Get the booking ID if available
    $order_id = $_POST['order_id'] ?? null; // Get the order ID if available

    $url = 'https://api.paystack.co/transaction/initialize';
    $fields = [
        'email' => $email,
        'amount' => $amount,
        'callback_url' => '/callback.php?booking_id=' . $booking_id . '&order_id=' . $order_id // Include IDs in callback
    ];

    $fields_string = http_build_query($fields);

    // cURL setup
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $secret_key",
        "Cache-Control: no-cache"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL request
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($result['status']) {
        // Payment initialization successful, redirect to Paystack payment page
        $payment_url = $result['data']['authorization_url'];
        header('Location: ' . $payment_url);
        exit();
    } else {
        // Payment initialization failed
        echo 'Payment initialization failed: ' . $result['message'];
    }
}

// After payment is completed, update the status
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    // Check if booking or order ID is present
    if ($booking_id) {
        // Update booking status to 'paid' and set payment_status if applicable
        $update_booking_query = "UPDATE bookings_og SET status = 'paid', payment_status = 'completed', updated_at = NOW() WHERE booking_id = ?";
        $stmt = $conn->prepare($update_booking_query);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
    } elseif ($order_id) {
        // Update order status to 'paid'
        $update_order_query = "UPDATE orders_og SET status = 'paid' WHERE order_id = ?";
        $stmt = $conn->prepare($update_order_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
    }

    // Redirect to my bookings or orders page
    header("Location: customer_dashboard.php"); // or myorders.php based on your logic
    exit();
}
?>
