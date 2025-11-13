// Snake Game Implementation
class SnakeGame {
    constructor() {
        this.canvas = document.getElementById('gameCanvas');
        this.ctx = this.canvas.getContext('2d');
        this.gridSize = 20;
        this.tileCount = this.canvas.width / this.gridSize;

        // Game state
        this.snake = [{ x: 10, y: 10 }];
        this.food = { x: 15, y: 15 };
        this.dx = 0;
        this.dy = 0;
        this.score = 0;
        this.isRunning = false;
        this.isPaused = false;
        this.directionalChangeThisTick = false;

        this.pauseStartTime = null;
        this.totalPausedTime = 0;

        this.gameLoop = null;
        this.obstacles = [];
        this.gameId = null;
        this.startTime = null;
        this.timerInterval = null;

        // UI elements
        this.scoreElement = document.getElementById('score');
        this.timerElement = document.getElementById('timer');
        this.statusElement = document.getElementById('gameStatus');
        this.startBtn = document.getElementById('startBtn');
        this.pauseBtn = document.getElementById('pauseBtn');
        this.resetBtn = document.getElementById('resetBtn');
        this.modal = document.getElementById('gameOverModal');
        this.finalScoreElement = document.getElementById('finalScore');
        this.finalTimeElement = document.getElementById('finalTime');
        this.playAgainBtn = document.getElementById('playAgainBtn');
        this.closeModalBtn = document.getElementById('closeModalBtn');
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.draw();
    }

    setupEventListeners() {
        // Button events
        this.startBtn.addEventListener('click', () => this.startGame());
        this.pauseBtn.addEventListener('click', () => this.togglePause());
        this.resetBtn.addEventListener('click', () => this.resetGame());
        this.playAgainBtn.addEventListener('click', () => this.playAgain());
        this.closeModalBtn.addEventListener('click', () => this.closeModal());

        // Keyboard events
        document.addEventListener('keydown', (e) => this.handleKeypress(e));

        // Prevent arrow keys from scrolling
        window.addEventListener('keydown', (e) => {
            if(['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.code)) {
                e.preventDefault();
            }
        });
    }

    async startGame() {
        if (this.isRunning) return;

        try {
            const response = await fetch('GameServlet', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=startGame'
            });

            const result = await response.json();

            if (result.success) {
                this.gameId = result.gameId;
                this.obstacles = result.obstacles;
                this.resetGameState();
                this.isRunning = true;
                this.isPaused = false;
                this.startTime = Date.now();
                this.totalPausedTime = 0;
                this.pauseStartTime = null;
                this.timerElement.textContent = '00:00';
                this.startTimer();
                this.generateFood();
                this.gameLoop = setInterval(() => this.update(), 150);
                this.updateUI();
                this.updateStatus('Playing');

                console.log('Game started with ID:', this.gameId);
                console.log('Obstacles:', this.obstacles);
            } else {
                alert('Failed to start game: ' + result.message);
            }
        } catch (error) {
            console.error('Error starting game:', error);
            alert('Error starting game. Please try again.');
        }
    }

    resetGameState() {
        this.snake = [{ x: 10, y: 10 }];
        this.dx = 0;
        this.dy = 0;
        this.directionalChangeThisTick = false;
        this.score = 0;
        this.updateScore();
    }

    togglePause() {
        if (!this.isRunning) return;

        const wasPaused = this.isPaused;
        this.isPaused = !this.isPaused;

        if (this.isPaused) {
            clearInterval(this.gameLoop);
            this.pauseStartTime = Date.now();
            this.pauseBtn.textContent = 'Resume';
            this.updateStatus('Paused');
        } else {

            const now = Date.now();
            if (this.pauseStartTime) {
                const pauseDuration = now - this.pauseStartTime;  // în ms
                this.totalPausedTime += pauseDuration;
                this.pauseStartTime = null;
            }
            this.gameLoop = setInterval(() => this.update(), 150);
            this.pauseBtn.textContent = 'Pause';
            this.updateStatus('Playing');
        }
    }

    async resetGame() {
        if (this.isRunning && !confirm('Are you sure you want to reset the current game?')) {
            return;
        }

        this.stopGame();
        this.resetGameState();
        this.obstacles = [];
        this.draw();
        this.updateStatus('Ready to Start');
    }

    stopGame() {
        this.isRunning = false;
        this.isPaused = false;

        if (this.gameLoop) {
            clearInterval(this.gameLoop);
            this.gameLoop = null;
        }

        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }

        this.updateUI();
    }

    handleKeypress(e) {
        const key = e.code;

        if (key === 'KeyP' && this.isRunning && !this.isPaused) {
            this.togglePause();
            return;
        }

        if (key === 'KeyR' && this.isRunning && this.isPaused) {
            this.togglePause();
            return;
        }

        if (!this.isRunning || this.isPaused) return;

        if (this.directionalChangeThisTick) {
            // Prevent multiple directional changes in the same tick
            return;
        }

        switch (key) {
            case 'ArrowUp':
            case 'KeyW':
                if (this.dy === 0) {
                    this.dx = 0;
                    this.dy = -1;
                    this.recordMove('UP');
                    this.directionalChangeThisTick = true;
                }
                break;
            case 'ArrowDown':
            case 'KeyS':
                if (this.dy === 0) {
                    this.dx = 0;
                    this.dy = 1;
                    this.recordMove('DOWN');
                    this.directionalChangeThisTick = true;
                }
                break;
            case 'ArrowLeft':
            case 'KeyA':
                if (this.dx === 0) {
                    this.dx = -1;
                    this.dy = 0;
                    this.recordMove('LEFT');
                    this.directionalChangeThisTick = true;
                }
                break;
            case 'ArrowRight':
            case 'KeyD':
                if (this.dx === 0) {
                    this.dx = 1;
                    this.dy = 0;
                    this.recordMove('RIGHT');
                    this.directionalChangeThisTick = true;
                }
                break;
            case 'Space':
                e.preventDefault();
                this.togglePause();
                break;
        }
    }

    async recordMove(direction) {
        if (!this.gameId) return;

        try {
            await fetch('GameMoveServlet', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `direction=${direction}&snakePosition=${JSON.stringify(this.snake)}`
            });
        } catch (error) {
            console.error('Error recording move:', error);
        }
    }

    update() {
        if (!this.isRunning || this.isPaused) return;

        this.directionalChangeThisTick = false; // Reset for the next tick

        if (this.dx === 0 && this.dy === 0) {
            this.draw();
            return;
        }
        // Move snake head
        const head = { x: this.snake[0].x + this.dx, y: this.snake[0].y + this.dy };

        // Check wall collision
        if (head.x < 0 || head.x >= this.tileCount || head.y < 0 || head.y >= this.tileCount) {
            this.gameOver();
            return;
        }

        // Check self collision
        if (this.snake.some(segment => segment.x === head.x && segment.y === head.y)) {
            this.gameOver();
            return;
        }

        // Check obstacle collision
        if (this.obstacles.some(obstacle => obstacle.x === head.x && obstacle.y === head.y)) {
            this.gameOver();
            return;
        }

        this.snake.unshift(head);

        // Check food collision
        if (head.x === this.food.x && head.y === this.food.y) {
            this.score += 10;
            this.updateScore();
            this.generateFood();
            this.updateGameState();
        } else {
            this.snake.pop();
        }

        this.draw();
    }

    generateFood() {
        do {
            this.food = {
                x: Math.floor(Math.random() * this.tileCount),
                y: Math.floor(Math.random() * this.tileCount)
            };
        } while (
            this.snake.some(segment => segment.x === this.food.x && segment.y === this.food.y) ||
            this.obstacles.some(obstacle => obstacle.x === this.food.x && obstacle.y === this.food.y)
            );
    }

    async updateGameState() {
        if (!this.gameId) return;

        try {
            await fetch('GameServlet', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=updateGame&score=${this.score}&gameState=${JSON.stringify({
                    snake: this.snake,
                    food: this.food,
                    direction: { dx: this.dx, dy: this.dy }
                })}`
            });
        } catch (error) {
            console.error('Error updating game state:', error);
        }
    }

    draw() {
        // Clear canvas
        this.ctx.fillStyle = '#1a202c';
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);

        // Draw grid lines
        this.ctx.strokeStyle = '#2d3748';
        this.ctx.lineWidth = 1;
        for (let i = 0; i <= this.tileCount; i++) {
            // Vertical lines
            this.ctx.beginPath();
            this.ctx.moveTo(i * this.gridSize, 0);
            this.ctx.lineTo(i * this.gridSize, this.canvas.height);
            this.ctx.stroke();

            // Horizontal lines
            this.ctx.beginPath();
            this.ctx.moveTo(0, i * this.gridSize);
            this.ctx.lineTo(this.canvas.width, i * this.gridSize);
            this.ctx.stroke();
        }

        // Draw obstacles
        this.ctx.fillStyle = '#718096';
        this.obstacles.forEach(obstacle => {
            this.ctx.fillRect(
                obstacle.x * this.gridSize + 1,
                obstacle.y * this.gridSize + 1,
                this.gridSize - 2,
                this.gridSize - 2
            );
        });

        // Draw food
        this.ctx.fillStyle = '#e53e3e';
        this.ctx.fillRect(
            this.food.x * this.gridSize + 2,
            this.food.y * this.gridSize + 2,
            this.gridSize - 4,
            this.gridSize - 4
        );

        // Draw snake
        this.snake.forEach((segment, index) => {
            if (index === 0) {
                // Snake head
                this.ctx.fillStyle = '#48bb78';
            } else {
                // Snake body
                this.ctx.fillStyle = '#68d391';
            }

            this.ctx.fillRect(
                segment.x * this.gridSize + 1,
                segment.y * this.gridSize + 1,
                this.gridSize - 2,
                this.gridSize - 2
            );
        });
    }

    async gameOver() {
        this.stopGame();
        this.updateStatus('Game Over');

        // End game on server
        if (this.gameId) {
            try {
                const response = await fetch('GameServlet', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=endGame&score=${this.score}&gameState=${JSON.stringify({
                        snake: this.snake,
                        food: this.food,
                        finalScore: this.score
                    })}`
                });

                const result = await response.json();
                if (result.success) {
                    console.log('Game ended successfully. Time spent:', result.timeSpent, 'seconds');
                }
            } catch (error) {
                console.error('Error ending game:', error);
            }
        }

        this.showGameOverModal();
    }

    showGameOverModal() {
        this.finalScoreElement.textContent = this.score;
        this.finalTimeElement.textContent = this.timerElement.textContent;
        this.modal.style.display = 'flex';
    }

    closeModal() {
        this.modal.style.display = 'none';
    }

    playAgain() {
        this.closeModal();
        this.resetGame();
    }

    startTimer() {
        this.timerInterval = setInterval(() => {
            if (!this.isRunning || this.isPaused || !this.startTime) return;

            if (this.isPaused) return;

            const effectiveMs = Date.now() - this.startTime - this.totalPausedTime;
            const elapsed = Math.floor(effectiveMs / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;

            this.timerElement.textContent =
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    updateScore() {
        this.scoreElement.textContent = this.score;
    }

    updateStatus(status) {
        this.statusElement.textContent = status;
    }

    updateUI() {
        this.startBtn.disabled = this.isRunning;
        this.pauseBtn.disabled = !this.isRunning;

        if (!this.isRunning) {
            this.pauseBtn.textContent = 'Pause';
        }
    }
}

// Initialize game when page loads
let game;
document.addEventListener('DOMContentLoaded', () => {
    game = new SnakeGame();
});

// Handle page visibility change (pause game when tab is not active)
document.addEventListener('visibilitychange', () => {
    if (game && game.isRunning && !game.isPaused && document.hidden) {
        game.togglePause();
    }
});