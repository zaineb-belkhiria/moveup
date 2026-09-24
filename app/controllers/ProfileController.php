<?php
/**
 * app/controllers/ProfileController.php
 */
require_once __DIR__ . '/../services/PlannerService.php';
class ProfileController
{
    public function show(): void
    {
        Auth::requireLogin();
        if ((Auth::user()['role'] ?? '') === 'salle') {
            header('Location: ' . View::base('salle/dashboard'));
            exit();
        }
        $user = $this->enrichUser(Auth::user());
        View::render('profile/index', $user, 'dashboard');
    }

    public function update(): void
    {
        Auth::requireLogin();
        if ((Auth::user()['role'] ?? '') === 'salle') {
            header('Location: ' . View::base('salle/dashboard'));
            exit();
        }
        $db     = Database::get();
        $userId = Auth::userId();
        $user   = Auth::user();

        $data = [
            'prenom'        => trim($_POST['prenom']   ?? ''),
            'nom'           => trim($_POST['nom']      ?? ''),
            'age'           => (int)($_POST['age']     ?? 25),
            'poids'         => (float)($_POST['poids'] ?? 70),
            'taille'        => (float)($_POST['taille']?? 175),
            'objectif'      => $_POST['objectif']      ?? 'maintien',
            'niveau'        => $_POST['niveau']        ?? 'intermediaire',
            'profile_photo' => $user['profile_photo']  ?? null,
        ];

        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $dir = __DIR__ . '/../../public/uploads/profiles/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $filename = 'user_' . $userId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $dir . $filename)) {
                    $data['profile_photo'] = '/uploads/profiles/' . $filename;
                }
            }
        }

        try {
            $userModel = new User($db);
            $userModel->updateProfile($userId, $data);
            $planner = new PlannerService();
            $userId = (int)Auth::user()['id'];

            $planner = new PlannerService();
            $planner->refreshUserPlan($userId);

            try {
                (new Weight($db))->log($userId, $data['poids'], date('Y-m-d'));
            } catch (Exception) {}

            $_SESSION['user'] = $userModel->findById($userId);
            $flash = ['type' => 'success', 'msg' => 'Profil mis à jour avec succès.'];
        } catch (Exception $e) {
            $flash = ['type' => 'error', 'msg' => $e->getMessage()];
        }

        $enriched = $this->enrichUser($_SESSION['user']);
        $enriched['flash'] = $flash;
        View::render('profile/index', $enriched, 'dashboard');
    }

    private function enrichUser(array $user): array
    {
        $db     = Database::get();
        $userId = (int)$user['id'];

        $poids  = (float)($user['poids']  ?? 70);
        $taille = (float)($user['taille'] ?? 175);
        $age    = (int)($user['age']      ?? 25);
        $genre  = $user['genre']          ?? 'homme';
        $niveau = $user['niveau']         ?? 'intermediaire';

        $bmi       = $taille > 0 ? round($poids / pow($taille / 100, 2), 1) : 0;
        $bmi_label = match(true) { $bmi < 18.5 => 'Bas', $bmi < 25 => 'Normal', $bmi < 30 => 'Surpoids', default => 'Élevé' };

        $total_workouts = 0;
        $week_workouts  = 0;
        try {
            $stmt = $db->prepare('SELECT COUNT(*) FROM user_sessions_log WHERE user_id=?');
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $total_workouts = (int)$stmt->get_result()->fetch_row()[0];
            $stmt->close();

            $stmt2 = $db->prepare('SELECT COUNT(*) FROM user_sessions_log WHERE user_id=? AND date_completed >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)');
            $stmt2->bind_param('i', $userId);
            $stmt2->execute();
            $week_workouts = (int)$stmt2->get_result()->fetch_row()[0];
            $stmt2->close();
        } catch (Exception) {}

        $score = 50;
        if ($bmi >= 18.5 && $bmi <= 25) $score += 15;
        if ($week_workouts >= 3) $score += 20;
        if ($week_workouts >= 5) $score += 10;
        if ($niveau === 'avance') $score += 5;
        $score = min(100, $score);

        $bodyfat = $genre === 'homme'
            ? round((1.20 * $bmi) + (0.23 * $age) - 16.2, 1)
            : round((1.20 * $bmi) + (0.23 * $age) - 5.4,  1);
        $bodyfat = max(5, min(45, $bodyfat));

        $xp          = ($total_workouts * 25) + ($week_workouts * 40);
        $level       = min(99, floor($xp / 250) + 1);
        $next_xp     = $level * 250;
        $xp_progress = min(100, round(($xp / $next_xp) * 100));

        $bmr            = (10 * $poids) + (6.25 * $taille) - (5 * $age) + ($genre === 'homme' ? 5 : -161);
        $maintenance    = round($bmr * 1.45);
        $objectif       = $user['objectif'] ?? 'maintien';
        $calorie_target = $maintenance + match($objectif) { 'perte' => -400, 'masse' => 300, default => 0 };

        $smart_tip = match($objectif) {
            'perte'  => "Déficit conseillé : {$calorie_target} kcal/jour + cardio.",
            'masse'  => "Surplus conseillé : {$calorie_target} kcal/jour + force.",
            default  => "Maintien optimal : {$calorie_target} kcal/jour.",
        };

        $goal_weight = match($objectif) { 'perte' => max(45, $poids - 8), 'masse' => $poids + 5, default => $poids };

        $status_title = match(true) {
            $level >= 35 => 'Légende',
            $level >= 20 => 'Elite',
            $level >= 10 => 'Athlète',
            $level >= 5  => 'Discipliné',
            default      => 'Débutant',
        };

        return compact(
            'user','poids','taille','age','genre','bmi','bmi_label',
            'total_workouts','week_workouts','score','bodyfat',
            'xp','level','xp_progress','calorie_target','smart_tip',
            'goal_weight','status_title','objectif','niveau'
        );
    }
}
