<?php
/**
 * app/models/Activite.php
 * Correspond exactement à la table `activites` de la base moveup.
 * Utilise mysqli — même pattern que Weight.php / Nutrition.php.
 */
class Activite
{
    public function __construct(private mysqli $db) {}

    /** Dernières activités de l'utilisateur */
    public function recent(int $userId, int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT id, type, nom, duree_minutes, calories_brulees, date_activite, notes
            FROM activites
            WHERE user_id = ?
            ORDER BY date_activite DESC, created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /** Stats globales : total séances, minutes, calories */
    public function stats(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*)                           AS total,
                COALESCE(SUM(duree_minutes),   0)  AS total_min,
                COALESCE(SUM(calories_brulees),0)  AS total_cal
            FROM activites
            WHERE user_id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?? ['total' => 0, 'total_min' => 0, 'total_cal' => 0];
    }

    /** Ajouter une activité — inclut le champ `notes` optionnel */
    public function add(
        int    $userId,
        string $type,
        string $nom,
        int    $duree,
        int    $calories,
        string $date,
        string $notes = ''
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO activites
                (user_id, type, nom, duree_minutes, calories_brulees, date_activite, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $notesVal = $notes === '' ? null : $notes;
        $stmt->bind_param('isssiis', $userId, $type, $nom, $duree, $calories, $date, $notesVal);
        $stmt->execute();
        $id = (int)$this->db->insert_id;
        $stmt->close();
        return $id;
    }

    /** Supprimer — vérifie que l'activité appartient bien à cet utilisateur */
    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM activites WHERE id = ? AND user_id = ?");
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    }
}
