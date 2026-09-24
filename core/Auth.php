<?php
/**
 * core/Auth.php
 * Authentication helpers — uses mysqli (no PDO).
 * Session stored as $_SESSION['user'] (full user row).
 */
class Auth
{
    /* ── Boot ────────────────────────────────────────────── */
    public static function boot(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 0); // Set 1 on HTTPS
            session_start();
        }
        self::autoLogin();
    }

    /* ── Check ───────────────────────────────────────────── */
    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function userId(): ?int
    {
        return isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
    }

    /* ── Guards ──────────────────────────────────────────── */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . View::base('login') . '?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();
        $role = $_SESSION['user']['role'] ?? 'user';
        if (!in_array($role, $roles, true)) {
            header('Location: ' . View::base('unauthorized'));
            exit;
        }
    }

    public static function redirectIfLoggedIn(): void
    {
        if (self::isLoggedIn()) {
            self::redirectByRole($_SESSION['user']['role'] ?? 'user');
        }
    }

    /* ── Login ───────────────────────────────────────────── */
    public static function attempt(string $email, string $password, bool $remember = false, string $redirect = ''): string|false
    {
        if (self::isBlocked($email)) {
            return 'Trop de tentatives. Réessayez dans 15 minutes.';
        }

        $db   = Database::get();
        $stmt = $db->prepare('SELECT * FROM utilisateurs WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) {
            self::registerFailedLogin($email);
            return 'Email introuvable.';
        }

        $hash = $user['mot_de_passe'] ?? $user['password'] ?? '';
        if (!password_verify($password, $hash)) {
            self::registerFailedLogin($email);
            return 'Mot de passe incorrect.';
        }

        if (isset($user['statut']) && $user['statut'] !== 'actif') {
            return 'Compte désactivé.';
        }

        self::clearAttempts($email);
       session_regenerate_id(true);
    $_SESSION['user'] = $user;

   $role = $user['role'] ?? 'user';
        if ($redirect !== '' && str_starts_with($redirect, '/')) {
            header('Location: ' . $redirect);
            exit;
        }
        self::redirectByRole($role);
        return false;
    }

    /* ── Logout ──────────────────────────────────────────── */
    public static function logout(): void
    {
        $db = Database::get();
        if (!empty($_SESSION['user']['id'])) {
            try {
                $stmt = $db->prepare('UPDATE utilisateurs SET remember_token = NULL WHERE id = ?');
                $stmt->bind_param('i', $_SESSION['user']['id']);
                $stmt->execute();
                $stmt->close();
            } catch (Exception) {}
        }

        setcookie('remember_token', '', time() - 3600, '/');
        session_unset();
        session_destroy();

        header('Location: ' . View::base('login'));
        exit;
    }

    /* ── Remember Me ─────────────────────────────────────── */
    private static function autoLogin(): void
    {
        if (!empty($_SESSION['user'])) return;
        if (empty($_COOKIE['remember_token'])) return;

        try {
            $db   = Database::get();
            $stmt = $db->prepare('SELECT * FROM utilisateurs WHERE remember_token = ? LIMIT 1');
            $stmt->bind_param('s', $_COOKIE['remember_token']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($user) { $_SESSION['user'] = $user; }
        } catch (Exception) {}
    }

    private static function setRememberCookie(int $id): void
    {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + 86400 * 30, '/', '', false, true);

        $db   = Database::get();
        $stmt = $db->prepare('UPDATE utilisateurs SET remember_token = ? WHERE id = ?');
        $stmt->bind_param('si', $token, $id);
        $stmt->execute();
        $stmt->close();
    }

    /* ── Brute-force protection ──────────────────────────── */
    private static function registerFailedLogin(string $email): void
    {
        $_SESSION['attempts'][$email][] = time();
    }

    private static function isBlocked(string $email): bool
    {
        if (empty($_SESSION['attempts'][$email])) return false;
        $recent = array_filter(
            $_SESSION['attempts'][$email],
            fn($t) => $t > time() - 900
        );
        $_SESSION['attempts'][$email] = array_values($recent);
        return count($recent) >= 5;
    }

    private static function clearAttempts(string $email): void
    {
        unset($_SESSION['attempts'][$email]);
    }

    /* ── Role redirect ───────────────────────────────────── */
    public static function redirectByRole(string $role): void
    {
        match ($role) {
            'admin' => header('Location: ' . View::base('admin/dashboard')),
            'salle' => header('Location: ' . View::base('salle/dashboard')),
            default => header('Location: ' . View::base('dashboard')),
        };
        exit;
    }

    /* ── CSRF ────────────────────────────────────────────── */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function verifyCsrf(string $token): bool
    {
        return hash_equals($_SESSION['csrf'] ?? '', $token);
    }
}
