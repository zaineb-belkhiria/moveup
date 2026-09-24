<?php
// ============================================
// API: UPDATE WEIGHT - MySQLi
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
$poids   = filter_input(INPUT_POST, 'poids', FILTER_VALIDATE_FLOAT);
$date    = htmlspecialchars(trim($_POST['date'] ?? ''));

if (!$poids || !$date) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit();
}

if ($poids < 20 || $poids > 300) {
    echo json_encode(['success' => false, 'error' => 'Poids hors limites (20-300 kg)']);
    exit();
}

// Insert into weight_log
$stmt = mysqli_prepare($conn,
    "INSERT INTO weight_log (user_id, poids, date_log) VALUES (?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    exit();
}

// Update session with new weight
$_SESSION['user']['poids'] = $poids;

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => false, 'error' => mysqli_stmt_error($stmt)]);
    mysqli_stmt_close($stmt);
    exit();
}
mysqli_stmt_close($stmt);

// Update user's current weight in their profile
$stmt2 = mysqli_prepare($conn,
    "UPDATE utilisateurs SET poids = ? WHERE id = ?"
);

if (!$stmt2) {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    exit();
}

mysqli_stmt_bind_param($stmt2, 'di', $poids, $user_id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

// --- IMPORTANT: Update the session with the new weight ---
$_SESSION['user']['poids'] = $poids;

echo json_encode(['success' => true, 'poids' => $poids]);
?>