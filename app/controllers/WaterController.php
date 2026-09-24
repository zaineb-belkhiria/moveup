<?php
/**
 * app/controllers/WaterController.php
 */
class WaterController
{
    private const DAILY_GOAL = 2.5; // litres

    private function ensureTable(\mysqli $db): void
    {
        $db->query("CREATE TABLE IF NOT EXISTS water_log (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            user_id    INT NOT NULL,
            log_date   DATE NOT NULL,
            litres     DECIMAL(4,2) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_user_date (user_id, log_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function log(): void
    {
        Auth::requireLogin();
        $litres = round((float)($_POST['litres'] ?? 0), 2);
        $date   = $_POST['date'] ?? date('Y-m-d');

        if ($litres <= 0 || $litres > 20) {
            View::json(['success' => false, 'error' => 'Valeur invalide']);
            return;
        }

        try {
            $db = Database::get();
            $this->ensureTable($db);
            $uid = Auth::userId();
            $stmt = $db->prepare("INSERT INTO water_log (user_id, log_date, litres)
                                  VALUES (?, ?, ?)
                                  ON DUPLICATE KEY UPDATE litres = ?");
            $stmt->bind_param('isdd', $uid, $date, $litres, $litres);
            $stmt->execute();
            $stmt->close();
            View::json(['success' => true, 'litres' => $litres, 'goal' => self::DAILY_GOAL]);
        } catch (Exception $e) {
            View::json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function history(): void
    {
        Auth::requireLogin();
        try {
            $db = Database::get();
            $this->ensureTable($db);
            $uid  = Auth::userId();
            $stmt = $db->prepare("SELECT log_date, litres FROM water_log
                                  WHERE user_id = ?
                                  ORDER BY log_date DESC LIMIT 30");
            $stmt->bind_param('i', $uid);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            View::json(['success' => true, 'history' => $rows, 'goal' => self::DAILY_GOAL]);
        } catch (Exception $e) {
            View::json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
