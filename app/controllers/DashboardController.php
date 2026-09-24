<?php
/**
 * app/controllers/DashboardController.php
 * All business logic that was previously embedded in dashboard.php.
 */ require_once __DIR__ . '/../services/PlannerService.php';
class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();

        // Admins and coaches have no business on the user fitness dashboard
        $role = Auth::user()['role'] ?? 'user';
        if ($role === 'admin') {
            header('Location: ' . View::base('admin/dashboard'));
            exit;
        }
        if ($role === 'coach') {
            header('Location: ' . View::base('coach/dashboard'));
            exit;
        }

        $db   = Database::get();
        $user = Auth::user();
        $id   = (int)$user['id'];

        $userModel      = new User($db);
        $nutritionModel = new Nutrition($db);
        $weightModel    = new Weight($db);

        /* ── Basic user fields ──────────────────────────── */
        $prenom        = htmlspecialchars($user['prenom'] ?? 'Utilisateur');
        $nom           = htmlspecialchars($user['nom']    ?? '');
        $age           = (int)($user['age']    ?? 25);
        $poids_actuel  = (float)($user['poids']  ?? 70);
        $taille        = (float)($user['taille'] ?? 175);
        $objectif      = $user['objectif'] ?? 'maintien';
        $niveau        = $user['niveau']   ?? 'intermediaire';
        $profile_photo = $user['profile_photo'] ?? null;

        /* ── Latest logged weight (may differ from profile) */
        $poids = $weightModel->latest($id) ?? $poids_actuel;

        $goal_weight = match($objectif) {
            'perte' => max(45, $poids_actuel - 8),
            'masse' => min(130, $poids_actuel + 5),
            default => $poids_actuel,
        };

        /* ── Nutrition ──────────────────────────────────── */
        $today_calories = $nutritionModel->getTodayCalories($id);
        $today_meals    = $nutritionModel->getTodayMeals($id);

        $calorie_goal = match($objectif) { 'perte'=>1800, 'masse'=>3000, default=>2200 };
        $cal_pct      = $calorie_goal > 0 ? min(100, round($today_calories / $calorie_goal * 100)) : 0;

        /* ── Workouts ───────────────────────────────────── */
        $workouts_week  = $userModel->weekWorkouts($id);
        $total_workouts = $userModel->totalWorkouts($id);
        $workouts_14    = $userModel->workoutsLast14Days($id);

        // BUG FIX: was using PDO fetchColumn() — now uses mysqli
        try {
            $stmt = $db->prepare('SELECT COUNT(*) FROM user_sessions_log WHERE user_id=? AND date_completed >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $workouts_7 = (int)$stmt->get_result()->fetch_row()[0];
            $stmt->close();
        } catch(Exception) { $workouts_7 = 0; }

        $workout_pct = min(100, round($workouts_week / 5 * 100));

        /* ── Weight history & chart ─────────────────────── */
        $weight_history = $weightModel->history($id);
        $weights        = $weightModel->chartData($id);

        $firstWeight        = count($weights) ? (float)$weights[0]['poids'] : $poids;
        $change             = round($poids - $firstWeight, 1);
        $weight_trend_val   = count($weight_history) >= 2
            ? round((float)end($weight_history)['poids'] - (float)$weight_history[0]['poids'], 1)
            : 0;
        $weight_trend_str   = ($weight_trend_val > 0 ? '+' : '') . $weight_trend_val . ' kg';

        $weight_labels = json_encode(array_column($weight_history, 'd'));
        $weight_values = json_encode(array_column($weight_history, 'poids'));

        /* ── BMI ────────────────────────────────────────── */
        $bmi        = $taille > 0 ? round($poids_actuel / pow($taille / 100, 2), 1) : 0;
        $bmi_status = match(true) {
            $bmi < 18.5 => ['Insuffisant', '#60a5fa'],
            $bmi < 25   => ['Normal',      '#C8F04A'],
            $bmi < 30   => ['Surpoids',    '#facc15'],
            default     => ['Obésité',     '#f87171'],
        };

       /* ── AI adaptive weekly planner (REPLACED) ─────────────────── */

$planner = new PlannerService();

$planData = $planner->generate([
    'objectif'      => $objectif,
    'niveau'        => $niveau,
    'age'           => $age,
    'bmi'           => $bmi,
    'workouts_7'    => $workouts_7,
    'workouts_14'   => $workouts_14,
    'todayCalories' => $today_calories,
    'weightTrend'   => $weight_trend_val,
]);

/* Planner Results */
$weekly_plan = $planData['weekly_plan'];
$today_fr    = $planData['today_fr'];
$today_plan  = $planData['today_plan'];
$mode        = $planData['mode'];

/* Scores */
$consistency = $planData['scores']['consistency'] ?? 0;
$fatigue     = $planData['scores']['fatigue'] ?? 0;
$motivation  = $planData['scores']['motivation'] ?? 0;
$readiness   = $planData['scores']['readiness'] ?? 0;

/* Labels */
$obj_label = match($objectif) {
    'perte' => 'Perte de poids',
    'masse' => 'Prise de masse',
    default => 'Maintien'
};

$level_label = match($niveau) {
    'debutant' => 'Débutant',
    'avance'   => 'Avancé',
    default    => 'Intermédiaire'
};
        /* ── Personalized tips ──────────────────────────── */
        $conseils = $this->buildConseils(
            $today_calories, $calorie_goal, $cal_pct,
            $objectif, $weight_trend_val, $weight_trend_str,
            $workouts_week, $bmi, $weight_history
        );

        /* ── SVG ring geometry ──────────────────────────── */
        $r_val      = 34;
        $circ       = 2 * M_PI * $r_val;
        $cal_offset = $circ - ($cal_pct / 100 * $circ);
        $wrk_offset = $circ - ($workout_pct / 100 * $circ);

        /* ── Misc ───────────────────────────────────────── */
        $my_reservations = $userModel->upcomingReservations($id);
        $activities      = $userModel->activityFeed($id);
        $rec_programmes  = $userModel->recommendedProgrammes($id, $objectif, $niveau);

        $updates = [
           
            ['📊','#F0A84A','AMÉLIORATION','Graphiques 30j','Suivi de progression étendu sur 30 jours.', View::base('progress')],
        ];

        View::render('dashboard/index', compact(
            'user','prenom','nom','age','poids','poids_actuel','taille',
            'objectif','niveau','profile_photo','goal_weight',
            'today_calories','today_meals','calorie_goal','cal_pct',
            'workouts_week','total_workouts','workout_pct',
            'weight_history','weights','weight_labels','weight_values',
            'weight_trend_val','weight_trend_str','change',
            'bmi','bmi_status',
            'consistency','fatigue','motivation',
            'weekly_plan','today_fr','today_plan','obj_label','level_label',
            'conseils','r_val','circ','cal_offset','wrk_offset',
            'my_reservations','activities','rec_programmes','updates'
        ), 'dashboard');
    }

    /* ── Weekly plan builder ────────────────────────────── */
    private function buildWeeklyPlan(string $objectif, bool $moreCardio, bool $moreVolume): array
    {
        if ($objectif === 'perte') {
            $raw = [
                ['Upper Body',  45, '#C8F04A', 'Force haut du corps'],
                ['Cardio HIIT', 30, '#F0A84A', 'Brûlage calorique'],
                ['Lower Body',  45, '#C8F04A', 'Force jambes'],
                ['Mobilité',    25, '#4AF0D8', 'Prévention blessures'],
                ['Full Body',   45, '#C8F04A', 'Condition physique'],
                ['Endurance',   40, '#F0A84A', 'Capacité cardio'],
                ['Repos',        0, '#2a3328', 'Repos complet'],
            ];
            if ($moreCardio) $raw[5] = ['Cardio Extra', 45, '#F0A84A', 'Accélération perte'];
        } elseif ($objectif === 'masse') {
            $raw = [
                ['Push',        55, '#C8F04A', 'Pectoraux épaules'],
                ['Pull',        55, '#C8F04A', 'Dos biceps'],
                ['Legs',        60, '#C8F04A', 'Jambes lourdes'],
                ['Repos Actif', 20, '#4AF0D8', 'Marche mobilité'],
                ['Upper Power', 55, '#C8F04A', 'Force haut'],
                ['Lower Power', 60, '#C8F04A', 'Force bas'],
                ['Repos',        0, '#2a3328', 'Repos complet'],
            ];
            if ($moreVolume) $raw[4] = ['Upper Volume', 65, '#C8F04A', 'Hypertrophie bonus'];
        } else {
            $raw = [
                ['Upper Body', 45, '#C8F04A', 'Équilibre'],
                ['Cardio',     35, '#F0A84A', 'Santé cardio'],
                ['Lower Body', 45, '#C8F04A', 'Force jambes'],
                ['Mobilité',   25, '#4AF0D8', 'Prévention blessures'],
                ['Full Body',  45, '#C8F04A', 'Condition physique'],
                ['Endurance',  40, '#F0A84A', 'Capacité cardio'],
                ['Repos',       0, '#2a3328', 'Repos complet'],
            ];
        }
        return $raw;
    }

    /* ── Personalized tips builder ──────────────────────── */
    private function buildConseils(
        int $todayCal, int $calGoal, int $calPct,
        string $objectif, float $trend, string $trendStr,
        int $workoutsWeek, float $bmi, array $weightHistory
    ): array {
        $conseils = [];

        if ($todayCal === 0) {
            $conseils[] = ['ico'=>'fas fa-utensils','col'=>'#F0A84A','badge'=>'Nutrition','titre'=>'Commence à tracker tes repas','texte'=>"Tu n'as enregistré aucun repas aujourd'hui. Suivi des apports = résultats plus rapides."];
        } elseif ($calPct < 40) {
            $conseils[] = ['ico'=>'fas fa-fire-flame-curved','col'=>'#F0A84A','badge'=>'Nutrition','titre'=>'Apport calorique trop faible','texte'=>"Seulement {$todayCal} kcal sur {$calGoal} kcal ciblées ({$calPct}%). Un déficit excessif ralentit le métabolisme."];
        } elseif ($calPct > 115 && $objectif === 'perte') {
            $surplus = $todayCal - $calGoal;
            $conseils[] = ['ico'=>'fas fa-triangle-exclamation','col'=>'#f87171','badge'=>'Nutrition','titre'=>'Surplus calorique détecté','texte'=>"+{$surplus} kcal au-dessus de ton objectif. Compense avec 30 min de cardio ce soir."];
        } else {
            $tip = match($objectif) {
                'masse'  => "Répartis tes protéines sur 4-5 repas. Ajoute des glucides complexes autour de tes séances.",
                'perte'  => "Privilégie les protéines maigres et les fibres à chaque repas. Bois de l'eau avant les repas.",
                default  => "Maintiens un équilibre entre protéines, glucides et lipides. Hydrate-toi avec au moins 2L d'eau.",
            };
            $conseils[] = ['ico'=>'fas fa-leaf','col'=>'#C8F04A','badge'=>'Nutrition','titre'=>'Conseil nutrition du jour','texte'=>$tip];
        }

        if ($trend !== 0.0 && count($weightHistory) >= 2) {
            if ($objectif === 'perte' && $trend > 0.5)
                $conseils[] = ['ico'=>'fas fa-arrow-trend-up','col'=>'#f87171','badge'=>'Poids','titre'=>'Tendance poids à surveiller','texte'=>"Ton poids a augmenté de {$trendStr} cette semaine. Revois ton bilan calorique."];
            elseif ($objectif === 'perte' && $trend <= -0.3)
                $conseils[] = ['ico'=>'fas fa-arrow-trend-down','col'=>'#C8F04A','badge'=>'Poids','titre'=>'Belle progression !','texte'=>"Tu as perdu ".abs($trend)." kg cette semaine. Continue avec tes séances planifiées."];
            elseif ($objectif === 'masse' && $trend > 0)
                $conseils[] = ['ico'=>'fas fa-dumbbell','col'=>'#4AF0D8','badge'=>'Poids','titre'=>'Prise de masse en cours','texte'=>"Tu as pris {$trendStr} cette semaine. Progresse en charges pour maximiser les gains."];
        }

        if ($workoutsWeek === 0)
            $conseils[] = ['ico'=>'fas fa-person-running','col'=>'#f87171','badge'=>'Activité','titre'=>'Aucune séance cette semaine','texte'=>"Lance-toi avec une séance courte de 20-30 min pour relancer ta progression."];
        elseif ($workoutsWeek >= 5)
            $conseils[] = ['ico'=>'fas fa-trophy','col'=>'#C8F04A','badge'=>'Activité','titre'=>'Objectif hebdo atteint 🎉','texte'=>"{$workoutsWeek} séances cette semaine ! Prévois une journée de récupération active."];
        elseif ($workoutsWeek < 3) {
            $reste = 5 - $workoutsWeek;
            $conseils[] = ['ico'=>'fas fa-calendar-check','col'=>'#F0A84A','badge'=>'Activité','titre'=>'Rattrape ton planning','texte'=>"Il te reste {$reste} séance".($reste>1?'s':'')." pour atteindre ton objectif hebdomadaire."];
        }

        if ($bmi >= 30)
            $conseils[] = ['ico'=>'fas fa-heart-pulse','col'=>'#f87171','badge'=>'Santé','titre'=>'Priorité santé cardiovasculaire','texte'=>"IMC {$bmi} — Combine 3 séances de cardio modéré/semaine avec un déficit de 300-500 kcal/jour."];
        elseif ($bmi < 18.5)
            $conseils[] = ['ico'=>'fas fa-weight-scale','col'=>'#60a5fa','badge'=>'Santé','titre'=>'IMC insuffisant','texte'=>"IMC {$bmi} — Augmente progressivement tes apports et concentre-toi sur le renforcement musculaire."];

        return array_slice($conseils, 0, 3);
    }
}
