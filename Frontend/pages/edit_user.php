<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/admin_dashboard.php');
    exit();
}

// Include database connection file
require_once 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the admin
renderSidebar($_SESSION['role']);

// Get user_id from the URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Prepare and execute the query to fetch user details
    $stmt = $conn->prepare("SELECT * FROM users_og WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    // Redirect if no user_id is provided
    header('Location: manage_users.php');
    exit();
}

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users_og SET username = ?, email = ?, full_name = ?, role = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $username, $email, $full_name, $role, $user_id);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to manage users after update
    header('Location: manage_users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="admin_global.css">
</head>
<body>
<main>
<div class="main-content"> 
    <h1>Edit User</h1>
    <?php if ($user): ?>
        <form action="" method="POST">
            <label for="username">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

            <label for="role">Role</label>
            <select name="role" required>
                <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="photographer" <?= $user['role'] === 'photographer' ? 'selected' : '' ?>>Photographer</option>
            </select>

            <button type="submit">Update User</button>
        </form>
    <?php else: ?>
        <p>User not found.</p>
    <?php endif; ?>
    <a href="manage_users.php">Back to Manage Users</a>
    </div>
</main>

</body>
</html> 