<?php
// ============================================
// API BRIDGE CONFIG
// Connects friend's MySQLi API files to the
// MoveUP project database (via Database.php)
// ============================================

// Start session safely (may already be active from the main app)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Walk up two levels: public/api → public → project root
define('API_ROOT', dirname(__DIR__, 2));

// Load the Database singleton (reads config/database.php internally)
require_once API_ROOT . '/core/Database.php';

// $conn is what all the API files expect
$conn = Database::get();
