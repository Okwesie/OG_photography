<?php
session_start();
require_once 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the user
renderSidebar($_SESSION['role']);


// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle price update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $price = $_POST['price'];

    // Update the booking price and change status to 'in_progress' in the database
    $update_query = "UPDATE bookings_og SET total_price = ?, status = 'in_progress' WHERE booking_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("di", $price, $booking_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Price updated and status changed to in_progress successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update price and change status.";
    }
    header("Location: manage_bookings.php");
    exit();
}
?> 