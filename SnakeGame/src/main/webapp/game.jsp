<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<%@ page import="com.snakegame.model.User" %>
<%
    User user = (User) session.getAttribute("user");
    if (user == null) {
        response.sendRedirect("login.jsp");
        return;
    }
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="game-container">
    <div class="game-header">
        <h1>🐍 Snake Game</h1>
        <div class="user-info">
            <span>Welcome, <strong><%= user.getUsername() %></strong>!</span>
            <a href="LogoutServlet" class="logout-btn" onclick="return confirmLogout()">Logout</a>
        </div>
    </div>

    <div class="game-stats">
        <div class="stat">
            <label>Score:</label>
            <span id="score">0</span>
        </div>
        <div class="stat">
            <label>Time:</label>
            <span id="timer">00:00</span>
        </div>
        <div class="stat">
            <label>Status:</label>
            <span id="gameStatus">Ready to Start</span>
        </div>
    </div>

    <div class="game-controls">
        <button id="startBtn" class="btn btn-primary">Start Game</button>
        <button id="pauseBtn" class="btn btn-secondary" disabled>Pause</button>
        <button id="resetBtn" class="btn btn-warning">Reset</button>
    </div>

    <div class="game-board-container">
        <canvas id="gameCanvas" width="400" height="400"></canvas>
    </div>

    <div class="game-instructions">
        <h3>How to Play:</h3>
        <ul>
            <li>Use arrow keys or WASD to control the snake</li>
            <li>Eat the red food to grow and increase score</li>
            <li>Avoid hitting walls, obstacles, or yourself</li>
            <li>Gray squares are obstacles - avoid them!</li>
        </ul>
    </div>

    <div id="gameOverModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Game Over!</h2>
            <p>Your Score: <span id="finalScore">0</span></p>
            <p>Time Played: <span id="finalTime">00:00</span></p>
            <div class="modal-buttons">
                <button id="playAgainBtn" class="btn btn-primary">Play Again</button>
                <button id="closeModalBtn" class="btn btn-secondary">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="js/snake.js"></script>

<script>
    function confirmLogout() {
        return confirm('Are you sure you want to logout? Any current game progress will be lost.');
    }

    // Prevent accidental page refresh during game
    window.addEventListener('beforeunload', function(e) {
        if (game && game.isRunning) {
            e.preventDefault();
            e.returnValue = 'You have an active game. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
</script>
</body>
</html>