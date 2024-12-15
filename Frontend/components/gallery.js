document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('category-filter');
    const searchBar = document.getElementById('search-bar');
    const galleryContainer = document.getElementById('gallery-container');
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');

    // Hard-coded categories based on folder names
    const categories = ['All', 'Upload', 'Event', 'Model', 'Nature'];
    
    // Populate category filter dropdown
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.toLowerCase();
        option.textContent = category;
        categoryFilter.appendChild(option);
    });

    // Load photos based on category and search
    function loadPhotos() {
        const category = categoryFilter.value;
        const searchTerm = searchBar.value.toLowerCase();
        
        fetch(`/utils/get_photos.php?category=${category}&search=${searchTerm}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(photos => {
                galleryContainer.innerHTML = '';
                if (photos.length === 0) {
                    galleryContainer.innerHTML = '<p class="no-photos">No photos found</p>';
                    return;
                }
                
                photos.forEach(photo => {
                    const img = document.createElement('img');
                    img.src = photo.url;
                    img.alt = photo.title;
                    img.loading = 'lazy'; // Add lazy loading
                    
                    const title = document.createElement('p');
                    title.className = 'photo-title';
                    title.textContent = photo.title;

                    const div = document.createElement('div');
                    div.className = 'gallery-item';
                    div.appendChild(img);
                    div.appendChild(title);
                    
                    div.addEventListener('click', () => openLightbox(photo.url));
                    galleryContainer.appendChild(div);
                });
            })
            .catch(error => {
                console.error('Error loading photos:', error);
                galleryContainer.innerHTML = '<p class="error">Error loading photos. Please try again later.</p>';
            });
    }

    // Add debounce to search
    let searchTimeout;
    searchBar.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadPhotos, 300);
    });

    categoryFilter.addEventListener('change', loadPhotos);

    // Lightbox functionality
    function openLightbox(imgSrc) {
        lightboxImg.src = imgSrc;
        lightbox.classList.remove('hidden');
        lightbox.classList.add('visible');
    }

    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox || e.target.className === 'close') {
            lightbox.classList.remove('visible');
            lightbox.classList.add('hidden');
        }
    });

    // Close lightbox with escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && lightbox.classList.contains('visible')) {
            lightbox.classList.remove('visible');
            lightbox.classList.add('hidden');
        }
    });

    // Initial load
    loadPhotos();
});