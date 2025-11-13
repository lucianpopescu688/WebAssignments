package com.snakegame.model;

import java.sql.Timestamp;

public class GameMove {
    private int id;
    private int gameId;
    private String moveDirection;
    private String snakePosition;
    private Timestamp timestamp;

    public GameMove() {}

    public GameMove(int gameId, String moveDirection, String snakePosition) {
        this.gameId = gameId;
        this.moveDirection = moveDirection;
        this.snakePosition = snakePosition;
    }

    // Getters and setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getGameId() {
        return gameId;
    }

    public void setGameId(int gameId) {
        this.gameId = gameId;
    }

    public String getMoveDirection() {
        return moveDirection;
    }

    public void setMoveDirection(String moveDirection) {
        this.moveDirection = moveDirection;
    }

    public String getSnakePosition() {
        return snakePosition;
    }

    public void setSnakePosition(String snakePosition) {
        this.snakePosition = snakePosition;
    }

    public Timestamp getTimestamp() {
        return timestamp;
    }

    public void setTimestamp(Timestamp timestamp) {
        this.timestamp = timestamp;
    }
}