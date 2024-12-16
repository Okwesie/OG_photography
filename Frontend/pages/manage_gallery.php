<?php
session_start();

// Check if user is logged in and is an admin


// Include database connection
include 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the admin
renderSidebar($_SESSION['role']);

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFilePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFilePath)) {
            $stmt = $conn->prepare("INSERT INTO photos_og (gallery_id, file_path, title, description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $category_id, $uploadFilePath, $title, $description);
            $stmt->execute();
            $stmt->close();
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "No image selected or upload error occurred.";
    }
}

// Handle image deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $photo_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM photos_og WHERE photo_id = ?");
    $stmt->bind_param("i", $photo_id);
    $stmt->execute();
    $stmt->close();
}

// Retrieve images for display
$result = $conn->query("SELECT * FROM photos_og");
$photos = $result->fetch_all(MYSQLI_ASSOC);

// Retrieve categories for dropdown
$result = $conn->query("SELECT * FROM gallery_categories_og");
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery</title>
    <link rel="stylesheet" href="admin_global.css"> <!-- Global Admin CSS --></head>
<body>
    <main>
    <div class="main-content"> 
        <section id="upload-section">
            <h2>Upload New Image</h2>
            <?php if (isset($error)) echo '<p class="error">' . $error . '</p>'; ?>
            <form action="" method="post" enctype="multipart/form-data">
                <label for="title">Image Title:</label>
                <input type="text" id="title" name="title" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>

                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo $category['category_id']; ?>">
                            <?php echo ucfirst($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="image">Choose Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required>

                <button type="submit" name="upload_image">Upload</button>
            </form>
        </section>

        <section id="gallery-section">
            <h2>Gallery</h2>
            <div class="gallery-grid">
                <?php foreach ($photos as $photo) : ?>
                    <div class="gallery-item">
                        <img src="<?php echo $photo['file_path']; ?>" alt="<?php echo $photo['title']; ?>">
                        <h3><?php echo $photo['title']; ?></h3>
                        <p><?php echo $photo['description']; ?></p>
                        <a href="?delete=<?php echo $photo['photo_id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this image?')">Delete</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
                </div>
    </main>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        header nav a {
            margin-right: 10px;
        }

        #upload-section, #gallery-section {
            margin: 20px 0;
        }

        form label {
            display: block;
            margin-bottom: 5px;
        }

        form input, form textarea, form select, form button {
            display: block;
            margin-bottom: 15px;
            width: 100%;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .gallery-item {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .gallery-item img {
            width: 100%;
            height: auto;
        }

        .delete-button {
            color: red;
            text-decoration: none;
        }

        .delete-button:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
        }
    </style>
</body>
</html>
