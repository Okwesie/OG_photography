document.addEventListener('DOMContentLoaded', () => {
    // Portfolio filtering
    const portfolioGrid = document.querySelector('.portfolio-grid');
    const filterButtons = document.querySelectorAll('.filter-btn');

    // Sample portfolio items (replace with your actual items)
    const portfolioItems = [
        { category: 'event', image: 'placeholder.jpg', title: 'Event 1' },
        { category: 'nature', image: 'placeholder.jpg', title: 'Nature 1' },
        { category: 'architecture', image: 'placeholder.jpg', title: 'Architecture 1' },
        // Add more items as needed
    ];

    function renderPortfolioItems(items) {
        portfolioGrid.innerHTML = items.map(item => `
            <div class="portfolio-item ${item.category}">
                <img src="${item.image}" alt="${item.title}">
                <h3>${item.title}</h3>
            </div>
        `).join('');
    }

    renderPortfolioItems(portfolioItems);

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.dataset.filter;
            const filteredItems = filter === 'all' ? portfolioItems : portfolioItems.filter(item => item.category === filter);
            renderPortfolioItems(filteredItems);

            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
        });
    });

    // Parallax effect
    window.addEventListener('scroll', () => {
        const parallaxSection = document.querySelector('.parallax-section');
        const scrollPosition = window.pageYOffset;
        parallaxSection.style.backgroundPositionY = `${scrollPosition * 0.5}px`;
    });

    // Testimonial carousel
    const testimonials = [
        { name: 'Sarah A.', quote: 'OG_Photography made our wedding magical. Every moment was beautifully captured.' },
        { name: 'James T.', quote: 'Their nature shots are simply out of this world. I bought one for my living room!' },
        // Add more testimonials as needed
    ];

    let currentTestimonial = 0;
    const testimonialCarousel = document.querySelector('.testimonial-carousel');
    const prevButton = document.getElementById('prev-testimonial');
    const nextButton = document.getElementById('next-testimonial');

    function renderTestimonial() {
        const testimonial = testimonials[currentTestimonial];
        testimonialCarousel.innerHTML = `
            <blockquote>${testimonial.quote}</blockquote>
            <p>- ${testimonial.name}</p>
        `;
    }

    renderTestimonial();

    prevButton.addEventListener('click', () => {
        currentTestimonial = (currentTestimonial - 1 + testimonials.length) % testimonials.length;
        renderTestimonial();
    });

    nextButton.addEventListener('click', () => {
        currentTestimonial = (currentTestimonial + 1) % testimonials.length;
        renderTestimonial();
    });

    // Team members
    const teamMembers = [
        { name: 'John Doe', role: 'Lead Photographer', image: 'placeholder.jpg', bio: 'Passionate about capturing life\'s precious moments.', funFact: 'Can name over 100 bird species!' },
        { name: 'Jane Smith', role: 'Nature Specialist', image: 'placeholder.jpg', bio: 'Brings the beauty of nature to life through her lens.', funFact: 'Once camped for a week to get the perfect shot!' },
        // Add more team members as needed
    ];

    const teamGrid = document.querySelector('.team-grid');
    teamGrid.innerHTML = teamMembers.map(member => `
        <div class="team-member">
            <img src="${member.image}" alt="${member.name}">
            <h3>${member.name}</h3>
            <p>${member.role}</p>
            <p>${member.bio}</p>
            <p>Fun fact: ${member.funFact}</p>
        </div>
    `).join('');

    // Prints for sale
    const prints = [
        { title: 'Sunset over Mountains', price: 199, image: 'placeholder.jpg' },
        { title: 'City Lights at Night', price: 249, image: 'placeholder.jpg' },
        // Add more prints as needed
    ];

    const printsGrid = document.querySelector('.prints-grid');
    printsGrid.innerHTML = prints.map(print => `
        <div class="print-item">
            <img src="${print.image}" alt="${print.title}">
            <h3>${print.title}</h3>
            <p>$${print.price}</p>
            <button class="btn btn-primary">Buy Print</button>
            <button class="btn btn-outline">Add to Wishlist</button>
        </div>
    `).join('');

    // Form submissions
    const bookingForm = document.getElementById('booking-form');
    const newsletterForm = document.getElementById('newsletter-form');

    bookingForm.addEventListener('submit', (e) => {
        e.preventDefault();
        // Here you would typically send the form data to your server
        alert('Booking request submitted! We will contact you soon.');
    });

    newsletterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        // Here you would typically send the email to your server for newsletter signup
        alert('Thank you for subscribing to our newsletter!');
    });
});