<?php
/**
 * app/controllers/HomeController.php
 */
class HomeController
{
    public function index(): void
    {
        // If already logged in, send straight to dashboard — no public landing page shown
        if (Auth::isLoggedIn()) {
            View::redirect('dashboard');
        }

        $db = Database::get();

        $programmes = [];
        try {
            $result = $db->query('SELECT * FROM programmes ORDER BY id LIMIT 3');
            if ($result) {
                $programmes = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
            }
        } catch (Exception) {}

        $avis = (new Review($db))->getRecent(6);

        View::render('home/index', compact('programmes', 'avis'), 'public');
    }
}
