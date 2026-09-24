<?php
/**
 * app/controllers/NutritionController.php
 * All responses are JSON — used via fetch() from dashboard JS.
 */
class NutritionController
{
    public function addMeal(): void
    {
        Auth::requireLogin();
        $model     = new Nutrition(Database::get());
        $userId    = Auth::userId();
        $meal_name = trim($_POST['meal_name'] ?? '');
        $calories  = intval($_POST['calories'] ?? 0) ?: null;

        if (!$meal_name) {
            // BUG FIX: View::json() calls exit, but explicit return makes intent clear
            View::json(['success' => false, 'error' => 'Nom du repas requis']);
            return;
        }

        try {
            $model->addMeal($userId, $meal_name, $calories);
            View::json(['success' => true]);
        } catch (Exception $e) {
            View::json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function analyze(): void
    {
        Auth::requireLogin();
        $user  = Auth::user();
        $model = new Nutrition(Database::get());
        $total = $model->getTodayCalories((int)$user['id']);
        $recs  = $model->analyze($total, $user['objectif'] ?? 'maintien');
        View::json(['success' => true, 'recommendations' => $recs]);
    }

    public function history(): void
    {
        Auth::requireLogin();
        $model = new Nutrition(Database::get());
        $meals = $model->getHistory(Auth::userId());
        View::json(['success' => true, 'meals' => $meals]);
    }
}
