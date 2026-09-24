<?php
// ── Show ALL errors during development ───────────────────
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('ROOT', dirname(__DIR__));


// ── PHPMailer ─────────────────────────────────────────────  ← ADD THESE
require ROOT . '/vendor/phpmailer/Exception.php';
require ROOT . '/vendor/phpmailer/PHPMailer.php';
require ROOT . '/vendor/phpmailer/SMTP.php';

// ── Autoload core classes ─────────────────────────────────
require ROOT . '/core/Database.php';
require ROOT . '/core/Auth.php';
require ROOT . '/core/View.php';
require ROOT . '/core/Router.php';

// ── Autoload models ───────────────────────────────────────
foreach (glob(ROOT . '/app/models/*.php') as $model) {
    require_once $model;
}

// ── Boot auth — wrapped so a DB error shows clearly ──────
try {
    Auth::boot();
} catch (Throwable $e) {
    die('<div style="font-family:monospace;padding:2rem;background:#1a0505;color:#f87171">
        <strong>Database connection failed</strong><br><br>
        ' . htmlspecialchars($e->getMessage()) . '<br><br>
        <em>Check <code>config/database.php</code> — make sure dbname, user and pass are correct and XAMPP MySQL is running.</em>
    </div>');
}

// ── Routes ───────────────────────────────────────────────
$router = new Router();

$router->get('/',                'HomeController@index');
$router->get('/contact',         'ContactController@show');
$router->post('/contact',        'ContactController@submit');
$router->get('/cours',           'CoursController@index');
$router->get('/cours/publish',   'CoursController@showPublish');
$router->post('/cours/publish',  'CoursController@publish');
$router->post('/cours/delete',   'CoursController@delete');
$router->post('/cours/reserve',  'CoursController@reserve');
$router->post('/cours/cancel',   'CoursController@cancel');

// ── Salle routes ─────────────────────────────────────────
$router->get('/salle/dashboard', 'SalleController@dashboard');

$router->get('/login',           'AuthController@showLogin');
$router->get('/register',        'AuthController@showRegister');
$router->post('/login',          'AuthController@login');
$router->post('/register',       'AuthController@register');
$router->get('/logout',          'AuthController@logout');

// ── Password reset ────────────────────────────────────────
$router->get('/password/forgot',  'PasswordController@showForgot');
$router->post('/password/send',   'PasswordController@sendReset');
$router->get('/password/reset',   'PasswordController@showReset');
$router->post('/password/update', 'PasswordController@updatePassword');

$router->get('/dashboard',       'DashboardController@index');

$router->get('/profile',         'ProfileController@show');
$router->post('/profile',        'ProfileController@update');

$router->post('/nutrition/add',     'NutritionController@addMeal');
$router->post('/nutrition/analyze', 'NutritionController@analyze');
$router->get('/nutrition/history',  'NutritionController@history');

$router->post('/weight/update',  'WeightController@update');
$router->post('/weight/workout', 'WeightController@logWorkout');

$router->post('/water/log',      'WaterController@log');
$router->get('/water/history',   'WaterController@history');

$router->get('/reviews',         'ReviewController@all');
$router->get('/reviews/submit',  'ReviewController@show');
$router->post('/reviews/submit', 'ReviewController@submit');

// ── Admin routes ─────────────────────────────────────────
$router->get('/admin/dashboard',      'AdminController@dashboard');
$router->post('/admin/settings',      'AdminController@updateSettings');
$router->post('/admin/update-role',   'AdminController@updateRole');
$router->post('/admin/toggle-status', 'AdminController@toggleStatus');

$router->get('/admin/create-user',  'AdminController@showCreateUser');
$router->post('/admin/create-user', 'AdminController@createUser');

$router->get('/admin/contacts',  'ContactController@admin');
$router->post('/admin/contacts', 'ContactController@admin');

$router->get('/progression',       'ProgressionController@index');
$router->get('/progress',          'ProgressionController@index');
$router->post('/objectifs/add',           'ObjectifsController@add');
$router->post('/objectifs/delete',        'ObjectifsController@delete');
$router->post('/objectifs/update-progress','ObjectifsController@updateProgress');
// ── Dispatch ──────────────────────────────────────────────
$router->get('/activites',         'ActiviteController@index');
$router->post('/activites/add',    'ActiviteController@add');
$router->post('/activites/log',    'ActiviteController@logExercise');
$router->post('/activites/delete', 'ActiviteController@delete');

$router->get('/reservation', 'CoursController@index');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);