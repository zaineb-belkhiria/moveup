<?php
/**
 * app/controllers/CoursController.php
 */
class CoursController
{
    public function index(): void
    {
        Auth::requireLogin();
        $category = trim($_GET['type'] ?? 'all');
        $db       = Database::get();
        $model    = new Session($db);
        $model->purgeExpired();
        $seances  = $model->getAll($category);
        $userId   = (int) Auth::user()['id'];

        // Add reservation status for each session
        foreach ($seances as &$s) {
            $s['user_reserved'] = $model->hasReserved((int)$s['id'], $userId);
        }
        unset($s);

        View::render('cours/index', compact('seances', 'category'), 'dashboard');
    }

    public function showPublish(): void
    {
        Auth::requireRole('admin', 'salle');
        View::render('cours/publish', [], 'dashboard');
    }

    public function publish(): void
    {
        Auth::requireRole('admin', 'salle');
        $db      = Database::get();
        $model   = new Session($db);
        $adminId = (int) Auth::user()['id'];

        $title    = trim($_POST['title']    ?? '');
        $category = trim($_POST['category'] ?? 'gym');
        $date     = trim($_POST['date']     ?? '');
        $time     = trim($_POST['time']     ?? '');
        $price    = (float) ($_POST['price']    ?? 0);
        $capacity = (int)   ($_POST['capacity'] ?? 30);

        $imagePath = 'default_gym.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed)) {
                $filename  = time() . '_' . uniqid() . '.' . $ext;
                $uploadDir = ROOT . '/public/uploads/sessions/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                    $imagePath = 'uploads/sessions/' . $filename;
                }
            }
        }

        if ($title && $date && $time) {
            $model->publish($adminId, $title, $category, $date, $time, $price, $capacity, $imagePath);
        }

        header('Location: ' . View::base('cours'));
        exit();
    }

    public function delete(): void
    {
        Auth::requireRole('admin', 'salle');
        $db    = Database::get();
        $model = new Session($db);
        $model->delete((int) ($_POST['id'] ?? 0));
        header('Location: ' . View::base('cours'));
        exit();
    }

    /** POST /cours/reserve — user reserves a spot */
    public function reserve(): void
    {
        Auth::requireLogin();
        header('Content-Type: application/json');
        $sessionId = (int) ($_POST['session_id'] ?? 0);
        $userId    = (int) Auth::user()['id'];
        $model     = new Session(Database::get());
        $result    = $model->reserve($sessionId, $userId);
        echo json_encode(['status' => $result]);
        exit();
    }

    /** POST /cours/cancel — user cancels their reservation */
    public function cancel(): void
    {
        Auth::requireLogin();
        header('Content-Type: application/json');
        $sessionId = (int) ($_POST['session_id'] ?? 0);
        $userId    = (int) Auth::user()['id'];
        $model     = new Session(Database::get());
        $ok        = $model->cancelReservation($sessionId, $userId);
        echo json_encode(['status' => $ok ? 'ok' : 'error']);
        exit();
    }
}
