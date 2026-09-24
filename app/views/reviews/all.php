<?php $pageTitle = 'Tous les avis'; ?>

<main class="main">

<section class="page-hero" style="padding:9rem 3rem 3rem;">
    <div class="page-hero-inner">
        <span class="section-tag">Communauté</span>
        <h1 class="page-hero-title">Avis <span class="hl">clients</span></h1>
        <p class="page-hero-sub">Ce que notre communauté pense de MoveUp.</p>
    </div>
</section>

<div class="page-content">

    <?php if ($success): ?>
    <div class="flash flash-success" style="margin-bottom:1.5rem;padding:1rem 1.5rem;border-radius:12px;background:rgba(200,240,74,.1);border:1px solid rgba(200,240,74,.3);color:#C8F04A;">
        Merci ! Ton avis a été publié.
    </div>
    <?php endif; ?>

    <?php if (empty($reviews)): ?>
    <div style="text-align:center;padding:5rem 2rem;">
        <div style="font-size:3rem;margin-bottom:1rem;">💬</div>
        <div style="font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;margin-bottom:.5rem;">Aucun avis pour l'instant</div>
        <p style="color:var(--muted);margin-bottom:2rem;">Sois le premier à partager ton expérience.</p>
        <a href="<?= View::base('reviews/submit') ?>" class="btn-primary">Laisser un avis</a>
    </div>
    <?php else: ?>

    <div style="display:flex;justify-content:flex-end;margin-bottom:1.5rem;">
        <a href="<?= View::base('reviews/submit') ?>" class="btn-primary">+ Laisser un avis</a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.25rem;">
        <?php foreach($reviews as $a): ?>
        <div style="background:var(--card2);border:1px solid var(--border);border-radius:20px;padding:1.5rem;display:flex;flex-direction:column;gap:.75rem;">

            <!-- Header: avatar + name + stars -->
            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--green-dark));display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:.9rem;font-weight:900;color:#fff;flex-shrink:0;">
                        <?= strtoupper(substr($a['prenom'] ?? '?', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:.9rem;color:var(--text);"><?= htmlspecialchars($a['prenom'] ?? '') ?></div>
                        <div style="font-size:.65rem;color:var(--muted2);"><?= date('d M Y', strtotime($a['created_at'])) ?></div>
                    </div>
                </div>
                <div style="color:var(--gold);letter-spacing:2px;font-size:1rem;flex-shrink:0;">
                    <?= str_repeat('★', (int)$a['note']) ?><span style="opacity:.25;"><?= str_repeat('★', 5 - (int)$a['note']) ?></span>
                </div>
            </div>

            <!-- Comment -->
            <p style="font-size:.85rem;color:var(--muted);line-height:1.7;margin:0;"><?= htmlspecialchars($a['commentaire']) ?></p>

        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

</main>
