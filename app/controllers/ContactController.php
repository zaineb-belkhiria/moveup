<?php
/**
 * app/controllers/ContactController.php
 */
class ContactController
{
    public function show(): void
    {
        View::render('contact/index', ['flash' => null], 'public');
    }

    public function submit(): void
    {
        $nom       = trim($_POST['nom']       ?? '');
        $email     = trim($_POST['email']     ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $sujet     = trim($_POST['sujet']     ?? '');
        $message   = trim($_POST['message']   ?? '');

        if (!$nom || !$email || !$message) {
            View::render('contact/index', ['flash' => ['type'=>'error','msg'=>'Veuillez remplir tous les champs obligatoires.'], 'old' => $_POST], 'public');
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            View::render('contact/index', ['flash' => ['type'=>'error','msg'=>'Adresse email invalide.'], 'old' => $_POST], 'public');
            return;
        }

        try {
            (new Contact(Database::get()))->submit(compact('nom','email','telephone','sujet','message'));
            View::render('contact/index', ['flash' => ['type'=>'success','msg'=>'Message envoyé ! Nous vous répondrons sous 24h.']], 'public');
        } catch (Exception) {
            View::render('contact/index', ['flash' => ['type'=>'error','msg'=>"Erreur lors de l'envoi. Veuillez réessayer."]], 'public');
        }
    }

    /* Admin inbox */
    public function admin(): void
    {
        Auth::requireRole('admin');
        $model  = new Contact(Database::get());
        $filter = $_GET['filter'] ?? 'all';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $msgId  = (int)($_POST['msg_id'] ?? 0);
            if ($msgId) {
                match($action) {
                    'mark_read'     => $model->markRead($msgId),
                    'mark_unread'   => $model->markUnread($msgId),
                    'delete'        => $model->delete($msgId),
                    'mark_all_read' => $model->markAllRead(),
                    default         => null,
                };
            }
        }

        $messages = $model->getAll($filter);
        $counts   = $model->counts();
        $openId   = (int)($_GET['open'] ?? 0);

        if ($openId) {
            $model->markRead($openId);
            $counts = $model->counts();
        }

        View::render('contact/admin', compact('messages','counts','filter','openId'), 'dashboard');
    }
}
