<?php
session_start();
require_once '../../backend/config/dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Default role is 'customer'
    $role = 'customer';

    // Validate inputs
    $errors = [];

    // Name validation
    if (empty($name) || strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long.";
    }

    // Email validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users_og WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($emailCount);
    $stmt->fetch();
    $stmt->close();

    if ($emailCount > 0) {
        $errors[] = "Email is already registered.";
    }

    // Password validation
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Confirm password validation
    if ($password !== $_POST['confirm_password']) {
        $errors[] = "Passwords do not match.";
    }

    // If there are validation errors
    if (!empty($errors)) {
        $_SESSION['register_error'] = implode(" ", $errors);
        header("Location: register.php");
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO users_og (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
    
    // Create a username from the full name (remove spaces, convert to lowercase)
    $username = strtolower(str_replace(' ', '', $name));
    
    // Check if username already exists
    $usernameCount = 0;
    $username_stmt = $conn->prepare("SELECT COUNT(*) FROM users_og WHERE username = ?");
    $username_stmt->bind_param("s", $username);
    $username_stmt->execute();
    $username_stmt->bind_result($usernameCount);
    $username_stmt->fetch();
    $username_stmt->close();

    if ($usernameCount > 0) {
        $errors[] = "Username is already taken.";
    }

    $stmt->bind_param("sssss", $username, $email, $hashedPassword, $name, $role);

    if ($stmt->execute()) {
        // Get the last inserted user ID
        $user_id = $conn->insert_id;

        // Optional: Create an entry in user_profiles_og if needed
        $profile_stmt = $conn->prepare("INSERT INTO user_profiles_og (user_id) VALUES (?)");
        $profile_stmt->bind_param("i", $user_id);
        $profile_stmt->execute();

        $_SESSION['notification'] = "Registration successful! You can now log in.";
        header("Location: login.php");
    } else {
        $_SESSION['register_error'] = "Registration failed: " . $stmt->error;
        header("Location: register.php");
    }

    $stmt->close();
    $conn->close();
}
?>