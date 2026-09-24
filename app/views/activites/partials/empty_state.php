<?php
/**
 * Partial : état vide (aucune catégorie sélectionnée)
 * Variables : $library
 */
?>
<div class="empty-state">
  <div class="empty-emoji">🏋️</div>
  <div>
    <div class="empty-title">Choisissez un groupe musculaire</div>
    <div class="empty-sub">Cliquez sur une catégorie dans le menu pour découvrir les exercices guidés.</div>
  </div>
  <div class="pill-row">
    <?php foreach ($library as $key => $cat): ?>
    <a
      href="<?= View::base('activites') ?>?category=<?= $key ?>"
      class="cat-pill"
      style="background:<?= $cat['color'] ?>10;border-color:<?= $cat['color'] ?>22;color:<?= $cat['color'] ?>;"
    >
      <?= $cat['icon'] ?>&nbsp;<?= htmlspecialchars($cat['label']) ?>
    </a>
    <?php endforeach; ?>
  </div>
</div>
