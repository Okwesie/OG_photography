<?php
session_start();
include 'sidebar.php'; // Include the sidebar

// Render the sidebar based on user role
renderSidebar($_SESSION['role']);

$pageTitle = "Gallery";
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';

// Define the image paths based on your structure
$images = [
    'event' => [
        ['path' => 'event/After_church.jpg', 'title' => 'After Church'],
        ['path' => 'event/Ubora1.jpg', 'title' => 'Ubora 1'],
        ['path' => 'event/Ubora2.jpg', 'title' => 'Ubora 2'],
    ],
    'model' => [
        ['path' => 'model/cute.jpg', 'title' => 'Cute'],
        ['path' => 'model/fran_full.jpg', 'title' => 'Fran Full'],
        ['path' => 'model/fran_Ubora.jpg', 'title' => 'Fran Ubora'],
        ['path' => 'model/fran1.jpg', 'title' => 'Fran 1'],
        ['path' => 'model/fran2.jpg', 'title' => 'Fran 2'],
        ['path' => 'model/fran3.jpg', 'title' => 'Fran 3'],
        ['path' => 'model/py.jpg', 'title' => 'PY'],
        ['path' => 'model/sis_mo_map_and_mak.jpg', 'title' => 'Sisters'],
        ['path' => 'model/the_baby.jpg', 'title' => 'The Baby'],
    ],
    'nature' => [
        ['path' => 'nature/beach.jpg', 'title' => 'Beach'],
        ['path' => 'nature/contrast.jpg', 'title' => 'Contrast'],
        ['path' => 'nature/Descend.jpg', 'title' => 'Descend'],
        ['path' => 'nature/fire.jpg', 'title' => 'Fire'],
        ['path' => 'nature/hill.jpg', 'title' => 'Hill'],
        ['path' => 'nature/mountain.jpg', 'title' => 'Mountain'],
        ['path' => 'nature/nature.jpg', 'title' => 'Nature'],
        ['path' => 'nature/night_pic.jpg', 'title' => 'Night'],
        ['path' => 'nature/sky.jpg', 'title' => 'Sky'],
        ['path' => 'nature/sunset.jpg', 'title' => 'Sunset'],
        ['path' => 'nature/view.jpg', 'title' => 'View'],
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OG_Photography Gallery</title>
    <link rel="stylesheet" href="gallery.css">
</head>
<body>
    <header>
        <h1>OG_Photography Gallery</h1>
    </header>

    <main>

        <section id="search-section">
            <input type="text" id="search-bar" placeholder="Search photos...">
            <select id="category-filter">
                <option value="all" <?php echo $selectedCategory === 'all' ? 'selected' : ''; ?>>All Categories</option>
                <option value="event" <?php echo $selectedCategory === 'event' ? 'selected' : ''; ?>>Event</option>
                <option value="nature" <?php echo $selectedCategory === 'nature' ? 'selected' : ''; ?>>Nature</option>
                <option value="model" <?php echo $selectedCategory === 'model' ? 'selected' : ''; ?>>Model</option>
            </select>
        </section>


        <div class="main-content"> 
        <section id="gallery-container">
            <?php
            if ($selectedCategory === 'all') {
                foreach ($images as $category => $categoryImages) {
                    foreach ($categoryImages as $image) {
                        echo '<div class="gallery-item" data-category="' . $category . '" data-title="' . strtolower($image['title']) . '">';
                        echo '<img src="' . $image['path'] . '" alt="' . $image['title'] . '" loading="lazy">';
                        echo '</div>';
                    }
                }
            } else {
                foreach ($images[$selectedCategory] as $image) {
                    echo '<div class="gallery-item" data-category="' . $selectedCategory . '" data-title="' . strtolower($image['title']) . '">';
                    echo '<img src="' . $image['path'] . '" alt="' . $image['title'] . '" loading="lazy">';
                    echo '</div>';
                }
            }
            ?>
        </section>
        </div>
    </main>

    <div id="lightbox" class="hidden">
        <span class="close">&times;</span>
        <img id="lightbox-img" src="" alt="Lightbox image">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchBar = document.getElementById('search-bar');
            const categoryFilter = document.getElementById('category-filter');
            const galleryItems = document.querySelectorAll('.gallery-item');
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightbox-img');

            // Search functionality
            searchBar.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                galleryItems.forEach(item => {
                    const title = item.dataset.title;
                    const category = categoryFilter.value;
                    const showCategory = category === 'all' || item.dataset.category === category;
                    const showSearch = title.includes(searchTerm);
                    item.style.display = (showCategory && showSearch) ? 'block' : 'none';
                });
            });

            // Category filter
            categoryFilter.addEventListener('change', function() {
                const category = this.value;
                const searchTerm = searchBar.value.toLowerCase();
                
                galleryItems.forEach(item => {
                    const title = item.dataset.title;
                    const showCategory = category === 'all' || item.dataset.category === category;
                    const showSearch = title.includes(searchTerm);
                    item.style.display = (showCategory && showSearch) ? 'block' : 'none';
                });

                // Update URL without page reload
                const url = new URL(window.location);
                url.searchParams.set('category', category);
                window.history.pushState({}, '', url);
            });

            // Lightbox functionality
            galleryItems.forEach(item => {
                item.addEventListener('click', function() {
                    const imgSrc = this.querySelector('img').src;
                    lightboxImg.src = imgSrc;
                    lightbox.classList.remove('hidden');
                });
            });

            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox || e.target.className === 'close') {
                    lightbox.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>