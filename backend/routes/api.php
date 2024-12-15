<?php
// Include the database connection
require_once '../config/dbconnection.php';
session_start(); // Start session to track user session

// Set the response header to return JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

// Check if the request method is POST (to handle API calls securely)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the 'action' from the POST request
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'register':
            registerUser($conn);
            break;
        case 'login':
            loginUser($conn);
            break;
        case 'fetch_galleries':
            fetchGalleries($conn);
            break;
        case 'fetch_photos':
            fetchPhotos($conn);
            break;
        default:
            $response['message'] = 'Invalid action specified.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Return the JSON response
echo json_encode($response);

// Function to register a user
function registerUser($conn) {
    global $response;

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    // Check if all fields are filled
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $response['message'] = 'All fields are required.';
        return;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
        return;
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $response['message'] = 'Passwords do not match.';
        return;
    }

    // Check if email already exists
    $checkQuery = "SELECT * FROM users_og WHERE email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['message'] = 'Email already exists.';
        return;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user into the database
    $insertQuery = "INSERT INTO users_og (full_name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Registration successful.';
    } else {
        $response['message'] = 'Registration failed. Please try again.';
    }
}

function loginUser($conn) {
    global $response;

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $response['message'] = 'Both email and password are required.';
        return;
    }

    $query = "SELECT user_id, role, password, username, email FROM users_og WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Successful login - start session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];

            // Determine redirect URL based on user role
            $redirectUrl = 'user_dashboard.php'; // Default redirect
            if ($user['role'] === 'admin') {
                $redirectUrl = '../backend/admin_dashboard.php';
            } else if ($user['role'] === 'photographer') {
                $redirectUrl = 'crew_dashboard.php';
            }

            $response['success'] = true;
            $response['message'] = 'Login successful.';
            $response['data'] = [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'redirect' => $redirectUrl // Send the URL to the frontend
            ];

            logUserActivity($conn, $user['user_id'], 'login');
        } else {
            $response['message'] = 'Incorrect email or password.';
        }
    } else {
        $response['message'] = 'User not found.';
    }
}


// Helper function to log user activity
function logUserActivity($conn, $userId, $activityType) {
    $query = "INSERT INTO user_activity_log_og (user_id, activity_type, ip_address) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("iss", $userId, $activityType, $ipAddress);
    $stmt->execute();
}

// Function to fetch galleries
function fetchGalleries($conn) {
    global $response;

    $query = "SELECT * FROM gallery_categories_og";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $response['success'] = true;
        $response['data'] = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $response['message'] = 'No galleries found.';
    }
}

// Function to fetch photos based on a gallery
function fetchPhotos($conn) {
    global $response;

    $gallery_id = isset($_POST['gallery_id']) ? (int)$_POST['gallery_id'] : 0;

    if ($gallery_id === 0) {
        $response['message'] = 'Invalid gallery ID.';
        return;
    }

    $query = "SELECT * FROM photos_og WHERE gallery_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $gallery_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['success'] = true;
        $response['data'] = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $response['message'] = 'No photos found for this gallery.';
    }
}
