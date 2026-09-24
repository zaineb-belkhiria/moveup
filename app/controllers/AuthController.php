<?php
/**
 * app/controllers/AuthController.php
 */
class AuthController
{
    public function showLogin(): void
    {
        Auth::redirectIfLoggedIn();
        View::render('auth/login', ['tab' => 'login', 'flash' => null], 'public');
    }

    public function showRegister(): void
    {
        Auth::redirectIfLoggedIn();
        View::render('auth/login', ['tab' => 'register', 'flash' => null], 'public');
    }

   public function login(): void
{
$email    = trim($_POST['email'] ?? '');
        $password = $_POST['mot_de_passe'] ?? '';
        $remember = !empty($_POST['remember']);
        $redirect = trim($_POST['redirect'] ?? '');

        $error = Auth::attempt($email, $password, $remember, $redirect);

    if ($error) {
        View::render('auth/login', [
            'tab'   => 'login',
            'flash' => ['type' => 'error', 'msg' => $error],
        ], 'public');
    }
}

    public function register(): void
    {
        $db   = Database::get();
        $data = [
            'nom'      => trim($_POST['nom']      ?? ''),
            'prenom'   => trim($_POST['prenom']   ?? ''),
            'email'    => trim($_POST['email']    ?? ''),
            'password' => $_POST['mot_de_passe']  ?? '',
            'genre'    => $_POST['genre']         ?? 'homme',
            'age'      => (int)($_POST['age']     ?? 25),
            'poids'    => (float)($_POST['poids'] ?? 70),
            'taille'   => (float)($_POST['taille']?? 175),
            'niveau'   => $_POST['niveau']        ?? 'intermediaire',
            'objectif' => $_POST['objectif']      ?? 'maintien',
        ];

        if (!$data['nom'] || !$data['prenom'] || !$data['email'] || !$data['password']) {
            View::render('auth/login', ['tab' => 'register', 'flash' => ['type'=>'error','msg'=>'Veuillez remplir tous les champs obligatoires.'], 'old' => $data], 'public');
            return;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            View::render('auth/login', ['tab' => 'register', 'flash' => ['type'=>'error','msg'=>'Email invalide.'], 'old' => $data], 'public');
            return;
        }

        try {
            $userModel = new User($db);
            $userId    = $userModel->create($data);
            $user      = $userModel->findById($userId);
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            View::redirect('dashboard');
        } catch (mysqli_sql_exception $e) {
            // BUG FIX: was catching PDOException — now catches mysqli_sql_exception
            View::render('auth/login', ['tab' => 'register', 'flash' => ['type'=>'error','msg'=>'Cet email est déjà utilisé.'], 'old' => $data], 'public');
        }
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
