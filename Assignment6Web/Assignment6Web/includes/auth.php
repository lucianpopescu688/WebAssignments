<?php
session_start();

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function adminLogin($password) {
    // Replace with your actual admin password validation logic
    if ($password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function adminLogout() {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
}
?>