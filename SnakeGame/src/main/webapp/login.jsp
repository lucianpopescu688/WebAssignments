<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Game - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-container">
    <div class="login-form">
        <h1>🐍 Snake Game</h1>

        <% if (request.getParameter("logout") != null) { %>
        <div class="success-message">You have been logged out successfully!</div>
        <% } %>

        <% if (request.getAttribute("error") != null) { %>
        <div class="error-message"><%= request.getAttribute("error") %></div>
        <% } %>

        <% if (request.getAttribute("success") != null) { %>
        <div class="success-message"><%= request.getAttribute("success") %></div>
        <% } %>

        <form id="loginForm" action="LoginServlet" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required
                       value="<%= request.getParameter("username") != null ? request.getParameter("username") : "" %>"
                       minlength="3" maxlength="50">
                <div class="validation-message" id="usernameError"></div>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required
                       minlength="4" maxlength="100">
                <div class="validation-message" id="passwordError"></div>
            </div>

            <div class="form-buttons">
                <button type="submit" name="action" value="login" class="btn btn-primary">Login</button>
                <button type="submit" name="action" value="register" class="btn btn-secondary">Register</button>
            </div>
        </form>

        <div class="demo-credentials">
            <h3>Demo Credentials:</h3>
            <p><strong>Username:</strong> admin <strong>Password:</strong> admin123</p>
            <p><strong>Username:</strong> player1 <strong>Password:</strong> pass123</p>
        </div>
    </div>
</div>

<script>
    // Client-side validation
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        let isValid = true;

        // Clear previous error messages
        document.getElementById('usernameError').textContent = '';
        document.getElementById('passwordError').textContent = '';

        // Username validation
        if (username.length < 3) {
            document.getElementById('usernameError').textContent = 'Username must be at least 3 characters long';
            isValid = false;
        }

        // Password validation
        if (password.length < 4) {
            document.getElementById('passwordError').textContent = 'Password must be at least 4 characters long';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    // Auto-focus on username field
    document.getElementById('username').focus();
</script>
</body>
</html>