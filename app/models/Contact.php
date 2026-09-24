<?php
/**
 * app/models/Contact.php — uses mysqli
 */
class Contact
{
    public function __construct(private mysqli $db) {}

    private function ensureTable(): void
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(120) NOT NULL,
            email VARCHAR(180) NOT NULL,
            telephone VARCHAR(30) DEFAULT NULL,
            sujet VARCHAR(80) DEFAULT NULL,
            message TEXT NOT NULL,
            lu TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function submit(array $data): void
    {
        $this->ensureTable();
        $stmt = $this->db->prepare('INSERT INTO contacts (nom, email, telephone, sujet, message) VALUES (?,?,?,?,?)');
        $tel  = $data['telephone'] ?: null;
        $suj  = $data['sujet']     ?: null;
        $stmt->bind_param('sssss', $data['nom'], $data['email'], $tel, $suj, $data['message']);
        $stmt->execute();
        $stmt->close();
    }

    public function getAll(string $filter = 'all'): array
    {
        $this->ensureTable();
        $where = match($filter) {
            'unread' => 'WHERE lu=0',
            'read'   => 'WHERE lu=1',
            default  => '',
        };
        return $this->db->query("SELECT * FROM contacts $where ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
    }

    public function counts(): array
    {
        $this->ensureTable();
        $row = $this->db->query('SELECT COUNT(*) AS total, SUM(lu=0) AS unread, SUM(lu=1) AS lus FROM contacts')->fetch_assoc();
        return [
            'all'    => (int)($row['total']  ?? 0),
            'unread' => (int)($row['unread'] ?? 0),
            'read'   => (int)($row['lus']    ?? 0),
        ];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM contacts WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function markRead(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE contacts SET lu=1 WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    public function markUnread(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE contacts SET lu=0 WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    public function markAllRead(): void
    {
        $this->db->query('UPDATE contacts SET lu=1');
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM contacts WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}
