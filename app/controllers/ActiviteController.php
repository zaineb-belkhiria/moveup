<?php
/**
 * app/controllers/ActiviteController.php
 * Gère la page activités + les actions AJAX add / delete.
 * Utilise Database::get() (mysqli) — aucun PDO.
 */
class ActiviteController
{
    // ── GET /activites ────────────────────────────────────────────────────
    public function index(): void
    {
        Auth::requireLogin();

        $db     = Database::get();
        $userId = (int)Auth::user()['id'];
        $model  = new Activite($db);

        $activites = $model->recent($userId);
        $stats     = $model->stats($userId);
        require_once __DIR__ . '/../models/ExerciseModel.php';
    $library    = ExerciseModel::getLibrary();
    $diffColors = ExerciseModel::getDiffColors();
    $currentCat = trim($_GET['category'] ?? '') ?: null;
    // Guard: if the category doesn't exist in the library, fall back to the
    // "all categories" view instead of leaving $currentCat set with a null
    // $currentData. Previously that caused a fatal error in the view, and
    // because the error happened before the layout rendered, it also took
    // the sidebar/navbar down with it (blank/invisible nav).
    if ($currentCat && !ExerciseModel::categoryExists($currentCat)) {
        $currentCat = null;
    }
    $currentData = $currentCat ? ExerciseModel::getCategory($currentCat) : null;

        View::render('activites/index', compact('activites', 'stats', 'library', 'diffColors', 'currentCat', 'currentData'), 'dashboard');
    }

    // ── POST /activites/add  (réponse JSON) ───────────────────────────────
    public function add(): void
    {
        Auth::requireLogin();

        $userId   = (int)Auth::user()['id'];
        $type     = trim($_POST['type']      ?? 'autre');
        $nom      = trim($_POST['nom']       ?? '');
        $duree    = (int)($_POST['duree']    ?? 0);
        $calories = (int)($_POST['calories'] ?? 0);
        $date     = trim($_POST['date']      ?? date('Y-m-d'));
        $notes    = trim($_POST['notes']     ?? '');

        if ($nom === '' || $duree <= 0) {
            View::json(['success' => false, 'message' => 'Données invalides.'], 422);
        }

        // Sanitise le type (varchar 50 dans ta DB)
        $allowed = ['course', 'velo', 'natation', 'musculation', 'yoga', 'autre'];
        if (!in_array($type, $allowed, true)) {
            $type = 'autre';
        }

        $model = new Activite(Database::get());
        $id    = $model->add($userId, $type, $nom, $duree, $calories, $date, $notes);

        View::json(['success' => true, 'message' => 'Activité ajoutée !', 'id' => $id]);
    }

    // ── POST /activites/log  (réponse JSON) — bouton "J'ai fait cet exercice" ──
    public function logExercise(): void
    {
        Auth::requireLogin();

        $userId   = (int)Auth::user()['id'];
        $nom      = trim($_POST['nom']      ?? '');
        $category = trim($_POST['category'] ?? 'autre');
        $duration = (int)($_POST['duration'] ?? 0);
        $date     = trim($_POST['date']     ?? date('Y-m-d'));

        if ($nom === '') {
            View::json(['success' => false, 'message' => 'Nom manquant.'], 422);
            return;
        }

        $duration = max(1, $duration);
        $calories = (int)round($duration * 5.5); // ~5.5 kcal/min estimation

        $db = Database::get();

        // 1. Save to activites table
        $stmt = $db->prepare(
            'INSERT INTO activites (user_id, type, nom, duree_minutes, calories_brulees, date_activite)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('issiis', $userId, $category, $nom, $duration, $calories, $date);
        $stmt->execute();
        $stmt->close();

        // 2. Save to user_sessions_log for streak/stats tracking
        $stmt2 = $db->prepare(
            'INSERT INTO user_sessions_log (user_id, session_type, duration_minutes, calories_burned, date_completed)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt2->bind_param('isiis', $userId, $nom, $duration, $calories, $date);
        $stmt2->execute();
        $stmt2->close();

        // 3. Update fatigue table for this muscle group
        $stmt3 = $db->prepare(
            'INSERT INTO fatigue (user_id, muscle_group, fatigue_level)
             VALUES (?, ?, LEAST(100, ?))
             ON DUPLICATE KEY UPDATE fatigue_level = LEAST(100, fatigue_level + ?)'
        );
        $increase = min(25, (int)round($duration / 2));
        $stmt3->bind_param('isii', $userId, $category, $increase, $increase);
        $stmt3->execute();
        $stmt3->close();

        View::json([
            'success'  => true,
            'message'  => $nom . ' enregistré dans ta progression !',
            'calories' => $calories,
        ]);
    }

    // ── POST /activites/delete  (réponse JSON) ────────────────────────────
    public function delete(): void
    {
        Auth::requireLogin();

        $userId = (int)Auth::user()['id'];
        $id     = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            View::json(['success' => false, 'message' => 'ID invalide.'], 422);
        }

        $model = new Activite(Database::get());
        $ok    = $model->delete($id, $userId);

        View::json([
            'success' => $ok,
            'message' => $ok ? 'Activité supprimée.' : 'Activité introuvable.',
        ]);
    }
}
