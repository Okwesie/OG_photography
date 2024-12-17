<?php
session_start();
require_once 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the photographer
renderSidebar($_SESSION['role']);

// Check if the user is logged in 
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = "Photographer Profile";

// Get user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, full_name, email, profile_picture FROM users_og WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Photographer Profile</title>
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
                        <p>Professional Photographer</p>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php 
$user_stmt->close();
$conn->close(); 
?>

