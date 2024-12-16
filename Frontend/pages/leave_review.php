<?php
session_start();
require_once 'dbconnection.php';

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../public/login.php');
    exit();
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Fetch past bookings to allow the user to select one for review
$past_bookings_query = "
    SELECT b.booking_id, s.service_name
    FROM bookings_og b
    JOIN services_og s ON b.service_id = s.service_id
    WHERE b.user_id = ? AND b.status IN ('completed', 'cancelled')
    ORDER BY b.event_date DESC
";
$past_bookings_stmt = $conn->prepare($past_bookings_query);
$past_bookings_stmt->bind_param("i", $user_id);
$past_bookings_stmt->execute();
$past_bookings_result = $past_bookings_stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];

    // Insert the review into the database
    $insert_review_query = "INSERT INTO reviews_og (user_id, booking_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())";
    $insert_review_stmt = $conn->prepare($insert_review_query);
    $insert_review_stmt->bind_param("iiis", $user_id, $booking_id, $rating, $review_text);

    if ($insert_review_stmt->execute()) {
        $_SESSION['success_message'] = 'Review submitted successfully.';
        header('Location: leave_review.php'); // Redirect to the same page to avoid resubmission
        exit();
    } else {
        $_SESSION['error_message'] = 'Failed to submit review.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave a Review</title>
    <link rel="stylesheet" href="../Frontend/styles/admin.css"> <!-- Global Admin CSS -->
</head>
<body>
    <div class="main-content">
        <h1>Leave a Review</h1>

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

        <form action="" method="post">
            <div class="form-group">
                <label for="booking_id">Select Booking</label>
                <select id="booking_id" name="booking_id" required>
                    <option value="">Select a booking</option>
                    <?php while ($booking = $past_bookings_result->fetch_assoc()): ?>
                        <option value="<?php echo $booking['booking_id']; ?>">
                            <?php echo htmlspecialchars($booking['service_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="rating">Rating</label>
                <select id="rating" name="rating" required>
                    <option value="">Select a rating</option>
                    <option value="1">1 Star</option>
                    <option value="2">2 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="5">5 Stars</option>
                </select>
            </div>
            <div class="form-group">
                <label for="review_text">Review</label>
                <textarea id="review_text" name="review_text" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn">Submit Review</button>
        </form>
    </div>
</body>
</html>

<?php
// Close database connections
$past_bookings_stmt->close();
$conn->close();
?> 