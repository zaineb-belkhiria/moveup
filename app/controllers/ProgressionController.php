<?php
/**
 * app/controllers/ProgressionController.php
 */
require_once __DIR__ . '/../services/PlannerService.php';

class ProgressionController
{
    public function index(): void
    {
        Auth::requireLogin();

        $role = Auth::user()['role'] ?? 'user';
        if ($role === 'admin') {
            View::redirect('admin/dashboard');
        }

        $db   = Database::get();
        $user = Auth::user();
        $id   = (int)$user['id'];

        /* ── Reuse same models as DashboardController ──────── */
        $weightModel = new Weight($db);
        $userModel   = new User($db);

        /* ── User basics ────────────────────────────────────── */
        $poids_actuel = (float)($user['poids']  ?? 70);
        $taille       = (float)($user['taille'] ?? 175);
        $objectif     = $user['objectif'] ?? 'maintien';
        $niveau       = $user['niveau']   ?? 'intermediaire';
        $age          = (int)($user['age'] ?? 25);

        /* ── Latest weight ──────────────────────────────────── */
        $poids = $weightModel->latest($id) ?? $poids_actuel;

        /* ── Weight history (30 entries) ────────────────────── */
        $weight_history = $weightModel->history($id, 30);

        /* ── Workout counts ─────────────────────────────────── */
        $workouts_week = $userModel->weekWorkouts($id);
        $workouts_14   = $userModel->workoutsLast14Days($id);

        try {
            $stmt = $db->prepare('SELECT COUNT(*) FROM user_sessions_log WHERE user_id=? AND date_completed >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $workouts_7 = (int)$stmt->get_result()->fetch_row()[0];
            $stmt->close();
        } catch (Exception) { $workouts_7 = 0; }

        /* ── Calories this week ─────────────────────────────── */
        $cals_week = 0;
        try {
            $stmt = $db->prepare('SELECT COALESCE(SUM(calories_brulees),0) FROM activites WHERE user_id=? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $cals_week = (int)$stmt->get_result()->fetch_row()[0];
            $stmt->close();
        } catch (Exception) {}

        /* ── Streak ─────────────────────────────────────────── */
        $streak = 0;
        try {
            $stmt = $db->prepare('SELECT DISTINCT DATE(created_at) AS d FROM activites WHERE user_id=? ORDER BY d DESC LIMIT 30');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $activeDays = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'd');
            $stmt->close();
            foreach ($activeDays as $i2 => $day) {
                $expected = (new DateTime('today'))->modify("-{$i2} day")->format('Y-m-d');
                if ($day === $expected) $streak++;
                else break;
            }
        } catch (Exception) {}

        /* ── BMI ────────────────────────────────────────────── */
        $bmi = $taille > 0 ? round($poids_actuel / pow($taille / 100, 2), 1) : 0;

        /* ── Weight trend ───────────────────────────────────── */
        $weight_trend_val = 0;
        if (count($weight_history) >= 2) {
            $weight_trend_val = round((float)end($weight_history)['poids'] - (float)$weight_history[0]['poids'], 1);
        }

        /* ── Planner (same as dashboard) ────────────────────── */
        $planner  = new PlannerService();
        $planData = $planner->generate([
            'objectif'      => $objectif,
            'niveau'        => $niveau,
            'age'           => $age,
            'bmi'           => $bmi,
            'workouts_7'    => $workouts_7,
            'workouts_14'   => $workouts_14,
            'todayCalories' => 0,
            'weightTrend'   => $weight_trend_val,
        ]);

        $weekly_plan = $planData['weekly_plan'];
        $consistency = $planData['scores']['consistency'] ?? 0;
        $motivation  = $planData['scores']['motivation']  ?? 0;
        $readiness   = $planData['scores']['readiness']   ?? 0;

        // weekly_plan from PlannerService is keyed by day name with arrays
        // e.g. ['Lundi' => ['Upper Body', 45, '#C8F04A', 'desc'], ...]
        // Convert to the format the view expects: [{day, label, done, rest}]
        $jours_letters = [
            'Lundi'=>'L','Mardi'=>'M','Mercredi'=>'M',
            'Jeudi'=>'J','Vendredi'=>'V','Samedi'=>'S','Dimanche'=>'D'
        ];
        $restWords = ['repos','rest','recovery','récupération','repos actif'];

        // Get sessions done this week to mark days
        $doneSessions = [];
        try {
            $stmt = $db->prepare('SELECT session_type FROM user_sessions_log WHERE user_id=? AND date_completed >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $doneSessions = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'session_type');
            $stmt->close();
        } catch (Exception) {}

        $weekly_plan_view = [];
        foreach ($weekly_plan as $jour => $day) {
            $label  = is_array($day) ? ($day[0] ?? 'Repos') : $day;
            $isRest = in_array(strtolower(trim($label)), $restWords);
            $isDone = false;
            foreach ($doneSessions as $ds) {
                if (stripos($label, substr($ds, 0, 4)) !== false || stripos($ds, substr($label, 0, 4)) !== false) {
                    $isDone = true; break;
                }
            }
            $weekly_plan_view[] = [
                'day'   => $jours_letters[$jour] ?? strtoupper(substr($jour, 0, 1)),
                'label' => $label,
                'done'  => $isDone,
                'rest'  => $isRest,
            ];
        }

        /* ── Objectifs ──────────────────────────────────────── */
        $objectifs = [];
        try {
            $stmt = $db->prepare('SELECT * FROM objectifs WHERE user_id=? ORDER BY created_at DESC LIMIT 5');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $objectifs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } catch (Exception) {}

        /* ── Badges ─────────────────────────────────────────── */
        $badges = [];
        try {
            $stmt = $db->prepare('SELECT b.*, IF(ub.badge_id IS NOT NULL,1,0) AS unlocked, ub.obtenu_le FROM badges b LEFT JOIN user_badges ub ON ub.badge_id=b.id AND ub.user_id=? ORDER BY unlocked DESC, b.id ASC');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $badges = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } catch (Exception) {}

        /* ── Muscle fatigue ─────────────────────────────────── */
        $muscles = [];
        $palette = ['#C8F04A','#4AF0D8','#F0A84A','#f87171','#a78bfa','#60a5fa'];
        try {
            $stmt = $db->prepare('SELECT muscle_group, fatigue_level FROM fatigue WHERE user_id=? ORDER BY fatigue_level DESC');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $rawM = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            foreach ($rawM as $i2 => $m) {
                $muscles[] = ['nom'=>$m['muscle_group'], 'val'=>(int)$m['fatigue_level'], 'color'=>$palette[$i2 % 6]];
            }
        } catch (Exception) {}

        // Fallback from activites
        if (empty($muscles)) {
            try {
                $stmt = $db->prepare('SELECT type, COUNT(*) AS cnt FROM activites WHERE user_id=? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY type ORDER BY cnt DESC');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $acts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                $maxC = max(array_column($acts, 'cnt') ?: [1]);
                foreach ($acts as $i2 => $a) {
                    $muscles[] = ['nom'=>ucfirst($a['type']), 'val'=>(int)round($a['cnt']/$maxC*100), 'color'=>$palette[$i2 % 6]];
                }
            } catch (Exception) {}
        }

        /* ── Exercises from progression table ───────────────── */
        $exercises = [];
        try {
            $stmt = $db->prepare('SELECT exercise, weight, reps, sets FROM progression WHERE user_id=? ORDER BY exercise ASC');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $exercises = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } catch (Exception) {}

        /* ── Conseils ───────────────────────────────────────── */
        $conseils = $this->buildConseils($objectif, $weight_trend_val, $workouts_week, $bmi, $streak);

        /* ── Render ─────────────────────────────────────────── */
       View::render('progression/index', compact(
    'user', 'poids', 'poids_actuel', 'taille', 'objectif', 'niveau',
    'consistency', 'motivation', 'readiness', 'streak',
    'cals_week', 'weight_history', 'objectifs', 'badges',
    'muscles', 'exercises', 'conseils'
) + ['weekly_plan' => $weekly_plan_view, 'seances_week' => $workouts_7], 'dashboard');
    }

    private function buildConseils(string $objectif, float $trend, int $workoutsWeek, float $bmi, int $streak): array
    {
        $c = [];

        if ($trend < -0.3 && $objectif === 'perte')
            $c[] = ['ico'=>'📉','badge'=>'Poids','badgeColor'=>'#4AF0D8','badgeBg'=>'#4AF0D818','title'=>'Belle progression !','text'=>'Tu as perdu '.abs($trend).' kg cette semaine. Continue avec tes séances planifiées.','border'=>'#4AF0D8'];
        elseif ($trend > 0.5 && $objectif === 'perte')
            $c[] = ['ico'=>'⚠️','badge'=>'Poids','badgeColor'=>'#f87171','badgeBg'=>'#f8717118','title'=>'Tendance à surveiller','text'=>'Ton poids a augmenté de +'.$trend.' kg cette semaine. Revois ton bilan calorique.','border'=>'#f87171'];
        elseif ($trend > 0 && $objectif === 'masse')
            $c[] = ['ico'=>'📈','badge'=>'Poids','badgeColor'=>'#C8F04A','badgeBg'=>'#C8F04A18','title'=>'Prise de masse en cours','text'=>'+'.$trend.' kg cette semaine. Progresse en charges pour maximiser les gains.','border'=>'#C8F04A'];

        if ($workoutsWeek === 0)
            $c[] = ['ico'=>'🏃','badge'=>'Activité','badgeColor'=>'#f87171','badgeBg'=>'#f8717118','title'=>'Aucune séance cette semaine','text'=>'Lance-toi avec 20-30 min pour relancer ta progression. Même une courte séance compte !','border'=>'#f87171'];
        elseif ($workoutsWeek >= 5)
            $c[] = ['ico'=>'🏆','badge'=>'Activité','badgeColor'=>'#C8F04A','badgeBg'=>'#C8F04A18','title'=>'Objectif hebdo atteint 🎉','text'=>$workoutsWeek.' séances cette semaine ! Prévois une journée de récupération active.','border'=>'#C8F04A'];
        else {
            $r = 5 - $workoutsWeek;
            $c[] = ['ico'=>'📅','badge'=>'Planning','badgeColor'=>'#F0A84A','badgeBg'=>'#F0A84A18','title'=>'Continue sur ta lancée','text'=>$r.' séance'.($r>1?'s':'').' restante'.($r>1?'s':'').' pour atteindre ton objectif de 5 cette semaine.','border'=>'#F0A84A'];
        }

        $c[] = match($objectif) {
            'masse' => ['ico'=>'🥩','badge'=>'Nutrition','badgeColor'=>'#C8F04A','badgeBg'=>'#C8F04A18','title'=>'Surplus calorique','text'=>'Vise 1.8-2.2g de protéines/kg. Un surplus de 200-300 kcal/jour favorise la prise de masse.','border'=>'#C8F04A'],
            'perte' => ['ico'=>'🥗','badge'=>'Nutrition','badgeColor'=>'#C8F04A','badgeBg'=>'#C8F04A18','title'=>'Protéines post-séance','text'=>'25-30g de protéines dans les 30 min après la séance. Ça accélère la récupération.','border'=>'#C8F04A'],
            default => ['ico'=>'💧','badge'=>'Hydratation','badgeColor'=>'#4AF0D8','badgeBg'=>'#4AF0D818','title'=>'Hydratation clé','text'=>'2-3L d\'eau par jour selon ton activité. L\'hydratation améliore performances et récupération.','border'=>'#4AF0D8'],
        };

        return array_slice($c, 0, 3);
    }
}
