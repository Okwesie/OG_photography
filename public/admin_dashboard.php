<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = "Admin Dashboard";
//include 'includes/header.php';
include '../backend/config/dbconnection.php';

// Fetch total users
$total_users_query = "SELECT COUNT(*) as total_users FROM users_og";
$total_users_result = $conn->query($total_users_query);
$total_users = $total_users_result->fetch_assoc()['total_users'];

// Fetch pending orders
$pending_orders_query = "SELECT COUNT(*) as pending_orders FROM orders_og WHERE status = 'pending'";
$pending_orders_result = $conn->query($pending_orders_query);
$pending_orders = $pending_orders_result->fetch_assoc()['pending_orders'];

// Fetch total revenue
$total_revenue_query = "SELECT SUM(total_amount) as total_revenue FROM orders_og WHERE status = 'paid' OR status = 'completed'";
$total_revenue_result = $conn->query($total_revenue_query);
$total_revenue = $total_revenue_result->fetch_assoc()['total_revenue'];

// Fetch recent activity
$recent_activity_query = "
    SELECT u.username, ual.activity_type, ual.timestamp 
    FROM user_activity_log_og ual
    JOIN users_og u ON ual.user_id = u.user_id
    ORDER BY ual.timestamp DESC
    LIMIT 5
";
$recent_activity_result = $conn->query($recent_activity_query);

// Fetch popular services
$popular_services_query = "
    SELECT s.service_name, COUNT(b.booking_id) as booking_count
    FROM services_og s
    LEFT JOIN bookings_og b ON s.service_id = b.service_id
    GROUP BY s.service_id
    ORDER BY booking_count DESC
    LIMIT 5
";
$popular_services_result = $conn->query($popular_services_query);

// Fetch latest reviews
$latest_reviews_query = "
    SELECT r.rating, r.review_text, u.username, s.service_name
    FROM reviews_og r
    JOIN users_og u ON r.user_id = u.user_id
    JOIN bookings_og b ON r.booking_id = b.booking_id
    JOIN services_og s ON b.service_id = s.service_id
    ORDER BY r.created_at DESC
    LIMIT 5
";
$latest_reviews_result = $conn->query($latest_reviews_query);

// Add this to your admin_dashboard.php
$user_growth_query = "
    SELECT DATE(created_at) as date, COUNT(*) as new_users
    FROM users_og
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date
";
$user_growth_result = $conn->query($user_growth_query);

$user_growth_data = [];
while ($row = $user_growth_result->fetch_assoc()) {
    $user_growth_data[] = $row;
}
$user_growth_json = json_encode($user_growth_data);
?>

<div class="dashboard-container">
    <?php 
    include '../Frontend/components/sidebar.php';
    renderSidebar('admin');
    ?>

    <div class="main-content">
    <link rel="stylesheet" href="../Frontend/pages/admin_global.css">
        <h1>Welcome to the Admin Dashboard</h1>
        

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('userGrowthChart').getContext('2d');
    var userGrowthData = <?php echo $user_growth_json; ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: userGrowthData.map(item => item.date),
            datasets: [{
                label: 'New Users',
                data: userGrowthData.map(item => item.new_users),
                borderColor: '#3b82f6',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
</script>
        
        <div class="dashboard-summary">
            <div class="summary-card">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="summary-card">
                <h3>Pending Orders</h3>
                <p><?php echo $pending_orders; ?></p>
            </div>
            <div class="summary-card">
                <h3>Total Revenue</h3>
                <p>$<?php echo number_format($total_revenue, 2); ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-item">
                <h2>Recent Activity</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($activity = $recent_activity_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($activity['username']); ?></td>
                            <td><?php echo htmlspecialchars($activity['activity_type']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($activity['timestamp'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="dashboard-item">
                <h2>Popular Services</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Bookings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($service = $popular_services_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                            <td><?php echo $service['booking_count']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="dashboard-item">
                <h2>Latest Reviews</h2>
                <?php while ($review = $latest_reviews_result->fetch_assoc()): ?>
                <div class="review-item">
                    <div class="review-header">
                        <span class="review-rating">Rating: <?php echo $review['rating']; ?>/5</span>
                        <span class="review-service"><?php echo htmlspecialchars($review['service_name']); ?></span>
                    </div>
                    <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                    <p class="review-user">- <?php echo htmlspecialchars($review['username']); ?></p>
                </div>
                <?php endwhile; ?>
            </div>  
        </div>
    </div>
</div>

<?php 
$conn->close();
//include 'includes/footer.php'; 
?>
