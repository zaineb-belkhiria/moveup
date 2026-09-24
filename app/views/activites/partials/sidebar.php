<?php
/**
 * Partial : barre latérale — filtres + conseils + lien planning
 * Variables : $library, $currentCat
 */
?>
<aside class="acts-sidebar">

  <!-- Filtre catégories -->
  <div class="filter-box">
    <div class="filter-box-head">
      <i class="fas fa-filter"></i> Groupe musculaire
    </div>
    <?php foreach ($library as $key => $cat):
      $totalEx = count($cat['home']) + count($cat['gym']);
    ?>
    <a
      href="<?= View::base('activites') ?>?category=<?= $key ?>"
      class="cat-link <?= $currentCat === $key ? 'active' : '' ?>"
    >
      <span class="cat-bar" style="background:<?= $cat['color'] ?>"></span>
      <span class="cat-icon" style="background:<?= $cat['color'] ?>12;border-color:<?= $cat['color'] ?>22;">
        <?= $cat['icon'] ?>
      </span>
      <span class="cat-label"><?= htmlspecialchars($cat['label']) ?></span>
      <span class="cat-count"><?= $totalEx ?></span>
      <span class="cat-arrow"><i class="fas fa-chevron-right"></i></span>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Conseil -->
  <div class="advice-box">
    <div class="advice-box-head">
      <i class="fas fa-lightbulb"></i> Conseil
    </div>
    <p>Les exercices sont organisés par <strong>lieu de pratique</strong>.
       Alternez onglets <em>Domicile</em> et <em>Salle</em> selon votre disponibilité.</p>
    <p>Respectez les <strong style="color:var(--lime)">séries &amp; répétitions</strong>
       imposées — elles font partie du programme guidé.</p>
  </div>

  <!-- Lien vers le Planning -->
  <div class="planning-cta">
    <div class="planning-cta-title"><i class="fas fa-calendar-check"></i> Mon Planning</div>
    <p>Ces exercices sont liés à votre planning hebdomadaire.
       Cliquez ci-dessous pour voir vos séances.</p>
    <a href="<?= View::base('dashboard') ?>" class="btn-cta-planning">
      <i class="fas fa-arrow-right"></i> Voir mon planning
    </a>
  </div>

</aside>
