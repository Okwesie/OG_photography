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

    if (is_dir($folder)) {
        foreach (scandir($folder) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = "$folder/$file";
                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if (in_array($fileExtension, $allowedExtensions)) {
                    $files[] = $filePath;
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
            if ($searchTerm === '' || strpos(strtolower($imagePath), $searchTerm) !== false) {
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
