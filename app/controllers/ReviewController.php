<?php
/**
 * app/controllers/ReviewController.php
 */
class ReviewController
{
    public function show(): void
    {
        Auth::requireLogin();
        View::render('reviews/submit', ['flash' => null], 'dashboard');
    }

    public function submit(): void
    {
        Auth::requireLogin();
        $note        = (int)($_POST['note']        ?? 0);
        $commentaire = trim($_POST['commentaire']  ?? '');
        $type        = $_POST['type']              ?? 'general';
        $target_id   = $_POST['target_id']         ?: null;

        if ($note < 1 || $note > 5) {
            View::render('reviews/submit', ['flash' => ['type'=>'error','msg'=>'Choisissez une note entre 1 et 5.']], 'dashboard');
            return;
        }
        if (!$commentaire) {
            View::render('reviews/submit', ['flash' => ['type'=>'error','msg'=>'Veuillez écrire un commentaire.']], 'dashboard');
            return;
        }

        try {
            (new Review(Database::get()))->upsert(Auth::userId(), $type, $target_id, $note, $commentaire);
            View::redirect('reviews?success=1');
        } catch (Exception) {
            View::render('reviews/submit', ['flash' => ['type'=>'error','msg'=>"Erreur lors de l'enregistrement."]], 'dashboard');
        }
    }

    public function all(): void
    {
        $reviews = (new Review(Database::get()))->getAll();
        $success = isset($_GET['success']);
        View::render('reviews/all', compact('reviews', 'success'), 'dashboard');
    }
}
