<?php
/**
 * core/Database.php
 * mysqli singleton. Throws a clear error on connection failure.
 */
class Database
{
    private static ?mysqli $instance = null;

    public static function get(): mysqli
    {
        if (self::$instance === null) {
            $cfg = require __DIR__ . '/../config/database.php';

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            try {
                $conn = new mysqli(
                    $cfg['host'],
                    $cfg['user'],
                    $cfg['pass'],
                    $cfg['dbname']
                );
                $conn->set_charset($cfg['charset']);
                self::$instance = $conn;
            } catch (mysqli_sql_exception $e) {
                http_response_code(500);
                die('
                <div style="font-family:monospace;padding:2rem;background:#fff;color:#b91c1c;border:2px solid #b91c1c;margin:2rem;border-radius:8px;">
                    <h2 style="margin:0 0 1rem">Database connection failed</h2>
                    <p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                    <hr style="margin:1rem 0">
                    <p>Check <code>moveup/config/database.php</code>:</p>
                    <ul>
                        <li>Is XAMPP MySQL running? (green in XAMPP Control Panel)</li>
                        <li>Is the database name <strong>' . htmlspecialchars($cfg['dbname']) . '</strong> correct?</li>
                        <li>Is the password <strong>' . (empty($cfg['pass']) ? '(empty — XAMPP default)' : 'set correctly') . '</strong>?</li>
                    </ul>
                </div>');
            }
        }
        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
}
