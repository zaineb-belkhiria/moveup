<?php
/**
 * app/models/Review.php — uses mysqli
 */
class Review
{
    public function __construct(private mysqli $db) {}

    public function getAll(int $limit = 20): array
    {
        try {
            // BUG FIX: was embedding $limit directly in query string (SQL injection risk)
            $stmt = $this->db->prepare('
                SELECT a.*, u.prenom
                FROM avis a
                JOIN utilisateurs u ON u.id=a.user_id
                ORDER BY a.created_at DESC
                LIMIT ?
            ');
            $stmt->bind_param('i', $limit);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        } catch (Exception) { return []; }
    }

    public function getRecent(int $limit = 6): array
    {
        return $this->getAll($limit);
    }

    public function upsert(int $userId, string $type, ?string $targetId, int $note, string $comment): void
    {
        $check = $this->db->prepare('SELECT id FROM avis WHERE user_id=? AND type=? AND target_id=?');
        $check->bind_param('iss', $userId, $type, $targetId);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            // UPDATE: note(i), comment(s), userId(i), type(s), targetId(s)
            $stmt = $this->db->prepare('UPDATE avis SET note=?, commentaire=? WHERE user_id=? AND type=? AND target_id=?');
            $stmt->bind_param('isiss', $note, $comment, $userId, $type, $targetId);
        } else {
            // INSERT: userId(i), type(s), targetId(s), note(i), comment(s)
            $stmt = $this->db->prepare('INSERT INTO avis (user_id, type, target_id, note, commentaire) VALUES (?,?,?,?,?)');
            $stmt->bind_param('issis', $userId, $type, $targetId, $note, $comment);
        }
        $stmt->execute();
        $stmt->close();
    }
}
