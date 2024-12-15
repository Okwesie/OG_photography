<?php
header('Content-Type: application/json');

// List of folders where images are stored
$folders = ['upload', 'event', 'model', 'nature'];

// Get the category and search term from the request
$category = isset($_GET['category']) && $_GET['category'] !== 'all' ? strtolower($_GET['category']) : 'all';
$searchTerm = isset($_GET['search']) ? strtolower($_GET['search']) : '';

// Function to get all image files in a given directory
function getImagesFromFolder($folder) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $files = [];

    // Update the path to match your folder structure
    $folderPath = __DIR__ . "/../uploads/images/" . $folder;
    
    if (is_dir($folderPath)) {
        foreach (scandir($folderPath) as $file) {
            if ($file !== '.' && $file !== '..') {
                $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($fileExtension, $allowedExtensions)) {
                    // Create a web-accessible URL for the image
                    $files[] = "/uploads/images/" . $folder . "/" . $file;
                }
            }
        }
    }
    return $files;
}

$images = [];

foreach ($folders as $folder) {
    if ($category === 'all' || $category === strtolower($folder)) {
        $imagesInFolder = getImagesFromFolder($folder);
        foreach ($imagesInFolder as $imagePath) {
            if ($searchTerm === '' || strpos(strtolower(basename($imagePath)), $searchTerm) !== false) {
                $images[] = [
                    'url' => $imagePath,
                    'title' => basename($imagePath)
                ];
            }
        }
    }
}

// Return JSON response
echo json_encode($images);