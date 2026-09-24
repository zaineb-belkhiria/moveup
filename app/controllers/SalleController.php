<?php
/**
 * app/controllers/SalleController.php
 */
class SalleController
{
    public function dashboard(): void
    {
        Auth::requireRole('salle');

        $user   = Auth::user();
        $userId = (int) $user['id'];
        $db     = Database::get();

        // Auto-delete expired sessions
        $db->query("DELETE FROM gym_sessions WHERE admin_id = $userId AND session_date < CURDATE()");

        // Load salle profile
        $profileModel = new SalleProfile($db);
        $profile      = $profileModel->getByUserId($userId);

        // Load sessions with reservation count
        $s = $db->prepare("
            SELECT gs.*,
                   (SELECT COUNT(*) FROM gym_reservations r WHERE r.session_id = gs.id) AS reserved_count
            FROM gym_sessions gs
            WHERE gs.admin_id = ?
            ORDER BY gs.session_date ASC, gs.session_time ASC
        ");
        $s->bind_param('i', $userId);
        $s->execute();
        $mySeances = $s->get_result()->fetch_all(MYSQLI_ASSOC);
        $s->close();

        // Load who reserved per session
        $reservations = [];
        foreach ($mySeances as $seance) {
            $sid = (int) $seance['id'];
            $r   = $db->prepare("
                SELECT u.prenom, u.nom, u.email, gr.created_at
                FROM gym_reservations gr
                JOIN utilisateurs u ON gr.user_id = u.id
                WHERE gr.session_id = ? ORDER BY gr.created_at ASC
            ");
            $r->bind_param('i', $sid);
            $r->execute();
            $reservations[$sid] = $r->get_result()->fetch_all(MYSQLI_ASSOC);
            $r->close();
        }

        $totalSeances = count($mySeances);
        $totalRevenue = array_sum(array_column($mySeances, 'price'));

        View::render('salle/dashboard',
            compact('user', 'profile', 'mySeances', 'totalSeances', 'totalRevenue', 'reservations'),
            'dashboard');
    }

    public function updateProfile(): void
    {
        Auth::requireRole('salle');

        $userId      = (int) Auth::user()['id'];
        $db          = Database::get();
        $profileModel = new SalleProfile($db);

        $gymName     = trim($_POST['gym_name']     ?? '');
        $location    = trim($_POST['location']     ?? '');
        $description = trim($_POST['description']  ?? '');
        $phone       = trim($_POST['phone']        ?? '');

        // Handle cover photo upload
        $coverPhoto = null;
        if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
            $ext     = strtolower(pathinfo($_FILES['cover_photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed)) {
                $filename  = 'salle_' . $userId . '_' . time() . '.' . $ext;
                $uploadDir = ROOT . '/public/uploads/salles/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                if (move_uploaded_file($_FILES['cover_photo']['tmp_name'], $uploadDir . $filename)) {
                    $coverPhoto = 'uploads/salles/' . $filename;
                }
            }
        }

        $profileModel->save($userId, $gymName, $location, $description, $phone, $coverPhoto);

        header('Location: ' . View::base('salle/dashboard') . '?updated=1');
        exit();
    }
}
