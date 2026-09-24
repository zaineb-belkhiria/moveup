<?php
/**
 * app/models/Weight.php — uses mysqli
 */
class Weight
{
    public function __construct(private mysqli $db) {}

    private function ensureWeightTable(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS weight_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            poids DECIMAL(5,2) NOT NULL,
            date_log DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_date (user_id, date_log)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    }

    private function ensureSessionTable(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS user_sessions_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            session_type VARCHAR(120),
            duration_minutes INT DEFAULT 0,
            calories_burned INT DEFAULT 0,
            date_completed DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    }

    public function log(int $userId, float $weight, string $date): void
    {
        $this->ensureWeightTable();
        $stmt = $this->db->prepare('
            INSERT INTO weight_log (user_id, poids, date_log) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE poids=VALUES(poids)
        ');
        $stmt->bind_param('ids', $userId, $weight, $date);
        $stmt->execute();
        $stmt->close();

        // Keep utilisateurs.poids in sync
        $stmt2 = $this->db->prepare('UPDATE utilisateurs SET poids=? WHERE id=?');
        $stmt2->bind_param('di', $weight, $userId);
        $stmt2->execute();
        $stmt2->close();
    }

    public function latest(int $userId): ?float
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT poids FROM weight_log WHERE user_id=? ORDER BY date_log DESC LIMIT 1'
            );
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_row();
            $stmt->close();
            return $row ? (float)$row[0] : null;
        } catch (Exception) { return null; }
    }

    public function history(int $userId, int $limit = 7): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT poids, DATE_FORMAT(date_log,"%d/%m") AS d FROM weight_log WHERE user_id=? ORDER BY date_log DESC LIMIT ?'
            );
            $stmt->bind_param('ii', $userId, $limit);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return array_reverse($rows);
        } catch (Exception) { return []; }
    }

    public function chartData(int $userId, int $limit = 12): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT date_log, poids FROM weight_log WHERE user_id=? ORDER BY date_log ASC LIMIT ?'
            );
            $stmt->bind_param('ii', $userId, $limit);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        } catch (Exception) { return []; }
    }

    public function logWorkout(int $userId, string $sessionName, int $duration): void
    {
        $this->ensureSessionTable();
        $calories = round($duration * 8);
        $stmt = $this->db->prepare('
            INSERT INTO user_sessions_log (user_id, session_type, duration_minutes, calories_burned, date_completed)
            VALUES (?, ?, ?, ?, CURDATE())
        ');
        $stmt->bind_param('isii', $userId, $sessionName, $duration, $calories);
        $stmt->execute();
        $stmt->close();
    }
}
