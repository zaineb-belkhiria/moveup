<?php
/**
 * app/models/User.php
 * All database operations for a user row — uses mysqli.
 */
class User
{
    public function __construct(private mysqli $db) {}

    /* ── Fetch ───────────────────────────────────────────── */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateurs WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateurs WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /* ── Register ────────────────────────────────────────── */
    public function create(array $data): int
    {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Fitness fields are optional (NULL for admin/coach accounts)
        $genre   = $data['genre']    ?? null;
        $age     = isset($data['age'])    && $data['age']    !== '' ? (int)$data['age']     : null;
        $poids   = isset($data['poids'])  && $data['poids']  !== '' ? (float)$data['poids'] : null;
        $taille  = isset($data['taille']) && $data['taille'] !== '' ? (float)$data['taille']: null;
        $niveau  = $data['niveau']   ?? null;
        $objectif= $data['objectif'] ?? null;
        $role    = $data['role']     ?? 'user';

        $stmt = $this->db->prepare('
            INSERT INTO utilisateurs
                (nom, prenom, email, mot_de_passe, genre, age, poids, taille, niveau, objectif, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->bind_param(
            'sssssiddsss',
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $hash,
            $genre,
            $age,
            $poids,
            $taille,
            $niveau,
            $objectif,
            $role
        );
        $stmt->execute();
        $id = (int)$this->db->insert_id;
        $stmt->close();
        return $id;
    }

    /* ── Update profile ──────────────────────────────────── */
    public function updateProfile(int $id, array $data): void
    {
        $stmt = $this->db->prepare('
            UPDATE utilisateurs
            SET prenom=?, nom=?, age=?, poids=?, taille=?, objectif=?, niveau=?, profile_photo=?
            WHERE id=?
        ');
        $stmt->bind_param(
            'ssiddsssi',
            $data['prenom'],
            $data['nom'],
            $data['age'],
            $data['poids'],
            $data['taille'],
            $data['objectif'],
            $data['niveau'],
            $data['profile_photo'],
            $id
        );
        $stmt->execute();
        $stmt->close();
    }

    /* ── Stats ───────────────────────────────────────────── */
    public function totalWorkouts(int $id): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM user_sessions_log WHERE user_id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $count = (int)$stmt->get_result()->fetch_row()[0];
        $stmt->close();
        return $count;
    }

    public function weekWorkouts(int $id): int
    {
        $stmt = $this->db->prepare('
            SELECT COUNT(*) FROM user_sessions_log
            WHERE user_id=? AND date_completed >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
        ');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $count = (int)$stmt->get_result()->fetch_row()[0];
        $stmt->close();
        return $count;
    }

    public function workoutsLast14Days(int $id): int
    {
        $stmt = $this->db->prepare('
            SELECT COUNT(*) FROM user_sessions_log
            WHERE user_id=? AND date_completed >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
        ');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $count = (int)$stmt->get_result()->fetch_row()[0];
        $stmt->close();
        return $count;
    }

    /* ── Recommended programmes ──────────────────────────── */
    public function recommendedProgrammes(int $userId, string $objectif, string $niveau): array
    {
        try {
            $stmt = $this->db->prepare('
                SELECT p.* FROM programmes p
                LEFT JOIN user_programmes up
                       ON up.programme_id=p.id AND up.user_id=? AND up.statut="actif"
                WHERE up.id IS NULL AND p.type=?
                ORDER BY (p.niveau_requis=?) DESC, p.id ASC
                LIMIT 3
            ');
            $stmt->bind_param('iss', $userId, $objectif, $niveau);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            if (empty($result)) {
                $stmt2 = $this->db->prepare('
                    SELECT p.* FROM programmes p
                    LEFT JOIN user_programmes up
                           ON up.programme_id=p.id AND up.user_id=? AND up.statut="actif"
                    WHERE up.id IS NULL LIMIT 3
                ');
                $stmt2->bind_param('i', $userId);
                $stmt2->execute();
                $result = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt2->close();
            }
            return $result;
        } catch (Exception) {
            return [];
        }
    }

    /* ── Upcoming reservations ───────────────────────────── */
   public function upcomingReservations(int $userId): array
{
    try {
        $stmt = $this->db->prepare('
            SELECT gs.id, gs.title, gs.category, gs.session_date,
                   gs.session_time, gs.price, gs.image_path,
                   gr.created_at AS reserved_at
            FROM gym_reservations gr
            JOIN gym_sessions gs ON gs.id = gr.session_id
            WHERE gr.user_id = ?
              AND gs.session_date >= CURDATE()
            ORDER BY gs.session_date ASC, gs.session_time ASC
            LIMIT 4
        ');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    } catch (Exception) {
        return [];
    }
}

    /* ── Recent activity feed ────────────────────────────── */
    public function activityFeed(int $userId, int $limit = 12): array
    {
        $activities = [];
        $queries = [
            'SELECT "weight" AS type, date_log AS ad, DATE_FORMAT(date_log,"%d/%m") AS d, CONCAT("Poids : ", poids, " kg") AS message FROM weight_log WHERE user_id=?',
            'SELECT "workout" AS type, date_completed AS ad, DATE_FORMAT(date_completed,"%d/%m") AS d, CONCAT(session_type," — ",duration_minutes," min") AS message FROM user_sessions_log WHERE user_id=?',
            'SELECT "meal" AS type, date_log AS ad, DATE_FORMAT(date_log,"%d/%m") AS d, CONCAT(meal_name, IF(calories>0,CONCAT(" (",calories," kcal)"),"")) AS message FROM nutrition_log WHERE user_id=?',
        ];

        foreach ($queries as $sql) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('i', $userId);
                $stmt->execute();
                $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                $activities = array_merge($activities, $rows);
            } catch (Exception) {}
        }

        usort($activities, fn($a, $b) => strtotime($b['ad']) - strtotime($a['ad']));
        return array_slice($activities, 0, $limit);
    }
    public function countSessionsDays(int $userId, int $days): int
{
    $stmt = $this->db->prepare("
        SELECT COUNT(*)
        FROM user_sessions_log
        WHERE user_id = ?
        AND date_completed >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
    ");

    $stmt->bind_param("ii", $userId, $days);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_row()[0] ?? 0;
    $stmt->close();

    return (int)$result;
}
}
