<?php
class PlannerService
{
public function refreshUserPlan(int $userId): void
{
    $db = Database::get();

    // delete old plan
    $stmt = $db->prepare("DELETE FROM weekly_plans WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // load user
    $stmt = $db->prepare("
        SELECT age, poids, taille, objectif, niveau
        FROM utilisateurs
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) return;

    // basic stats
    $taille = (float)$user['taille'];
    $poids  = (float)$user['poids'];

    $bmi = $taille > 0
        ? round($poids / pow($taille / 100, 2), 1)
        : 0;

    // generate new plan
    $planData = $this->generate([
        'objectif'      => $user['objectif'],
        'niveau'        => $user['niveau'],
        'age'           => (int)$user['age'],
        'bmi'           => $bmi,
        'workouts_7'    => 0,
        'workouts_14'   => 0,
        'todayCalories' => 0,
        'weightTrend'   => 0,
    ]);

    // save new plan
    foreach ($planData['weekly_plan'] as $jour => $day) {

        $workout = $day[0] ?? 'Workout';

        $stmt = $db->prepare("
            INSERT INTO weekly_plans (user_id, jour, workout, created_at)
            VALUES (?, ?, ?, NOW())
        ");

        $stmt->bind_param("iss", $userId, $jour, $workout);
        $stmt->execute();
        $stmt->close();
    }
}
    public function generate(array $data): array
    {
        $consistency = min(100, round(($data['workouts_14'] / 10) * 100));

        $fatigue = $this->fatigueScore($data);
        $motivation = $this->motivationScore($data, $consistency);

        $readiness = round(
            ($motivation * 0.40) +
            ($consistency * 0.35) +
            ((100 - $fatigue) * 0.25)
        );

        $mode = $this->modeFromReadiness($readiness);
        $plan = $this->basePlan($data['objectif']);
        $plan = $this->adaptPlan($plan, $data, $mode);

        $jours = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
        $weekly = [];
        foreach ($jours as $i => $jour) {
            $weekly[$jour] = $plan[$i];
        }

        $map = ['Mon'=>'Lundi','Tue'=>'Mardi','Wed'=>'Mercredi','Thu'=>'Jeudi','Fri'=>'Vendredi','Sat'=>'Samedi','Sun'=>'Dimanche'];
        $today = $map[date('D')] ?? 'Lundi';

        return [
            'weekly_plan' => $weekly,
            'today_fr'    => $today,
            'today_plan'  => $weekly[$today],
            'mode'        => $mode,
            'scores'      => compact('consistency','fatigue','motivation','readiness')
        ];
    }

    private function fatigueScore(array $d): int
    {
        $fatigue = 0;
        $fatigue += $d['workouts_7'] * 6;
        if ($d['age'] >= 45) $fatigue += 15;
        if ($d['bmi'] >= 30) $fatigue += 10;
        if ($d['todayCalories'] < 1200) $fatigue += 10;
        return min(100, $fatigue);
    }

    private function motivationScore(array $d, int $consistency): int
    {
        $m = 60;
        if ($d['workouts_7'] >= 4) $m += 20;
        if ($d['workouts_7'] === 0) $m -= 25;
        if ($d['todayCalories'] > 0) $m += 10;
        if ($consistency > 70) $m += 10;
        return max(0, min(100, $m));
    }

    private function modeFromReadiness(int $r): string
    {
        return match (true) {
            $r >= 80 => 'intense',
            $r >= 60 => 'normal',
            $r >= 40 => 'light',
            default  => 'recovery',
        };
    }

   private function basePlan(string $objectif): array
{
    return match ($objectif) {

        'perte' => [
            ['Upper Body',45,'#C8F04A','Force haut du corps'],
            ['HIIT',30,'#F0A84A','Brûlage calorique'],
            ['Lower Body',45,'#C8F04A','Force jambes'],
            ['Mobilité',25,'#4AF0D8','Prévention blessures'],
            ['Full Body',45,'#C8F04A','Condition physique'],
            ['Cardio',40,'#F0A84A','Endurance'],
            ['Repos',0,'#2a3328','Repos complet'],
        ],

        'masse' => [
            ['Push',55,'#C8F04A','Pectoraux épaules'],
            ['Pull',55,'#C8F04A','Dos biceps'],
            ['Legs',60,'#C8F04A','Jambes lourdes'],
            ['Repos Actif',20,'#4AF0D8','Marche mobilité'],
            ['Upper Power',55,'#C8F04A','Force haut'],
            ['Lower Power',60,'#C8F04A','Force bas'],
            ['Repos',0,'#2a3328','Repos complet'],
        ],

        default => [
            ['Upper Body',45,'#C8F04A','Équilibre'],
            ['Cardio',35,'#F0A84A','Santé cardio'],
            ['Lower Body',45,'#C8F04A','Force jambes'],
            ['Mobilité',25,'#4AF0D8','Prévention blessures'],
            ['Full Body',45,'#C8F04A','Condition physique'],
            ['Endurance',40,'#F0A84A','Capacité cardio'],
            ['Repos',0,'#2a3328','Repos complet'],
        ]
    };
}

    private function adaptPlan(array $plan, array $d, string $mode): array
    {
        foreach ($plan as &$day) {
            if ($mode === 'intense' && $day[1] > 0) $day[1] += 10;
            if ($mode === 'light' && $day[1] > 0) $day[1] -= 10;
            if ($mode === 'recovery' && $day[1] > 0) $day[1] = max(20, $day[1] - 20);
        }
        unset($day);

        if ($d['niveau'] === 'debutant') {
            foreach ($plan as &$day) {
                if ($day[1] > 0) $day[1] = max(25, $day[1] - 10);
            }
            unset($day);
        }

        if ($d['age'] >= 50) {
            foreach ($plan as &$day) {
                if (stripos($day[0], 'HIIT') !== false) {
                    $day = ['Cardio Modéré', 30];
                }
            }
            unset($day);
        }

        return $plan;
    }
}

