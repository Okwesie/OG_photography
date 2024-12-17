<?php
session_start();
// Check if user is logged in and is a photographer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'photographer') {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = "Photographer Dashboard";
include '../backend/config/dbconnection.php';

// Get user details with extended profile information
$user_id = $_SESSION['user_id'];
$user_query = "
    SELECT u.username, u.full_name, u.email, 
           up.phone_number, up.bio, up.social_media_links
    FROM users_og u
    LEFT JOIN user_profiles_og up ON u.user_id = up.user_id
    WHERE u.user_id = ?
";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch photographer's performance metrics
$performance_query = "
    SELECT 
        COUNT(DISTINCT b.booking_id) AS total_bookings,
        COUNT(DISTINCT CASE WHEN b.status = 'completed' THEN b.booking_id END) AS completed_bookings,
        COUNT(DISTINCT g.gallery_id) AS total_galleries,
        COUNT(DISTINCT p.photo_id) AS total_photos,
        COALESCE(AVG(r.rating), 0) AS average_rating
    FROM bookings_og b
    LEFT JOIN galleries_og g ON g.photographer_id = b.photographer_id
    LEFT JOIN photos_og p ON p.gallery_id = g.gallery_id
    LEFT JOIN reviews_og r ON r.booking_id = b.booking_id
    WHERE b.photographer_id = ?
";
$performance_stmt = $conn->prepare($performance_query);
$performance_stmt->bind_param("i", $user_id);
$performance_stmt->execute();
$performance_result = $performance_stmt->get_result();
$performance = $performance_result->fetch_assoc();

// Fetch recent and upcoming bookings
$bookings_query = "
    SELECT 
        b.booking_id, 
        s.service_name, 
        b.event_date, 
        b.event_time, 
        b.status, 
        b.location
    FROM bookings_og b
    JOIN services_og s ON b.service_id = s.service_id
    WHERE b.photographer_id = ?
    AND (b.status IN ('confirmed', 'in_progress', 'pending') 
         OR (b.status = 'completed' AND b.event_date >= CURRENT_DATE - INTERVAL 30 DAY))
    ORDER BY 
        CASE 
            WHEN b.status IN ('confirmed', 'in_progress', 'pending') THEN 1 
            ELSE 2 
        END,
        b.event_date
    LIMIT 10
";
$bookings_stmt = $conn->prepare($bookings_query);
$bookings_stmt->bind_param("i", $user_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();

// Fetch recent galleries
$galleries_query = "
    SELECT 
        g.gallery_id, 
        g.title, 
        gc.category_name,
        g.featured,
        COUNT(p.photo_id) AS total_photos
    FROM galleries_og g
    LEFT JOIN gallery_categories_og gc ON g.category_id = gc.category_id
    LEFT JOIN photos_og p ON g.gallery_id = p.gallery_id
    WHERE g.photographer_id = ?
    GROUP BY g.gallery_id
    ORDER BY g.created_at DESC
    LIMIT 5
";
$galleries_stmt = $conn->prepare($galleries_query);
$galleries_stmt->bind_param("i", $user_id);
$galleries_stmt->execute();
$galleries_result = $galleries_stmt->get_result();

// Recent orders from photo sales
$orders_query = "
    SELECT 
        o.order_id,
        o.total_amount,
        o.order_date,
        o.status,
        COUNT(oi.order_item_id) AS total_items
    FROM orders_og o
    JOIN order_items_og oi ON o.order_id = oi.order_id
    JOIN photos_og p ON oi.photo_id = p.photo_id
    JOIN galleries_og g ON p.gallery_id = g.gallery_id
    WHERE g.photographer_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
    LIMIT 5
";
$orders_stmt = $conn->prepare($orders_query);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Photographer Dashboard</title>
    <link rel="stylesheet" href="../Frontend/pages/photographer_global.css">
</head>
<body>
<div class="dashboard-container">
    <?php 
    include '../Frontend/components/sidebar.php'; 
    renderSidebar('photographer'); 
    ?>

    <div class="main-content">
        <div class="profile-header">
            <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
            <?php if (!empty($user['bio'])): ?>
            <p class="profile-bio"><?php echo htmlspecialchars($user['bio']); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-summary">
            <div class="summary-card">
                <h3>Total Bookings</h3>
                <p><?php echo $performance['total_bookings']; ?></p>
            </div>
            <div class="summary-card">
                <h3>Completed Bookings</h3>
                <p><?php echo $performance['completed_bookings']; ?></p>
            </div>
            <div class="summary-card">
                <h3>Average Rating</h3>
                <p><?php echo number_format($performance['average_rating'], 1); ?> / 5</p>
            </div>
            <div class="summary-card">
                <h3>Total Galleries</h3>
                <p><?php echo $performance['total_galleries']; ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
 <!-- ... (PHP code remains the same until the HTML) ... -->

<div class="dashboard-item">
    <h2>Bookings</h2>
    <div class="card-content">
        <div class="card-scroll">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['event_date'])); ?></td>
                            <td><?php echo date('H:i', strtotime($booking['event_time'])); ?></td>
                            <td><?php echo htmlspecialchars($booking['location']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-item">
    <h2>My Galleries</h2>
    <div class="card-content">
        <div class="card-scroll">
            <div class="gallery-grid">
                <?php while ($gallery = $galleries_result->fetch_assoc()): ?>
                <div class="gallery-item">
                    <h3><?php echo htmlspecialchars($gallery['title']); ?></h3>
                    <p>Category: <?php echo htmlspecialchars($gallery['category_name']); ?></p>
                    <p>Total Photos: <?php echo $gallery['total_photos']; ?></p>
                    <span class="badge <?php echo $gallery['featured'] ? 'featured' : ''; ?>">
                        <?php echo $gallery['featured'] ? 'Featured' : 'Not Featured'; ?>
                    </span>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-item">
    <h2>Recent Photo Sales</h2>
    <div class="card-content">
        <div class="card-scroll">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                            <td><?php echo $order['total_items']; ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ... (rest of the HTML remains the same) ... -->


        </div>
    </div>
</div>

<?php 
// Close all statements and database connection
$user_stmt->close();
$performance_stmt->close();
$bookings_stmt->close();
$galleries_stmt->close();
$orders_stmt->close();
$conn->close(); 
?>
</body>
</html>