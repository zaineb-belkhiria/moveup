<?php
/**
 * Partial : section hero
 * Variables : $currentCat, $currentData
 */
?>
<div class="acts-hero">
  <div class="acts-hero-line"></div>
  <div class="acts-hero-watermark">
    <?= $currentCat ? strtoupper(htmlspecialchars($currentData['label'])) : 'ACTIVITÉS' ?>
  </div>

  <div class="acts-hero-inner">
    <div class="acts-hero-tag"><i class="fas fa-bolt"></i>&nbsp; Programme guidé</div>
    <div class="acts-hero-title">
      <?php if ($currentCat): ?>
        <span style="color:<?= $currentData['color'] ?>"><?= $currentData['icon'] ?></span>
        <?= htmlspecialchars($currentData['label']) ?>
      <?php else: ?>
        Bibliothèque <span style="color:var(--lime)">d'exercices</span>
      <?php endif; ?>
    </div>
    <div class="acts-hero-sub">
      <?php if ($currentCat): ?>
        <?= htmlspecialchars($currentData['muscles']) ?>
      <?php else: ?>
        Choisissez un groupe musculaire — exercices domicile &amp; salle avec séries et répétitions guidées.
      <?php endif; ?>
    </div>
  </div>

  <?php if ($currentCat): ?>
  <div class="acts-hero-stats">
    <div class="hero-stat-box">
      <div class="hero-stat-val"><?= count($currentData['home']) ?></div>
      <div class="hero-stat-lbl">🏠 Domicile</div>
    </div>
    <div class="hero-stat-box">
      <div class="hero-stat-val"><?= count($currentData['gym']) ?></div>
      <div class="hero-stat-lbl">🏋️ Salle</div>
    </div>
    <div class="hero-stat-box">
      <div class="hero-stat-val"><?= count($currentData['home']) + count($currentData['gym']) ?></div>
      <div class="hero-stat-lbl">Total</div>
    </div>
  </div>
  <?php endif; ?>
</div>
