package com.snakegame.dao;

import com.snakegame.model.Game;
import com.snakegame.model.GameMove;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class GameDAO {

    // Test database connection on initialization
    public GameDAO() {
        if (!DatabaseUtil.testConnection()) {
            System.err.println("WARNING: Database connection test failed in GameDAO constructor");
        } else {
            System.out.println("GameDAO initialized - database connection OK");
            DatabaseUtil.printDatabaseInfo();
        }
    }

    public int createGame(Game game) {
        String sql = "INSERT INTO games (user_id, score, time_spent, game_state, obstacles, started_at) VALUES (?, ?, ?, ?, ?, ?)";

        System.out.println("=== GameDAO.createGame called ===");
        System.out.println("Game user ID: " + game.getUserId());
        System.out.println("Game obstacles: " + game.getObstacles());

        // Validate input
        if (game.getUserId() <= 0) {
            System.err.println("ERROR: Invalid user ID: " + game.getUserId());
            return -1;
        }

        if (game.getObstacles() == null || game.getObstacles().trim().isEmpty()) {
            System.err.println("ERROR: Obstacles is null or empty");
            return -1;
        }

        Connection conn = null;
        PreparedStatement stmt = null;

        try {
            // Test connection first
            if (!DatabaseUtil.testConnection()) {
                System.err.println("ERROR: Database connection test failed before creating game");
                return -1;
            }

            conn = DatabaseUtil.getConnection();
            System.out.println("Database connection obtained successfully");

            // Check if connection is valid
            if (conn == null || conn.isClosed()) {
                System.err.println("ERROR: Connection is null or closed");
                return -1;
            }

            // Verify user exists
            try (PreparedStatement userCheck = conn.prepareStatement("SELECT id FROM users WHERE id = ?")) {
                userCheck.setInt(1, game.getUserId());
                ResultSet userRs = userCheck.executeQuery();
                if (!userRs.next()) {
                    System.err.println("ERROR: User with ID " + game.getUserId() + " does not exist");
                    return -1;
                }
                System.out.println("User validation successful");
            }

            stmt = conn.prepareStatement(sql);
            System.out.println("PreparedStatement created successfully");

            // Bind parameters with detailed logging
            stmt.setInt(1, game.getUserId());
            System.out.println("Parameter 1 (user_id) set to: " + game.getUserId());

            stmt.setInt(2, 0);
            System.out.println("Parameter 2 (score) set to: 0");

            stmt.setLong(3, 0L);
            System.out.println("Parameter 3 (time_spent) set to: 0");

            stmt.setString(4, "{}");
            System.out.println("Parameter 4 (game_state) set to: {}");

            stmt.setString(5, game.getObstacles());
            System.out.println("Parameter 5 (obstacles) set to: " + game.getObstacles());

            Timestamp now = new Timestamp(System.currentTimeMillis());
            stmt.setTimestamp(6, now);
            System.out.println("Parameter 6 (started_at) set to: " + now);

            System.out.println("All parameters bound successfully");
            System.out.println("Executing SQL: " + sql);

            int rows = stmt.executeUpdate();
            System.out.println("SQL executed. Rows affected: " + rows);

            if (rows == 0) {
                System.err.println("ERROR: No rows were inserted");
                return -1;
            }

            try (PreparedStatement lastIdStmt = conn.prepareStatement("SELECT last_insert_rowid()")) {
                ResultSet rs = lastIdStmt.executeQuery();
                if (rs.next()) {
                    int gameId = rs.getInt(1);
                    System.out.println("SUCCESS: Generated game ID: " + gameId);
                    return gameId;
                } else {
                    System.err.println("ERROR: Could not retrieve generated ID");
                    return -1;
                }
            }

        } catch (SQLException e) {
            System.err.println("=== SQLException in GameDAO.createGame ===");
            System.err.println("Error Code: " + e.getErrorCode());
            System.err.println("SQL State: " + e.getSQLState());
            System.err.println("Message: " + e.getMessage());
            System.err.println("SQL: " + sql);
            e.printStackTrace();
            return -1;
        } catch (Exception e) {
            System.err.println("=== Unexpected exception in GameDAO.createGame ===");
            System.err.println("Exception type: " + e.getClass().getSimpleName());
            System.err.println("Message: " + e.getMessage());
            e.printStackTrace();
            return -1;
        } finally {
            // Clean up resources
            try {
                if (stmt != null) stmt.close();
                if (conn != null) conn.close();
                System.out.println("Database resources cleaned up");
            } catch (SQLException e) {
                System.err.println("Error closing database resources:");
                e.printStackTrace();
            }
        }
    }

    public boolean updateGame(Game game) {
        String sql = "UPDATE games SET score = ?, time_spent = ?, game_state = ?, ended_at = ? WHERE id = ?";

        System.out.println("GameDAO.updateGame called for game ID: " + game.getId());

        try (Connection conn = DatabaseUtil.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setInt(1, game.getScore());
            stmt.setLong(2, game.getTimeSpent());
            stmt.setString(3, game.getGameState());
            stmt.setTimestamp(4, game.getEndedAt());
            stmt.setInt(5, game.getId());

            int rowsAffected = stmt.executeUpdate();
            System.out.println("Update query executed. Rows affected: " + rowsAffected);
            return rowsAffected > 0;

        } catch (SQLException e) {
            System.err.println("SQLException in GameDAO.updateGame:");
            System.err.println("Error Code: " + e.getErrorCode());
            System.err.println("SQL State: " + e.getSQLState());
            System.err.println("Message: " + e.getMessage());
            e.printStackTrace();
            return false;
        }
    }

    public Game getGameById(int id) {
        String sql = "SELECT * FROM games WHERE id = ?";

        System.out.println("GameDAO.getGameById called for ID: " + id);

        try (Connection conn = DatabaseUtil.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setInt(1, id);
            ResultSet rs = stmt.executeQuery();

            if (rs.next()) {
                Game game = new Game();
                game.setId(rs.getInt("id"));
                game.setUserId(rs.getInt("user_id"));
                game.setScore(rs.getInt("score"));
                game.setTimeSpent(rs.getLong("time_spent"));
                game.setGameState(rs.getString("game_state"));
                game.setObstacles(rs.getString("obstacles"));
                game.setStartedAt(rs.getTimestamp("started_at"));
                game.setEndedAt(rs.getTimestamp("ended_at"));

                System.out.println("Game found and loaded successfully");
                return game;
            } else {
                System.out.println("No game found with ID: " + id);
            }

        } catch (SQLException e) {
            System.err.println("SQLException in GameDAO.getGameById:");
            e.printStackTrace();
        }

        return null;
    }

    public List<Game> getGamesByUserId(int userId) {
        List<Game> games = new ArrayList<>();
        String sql = "SELECT * FROM games WHERE user_id = ? ORDER BY started_at DESC";

        try (Connection conn = DatabaseUtil.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setInt(1, userId);
            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                Game game = new Game();
                game.setId(rs.getInt("id"));
                game.setUserId(rs.getInt("user_id"));
                game.setScore(rs.getInt("score"));
                game.setTimeSpent(rs.getLong("time_spent"));
                game.setGameState(rs.getString("game_state"));
                game.setObstacles(rs.getString("obstacles"));
                game.setStartedAt(rs.getTimestamp("started_at"));
                game.setEndedAt(rs.getTimestamp("ended_at"));
                games.add(game);
            }

            System.out.println("Found " + games.size() + " games for user ID: " + userId);

        } catch (SQLException e) {
            System.err.println("SQLException in GameDAO.getGamesByUserId:");
            e.printStackTrace();
        }

        return games;
    }

    public boolean addGameMove(GameMove move) {
        String sql = "INSERT INTO game_moves (game_id, move_direction, snake_position) VALUES (?, ?, ?)";

        try (Connection conn = DatabaseUtil.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setInt(1, move.getGameId());
            stmt.setString(2, move.getMoveDirection());
            stmt.setString(3, move.getSnakePosition());

            int rowsAffected = stmt.executeUpdate();
            return rowsAffected > 0;

        } catch (SQLException e) {
            System.err.println("SQLException in GameDAO.addGameMove:");
            e.printStackTrace();
            return false;
        }
    }

    public List<GameMove> getGameMoves(int gameId) {
        List<GameMove> moves = new ArrayList<>();
        String sql = "SELECT * FROM game_moves WHERE game_id = ? ORDER BY timestamp";

        try (Connection conn = DatabaseUtil.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setInt(1, gameId);
            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                GameMove move = new GameMove();
                move.setId(rs.getInt("id"));
                move.setGameId(rs.getInt("game_id"));
                move.setMoveDirection(rs.getString("move_direction"));
                move.setSnakePosition(rs.getString("snake_position"));
                move.setTimestamp(rs.getTimestamp("timestamp"));
                moves.add(move);
            }

        } catch (SQLException e) {
            System.err.println("SQLException in GameDAO.getGameMoves:");
            e.printStackTrace();
        }

        return moves;
    }
}