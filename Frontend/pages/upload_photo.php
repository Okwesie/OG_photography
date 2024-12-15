<?php
session_start();

// Check if user is logged in and has the role of a photographer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'photographer') {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = "Upload Photo";
include 'dbconnection.php';

// Initialize error and success messages
$errors = [];
$successMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gallery_id = $_POST['gallery_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $is_purchasable = isset($_POST['is_purchasable']) ? 1 : 0;
    $digital_price = $_POST['digital_price'] ?? null;
    $print_price = $_POST['print_price'] ?? null;

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/photos/';
        $fileName = basename($_FILES['photo']['name']);
        $filePath = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }

        // Validate file size (max 5MB)
        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            $errors[] = "File size must be less than 5MB.";
        }

        // If no errors, move file to uploads directory
        if (empty($errors)) {
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $filePath)) {
                // Insert photo details into database
                $stmt = $conn->prepare("
                    INSERT INTO photos_og (gallery_id, file_path, title, description, is_purchasable, digital_price, print_price) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param(
                    "isssidd", 
                    $gallery_id, 
                    $filePath, 
                    $title, 
                    $description, 
                    $is_purchasable, 
                    $digital_price, 
                    $print_price
                );

                if ($stmt->execute()) {
                    $successMessage = "Photo uploaded successfully!";
                } else {
                    $errors[] = "Failed to upload photo. Please try again.";
                }

                $stmt->close();
            } else {
                $errors[] = "Failed to move uploaded file.";
            }
        }
    } else {
        $errors[] = "Please select a file to upload.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Photo</title>
    <link rel="stylesheet" href="../Frontend/styles/dashboard.css">
</head>
<body>

<div class="dashboard-container">
    <div class="main-content">
        <h1>Upload Photo</h1>

        <!-- Display success message -->
        <?php if ($successMessage): ?>
            <div class="success-message">
                <p><?php echo $successMessage; ?></p>
            </div>
        <?php endif; ?>

        <!-- Display errors -->
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Upload form -->
        <form action="upload_photo.php" method="post" enctype="multipart/form-data">
            <label for="gallery_id">Select Gallery:</label>
            <select name="gallery_id" id="gallery_id" required>
                <option value="">Select Gallery</option>
                <?php
                // Fetch available galleries for the photographer
                $user_id = $_SESSION['user_id'];
                $galleryQuery = "SELECT gallery_id, title FROM galleries_og";
                $galleryResult = $conn->query($galleryQuery);

                while ($gallery = $galleryResult->fetch_assoc()) {
                    echo '<option value="' . $gallery['gallery_id'] . '">' . htmlspecialchars($gallery['title']) . '</option>';
                }

                $galleryResult->free();
                ?>
            </select>

            <label for="title">Photo Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Photo Description:</label>
            <textarea id="description" name="description" rows="4"></textarea>

            <label for="photo">Select Photo (JPG, JPEG, PNG, GIF | Max: 5MB):</label>
            <input type="file" id="photo" name="photo" required>

            <label for="is_purchasable">
                <input type="checkbox" id="is_purchasable" name="is_purchasable" value="1">
                Make this photo purchasable
            </label>

            <label for="digital_price">Digital Price (optional):</label>
            <input type="number" id="digital_price" name="digital_price" step="0.01" min="0">

            <label for="print_price">Print Price (optional):</label>
            <input type="number" id="print_price" name="print_price" step="0.01" min="0">

            <button type="submit">Upload Photo</button>
        </form>
    </div>
</div>

</body>
</html>
