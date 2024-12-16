<?php
function renderSidebar($userType) {
    $menuItems = [
        'admin' => [
            ['Dashboard', '../public/admin_dashboard.php', 'fas fa-tachometer-alt'],
            ['Manage Users', '../Frontend/pages/manage_users.php', 'fas fa-users'],
            ['Manage Orders', '../Frontend/pages/manage_orders.php', 'fas fa-shopping-cart'],
            ['Manage Gallery', '../Frontend/pages/manage_gallery.php', 'fas fa-images'],
            ['Analytics', '../Frontend/pages/analytics.php', 'fas fa-chart-bar'],
            ['Settings', '../Frontend/pages/settings.php', 'fas fa-cog'],
            ['Logout', '../Frontend/pages/logout.php', 'fas fa-sign-out-alt', true],
        ],
        'customer' => [
            ['Dashboard', '../public/customer_dashboard.php', 'fas fa-tachometer-alt'],
            ['My Bookings', '../Frontend/pages/mybookings.php', 'fas fa-calendar-alt'],
            ['My Orders', '../Frontend/pages/myorders.php', 'fas fa-shopping-bag'],
            ['Gallery', '../uploads/images/gallery.php', 'fas fa-images'],
            ['Profile', '../Frontend/pages/customer_profile.php', 'fas fa-user'],
            ['Leave a Review', '../pages/leave_review.php', 'fas fa-star']
            ['Logout', '../Frontend/pages/logout.php', 'fas fa-sign-out-alt', true],
        ],
        'photographer' => [
            ['Dashboard', '../public/photographer_dashboard.php', 'fas fa-tachometer-alt'],
            ['Gallery', '../uploads/images/gallery.php', 'fas fa-images'],
            ['Schedule', '../Frontend/pages/schedule.php', 'fas fa-calendar-alt'],
            ['Upload Photos', '../Frontend/pages/upload_photo.php', 'fas fa-upload'],
            ['Profile', '../Frontend/pages/photographer_profile.php', 'fas fa-user'],
            ['Logout', '../Frontend/pages/logout.php', 'fas fa-sign-out-alt', true],
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
