<?php
session_start();
// Check if user is logged in and is a photographer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'photographer') {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = "Photographer Profile";
include 'dbconnection.php';

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
    <link rel="stylesheet" href="../Frontend/styles/dashboard.css">
</head>
<body>
<div class="profile-container">
    <?php 
    //include '../Frontend/components/sidebar.php'; 
    //renderSidebar('photographer'); 
    ?>

    <div class="main-content">
        <h1>Photographer Profile</h1>
        <div class="profile-details">
            <img src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
    </div>
</div>

<?php 
$user_stmt->close();
$conn->close(); 
?>
</body>
</html>
