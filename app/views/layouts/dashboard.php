<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> — MoveUp</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800;900&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="<?= View::base('css/dashboard.css') ?>">
        <link rel="stylesheet" href="<?= View::base('css/activites.css') ?>">
</head>
<body>

<?php
$auth_user     = Auth::user();
$prenom_nav    = htmlspecialchars($auth_user['prenom'] ?? 'U');
$photo_nav     = $auth_user['profile_photo'] ?? null;
$current_route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$b             = fn(string $p) => View::base($p);
$is_admin = ($auth_user['role'] ?? 'user') === 'admin';
$is_salle = ($auth_user['role'] ?? 'user') === 'salle';
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo"><span>MU</span></div>


    <nav class="sidebar-nav">
        <?php if ($is_admin): ?>
            <!-- Admin navigation -->
            <a class="nav-item <?= str_contains($current_route, 'admin/dashboard') ? 'active' : '' ?>"
               href="<?= $b('admin/dashboard') ?>" data-tip="Dashboard admin">
                <i class="fas fa-gauge-high"></i>
            </a>
            <a class="nav-item <?= str_contains($current_route, 'admin/contacts') ? 'active' : '' ?>"
               href="<?= $b('admin/contacts') ?>" data-tip="Messages">
                <i class="fas fa-envelope"></i>
            </a>
            <a class="nav-item <?= str_contains($current_route, 'create-user') ? 'active' : '' ?>"
               href="<?= $b('admin/create-user') ?>" data-tip="Créer un compte">
                <i class="fas fa-user-plus"></i>
            </a>
            <div class="nav-divider"></div>
            <a class="nav-item <?= str_ends_with($current_route, 'reviews') ? 'active' : '' ?>"
               href="<?= $b('reviews') ?>" data-tip="Avis clients">
                <i class="fas fa-star"></i>
            </a>
        <?php elseif ($is_salle): ?>
            <!-- Salle navigation -->
            <a class="nav-item <?= str_contains($current_route, 'salle/dashboard') ? 'active' : '' ?>"
               href="<?= $b('salle/dashboard') ?>" data-tip="Mon espace">
                <i class="fas fa-gauge-high"></i>
            </a>
            <a class="nav-item <?= str_ends_with($current_route, 'cours') ? 'active' : '' ?>"
               href="<?= $b('cours') ?>" data-tip="Séances publiées">
                <i class="fas fa-calendar-check"></i>
            </a>
            <a class="nav-item <?= str_contains($current_route, 'cours/publish') ? 'active' : '' ?>"
               href="<?= $b('cours/publish') ?>" data-tip="Publier une séance">
                <i class="fas fa-plus-circle"></i>
            </a>

        <?php else: ?>
            <!-- User navigation -->
            <a class="nav-item <?= str_ends_with($current_route, 'dashboard') ? 'active' : '' ?>"
               href="<?= $b('dashboard') ?>" data-tip="Accueil">
                <i class="fas fa-home"></i>
            </a>
            <a class="nav-item <?= str_ends_with($current_route, 'cours') ? 'active' : '' ?>"
               href="<?= $b('cours') ?>" data-tip="Seances">
                <i class="fas fa-dumbbell"></i>
            </a>
            <a class="nav-item <?= str_ends_with($current_route, 'activites') ? 'active' : '' ?>"
               href="<?= $b('activites') ?>" data-tip="Activités">
                <i class="fas fa-fire"></i>
            </a>
            <a class="nav-item" href="<?= $b('progress') ?>" data-tip="Progression">
                <i class="fas fa-chart-line"></i>
            </a>
            <div class="nav-divider"></div>
            <a class="nav-item <?= str_ends_with($current_route, 'profile') ? 'active' : '' ?>"
               href="<?= $b('profile') ?>" data-tip="Mon profil">
                <i class="fas fa-user-circle"></i>
            </a>
            <a class="nav-item <?= str_ends_with($current_route, 'reviews') ? 'active' : '' ?>"
               href="<?= $b('reviews') ?>" data-tip="Avis clients">
                <i class="fas fa-star"></i>
            </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-bottom">
        <?php if (!$is_admin && !$is_salle): ?>
        <a href="<?= $b('profile') ?>" class="user-pip" id="liveSidebarAvatar" data-tip="Mon profil">
            <?php if ($photo_nav && file_exists(__DIR__ . '/../../../public' . $photo_nav)): ?>
                <img src="<?= $b(ltrim($photo_nav, '/')) ?>?v=<?= time() ?>" alt="Photo">
            <?php else: ?>
                <span><?= strtoupper(substr($prenom_nav, 0, 1)) ?></span>
            <?php endif; ?>
        </a>
        <?php endif; ?>
        <a class="nav-item" href="<?= $b('logout') ?>" data-tip="Déconnexion" style="color:rgba(248,113,113,.5)">
            <i class="fas fa-sign-out-alt"></i>
        </a>

    </div>
</aside>

<?= $content ?>

<script>
document.getElementById('mobileToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});
document.addEventListener('click', e => {
    const sb = document.getElementById('sidebar');
    const toggle = document.getElementById('mobileToggle');
    if (window.innerWidth <= 768 && sb?.classList.contains('open') && !sb.contains(e.target) && !toggle?.contains(e.target)) {
        sb.classList.remove('open');
    }
});
function openModal(id)  { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }
document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});
</script>

</body>
</html>