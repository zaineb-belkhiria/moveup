<?php
/**
 * app/controllers/AdminController.php
 * Admin-only area — requires role = 'admin'
 */
class AdminController
{
    /* ── Main admin dashboard ─────────────────────────── */
    public function dashboard(): void
    {
        Auth::requireRole('admin');

        $db = Database::get();

        /* ── Users stats ─────────────────────────────── */
        $totalUsers   = (int)$db->query('SELECT COUNT(*) FROM utilisateurs')->fetch_row()[0];
        $newThisMonth = (int)$db->query("SELECT COUNT(*) FROM utilisateurs WHERE created_at >= DATE_FORMAT(NOW(),'%Y-%m-01')")->fetch_row()[0];
        $newToday     = (int)$db->query("SELECT COUNT(*) FROM utilisateurs WHERE DATE(created_at) = CURDATE()")->fetch_row()[0];

        // Users by role
        $byRole = [];
        foreach ($db->query("SELECT role, COUNT(*) AS cnt FROM utilisateurs GROUP BY role")->fetch_all(MYSQLI_ASSOC) as $r) {
            $byRole[$r['role']] = (int)$r['cnt'];
        }

        // Users by objectif
        $byObjectif = [];
        foreach ($db->query("SELECT objectif, COUNT(*) AS cnt FROM utilisateurs GROUP BY objectif")->fetch_all(MYSQLI_ASSOC) as $r) {
            $byObjectif[$r['objectif']] = (int)$r['cnt'];
        }

        // Users by niveau
        $byNiveau = [];
        foreach ($db->query("SELECT niveau, COUNT(*) AS cnt FROM utilisateurs GROUP BY niveau")->fetch_all(MYSQLI_ASSOC) as $r) {
            $byNiveau[$r['niveau']] = (int)$r['cnt'];
        }

        // BUG FIX: registrations chart used raw date string in query (SQL injection risk).
        // Use DATE_SUB with a range query instead.
        $regChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $d   = date('Y-m-d', strtotime("-$i days"));
            $stmt = $db->prepare("SELECT COUNT(*) FROM utilisateurs WHERE DATE(created_at) = ?");
            $stmt->bind_param('s', $d);
            $stmt->execute();
            $cnt = (int)$stmt->get_result()->fetch_row()[0];
            $stmt->close();
            $regChart[] = ['date' => date('d/m', strtotime($d)), 'count' => $cnt];
        }

        /* ── Activity stats ──────────────────────────── */
        $totalWorkouts = 0;
        $workoutsWeek  = 0;
        try {
            $totalWorkouts = (int)$db->query('SELECT COUNT(*) FROM user_sessions_log')->fetch_row()[0];
            $workoutsWeek  = (int)$db->query("SELECT COUNT(*) FROM user_sessions_log WHERE date_completed >= DATE_SUB(CURDATE(),INTERVAL 7 DAY)")->fetch_row()[0];
        } catch (Exception) {}

        /* ── Contact messages ────────────────────────── */
        $contactModel   = new Contact($db);
        $contactCounts  = $contactModel->counts();
        $latestMessages = array_slice($contactModel->getAll('all'), 0, 5);

        /* ── Reviews / Avis ──────────────────────────── */
        $totalAvis = 0;
        $avgNote   = 0;
        try {
            $row = $db->query('SELECT COUNT(*) AS cnt, AVG(note) AS avg FROM avis')->fetch_assoc();
            $totalAvis = (int)($row['cnt'] ?? 0);
            $avgNote   = round((float)($row['avg'] ?? 0), 1);
        } catch (Exception) {}

        /* ── Latest registered users ─────────────────── */
        $latestUsers = $db->query(
            "SELECT id, prenom, nom, email, role, objectif, niveau, created_at
             FROM utilisateurs ORDER BY created_at DESC LIMIT 8"
        )->fetch_all(MYSQLI_ASSOC);

        /* ── All users for management tab ───────────── */
        $allUsers = $db->query(
            "SELECT id, prenom, nom, email, role, objectif, niveau, statut, created_at, last_login
             FROM utilisateurs ORDER BY created_at DESC"
        )->fetch_all(MYSQLI_ASSOC);

        /* ── Gym sessions & Salles ─────────────────── */
        $totalSalles = (int)($byRole['salle'] ?? 0);
        $allGymSessions = [];
        $allSalles = [];
        try {
            $allGymSessions = $db->query("
                SELECT gs.*,
                       COALESCE(sp.gym_name, CONCAT(u.prenom, ' ', u.nom)) AS salle_name,
                       COALESCE(sp.location, '') AS salle_location,
                       sp.cover_photo AS salle_photo,
                       (SELECT COUNT(*) FROM gym_reservations r WHERE r.session_id = gs.id) AS reserved_count
                FROM gym_sessions gs
                JOIN utilisateurs u ON gs.admin_id = u.id
                LEFT JOIN salle_profiles sp ON sp.user_id = gs.admin_id
                ORDER BY gs.session_date ASC, gs.session_time ASC
            ")->fetch_all(MYSQLI_ASSOC);
            $allSalles = $db->query("
                SELECT u.id, u.prenom, u.nom, u.email, u.created_at,
                       COALESCE(sp.gym_name, '') AS gym_name,
                       COALESCE(sp.location, '') AS location,
                       COALESCE(sp.phone, '')    AS phone,
                       sp.cover_photo
                FROM utilisateurs u
                LEFT JOIN salle_profiles sp ON sp.user_id = u.id
                WHERE u.role = 'salle'
                ORDER BY u.created_at DESC
            ")->fetch_all(MYSQLI_ASSOC);
        } catch (Exception) {}
        $totalGymSessions = count($allGymSessions);

        $adminUser = $db->query("SELECT id, prenom, nom, email FROM utilisateurs WHERE id = " . Auth::userId())->fetch_assoc();

        View::render('admin/dashboard', compact(
            'totalUsers','newThisMonth','newToday',
            'byRole','byObjectif','byNiveau',
            'regChart',
            'totalWorkouts','workoutsWeek',
            'contactCounts','latestMessages',
            'totalAvis','avgNote',
            'latestUsers','allUsers',
            'totalSalles','allGymSessions','totalGymSessions','allSalles',
            'adminUser'
        ), 'dashboard');
    }

    /* ── Update user role (POST) ──────────────────────── */
    public function updateRole(): void
    {
        Auth::requireRole('admin');

        $userId = (int)($_POST['user_id'] ?? 0);
        $role   = $_POST['role'] ?? 'user';

        if (!in_array($role, ['user','admin','salle'], true)) {
            header('Location: ' . View::base('admin/dashboard') . '?tab=users&error=invalid_role');
            exit;
        }

        // Prevent removing own admin role
        if ($userId === Auth::userId() && $role !== 'admin') {
            header('Location: ' . View::base('admin/dashboard') . '?tab=users&error=self_demotion');
            exit;
        }

        try {
            $stmt = Database::get()->prepare('UPDATE utilisateurs SET role=? WHERE id=?');
            $stmt->bind_param('si', $role, $userId);
            $stmt->execute();
            $stmt->close();
        } catch (Exception) {}

        header('Location: ' . View::base('admin/dashboard') . '?tab=users&success=1');
        exit;
    }

    /* ── Show create admin/coach form (GET) ──────────── */
    public function showCreateUser(): void
    {
        Auth::requireRole('admin');
        View::render('admin/create_user', ['flash' => null], 'dashboard');
    }

    /* ── Create admin or coach account (POST) ─────────── */
    public function createUser(): void
    {
        Auth::requireRole('admin');

        $nom      = trim($_POST['nom']          ?? '');
        $prenom   = trim($_POST['prenom']       ?? '');
        $email    = trim($_POST['email']        ?? '');
        $password = $_POST['mot_de_passe']      ?? '';
        $role     = $_POST['role']              ?? 'admin';

        if (!$nom || !$prenom || !$email || !$password) {
            View::render('admin/create_user', [
                'flash' => ['type' => 'error', 'msg' => 'Tous les champs sont obligatoires.'],
                'old'   => $_POST,
            ], 'dashboard');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            View::render('admin/create_user', [
                'flash' => ['type' => 'error', 'msg' => 'Email invalide.'],
                'old'   => $_POST,
            ], 'dashboard');
            return;
        }

        if (!in_array($role, ['admin', 'salle'], true)) {
            $role = 'admin';
        }

        try {
            (new User(Database::get()))->create([
                'nom'      => $nom,
                'prenom'   => $prenom,
                'email'    => $email,
                'password' => $password,
                'role'     => $role,
                // fitness fields intentionally omitted — will be NULL
            ]);
            header('Location: ' . View::base('admin/dashboard') . '?tab=users&success=1');
            exit;
        } catch (mysqli_sql_exception) {
            View::render('admin/create_user', [
                'flash' => ['type' => 'error', 'msg' => 'Cet email est déjà utilisé.'],
                'old'   => $_POST,
            ], 'dashboard');
        }
    }

    /* ── Update admin settings (POST) ────────────────── */
    public function updateSettings(): void
    {
        Auth::requireRole('admin');
        $db      = Database::get();
        $section = $_POST['section'] ?? '';
        $id      = Auth::userId();

        if ($section === 'info') {
            $prenom = trim($_POST['prenom'] ?? '');
            $nom    = trim($_POST['nom']    ?? '');
            $email  = trim($_POST['email']  ?? '');

            if (!$prenom || !$nom || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header('Location: ' . View::base('admin/dashboard') . '?tab=settings&settings_error=invalid');
                exit;
            }

            // Check email not taken by another user
            $chk = $db->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
            $chk->bind_param('si', $email, $id);
            $chk->execute();
            if ($chk->get_result()->fetch_assoc()) {
                header('Location: ' . View::base('admin/dashboard') . '?tab=settings&settings_error=email_taken');
                exit;
            }
            $chk->close();

            $stmt = $db->prepare("UPDATE utilisateurs SET prenom=?, nom=?, email=? WHERE id=?");
            $stmt->bind_param('sssi', $prenom, $nom, $email, $id);
            $stmt->execute();
            $stmt->close();

            // Update session
            $_SESSION['user']['prenom'] = $prenom;
            $_SESSION['user']['nom']    = $nom;
            $_SESSION['user']['email']  = $email;

        } elseif ($section === 'password') {
            $current = $_POST['current_password'] ?? '';
            $new     = $_POST['new_password']     ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            $row = $db->query("SELECT mot_de_passe FROM utilisateurs WHERE id = $id")->fetch_assoc();

            if (!password_verify($current, $row['mot_de_passe'])) {
                header('Location: ' . View::base('admin/dashboard') . '?tab=settings&settings_error=wrong_password');
                exit;
            }
            if (strlen($new) < 8 || $new !== $confirm) {
                header('Location: ' . View::base('admin/dashboard') . '?tab=settings&settings_error=invalid');
                exit;
            }

            $hashed = password_hash($new, PASSWORD_BCRYPT);
            $stmt   = $db->prepare("UPDATE utilisateurs SET mot_de_passe=? WHERE id=?");
            $stmt->bind_param('si', $hashed, $id);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: ' . View::base('admin/dashboard') . '?tab=settings&settings_success=1');
        exit;
    }

    /* ── Toggle user status active/inactif (POST) ─────── */
    public function toggleStatus(): void
    {
        Auth::requireRole('admin');

        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId === Auth::userId()) {
            header('Location: ' . View::base('admin/dashboard') . '?tab=users&error=self_ban');
            exit;
        }

        try {
            $db   = Database::get();
            $stmt = $db->prepare('SELECT statut FROM utilisateurs WHERE id=?');
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $row  = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $newStatus = ($row['statut'] ?? 'actif') === 'actif' ? 'inactif' : 'actif';

            $upd = $db->prepare('UPDATE utilisateurs SET statut=? WHERE id=?');
            $upd->bind_param('si', $newStatus, $userId);
            $upd->execute();
            $upd->close();
        } catch (Exception) {}

        header('Location: ' . View::base('admin/dashboard') . '?tab=users&success=1');
        exit;
    }
}
