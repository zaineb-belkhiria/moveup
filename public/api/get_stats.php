<?php
// ============================================
// API: GET STATS (JSON) - MySQLi
// ============================================

require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user']['id'];

// ── 1. Total stats ───────────────────────────────────────────────
$stmt = mysqli_prepare($conn, "
    SELECT
        COUNT(*)                           AS total_workouts,
        COALESCE(SUM(calories_burned),  0) AS total_calories,
        COALESCE(SUM(duration_minutes), 0) AS total_minutes
    FROM user_sessions_log
    WHERE user_id = ?
");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$stats  = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// ── 2. Real consecutive-day streak ──────────────────────────────
$stmt = mysqli_prepare($conn, "
    SELECT DISTINCT date_completed
    FROM user_sessions_log
    WHERE user_id = ?
    ORDER BY date_completed DESC
");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$dates = [];
while ($row = mysqli_fetch_row($result)) {
    $dates[] = $row[0];
}
mysqli_stmt_close($stmt);

$streak = 0;
if (!empty($dates)) {
    $yesterday = (new DateTime('today'))->modify('-1 day');
    $first     = new DateTime($dates[0]);
    if ($first >= $yesterday) {
        $streak = 1;
        for ($i = 1; $i < count($dates); $i++) {
            $curr = new DateTime($dates[$i]);
            $prev = new DateTime($dates[$i - 1]);
            if ((int)$prev->diff($curr)->days === 1) {
                $streak++;
            } else {
                break;
            }
        }
    }
}

// ── 3. Weekly data (last 7 days) ─────────────────────────────────
$stmt = mysqli_prepare($conn, "
    SELECT
        DAYOFWEEK(date_completed) AS day_num,
        SUM(duration_minutes)    AS minutes,
        SUM(calories_burned)     AS calories
    FROM user_sessions_log
    WHERE user_id = ?
      AND date_completed >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DAYOFWEEK(date_completed)
");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$weekly = [];
while ($row = mysqli_fetch_assoc($result)) {
    $weekly[] = $row;
}
mysqli_stmt_close($stmt);

// ── 4. Recent activities (includes id for delete button) ──────────
$stmt = mysqli_prepare($conn, "
    SELECT id, session_type, duration_minutes, calories_burned, date_completed
    FROM user_sessions_log
    WHERE user_id = ?
    ORDER BY date_completed DESC, id DESC
    LIMIT 10
");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$activities = [];
while ($row = mysqli_fetch_assoc($result)) {
    $activities[] = $row;
}
mysqli_stmt_close($stmt);

// ── 5. Weight history (includes id for delete button) ─────────────
$stmt = mysqli_prepare($conn, "
    SELECT id, poids, date_log
    FROM weight_log
    WHERE user_id = ?
    ORDER BY date_log DESC
    LIMIT 7
");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$weightHistory = [];
while ($row = mysqli_fetch_assoc($result)) {
    $weightHistory[] = $row;
}
mysqli_stmt_close($stmt);

echo json_encode([
    'success'       => true,
    'stats'         => array_merge($stats, ['streak' => $streak]),
    'weekly'        => $weekly,
    'activities'    => $activities,
    'weightHistory' => $weightHistory,
]);
?>
