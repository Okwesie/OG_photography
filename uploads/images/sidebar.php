<?php
function renderSidebar($userType) {
    $menuItems = [
        'admin' => [
            ['Dashboard', 'http://localhost/OG_photo_2/public/admin_dashboard.php', 'fas fa-tachometer-alt'],
            ['Manage Users', 'http://localhost/OG_photo_2/Frontend/pages/manage_users.php', 'fas fa-users'],
            ['Manage Orders', 'http://localhost/OG_photo_2/Frontend/pages/manage_orders.php', 'fas fa-shopping-cart'],
            ['Manage Gallery', 'http://localhost/OG_photo_2/Frontend/pages/manage_gallery.php', 'fas fa-images'],
            ['Analytics', 'http://localhost/OG_photo_2/Frontend/pages/analytics.php', 'fas fa-chart-bar'],
            ['Manage Bookings', 'http://localhost/OG_photo_2/Frontend/pages/manage_bookings.php', 'fas fa-calendar-check'],
            ['Settings', 'http://localhost/OG_photo_2/Frontend/pages/settings.php', 'fas fa-cog'],
            ['Logout', 'http://localhost/OG_photo_2/Frontend/pages/logout.php', 'fas fa-sign-out-alt', true],
        ],
        'customer' => [
            ['Dashboard', 'http://localhost/OG_photo_2/public/customer_dashboard.php', 'fas fa-tachometer-alt'],
            ['My Bookings', 'http://localhost/OG_photo_2/Frontend/pages/mybookings.php', 'fas fa-calendar-alt'],
            ['My Orders', 'http://localhost/OG_photo_2/Frontend/pages/myorders.php', 'fas fa-shopping-bag'],
            ['Gallery', 'http://localhost/OG_photo_2/uploads/images/gallery.php', 'fas fa-images'],
            ['Profile', 'http://localhost/OG_photo_2/Frontend/pages/customer_profile.php', 'fas fa-user'],
            ['Leave a Review', 'http://localhost/OG_photo_2/Frontend/pages/leave_review.php', 'fas fa-star'],
            ['Logout', 'http://localhost/OG_photo_2/Frontend/pages/logout.php', 'fas fa-sign-out-alt', true],
        ],
        'photographer' => [
            ['Dashboard', 'http://localhost/OG_photo_2/public/photographer_dashboard.php', 'fas fa-tachometer-alt'],
            ['Gallery', 'http://localhost/OG_photo_2/uploads/images/gallery.php', 'fas fa-images'],
            ['Schedule', 'http://localhost/OG_photo_2/Frontend/pages/schedule.php', 'fas fa-calendar-alt'],
            ['Upload Photos', 'http://localhost/OG_photo_2/Frontend/pages/upload_photo.php', 'fas fa-upload'],
            ['Profile', 'http://localhost/OG_photo_2/Frontend/pages/photographer_profile.php', 'fas fa-user'],
            ['Logout', 'http://localhost/OG_photo_2/Frontend/pages/logout.php', 'fas fa-sign-out-alt', true],
        ],
    ];

    echo '<div class="sidebar">';
    echo '<div class="sidebar-header">';
    echo '<h3>OG_Photography</h3>';
    echo '</div>';
    echo '<ul class="sidebar-menu">';
    foreach ($menuItems[$userType] as $item) {
        if (isset($item[3]) && $item[3] === true) {
            echo '<li><a href="' . $item[1] . '" onclick="return confirmLogout();"><i class="' . $item[2] . '"></i> ' . $item[0] . '</a></li>';
        } else {
            echo '<li><a href="' . $item[1] . '"><i class="' . $item[2] . '"></i> ' . $item[0] . '</a></li>';
        }
    }
    echo '</ul>';
    echo '</div>';
}
?>
<script>
function confirmLogout() {
    return confirm("Are you sure you want to log out?");
}
</script>