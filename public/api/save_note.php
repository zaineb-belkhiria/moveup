<?php
// ============================================
// API: SAVE WORKOUT NOTE - MySQLi
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

$user_id = $_SESSION['user']['id'];
$note = htmlspecialchars(trim($_POST['note'] ?? ''));
$mood = htmlspecialchars(trim($_POST['mood'] ?? ''));

// Validate mood (optional)
$allowed_moods = ['😊', '💪', '😌', '😤', '🎉', ''];
if (!in_array($mood, $allowed_moods)) {
    $mood = '';
}

// Check if a note already exists for today
$stmt = mysqli_prepare($conn, 
    "SELECT id FROM workout_notes WHERE user_id = ? AND note_date = CURDATE()"
);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existing = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($existing) {
    // Update existing note
    $stmt = mysqli_prepare($conn,
        "UPDATE workout_notes SET note = ?, mood = ? WHERE user_id = ? AND note_date = CURDATE()"
    );
    mysqli_stmt_bind_param($stmt, 'ssi', $note, $mood, $user_id);
} else {
    // Insert new note
    $stmt = mysqli_prepare($conn,
        "INSERT INTO workout_notes (user_id, note, mood, note_date) VALUES (?, ?, ?, CURDATE())"
    );
    mysqli_stmt_bind_param($stmt, 'iss', $user_id, $note, $mood);
}

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    exit();
}

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_stmt_error($stmt)]);
}

mysqli_stmt_close($stmt);
?>