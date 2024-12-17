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

// Fetch available services from the database
$services_query = "SELECT * FROM services_og";
$services_result = $conn->query($services_query);

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $service_id = $_POST['service_id'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $additional_requirements = $_POST['additional_requirements'];
    $total_price = $_POST['total_price']; // Assuming you calculate this based on the service

    // Insert booking into the database
    $booking_query = "INSERT INTO bookings_og (user_id, service_id, event_date, event_time, location, additional_requirements, total_price) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($booking_query);
    $stmt->bind_param("iissssi", $user_id, $service_id, $event_date, $event_time, $location, $additional_requirements, $total_price);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Booking created successfully.";
        header("Location: mybookings.php"); // Redirect to user's bookings page
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to create booking.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Service</title>
    <link rel="stylesheet" href="user_global.css">
</head>
<body>
<main>
<div class="main-content">
    <h1>Book a Service</h1>

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
        <label for="service_id">Select Service:</label>
        <select name="service_id" required>
            <option value="">-- Select a Service --</option>
            <?php while ($service = $services_result->fetch_assoc()): ?>
                <option value="<?= $service['service_id'] ?>"><?= htmlspecialchars($service['service_name']) ?> - $<?= number_format($service['base_price'], 2) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="event_date">Event Date:</label>
        <input type="date" name="event_date" required>

        <label for="event_time">Event Time:</label>
        <input type="time" name="event_time" required>

        <label for="location">Location:</label>
        <input type="text" name="location" required>

        <label for="additional_requirements">Additional Requirements:</label>
        <textarea name="additional_requirements"></textarea>

        <input type="hidden" name="total_price" value="0"> <!-- Set this based on the selected service -->

        <button type="submit">Book Now</button>
    </form>
            </div>
</main>

</body>
</html> 