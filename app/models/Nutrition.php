<?php
/**
 * app/models/Nutrition.php — uses mysqli
 */
class Nutrition
{
    public function __construct(private mysqli $db) {}

    public function getTodayCalories(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(calories),0) FROM nutrition_log WHERE user_id=? AND date_log=CURDATE()'
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $val = (int)$stmt->get_result()->fetch_row()[0];
        $stmt->close();
        return $val;
    }

    public function getTodayMeals(int $userId, int $limit = 4): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT * FROM nutrition_log WHERE user_id=? AND date_log=CURDATE() ORDER BY created_at DESC LIMIT ?'
            );
            $stmt->bind_param('ii', $userId, $limit);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        } catch (Exception) { return []; }
    }

    public function addMeal(int $userId, string $name, ?int $calories): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO nutrition_log (user_id, date_log, meal_name, calories) VALUES (?, CURDATE(), ?, ?)'
        );
        $cal = $calories ?: null;
        $stmt->bind_param('isi', $userId, $name, $cal);
        $stmt->execute();
        $stmt->close();
    }

    public function getHistory(int $userId, int $limit = 20): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT * FROM nutrition_log WHERE user_id=? ORDER BY date_log DESC, created_at DESC LIMIT ?'
            );
            $stmt->bind_param('ii', $userId, $limit);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        } catch (Exception) { return []; }
    }

    public function analyze(int $totalCalories, string $objectif): string
    {
        $lines = [];
        if ($totalCalories === 0) {
            $lines[] = '📝 Commence par enregistrer tes repas pour recevoir des conseils personnalisés.';
        } else {
            if ($objectif === 'perte') {
                if ($totalCalories > 2000)     { $lines[] = '⚠️ Pour ton objectif de perte de poids, essaie de réduire ton apport à 1800–2000 kcal par jour.'; $lines[] = '🥗 Privilégie les protéines maigres et les légumes verts.'; }
                elseif ($totalCalories < 1500) { $lines[] = '⚠️ Ton apport est un peu bas. Assure-toi de manger suffisamment !'; $lines[] = '🥑 Ajoute des aliments riches en nutriments.'; }
                else                            { $lines[] = '✅ Bon équilibre calorique ! Continue comme ça.'; }
            } elseif ($objectif === 'masse') {
                if ($totalCalories < 2500) { $lines[] = '⚠️ Pour la prise de masse, vise 2500–3000 kcal par jour.'; $lines[] = '🍚 Augmente tes portions de féculents.'; }
                else                        { $lines[] = '✅ Excellent apport calorique pour la construction musculaire !'; }
            } else {
                if ($totalCalories < 1800 || $totalCalories > 2500) { $lines[] = '⚠️ Pour le maintien, vise 2000–2200 kcal par jour.'; }
                else                                                  { $lines[] = '✅ Parfait ! Tu maintiens un bon équilibre.'; }
            }
            $tips = [
                '💧 N\'oublie pas de boire au moins 2L d\'eau par jour.',
                '😴 Un bon sommeil (7–8h) est essentiel.',
                '🍎 Privilégie les aliments non transformés.',
                '⏰ Essaie de manger à heures régulières.',
            ];
            $lines[] = $tips[array_rand($tips)];
        }
        return implode('<br>', $lines);
    }
}
