<?php
session_start();
require_once 'dbconnection.php';

// Fetch user growth data
$user_growth_sql = "SELECT DATE(created_at) AS date, COUNT(*) AS userCount 
                    FROM users_og 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY date
                    ORDER BY date ASC";
$user_growth_result = $conn->query($user_growth_sql);
$user_growth_data = [];
while ($row = $user_growth_result->fetch_assoc()) {
    $user_growth_data[] = $row;
}

// Fetch top selling photos
$top_selling_photos_sql = "SELECT p.title, SUM(oi.quantity) AS totalSales
                           FROM photos_og p
                           JOIN order_items_og oi ON p.photo_id = oi.photo_id
                           JOIN orders_og o ON oi.order_id = o.order_id
                           WHERE o.status = 'completed'
                           GROUP BY p.photo_id
                           ORDER BY totalSales DESC
                           LIMIT 5";
$top_selling_photos_result = $conn->query($top_selling_photos_sql);
$top_selling_photos_data = [];
while ($row = $top_selling_photos_result->fetch_assoc()) {
    $top_selling_photos_data[] = $row;
}

// Fetch revenue by category
$revenue_by_category_sql = "SELECT gc.category_name, SUM(o.total_amount) AS totalRevenue
                            FROM orders_og o
                            JOIN order_items_og oi ON o.order_id = oi.order_id
                            JOIN photos_og p ON oi.photo_id = p.photo_id
                            JOIN galleries_og g ON p.gallery_id = g.gallery_id
                            JOIN gallery_categories_og gc ON g.category_id = gc.category_id
                            WHERE o.status = 'completed'
                            GROUP BY gc.category_id
                            ORDER BY totalRevenue DESC";
$revenue_by_category_result = $conn->query($revenue_by_category_sql);
$revenue_by_category_data = [];
while ($row = $revenue_by_category_result->fetch_assoc()) {
    $revenue_by_category_data[] = $row;
}

// Fetch customer satisfaction data
$customer_satisfaction_sql = "SELECT s.service_name, AVG(r.rating) AS averageRating, COUNT(r.review_id) AS reviewCount
                              FROM reviews_og r
                              JOIN bookings_og b ON r.booking_id = b.booking_id
                              JOIN services_og s ON b.service_id = s.service_id
                              GROUP BY s.service_id
                              ORDER BY averageRating DESC";
$customer_satisfaction_result = $conn->query($customer_satisfaction_sql);
$customer_satisfaction_data = [];
while ($row = $customer_satisfaction_result->fetch_assoc()) {
    $customer_satisfaction_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OG_Photography - Analytics</title>

    <style>
        /* Add your CSS styles here */
        .dashboard-item {
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: 400px; /* Set a fixed height */
            overflow: auto; /* Add scrolling if content exceeds the height */
        }

        .dashboard-item h2 {
            margin-top: 0;
            font-size: 1.5rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            grid-gap: 20px;
        }

        .chart-container {
            height: 300px; /* Set the height of the chart container */
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>OG_Photography Analytics</h1>

        <div class="dashboard-grid">
            <div class="dashboard-item">
                <h2>User Growth (Last 30 Days)</h2>
                <div class="chart-container">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>

            <div class="dashboard-item">
                <h2>Top Selling Photos</h2>
                <div class="chart-container">
                    <canvas id="topSellingPhotosChart"></canvas>
                </div>
            </div>

            <div class="dashboard-item">
                <h2>Revenue by Category</h2>
                <div class="chart-container">
                    <canvas id="revenueByCategoryChart"></canvas>
                </div>
            </div>

            <div class="dashboard-item">
                <h2>Customer Satisfaction</h2>
                <div class="chart-container">
                    <canvas id="customerSatisfactionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // User Growth Chart
        var userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($user_growth_data, 'date')); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode(array_column($user_growth_data, 'userCount')); ?>,
                    fill: false,
                    borderColor: '#007bff',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Top Selling Photos Chart
        var topSellingPhotosCtx = document.getElementById('topSellingPhotosChart').getContext('2d');
        new Chart(topSellingPhotosCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($top_selling_photos_data, 'title')); ?>,
                datasets: [{
                    label: 'Total Sales',
                    data: <?php echo json_encode(array_column($top_selling_photos_data, 'totalSales')); ?>,
                    backgroundColor: '#007bff',
                    borderColor: '#007bff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Revenue by Category Chart
        var revenueByCategoryCtx = document.getElementById('revenueByCategoryChart').getContext('2d');
        new Chart(revenueByCategoryCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($revenue_by_category_data, 'category_name')); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode(array_column($revenue_by_category_data, 'totalRevenue')); ?>,
                    backgroundColor: [
                        '#007bff', '#28a745', '#dc3545', '#ffc107', '#6c757d'
                    ],
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Customer Satisfaction Chart
        var customerSatisfactionCtx = document.getElementById('customerSatisfactionChart').getContext('2d');
        new Chart(customerSatisfactionCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($customer_satisfaction_data, 'service_name')); ?>,
                datasets: [
                    {
                        label: 'Average Rating',
                        data: <?php echo json_encode(array_column($customer_satisfaction_data, 'averageRating')); ?>,
                        backgroundColor: '#007bff',
                        borderColor: '#007bff',
                        borderWidth: 1
                    },
                    {
                        label: 'Review Count',
                        data: <?php echo json_encode(array_column($customer_satisfaction_data, 'reviewCount')); ?>,
                        backgroundColor: '#28a745',
                        borderColor: '#28a745',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>