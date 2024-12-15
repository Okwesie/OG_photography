<?php
session_start();

// Check for registration errors
if (isset($_SESSION['register_error'])) {
    $register_error = htmlspecialchars($_SESSION['register_error']);
    unset($_SESSION['register_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - OG_Photography</title>
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
        <h1>Sign Up</h1>
        
        <?php
        // Display registration error if exists
        if (isset($register_error)) {
            echo "<p class='error-message'>" . $register_error . "</p>";
        }
        ?>
        
        <form id="signupForm" action="register_user.php" method="POST">
            <input type="text" id="name" name="name" placeholder="Full Name" required>
            <span id="nameError" class="error"></span>
            
            <input type="email" id="email" name="email" placeholder="Email" required>
            <span id="emailError" class="error"></span>
            
            <input type="password" id="password" name="password" placeholder="Password" required>
            <span id="passwordError" class="error"></span>
            
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            <span id="confirmPasswordError" class="error"></span>
            
            <button type="submit">Sign Up</button>
        </form>
        
        <div class="signup-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(event) {
            // Client-side validation
            let isValid = true;
            
            // Get form elements
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            
            // Get error spans
            const nameError = document.getElementById('nameError');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            const confirmPasswordError = document.getElementById('confirmPasswordError');
            
            // Reset previous error messages
            nameError.textContent = '';
            emailError.textContent = '';
            passwordError.textContent = '';
            confirmPasswordError.textContent = '';
            
            // Name validation
            if (name.value.trim() === '') {
                nameError.textContent = 'Name cannot be empty';
                isValid = false;
            } else if (name.value.trim().length < 2) {
                nameError.textContent = 'Name must be at least 2 characters long';
                isValid = false;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.value.trim() === '') {
                emailError.textContent = 'Email cannot be empty';
                isValid = false;
            } else if (!emailRegex.test(email.value)) {
                emailError.textContent = 'Please enter a valid email address';
                isValid = false;
            }
            
            // Password validation
            if (password.value.trim() === '') {
                passwordError.textContent = 'Password cannot be empty';
                isValid = false;
            } else if (password.value.length < 8) {
                passwordError.textContent = 'Password must be at least 8 characters long';
                isValid = false;
            }
            
            // Confirm password validation
            if (confirmPassword.value.trim() === '') {
                confirmPasswordError.textContent = 'Please confirm your password';
                isValid = false;
            } else if (password.value !== confirmPassword.value) {
                confirmPasswordError.textContent = 'Passwords do not match';
                isValid = false;
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>