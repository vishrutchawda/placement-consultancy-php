<?php
// Start session for user authentication
session_start();

// Database connection
$host = "localhost";
$username = "root";
$password = "vishrutcoderpro";
$dbname = "placement_consultancy";

$con = mysqli_connect($host, $username, $password);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Select database
if (!mysqli_select_db($con, $dbname)) {
    die("Database selection failed: " . mysqli_error($con));
}

// Set charset to UTF-8
mysqli_set_charset($con, "utf8mb4");

// Helper function to sanitize input
function sanitize($data) {
    global $con;
    return mysqli_real_escape_string($con, trim($data));
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

// Helper function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Helper function to check if user is admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}
?>