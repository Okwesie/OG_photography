<?php
session_start();
require_once 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the admin
renderSidebar($_SESSION['role']);

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/login.php');
    exit();
}

// Fetch user information
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT * FROM users_og WHERE user_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Update user profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_picture = $_FILES['profile_picture']['name'];
    $tmp_name = $_FILES['profile_picture']['tmp_name'];
    $upload_dir = 'uploads/';
    $upload_file = $upload_dir . basename($profile_picture);

    $update_sql = "UPDATE users_og SET full_name = ?, email = ?, password = ?, profile_picture = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param('ssssi', $full_name, $email, $hashed_password, $profile_picture, $user_id);

    if ($stmt->execute()) {
        if (!empty($profile_picture)) {
            move_uploaded_file($tmp_name, $upload_file);
        }
        $_SESSION['success_message'] = 'Profile updated successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to update profile.';
    }
    header('Location: settings.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OG_Photography - Settings</title>
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
                    $name_parts = explode(' ', $user_data['full_name']);
                    foreach ($name_parts as $part) {
                        $initials .= strtoupper(substr($part, 0, 1));
                    }
                    ?>
                    <div class="avatar-circle">
                        <?php echo $initials; ?>
                    </div>
                    <div>
                        <h1>Account Settings</h1>
                        <p>Update your profile information</p>
                    </div>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo htmlspecialchars($_SESSION['success_message']); 
                        unset($_SESSION['success_message']); 
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-error">
                        <?php 
                        echo htmlspecialchars($_SESSION['error_message']); 
                        unset($_SESSION['error_message']); 
                        ?>
                    </div>
                <?php endif; ?>

                <div class="profile-section">
                    <h2>Update Profile</h2>
                    <form action="" method="post" enctype="multipart/form-data" class="profile-form">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" 
                                   placeholder="Leave blank to keep current password">
                        </div>
                        <div class="form-group">
                            <label for="profile_picture">Profile Picture</label>
                            <input type="file" id="profile_picture" name="profile_picture">
                        </div>
                        <button type="submit" class="btn">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

