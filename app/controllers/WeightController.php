<?php
/**
 * app/controllers/WeightController.php
 */
class WeightController
{
    public function update(): void
    {
        Auth::requireLogin();
        $poids = floatval($_POST['poids'] ?? 0);
        $date  = $_POST['date'] ?? date('Y-m-d');

        if ($poids <= 0 || $poids > 300) {
            // BUG FIX: missing return — code continued executing after json error response
            View::json(['success' => false, 'error' => 'Poids invalide']);
            return;
        }

        try {
            $db    = Database::get();
            $model = new Weight($db);
            $model->log(Auth::userId(), $poids, $date);

            // Refresh session
            $user = (new User($db))->findById(Auth::userId());
            $_SESSION['user'] = $user;

            View::json(['success' => true]);
        } catch (Exception $e) {
            View::json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function logWorkout(): void
    {
        Auth::requireLogin();
        $name     = trim($_POST['session_name'] ?? 'Séance');
        $duration = intval($_POST['duration']    ?? 0);

        try {
            (new Weight(Database::get()))->logWorkout(Auth::userId(), $name, $duration);
            View::json(['success' => true]);
        } catch (Exception $e) {
            View::json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
