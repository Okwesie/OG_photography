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

$pageTitle = "Customer Profile";

// Get user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, full_name, email, profile_picture FROM users_og WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Get user profile details
$profile_query = "SELECT phone_number, address, bio, social_media_links FROM user_profiles_og WHERE user_id = ?";
$profile_stmt = $conn->prepare($profile_query);
$profile_stmt->bind_param("i", $user_id);
$profile_stmt->execute();
$profile_result = $profile_stmt->get_result();
$profile = $profile_result->fetch_assoc();

// Get booking details
$bookings_query = "SELECT event_date, event_time, location, status, total_price FROM bookings_og WHERE user_id = ?";
$bookings_stmt = $conn->prepare($bookings_query);
$bookings_stmt->bind_param("i", $user_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();

// Get order details
$orders_query = "SELECT order_date, total_amount, payment_method, status FROM orders_og WHERE user_id = ?";
$orders_stmt = $conn->prepare($orders_query);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="dashboard-container">
        <?php renderSidebar($_SESSION['role']); ?>
        
        <div class="main-content">
            <div class="profile-container">
                <div class="profile-header">
                    <?php
                    $initials = '';
                    $name_parts = explode(' ', $user['full_name']);
                    foreach ($name_parts as $part) {
                        $initials .= strtoupper(substr($part, 0, 1));
                    }
                    ?>
                    <div class="avatar-circle">
                        <?php echo $initials; ?>
                    </div>
                    <div>
                        <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                        <p>Customer</p>
                    </div>
                </div>

                <div class="profile-grid">
                    <div class="profile-section">
                        <h2>Personal Information</h2>
                        <div class="profile-details">
                            <div class="detail-item">
                                <span class="detail-label">Username</span>
                                <span class="detail-value"><?php echo htmlspecialchars($user['username']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email</span>
                                <span class="detail-value"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone</span>
                                <span class="detail-value"><?php echo htmlspecialchars($profile['phone_number']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Address</span>
                                <span class="detail-value"><?php echo htmlspecialchars($profile['address']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section">
                        <h2>Bio</h2>
                        <p><?php echo htmlspecialchars($profile['bio']); ?></p>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Your Bookings</h2>
                    <table class="profile-table">
                        <thead>
                            <tr>
                                <th>Event Date</th>
                                <th>Event Time</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $bookings_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['event_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['event_time']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                    <td>$<?php echo htmlspecialchars($booking['total_price']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="profile-section">
                    <h2>Your Orders</h2>
                    <table class="profile-table">
                        <thead>
                            <tr>
                                <th>Order Date</th>
                                <th>Total Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $orders_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                    <td>$<?php echo htmlspecialchars($order['total_amount']); ?></td>
                                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php 
$user_stmt->close();
$profile_stmt->close();
$bookings_stmt->close();
$orders_stmt->close();
$conn->close(); 
?>

