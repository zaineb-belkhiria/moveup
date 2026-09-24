<?php
/**
 * app/models/SalleProfile.php
 * Manages salle_profiles table — separate from utilisateurs.
 */
class SalleProfile
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /** Get profile by user_id — returns empty array if none yet */
    public function getByUserId(int $userId): array
    {
        $s = $this->db->prepare("SELECT * FROM salle_profiles WHERE user_id = ?");
        $s->bind_param('i', $userId);
        $s->execute();
        $row = $s->get_result()->fetch_assoc();
        $s->close();
        return $row ?: [
            'user_id'     => $userId,
            'gym_name'    => '',
            'location'    => '',
            'description' => '',
            'phone'       => '',
            'cover_photo' => null,
        ];
    }

    /** Insert or update profile */
    public function save(int $userId, string $gymName, string $location,
                         string $description, string $phone,
                         ?string $coverPhoto = null): bool
    {
        if ($coverPhoto !== null) {
            $s = $this->db->prepare("
                INSERT INTO salle_profiles (user_id, gym_name, location, description, phone, cover_photo)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    gym_name    = VALUES(gym_name),
                    location    = VALUES(location),
                    description = VALUES(description),
                    phone       = VALUES(phone),
                    cover_photo = VALUES(cover_photo)
            ");
            $s->bind_param('isssss', $userId, $gymName, $location, $description, $phone, $coverPhoto);
        } else {
            $s = $this->db->prepare("
                INSERT INTO salle_profiles (user_id, gym_name, location, description, phone)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    gym_name    = VALUES(gym_name),
                    location    = VALUES(location),
                    description = VALUES(description),
                    phone       = VALUES(phone)
            ");
            $s->bind_param('issss', $userId, $gymName, $location, $description, $phone);
        }
        $ok = $s->execute();
        $s->close();
        return $ok;
    }

    /** Get all salle profiles joined with user data */
    public function getAllWithUsers(): array
    {
        $result = $this->db->query("
            SELECT u.id, u.prenom, u.nom, u.email, u.created_at,
                   COALESCE(sp.gym_name, '')    AS gym_name,
                   COALESCE(sp.location, '')    AS location,
                   COALESCE(sp.description, '') AS description,
                   COALESCE(sp.phone, '')        AS phone,
                   sp.cover_photo
            FROM utilisateurs u
            LEFT JOIN salle_profiles sp ON sp.user_id = u.id
            WHERE u.role = 'salle'
            ORDER BY u.created_at DESC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
