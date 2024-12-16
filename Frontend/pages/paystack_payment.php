<?php
session_start();
require 'dbconnection.php';

// Your Paystack secret key (test key)
$secret_key = 'sk_test_912d5e0bd9c238a9aec4fee4cd7471445b121a0b';

// Initialize a payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $amount = $_POST['amount'] * 100; // Convert to kobo (for NGN)
    $order_id = $_POST['order_id']; // Get the order ID

    $url = 'https://api.paystack.co/transaction/initialize';
    $fields = [
        'email' => $email,
        'amount' => $amount,
        'callback_url' => '/callback.php?order_id=' . $order_id // Include order ID in callback
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
        $payment_url = $result['data']['authorization_url'];
        header('Location: ' . $payment_url);
    } else {
        echo 'Payment initialization failed: ' . $result['message'];
    }
}
?>
