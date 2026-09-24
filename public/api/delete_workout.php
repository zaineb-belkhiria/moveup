<?php
// ============================================
// API: DELETE WORKOUT SESSION - MySQLi
// ============================================

require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

$user_id    = $_SESSION['user']['id'];
$session_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$session_id) {
    echo json_encode(['success' => false, 'error' => 'ID invalide']);
    exit();
}

// user_id in WHERE = security: users can only delete their own sessions
$stmt = mysqli_prepare($conn,
    "DELETE FROM user_sessions_log WHERE id = ? AND user_id = ?"
);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    exit();
}

mysqli_stmt_bind_param($stmt, 'ii', $session_id, $user_id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Séance introuvable']);
}

mysqli_stmt_close($stmt);
?>
