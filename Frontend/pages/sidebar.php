<?php
function renderSidebar($userType) {
    $menuItems = [
        'admin' => [
            ['Dashboard', 'http://localhost/OG_photo_2/public/admin_dashboard.php', 'fas fa-tachometer-alt'],
            ['Manage Users', '../pages/manage_users.php', 'fas fa-users'],
            ['Manage Orders', '../pages/manage_orders.php', 'fas fa-shopping-cart'],
            ['Manage Gallery', '../pages/manage_gallery.php', 'fas fa-images'],
            ['Manage Bookings', '../pages/manage_bookings.php', 'fas fa-calendar-check'],
            ['Analytics', '../pages/analytics.php', 'fas fa-chart-bar'],
            ['Settings', '../pages/settings.php', 'fas fa-cog'],
            ['Logout', '../pages/logout.php', 'fas fa-sign-out-alt', true],
        ],
        'customer' => [
            ['Dashboard', 'http://localhost/OG_photo_2/public/customer_dashboard.php', 'fas fa-tachometer-alt'],
            ['My Bookings', '../pages/mybookings.php', 'fas fa-calendar-alt'],
            ['My Orders', '../pages/myorders.php', 'fas fa-shopping-bag'],
            ['Gallery', 'http://localhost/OG_photo_2/uploads/images/gallery.php', 'fas fa-images'],
            ['Profile', '../pages/customer_profile.php', 'fas fa-user'],
            ['Leave a Review', '../pages/leave_review.php', 'fas fa-star'],
            ['Logout', '../pages/logout.php', 'fas fa-sign-out-alt', true],
        ],
        'photographer' => [
            ['Dashboard', 'http://localhost/OG_photo_2/public/photographer_dashboard.php', 'fas fa-tachometer-alt'],
            ['Gallery', 'http://localhost/OG_photo_2/uploads/images/gallery.php', 'fas fa-images'],
            ['Schedule', '../pages/schedule.php', 'fas fa-calendar-alt'],
            ['Upload Photos', '../pages/upload_photo.php', 'fas fa-upload'],
            ['Profile', '../pages/photographer_profile.php', 'fas fa-user'],
            ['Logout', '../pages/logout.php', 'fas fa-sign-out-alt', true],
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
