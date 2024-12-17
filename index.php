<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OG_Photography - Where Every Moment Becomes Art</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #e74c3c;
            --text-color: #333;
            --background-color: #ecf0f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header styles */
        header {
            background-color: var(--primary-color);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        nav ul li a:hover {
            color: var(--accent-color);
        }

        /* Hero section styles */
        .hero {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('path/to/hero-image.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
        }

        .hero-content {
            max-width: 800px;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 300;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: var(--accent-color);
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #c0392b;
        }

        /* Portfolio section styles */
        .portfolio {
            padding: 80px 0;
            background-color: #fff;
        }

        .portfolio h2 {
            text-align: center;
            margin-bottom: 40px;
            font-weight: 300;
            color: var(--primary-color);
        }

        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .portfolio-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .portfolio-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .portfolio-item-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .portfolio-item:hover .portfolio-item-overlay {
            opacity: 1;
        }

        .portfolio-item-title {
            color: #fff;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        /* Why Choose Us section styles */
        .why-choose-us {
            padding: 80px 0;
            background-color: var(--primary-color);
            color: #fff;
        }

        .why-choose-us h2 {
            text-align: center;
            margin-bottom: 40px;
            font-weight: 300;
        }

        .why-choose-us-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .why-choose-us-item {
            background-color: var(--secondary-color);
            padding: 30px;
            border-radius: 8px;
            text-align: center;
        }

        .why-choose-us-item i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--accent-color);
        }

        .why-choose-us-item h3 {
            margin-bottom: 15px;
            font-weight: 300;
        }

        /* Booking section styles */
        .booking {
            padding: 80px 0;
            background-color: #fff;
        }

        .booking h2 {
            text-align: center;
            margin-bottom: 40px;
            font-weight: 300;
            color: var(--primary-color);
        }

        .booking-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        /* Team section styles */
        .team {
            padding: 80px 0;
            background-color: var(--background-color);
        }

        .team h2 {
            text-align: center;
            margin-bottom: 40px;
            font-weight: 300;
            color: var(--primary-color);
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .team-member {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-5px);
        }

        .team-member img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .team-member-info {
            padding: 20px;
            text-align: center;
        }

        .team-member-info h3 {
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .team-member-info p {
            font-style: italic;
            margin-bottom: 15px;
        }

        /* Footer styles */
        footer {
            background-color: var(--primary-color);
            color: #fff;
            padding: 40px 0;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-links ul {
            list-style: none;
            display: flex;
        }

        .footer-links ul li {
            margin-right: 20px;
        }

        .footer-links ul li:last-child {
            margin-right: 0;
        }

        .footer-links ul li a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links ul li a:hover {
            color: var(--accent-color);
        }

        .footer-social a {
            color: #fff;
            font-size: 1.5rem;
            margin-left: 15px;
            transition: color 0.3s ease;
        }

        .footer-social a:hover {
            color: var(--accent-color);
        }

        .footer-bottom {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .portfolio-grid,
            .why-choose-us-grid,
            .team-grid {
                grid-template-columns: 1fr;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-links ul {
                flex-direction: column;
                margin-bottom: 20px;
            }

            .footer-links ul li {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#portfolio">Portfolio</a></li>
                <li><a href="#booking">Book Now</a></li>
                <li><a href="#team">Our Team</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="Frontend/pages/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="public/login.php">Login</a></li>
                    <li><a href="public/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section id="home" class="hero">
            <div class="hero-content">
                <h1>Where Every Moment Becomes Art</h1>
                <p>Experience the world through the lens of OG_Photography. From breathtaking landscapes to unforgettable events, our shots tell your story.</p>
                <a href="#portfolio" class="btn">View Our Portfolio</a>
                <a href="#booking" class="btn">Book a Session</a>
            </div>
        </section>

        <section id="portfolio" class="portfolio">
            <div class="container">
                <h2>Our Portfolio</h2>
                <div class="portfolio-grid">
                    <?php
                    $portfolio_items = [
                        ['title' => 'After Church', 'image' => 'uploads/images/event/After_church.jpg'],
                        ['title' => 'Ubora 1', 'image' => 'uploads/images/event/Ubora1.jpg'],
                        ['title' => 'Ubora 2', 'image' => 'uploads/images/event/Ubora2.jpg'],
                        ['title' => 'Cute', 'image' => 'uploads/images/model/cute.jpg'],
                        ['title' => 'Fran Full', 'image' => 'uploads/images/model/fran_full.jpg'],
                        ['title' => 'Fran 1', 'image' => 'uploads/images/model/fran1.jpg'],
                        ['title' => 'Beach', 'image' => 'uploads/images/nature/beach.jpg'],
                        ['title' => 'Contrast', 'image' => 'uploads/images/nature/contrast.jpg'],
                        ['title' => 'Fire', 'image' => 'uploads/images/nature/fire.jpg'],
                    ];

                    foreach ($portfolio_items as $item) {
                        echo '<div class="portfolio-item">';
                        echo '<img src="' . $item['image'] . '" alt="' . $item['title'] . '">';
                        echo '<div class="portfolio-item-overlay">';
                        echo '<h3 class="portfolio-item-title">' . $item['title'] . '</h3>';
                        echo '<a href="#" class="btn">View Full Image</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </section>

        <section class="why-choose-us">
            <div class="container">
                <h2>Why Choose Us</h2>
                <div class="why-choose-us-grid">
                    <div class="why-choose-us-item">
                        <i class="fas fa-camera"></i>
                        <h3>Unmatched Event Photography</h3>
                        <p>Capture the moments that matter most.</p>
                    </div>
                    <div class="why-choose-us-item">
                        <i class="fas fa-tree"></i>
                        <h3>Stunning Nature Shots</h3>
                        <p>See the world from a new perspective.</p>
                    </div>
                    <div class="why-choose-us-item">
                        <i class="fas fa-building"></i>
                        <h3>Architectural Brilliance</h3>
                        <p>Frame the beauty of design and structure.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="booking" class="booking">
            <div class="container">
                <h2>Book a Session</h2>
                <form class="booking-form" action="process_booking.php" method="POST">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Preferred Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="photography-type">Type of Photography</label>
                        <select id="photography-type" name="photography-type" required>
                            <option value="">Select a type</option>
                            <option value="event">Event</option>
                            <option value="nature">Nature</option>
                            <option value="architecture">Architecture</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <button type="submit" class="btn">Book Now</button>
                </form>
            </div>
        </section>

        <section id="team" class="team">
            <div class="container">
                <h2>Meet Our Team</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <img src="uploads/images/nature/Descend.jpg" alt="Caleb">
                        <div class="team-member-info">
                            <h3>Caleb Okwesie Arthur</h3>
                            <p>Lead Photographer</p>
                            <p>Specializes in landscape and nature photography.</p>
                        </div>
                    </div>
                    <div class="team-member">
                        <img src="uploads/images/model/fran_Ubora.jpg" alt="Frances">
                        <div class="team-member-info">
                            <h3>Frances Fianhagbe</h3>
                            <p>Event Photographer</p>
                            <p>Captures the essence of every celebration.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#portfolio">Portfolio</a></li>
                        <li><a href="#booking">Book Now</a></li>
                        <li><a href="#team">Our Team</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-social">
                    <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 OG_Photography. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Smooth scrolling for navigation links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });

            // Lightbox functionality for portfolio items
            $('.portfolio-item-overlay .btn').on('click', function(event) {
                event.preventDefault();
                var imageUrl = $(this).closest('.portfolio-item').find('img').attr('src');
                var imageTitle = $(this).closest('.portfolio-item').find('.portfolio-item-title').text();
                
                $('body').append('<div class="lightbox"><img src="' + imageUrl + '" alt="' + imageTitle + '"><p>' + imageTitle + '</p><span class="close">&times;</span></div>');
                
                $('.lightbox').fadeIn();
                
                $('.lightbox .close').on('click', function() {
                    $('.lightbox').fadeOut(function() {
                        $(this).remove();
                    });
                });
            });
        });
    </script>
</body>
</html>