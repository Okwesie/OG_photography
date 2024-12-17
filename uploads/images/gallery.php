<?php
session_start();
include 'sidebar.php'; // Include the sidebar


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
    <style>
        @import url('photographer_global.css');
/* Gallery Container */
.gallery-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
    background-color: var(--background-color);
}

  /* Sidebar */
  .sidebar {
    width: var(--sidebar-width);
    background-color: var(--primary-color);
    color: #fff;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
    transition: width 0.3s ease;
  }
  
  .sidebar-header {
    padding: 1.5rem;
    text-align: center;
    background-color: var(--secondary-color);
  }
  
  .sidebar-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 300;
    letter-spacing: 1px;
  }
  
  .sidebar-menu {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }
  
  .sidebar-menu li {
    padding: 0.5rem 1rem;
  }
  
  .sidebar-menu a {
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
  }
  
  .sidebar-menu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  .sidebar-menu i {
    margin-right: 0.5rem;
    font-size: 1.2rem;
  }
  

/* Search Section */
#search-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

#search-bar,
#category-filter {
    padding: 0.75rem;
    border: 1px solid var(--secondary-color);
    border-radius: 4px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

#search-bar {
    flex-grow: 1;
    margin-right: 1rem;
}

#category-filter {
    width: 200px;
}

/* Gallery Items */
.gallery-item {
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

.gallery-item img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-item:hover img {
    transform: scale(1.05);
}

.photo-title {
    padding: 0.75rem;
    font-size: 0.9rem;
    color: var(--text-color);
    text-align: center;
    background-color: #f9f9f9;
    font-weight: 500;
}

/* Lightbox */
#lightbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.85);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

#lightbox.hidden {
    opacity: 0;
    visibility: hidden;
}

#lightbox.visible {
    opacity: 1;
    visibility: visible;
}

#lightbox-img {
    max-width: 90%;
    max-height: 90%;
    border-radius: 8px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
    transition: transform 0.3s ease;
}

#lightbox-img:hover {
    transform: scale(1.02);
}

.close {
    position: absolute;
    top: 20px;
    right: 30px;
    font-size: 40px;
    color: #fff;
    cursor: pointer;
    transition: color 0.3s ease;
    opacity: 0.7;
}

.close:hover {
    color: var(--accent-color);
    opacity: 1;
}

/* Responsive Design */
@media (max-width: 768px) {
    .gallery-container {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        padding: 1rem;
    }

    #search-section {
        flex-direction: column;
        padding: 0.75rem;
    }

    #search-bar,
    #category-filter {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    #search-bar {
        margin-right: 0;
    }

    .gallery-item img {
        height: 200px;
    }

    .close {
        top: 10px;
        right: 15px;
        font-size: 30px;
    }
}

/* Accessibility and Interaction Enhancements */
.gallery-item:focus-within {
    outline: 2px solid var(--accent-color);
    outline-offset: 2px;
}

#search-bar:focus,
#category-filter:focus {
    border-color: var(--accent-color);
    outline: none;
    box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.2);
}



</style>
</head>
<body>
    <div class="dashboard-container">
    <?php renderSidebar($_SESSION['role']); ?>
        <div class="main-content">
            <header>
                <h1>OG_Photography Gallery</h1>
            </header>

            <section id="search-section">
                <input type="text" id="search-bar" placeholder="Search photos...">
                <select id="category-filter">
                    <option value="all" <?php echo $selectedCategory === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <option value="event" <?php echo $selectedCategory === 'event' ? 'selected' : ''; ?>>Event</option>
                    <option value="nature" <?php echo $selectedCategory === 'nature' ? 'selected' : ''; ?>>Nature</option>
                    <option value="model" <?php echo $selectedCategory === 'model' ? 'selected' : ''; ?>>Model</option>
                </select>
            </section>


            <section id="gallery-container" class="gallery-container">
                <?php
                if ($selectedCategory === 'all') {
                    foreach ($images as $category => $categoryImages) {
                        foreach ($categoryImages as $image) {
                            echo '<div class="gallery-item" data-category="' . $category . '" data-title="' . strtolower($image['title']) . '">';
                            echo '<img src="' . $image['path'] . '" alt="' . $image['title'] . '" loading="lazy">';
                            echo '<div class="photo-title">' . $image['title'] . '</div>';
                            echo '</div>';
                        }
                    }
                } else {
                    foreach ($images[$selectedCategory] as $image) {
                        echo '<div class="gallery-item" data-category="' . $selectedCategory . '" data-title="' . strtolower($image['title']) . '">';
                        echo '<img src="' . $image['path'] . '" alt="' . $image['title'] . '" loading="lazy">';
                        echo '<div class="photo-title">' . $image['title'] . '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </section>
        </div>
    </div>

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

