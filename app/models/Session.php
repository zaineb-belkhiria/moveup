<?php
class Session
{
    private mysqli $db;

    public function __construct(mysqli $db) { $this->db = $db; }

    public function getAll(string $category = 'all'): array
    {
        $sql = "
            SELECT gs.*,
                   COALESCE(sp.gym_name, CONCAT(u.prenom, ' ', u.nom)) AS gym_name,
                   COALESCE(sp.location, '') AS salle_location,
                   sp.cover_photo            AS salle_photo,
                   (SELECT COUNT(*) FROM gym_reservations r WHERE r.session_id = gs.id) AS reserved_count
            FROM gym_sessions gs
            JOIN utilisateurs u ON gs.admin_id = u.id
            LEFT JOIN salle_profiles sp ON sp.user_id = gs.admin_id
        ";

        // Une séance n'est affichée que tant que sa date/heure n'est pas passée.
        $notExpired = " WHERE TIMESTAMP(gs.session_date, gs.session_time) >= NOW() ";

        if ($category !== 'all' && $category !== '') {
            $s = $this->db->prepare($sql . $notExpired . " AND gs.category = ? ORDER BY gs.session_date ASC, gs.session_time ASC");
            $s->bind_param('s', $category);
            $s->execute();
            $rows = $s->get_result()->fetch_all(MYSQLI_ASSOC);
            $s->close();
            return $rows;
        }

        $result = $this->db->query($sql . $notExpired . " ORDER BY gs.session_date ASC, gs.session_time ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Supprime définitivement les séances dont la date est passée.
     * À appeler périodiquement (ex: cron, ou à chaque publication) pour
     * garder la table gym_sessions propre.
     */
    public function purgeExpired(): int
    {
        $this->db->query("DELETE FROM gym_reservations WHERE session_id IN (
            SELECT id FROM (
                SELECT id FROM gym_sessions WHERE TIMESTAMP(session_date, session_time) < NOW()
            ) AS expired
        )");
        $this->db->query("DELETE FROM gym_sessions WHERE TIMESTAMP(session_date, session_time) < NOW()");
        return $this->db->affected_rows;
    }

    public function publish(int $adminId, string $title, string $category,
                            string $date, string $time, float $price,
                            int $capacity, string $imagePath): bool
    {
        $s = $this->db->prepare("
            INSERT INTO gym_sessions (admin_id, title, category, session_date, session_time, price, capacity, image_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $s->bind_param('issssdis', $adminId, $title, $category, $date, $time, $price, $capacity, $imagePath);
        $ok = $s->execute();
        $s->close();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $s = $this->db->prepare('DELETE FROM gym_sessions WHERE id = ?');
        $s->bind_param('i', $id);
        $ok = $s->execute();
        $s->close();
        return $ok;
    }

    public function reserve(int $sessionId, int $userId): string
    {
        $s = $this->db->prepare("SELECT id FROM gym_reservations WHERE session_id = ? AND user_id = ?");
        $s->bind_param('ii', $sessionId, $userId);
        $s->execute();
        if ($s->get_result()->fetch_assoc()) { $s->close(); return 'already'; }
        $s->close();

        $s = $this->db->prepare("
            SELECT gs.capacity,
                   (SELECT COUNT(*) FROM gym_reservations r WHERE r.session_id = gs.id) AS reserved_count
            FROM gym_sessions gs WHERE gs.id = ?
        ");
        $s->bind_param('i', $sessionId);
        $s->execute();
        $row = $s->get_result()->fetch_assoc();
        $s->close();

        if (!$row || (int)$row['reserved_count'] >= (int)$row['capacity']) return 'full';

        $s = $this->db->prepare("INSERT INTO gym_reservations (session_id, user_id) VALUES (?, ?)");
        $s->bind_param('ii', $sessionId, $userId);
        $ok = $s->execute();
        $s->close();
        return $ok ? 'ok' : 'error';
    }

    public function cancelReservation(int $sessionId, int $userId): bool
    {
        $s = $this->db->prepare("DELETE FROM gym_reservations WHERE session_id = ? AND user_id = ?");
        $s->bind_param('ii', $sessionId, $userId);
        $ok = $s->execute();
        $s->close();
        return $ok;
    }

    public function hasReserved(int $sessionId, int $userId): bool
    {
        $s = $this->db->prepare("SELECT id FROM gym_reservations WHERE session_id = ? AND user_id = ?");
        $s->bind_param('ii', $sessionId, $userId);
        $s->execute();
        $row = $s->get_result()->fetch_assoc();
        $s->close();
        return (bool) $row;
    }
}
