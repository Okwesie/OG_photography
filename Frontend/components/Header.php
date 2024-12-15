<header>
    <nav>
        <div class="logo">
            <a href="index.php">OG_Photography</a>
        </div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="booking.php">Book Now</a></li>
            <?php
            session_start();
            if (isset($_SESSION['user_id'])) {
                echo '<li><a href="profile.php">Profile</a></li>';
                echo '<li><a href="logout.php">Logout</a></li>';
            } else {
                echo '<li><a href="login.php">Login</a></li>';
                echo '<li><a href="register.php">Register</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>