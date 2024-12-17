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

// Fetch pending bookings from the database
$pending_bookings_query = "SELECT b.booking_id, b.user_id, b.service_id, b.event_date, b.event_time, b.location, b.additional_requirements, b.total_price, s.service_name 
                            FROM bookings_og b 
                            JOIN services_og s ON b.service_id = s.service_id 
                            WHERE b.status = 'pending'";
$pending_bookings_result = $conn->query($pending_bookings_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="admin_global.css"> <!-- Link to your admin CSS -->
</head>
<body>
    <main>
        <div class="main-content">
            <h1>Manage Pending Bookings</h1>

            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Event Date</th>
                        <th>Event Time</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $pending_bookings_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['event_date'])); ?></td>
                            <td><?php echo date('H:i', strtotime($booking['event_time'])); ?></td>
                            <td><?php echo htmlspecialchars($booking['location']); ?></td>
                            <td>
                                <form action="update_booking_price.php" method="POST">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <input type="number" name="price" step="0.01" required>
                                    <button type="submit">Set Price</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html> 