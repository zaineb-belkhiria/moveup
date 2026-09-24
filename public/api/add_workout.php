<?php
// ============================================
// API: ADD WORKOUT SESSION - MySQLi
// ============================================

require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$user_id          = $_SESSION['user']['id'];
$session_type     = htmlspecialchars(trim($_POST['session_type']     ?? ''));
$duration_minutes = filter_input(INPUT_POST, 'duration_minutes', FILTER_VALIDATE_INT);
$calories_burned  = filter_input(INPUT_POST, 'calories_burned',  FILTER_VALIDATE_INT);
$date_completed   = htmlspecialchars(trim($_POST['date_completed'] ?? ''));

if (!$session_type || !$duration_minutes || !$date_completed) {
    echo json_encode(['success' => false, 'error' => 'Champs requis manquants']);
    exit();
}

// Auto-calculate calories if not provided (8 kcal/min average)
if (!$calories_burned || $calories_burned <= 0) {
    $calories_burned = $duration_minutes * 8;
}

$stmt = mysqli_prepare($conn,
    "INSERT INTO user_sessions_log (user_id, session_type, duration_minutes, calories_burned, date_completed)
     VALUES (?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    exit();
}

// i = user_id (int)
// s = session_type (string)
// i = duration_minutes (int)
// i = calories_burned (int)
// s = date_completed (string)
mysqli_stmt_bind_param($stmt, 'isiis', $user_id, $session_type, $duration_minutes, $calories_burned, $date_completed);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'calories' => $calories_burned]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_stmt_error($stmt)]);
}

mysqli_stmt_close($stmt);
?>
