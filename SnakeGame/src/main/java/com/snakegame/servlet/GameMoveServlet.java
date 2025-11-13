package com.snakegame.servlet;

import com.snakegame.dao.GameDAO;
import com.snakegame.model.GameMove;
import com.snakegame.model.User;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;
import java.io.IOException;
import java.io.PrintWriter;

@WebServlet("/GameMoveServlet")
public class GameMoveServlet extends HttpServlet {

    private GameDAO gameDAO;

    @Override
    public void init() throws ServletException {
        gameDAO = new GameDAO();
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession();
        User user = (User) session.getAttribute("user");
        Integer gameId = (Integer) session.getAttribute("currentGameId");

        if (user == null) {
            response.sendRedirect("login.jsp");
            return;
        }

        if (gameId == null) {
            response.setContentType("application/json");
            PrintWriter out = response.getWriter();
            out.print("{\"success\": false, \"message\": \"No active game\"}");
            out.flush();
            return;
        }

        String moveDirection = request.getParameter("direction");
        String snakePosition = request.getParameter("snakePosition");

        if (moveDirection != null && snakePosition != null) {
            GameMove move = new GameMove(gameId, moveDirection, snakePosition);
            boolean saved = gameDAO.addGameMove(move);

            response.setContentType("application/json");
            PrintWriter out = response.getWriter();
            out.print("{\"success\": " + saved + "}");
            out.flush();
        }
    }
}