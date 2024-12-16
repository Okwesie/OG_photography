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

$pageTitle = "My Bookings";

$user_id = $_SESSION['user_id'];

// Fetch all bookings with full service details
$bookings_query = "
    SELECT 
        b.booking_id, 
        s.service_name, 
        s.category,
        b.event_date, 
        b.event_time, 
        b.location,
        b.status, 
        b.total_price,
        b.additional_requirements
    FROM bookings_og b
    JOIN services_og s ON b.service_id = s.service_id
    WHERE b.user_id = ?
    ORDER BY b.event_date DESC
";
$bookings_stmt = $conn->prepare($bookings_query);
$bookings_stmt->bind_param("i", $user_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();

// Check if there's a cancellation request
if (isset($_GET['cancel']) && isset($_GET['booking_id'])) {
    $cancel_booking_id = intval($_GET['booking_id']);
    
    // Update booking status to cancelled
    $cancel_query = "UPDATE bookings_og SET status = 'cancelled' WHERE booking_id = ? AND user_id = ?";
    $cancel_stmt = $conn->prepare($cancel_query);
    $cancel_stmt->bind_param("ii", $cancel_booking_id, $user_id);
    $cancel_stmt->execute();
    
    if ($cancel_stmt->affected_rows > 0) {
        $success_message = "Booking successfully cancelled.";
    } else {
        $error_message = "Unable to cancel booking.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="user_global.css">
</head>
<body>
<div class="dashboard-container">
    <?php 
    //include '../Frontend/components/sidebar.php';
    //renderSidebar('user');
    ?>

    <div class="main-content">
        <h1>My Bookings</h1>

        <!-- Button to create a new booking -->
        <div class="create-booking">
            <a href="book_service.php" class="btn btn-primary">Create New Booking</a>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($bookings_result->num_rows == 0): ?>
            <div class="no-bookings">
                <p>You have no bookings yet. <a href="book_service.php">Book a Service</a></p>
            </div>
        <?php else: ?>
            <div class="bookings-container">
                <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                    <div class="booking-card <?php echo strtolower($booking['status']); ?>">
                        <div class="booking-header">
                            <h3><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                            <span class="booking-status">
                                <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                            </span>
                        </div>
                        <div class="booking-details">
                            <p>
                                <strong>Date:</strong> 
                                <?php echo date('F d, Y', strtotime($booking['event_date'])); ?>
                                <strong>Time:</strong> 
                                <?php echo date('h:i A', strtotime($booking['event_time'])); ?>
                            </p>
                            <p>
                                <strong>Category:</strong> 
                                <?php echo htmlspecialchars($booking['category']); ?>
                            </p>
                            <p>
                                <strong>Location:</strong> 
                                <?php echo htmlspecialchars($booking['location'] ?: 'Not specified'); ?>
                            </p>
                            <p>
                                <strong>Total Price:</strong> 
                                $<?php echo number_format($booking['total_price'], 2); ?>
                            </p>
                            <?php if (!empty($booking['additional_requirements'])): ?>
                                <p>
                                    <strong>Additional Requirements:</strong> 
                                    <?php echo htmlspecialchars($booking['additional_requirements']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="booking-actions">
                            <?php if (in_array($booking['status'], ['pending', 'confirmed'])): ?>
                                <a href="?cancel=1&booking_id=<?php echo $booking['booking_id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to cancel this booking?');">
                                    Cancel Booking
                                </a>
                            <?php endif; ?>
                            <?php if ($booking['status'] == 'completed'): ?>
                                <a href="leave_review.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-secondary">
                                    Leave a Review
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
$bookings_stmt->close();
$conn->close();
?>
</body>
</html>