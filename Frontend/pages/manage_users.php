<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/admin_dashboard.php');
    exit();
}

$pageTitle = "Manage users";
//include 'includes/header.php';

// Include database connection file
require_once 'dbconnection.php';

// Handle form submissions for adding, editing, or deleting users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $full_name = $_POST['full_name'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO users_og (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $password, $full_name, $role);
        $stmt->execute();
        $stmt->close();
        header('Location: manage_users.php');
    }

    if (isset($_POST['edit_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $full_name = $_POST['full_name'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("UPDATE users_og SET username = ?, email = ?, full_name = ?, role = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $username, $email, $full_name, $role, $user_id);
        $stmt->execute();
        $stmt->close();
        header('Location: manage_users.php');
    }

    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];

        $stmt = $conn->prepare("DELETE FROM users_og WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        header('Location: manage_users.php');
    }
}

// Fetch all users from the database
$result = $conn->query("SELECT * FROM users_og");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="http://localhost/OG_photo_2/Frontend/styles/manage_users.css">
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="http://localhost/OG_photo_2/public/admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Manage Users</h1>

    <button class="add-recipe-btn" onclick="openModal('addUserModal')">Add New User</button>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <button class="view-btn" onclick="viewUser(<?= $user['user_id'] ?>)">View</button>
                        <button class="edit-btn" onclick="editUser(<?= $user['user_id'] ?>)">Edit</button>
                        <form action="" method="POST" style="display:inline-block;">
                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                            <button type="submit" name="delete_user" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addUserModal')">&times;</span>
        <h2>Add New User</h2>
        <form action="" method="POST">
            <label for="username">Username</label>
            <input type="text" name="username" required>

            <label for="email">Email</label>
            <input type="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" required>

            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" required>

            <label for="role">Role</label>
            <select name="role" required>
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
                <option value="photographer">Photographer</option>
            </select>

            <button type="submit" name="add_user">Add User</button>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function editUser(userId) {
    // Populate modal with user data
    // Open edit user modal (similar to add user modal)
}

function viewUser(userId) {
    // Redirect to view user details page
    window.location.href = `view_user.php?user_id=${userId}`;
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>

</body>
</html>
