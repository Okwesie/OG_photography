<?php
session_start();
require_once 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the admin
renderSidebar($_SESSION['role']);

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = "Manage users";
//include 'includes/header.php';

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
    <link rel="stylesheet" href="admin_global.css"> <!-- Global Admin CSS --></head>

<body>

<main>
<div class="main-content"> 
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
                        <form action="" method="POST" style="display:inline-block;" onsubmit="return confirmDelete();">
                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                            <button type="submit" name="delete_user" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
            </div>
</main>

<!-- Add User Modal -->
            <style>
                .modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(5px);
  transition: all 0.3s ease;
}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto;
  padding: 2rem;
  border: 1px solid #888;
  width: 90%;
  max-width: 500px;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
  transform: translateY(-50px);
  opacity: 0;
  transition: all 0.3s ease;
}

.modal.show .modal-content {
  transform: translateY(0);
  opacity: 1;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
  transition: color 0.3s ease;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}

.modal h2 {
  margin-top: 0;
  color: #333;
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
}

.modal form {
  display: flex;
  flex-direction: column;
}

.modal label {
  margin-bottom: 0.5rem;
  color: #555;
  font-weight: 500;
}

.modal input[type="text"],
.modal input[type="email"],
.modal input[type="password"],
.modal select {
  width: 100%;
  padding: 0.75rem;
  margin-bottom: 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.modal input[type="text"]:focus,
.modal input[type="email"]:focus,
.modal input[type="password"]:focus,
.modal select:focus {
  outline: none;
  border-color: #4a90e2;
  box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}

.modal button[type="submit"] {
  background-color: #4a90e2;
  color: white;
  padding: 0.75rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}

.modal button[type="submit"]:hover {
  background-color: #3a7bc8;
}

</style>

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
    const modal = document.getElementById(modalId);
    modal.style.display = 'block';
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function editUser(userId) {
    window.location.href = `edit_user.php?user_id=${userId}`;
}

function viewUser(userId) {
    window.location.href = `view_user.php?user_id=${userId}`;
}

function confirmDelete() {
    return confirm("Are you sure you want to delete this user?");
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal(event.target.id);
    }
}
</script>

</body>
</html>

