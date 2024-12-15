<?php
session_start();

// Check for session timeout
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $timeout_message = "Your session has expired due to inactivity. Please log in again.";
}

// Check for login errors
if (isset($_SESSION['login_error'])) {
    $login_error = htmlspecialchars($_SESSION['login_error']);
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OG_Photography</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #e0f2fe, #e0e7ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h1 {
            text-align: center;
            color: #3b82f6;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input {
            margin-bottom: 1rem;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
        }
        button {
            background-color: #3b82f6;
            color: white;
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2563eb;
        }
        .signup-link {
            text-align: center;
            margin-top: 1rem;
        }
        .signup-link a {
            color: #3b82f6;
            text-decoration: none;
        }
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        .error {
            color: tomato;
            font-size: 0.8rem;
            margin-top: -0.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        
        <?php
        // Display timeout message if exists
        if (isset($timeout_message)) {
            echo "<p class='error-message'>" . $timeout_message . "</p>";
        }
        
        // Display login error if exists
        if (isset($login_error)) {
            echo "<p class='error-message'>" . $login_error . "</p>";
        }
        ?>
        
        <form id="loginForm" action="login_user.php" method="POST">
            <input type="email" id="email" name="email" placeholder="Email" required>
            <span id="emailError" class="error"></span>
            
            <input type="password" id="password" name="password" placeholder="Password" required>
            <span id="passwordError" class="error"></span>
            
            <button type="submit">Login</button>
        </form>

        <div class="signup-link">
            Don't have an account? <a href="register.php">Sign up</a>
        </div>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        // Reset previous error messages
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        emailError.textContent = '';
        passwordError.textContent = '';

        let isValid = true;

        // Email validation
        const email = document.getElementById('email');
        if (email.value.trim() === '') {
            emailError.textContent = 'Email cannot be empty';
            isValid = false;
        } else if (!/\S+@\S+\.\S+/.test(email.value)) {
            emailError.textContent = 'Invalid email format';
            isValid = false;
        }

        // Password validation
        const password = document.getElementById('password');
        if (password.value.trim() === '') {
            passwordError.textContent = 'Password cannot be empty';
            isValid = false;
        } else if (password.value.length < 6) {
            passwordError.textContent = 'Password must be at least 6 characters long';
            isValid = false;
        }

        // If validation fails, prevent form submission
        if (!isValid) {
            event.preventDefault();
        }
    });
    </script>
</body>
</html>