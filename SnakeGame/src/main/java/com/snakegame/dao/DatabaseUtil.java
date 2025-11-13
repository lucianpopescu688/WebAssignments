package com.snakegame.dao;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;
import java.io.File;

public class DatabaseUtil {
    private static final String DB_URL = "jdbc:sqlite:snakegame.db";
    private static boolean initialized = false;

    static {
        try {
            // Explicitly load SQLite JDBC driver
            Class.forName("org.sqlite.JDBC");
            System.out.println("SQLite JDBC driver loaded successfully");
            initializeDatabase();
        } catch (ClassNotFoundException e) {
            System.err.println("SQLite JDBC driver not found!");
            e.printStackTrace();
        }
    }

    public static Connection getConnection() throws SQLException {
        try {
            Connection conn = DriverManager.getConnection(DB_URL);
            System.out.println("Database connection established successfully");
            return conn;
        } catch (SQLException e) {
            System.err.println("Failed to establish database connection:");
            System.err.println("DB URL: " + DB_URL);
            System.err.println("Working directory: " + System.getProperty("user.dir"));
            System.err.println("Error: " + e.getMessage());
            throw e;
        }
    }

    private static void initializeDatabase() {
        if (initialized) {
            return;
        }

        try {
            // Check if database file exists
            File dbFile = new File("snakegame.db");
            System.out.println("Database file path: " + dbFile.getAbsolutePath());
            System.out.println("Database file exists: " + dbFile.exists());
            System.out.println("Working directory: " + System.getProperty("user.dir"));

            try (Connection conn = getConnection();
                 Statement stmt = conn.createStatement()) {

                // Enable foreign keys
                stmt.execute("PRAGMA foreign_keys = ON");

                // Create users table
                String createUsersTable = """
                    CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        username TEXT UNIQUE NOT NULL,
                        password TEXT NOT NULL,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    )
                """;

                // Create games table
                String createGamesTable = """
                    CREATE TABLE IF NOT EXISTS games (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        user_id INTEGER NOT NULL,
                        score INTEGER DEFAULT 0,
                        time_spent INTEGER DEFAULT 0,
                        game_state TEXT,
                        obstacles TEXT,
                        started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        ended_at DATETIME,
                        FOREIGN KEY (user_id) REFERENCES users (id)
                    )
                """;

                // Create game_moves table
                String createGameMovesTable = """
                    CREATE TABLE IF NOT EXISTS game_moves (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        game_id INTEGER NOT NULL,
                        move_direction TEXT NOT NULL,
                        snake_position TEXT NOT NULL,
                        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (game_id) REFERENCES games (id)
                    )
                """;

                System.out.println("Creating tables...");
                stmt.execute(createUsersTable);
                System.out.println("Users table created/verified");

                stmt.execute(createGamesTable);
                System.out.println("Games table created/verified");

                stmt.execute(createGameMovesTable);
                System.out.println("Game moves table created/verified");

                // Insert sample users
                String insertSampleUsers = """
                    INSERT OR IGNORE INTO users (username, password) VALUES 
                    ('admin', 'admin123'),
                    ('player1', 'pass123'),
                    ('player2', 'pass456')
                """;
                stmt.execute(insertSampleUsers);
                System.out.println("Sample users inserted/verified");

                // Test query to verify everything works
                stmt.execute("SELECT COUNT(*) FROM users");
                System.out.println("Database test query successful");

                initialized = true;
                System.out.println("Database initialized successfully!");

            } catch (SQLException e) {
                System.err.println("SQLException during database initialization:");
                System.err.println("Error Code: " + e.getErrorCode());
                System.err.println("SQL State: " + e.getSQLState());
                System.err.println("Message: " + e.getMessage());
                e.printStackTrace();
                throw new RuntimeException("Database initialization failed", e);
            }

        } catch (Exception e) {
            System.err.println("Unexpected error during database initialization:");
            e.printStackTrace();
            throw new RuntimeException("Database initialization failed", e);
        }
    }

    // Method to test database connection
    public static boolean testConnection() {
        try (Connection conn = getConnection()) {
            return conn != null && !conn.isClosed();
        } catch (SQLException e) {
            System.err.println("Database connection test failed:");
            e.printStackTrace();
            return false;
        }
    }

    // Method to get database info
    public static void printDatabaseInfo() {
        try (Connection conn = getConnection();
             Statement stmt = conn.createStatement()) {

            System.out.println("=== Database Information ===");
            System.out.println("Database URL: " + DB_URL);
            System.out.println("Working directory: " + System.getProperty("user.dir"));

            // Check tables
            var rs = stmt.executeQuery("SELECT name FROM sqlite_master WHERE type='table'");
            System.out.println("Tables in database:");
            while (rs.next()) {
                System.out.println("- " + rs.getString("name"));
            }

            // Check user count
            rs = stmt.executeQuery("SELECT COUNT(*) as count FROM users");
            if (rs.next()) {
                System.out.println("Users in database: " + rs.getInt("count"));
            }

        } catch (SQLException e) {
            System.err.println("Error getting database info:");
            e.printStackTrace();
        }
    }
}