<?php
// ============================================
// API: UPDATE USER PROFILE - MySQLi
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

$user_id  = $_SESSION['user']['id'];
$poids    = filter_input(INPUT_POST, 'poids',  FILTER_VALIDATE_FLOAT);
$taille   = filter_input(INPUT_POST, 'taille', FILTER_VALIDATE_FLOAT);
$age      = filter_input(INPUT_POST, 'age',    FILTER_VALIDATE_INT);
$objectif = htmlspecialchars(trim($_POST['objectif'] ?? ''));
$niveau   = htmlspecialchars(trim($_POST['niveau']   ?? ''));

// Whitelist allowed enum values
$allowed_objectifs = ['perte', 'masse', 'maintien'];
$allowed_niveaux   = ['debutant', 'intermediaire', 'avance'];

if ($objectif && !in_array($objectif, $allowed_objectifs)) {
    echo json_encode(['success' => false, 'error' => 'Objectif invalide']);
    exit();
}
if ($niveau && !in_array($niveau, $allowed_niveaux)) {
    echo json_encode(['success' => false, 'error' => 'Niveau invalide']);
    exit();
}

// Build SET clause dynamically — only update fields that were sent
$fields = [];
$types  = '';
$params = [];

if ($poids    && $poids  >= 30  && $poids  <= 300) { $fields[] = 'poids = ?';    $types .= 'd'; $params[] = $poids; }
if ($taille   && $taille >= 100 && $taille <= 250)  { $fields[] = 'taille = ?';   $types .= 'd'; $params[] = $taille; }
if ($age      && $age    >= 10  && $age    <= 120)   { $fields[] = 'age = ?';      $types .= 'i'; $params[] = $age; }
if ($objectif) { $fields[] = 'objectif = ?'; $types .= 's'; $params[] = $objectif; }
if ($niveau)   { $fields[] = 'niveau = ?';   $types .= 's'; $params[] = $niveau; }

if (empty($fields)) {
    echo json_encode(['success' => false, 'error' => 'Aucun champ à mettre à jour']);
    exit();
}

$types   .= 'i'; // for WHERE id = ?
$params[] = $user_id;

$sql  = "UPDATE utilisateurs SET " . implode(', ', $fields) . " WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    exit();
}

mysqli_stmt_bind_param($stmt, $types, ...$params);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => false, 'error' => mysqli_stmt_error($stmt)]);
    mysqli_stmt_close($stmt);
    exit();
}
mysqli_stmt_close($stmt);

// Refresh session with updated data
$stmt2  = mysqli_prepare($conn, "SELECT * FROM utilisateurs WHERE id = ?");
mysqli_stmt_bind_param($stmt2, 'i', $user_id);
mysqli_stmt_execute($stmt2);
$result       = mysqli_stmt_get_result($stmt2);
$updated_user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt2);

$_SESSION['user'] = $updated_user;

echo json_encode([
    'success'  => true,
    'poids'    => $updated_user['poids'],
    'objectif' => $updated_user['objectif'],
    'niveau'   => $updated_user['niveau'],
]);
?>
