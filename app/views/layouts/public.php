<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#080d0b">
    <title><?= $pageTitle ?? 'MoveUp' ?> — MoveUp Fitness</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= View::base('css/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<?php
$nav_user     = Auth::user();
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$b            = fn(string $p) => View::base($p); // shorthand
?>

<nav class="glass-nav">
    <a class="logo" href="<?= $b('') ?>"><span>MOVE</span>UP</a>

    <div class="menu-toggle" id="mobileMenuToggle"><span></span><span></span><span></span></div>

    <ul class="nav-links" id="navLinks">
        <?php if ($nav_user): ?>
            <li><a href="<?= $b('dashboard') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li>
                <div class="nav-user-pill">
                    <div class="nav-avatar"><?= strtoupper(substr($nav_user['prenom'] ?? 'U', 0, 1) . substr($nav_user['nom'] ?? '', 0, 1)) ?></div>
                    <span><?= htmlspecialchars($nav_user['prenom'] ?? 'Utilisateur') ?></span>
                    <a href="<?= $b('logout') ?>" class="nav-logout"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </li>
        <?php else: ?>
            <li><a href="<?= $b('') ?>">Accueil</a></li>
            <li><a href="<?= $b('cours') ?>">Cours</a></li>
            <li><a href="<?= $b('contact') ?>">Contact</a></li>
            <li><a href="<?= $b('login') ?>" class="nav-cta">Connexion</a></li>
        <?php endif; ?>
    </ul>
</nav>

<script>
document.getElementById('mobileMenuToggle')?.addEventListener('click', function () {
    document.getElementById('navLinks').classList.toggle('open');
});
</script>

<?= $content ?>

<footer>
    <a class="logo" href="<?= $b('') ?>" style="font-size:1.3rem;">Move<span>Up</span></a>
    
    <div class="footer-copy">© <?= date('Y') ?> MoveUp — Sousse, Tunisie. Tous droits réservés.</div>
</footer>

<script>
(function () {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) { entry.target.classList.add('in-view'); observer.unobserve(entry.target); }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
})();
</script>

</body>
</html>
