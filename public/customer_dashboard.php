<?php
session_start();
// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = "Customer Dashboard";
include '../backend/config/dbconnection.php';

// Get user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, full_name, email FROM users_og WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch user's upcoming bookings
$upcoming_bookings_query = "
    SELECT b.booking_id, s.service_name, b.event_date, b.event_time, b.status, s.base_price
    FROM bookings_og b
    JOIN services_og s ON b.service_id = s.service_id
    WHERE b.user_id = ? AND b.status IN ('pending', 'confirmed', 'in_progress')
    ORDER BY b.event_date
    LIMIT 5
";
$upcoming_bookings_stmt = $conn->prepare($upcoming_bookings_query);
$upcoming_bookings_stmt->bind_param("i", $user_id);
$upcoming_bookings_stmt->execute();
$upcoming_bookings_result = $upcoming_bookings_stmt->get_result();

// Fetch user's past bookings
$past_bookings_query = "
    SELECT b.booking_id, s.service_name, b.event_date, b.status
    FROM bookings_og b
    JOIN services_og s ON b.service_id = s.service_id
    WHERE b.user_id = ? AND b.status IN ('completed', 'cancelled')
    ORDER BY b.event_date DESC
    LIMIT 5
";
$past_bookings_stmt = $conn->prepare($past_bookings_query);
$past_bookings_stmt->bind_param("i", $user_id);
$past_bookings_stmt->execute();
$past_bookings_result = $past_bookings_stmt->get_result();

// Fetch user's recent photo purchases
$recent_purchases_query = "
    SELECT o.order_id, o.order_date, o.total_amount, o.status,
           GROUP_CONCAT(CONCAT(p.title, ' (', oi.quantity, ')') SEPARATOR ', ') AS purchased_items
    FROM orders_og o
    JOIN order_items_og oi ON o.order_id = oi.order_id
    JOIN photos_og p ON oi.photo_id = p.photo_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
    LIMIT 5
";
$recent_purchases_stmt = $conn->prepare($recent_purchases_query);
$recent_purchases_stmt->bind_param("i", $user_id);
$recent_purchases_stmt->execute();
$recent_purchases_result = $recent_purchases_stmt->get_result();

// Fetch user's submitted reviews
$user_reviews_query = "
    SELECT r.review_id, s.service_name, r.rating, r.review_text, r.created_at
    FROM reviews_og r
    JOIN bookings_og b ON r.booking_id = b.booking_id
    JOIN services_og s ON b.service_id = s.service_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
    LIMIT 3
";
$user_reviews_stmt = $conn->prepare($user_reviews_query);
$user_reviews_stmt->bind_param("i", $user_id);
$user_reviews_stmt->execute();
$user_reviews_result = $user_reviews_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../Frontend/pages/user_global.css">
</head>
<body>
<div class="dashboard-container">
    <?php 
    include '../Frontend/components/sidebar.php';
    renderSidebar('customer');
    ?>

    <div class="main-content">
        <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
        
        <div class="dashboard-summary">
            <div class="summary-card">
                <h3>Upcoming Bookings</h3>
                <p><?php echo $upcoming_bookings_result->num_rows; ?></p>
            </div>
            <div class="summary-card">
                <h3>Recent Purchases</h3>
                <p><?php echo $recent_purchases_result->num_rows; ?></p>
            </div>
            <div class="summary-card">
                <h3>My Reviews</h3>
                <p><?php echo $user_reviews_result->num_rows; ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-item">
                <h2>Upcoming Bookings</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $upcoming_bookings_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['event_date'])); ?></td>
                            <td><?php echo date('H:i', strtotime($booking['event_time'])); ?></td>
                            <td><?php echo ucfirst($booking['status']); ?></td>
                            <td>$<?php echo number_format($booking['base_price'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="dashboard-item">
                <h2>Past Bookings</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($past_booking = $past_bookings_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($past_booking['service_name']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($past_booking['event_date'])); ?></td>
                            <td><?php echo ucfirst($past_booking['status']); ?></td>
                            <td>
                                <a href="book_again.php?service=<?php echo urlencode($past_booking['service_name']); ?>" class="btn btn-small">Book Again</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="dashboard-item">
                <h2>Recent Purchases</h2>
                <?php while ($purchase = $recent_purchases_result->fetch_assoc()): ?>
                <div class="purchase-item">
                    <div class="purchase-header">
                        <span class="purchase-date"><?php echo date('Y-m-d H:i', strtotime($purchase['order_date'])); ?></span>
                        <span class="purchase-status <?php echo strtolower($purchase['status']); ?>">
                            <?php echo ucfirst($purchase['status']); ?>
                        </span>
                    </div>
                    <p class="purchase-items"><?php echo htmlspecialchars($purchase['purchased_items']); ?></p>
                    <p class="purchase-total">Total: $<?php echo number_format($purchase['total_amount'], 2); ?></p>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="dashboard-item">
                <h2>My Reviews</h2>
                <?php while ($review = $user_reviews_result->fetch_assoc()): ?>
                <div class="review-item">
                    <div class="review-header">
                        <span class="review-rating">
                            Rating: <?php 
                            echo str_repeat('★', $review['rating']) . 
                                 str_repeat('☆', 5 - $review['rating']); 
                            ?>
                        </span>
                        <span class="review-service"><?php echo htmlspecialchars($review['service_name']); ?></span>
                    </div>
                    <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                    <p class="review-date"><?php echo date('Y-m-d', strtotime($review['created_at'])); ?></p>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="dashboard-item">
                <h2>Quick Actions</h2>
                <div class="quick-actions">
                    <a href="book_service.php" class="btn">Book a Service</a>
                    <a href="view_galleries.php" class="btn">Browse Galleries</a>
                    <a href="edit_profile.php" class="btn">Edit Profile</a>
                    <a href="my_purchases.php" class="btn">My Purchases</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Close database connections and statements
$user_stmt->close();
$upcoming_bookings_stmt->close();
$past_bookings_stmt->close();
$recent_purchases_stmt->close();
$user_reviews_stmt->close();
$conn->close();
?>
</body>
</html>