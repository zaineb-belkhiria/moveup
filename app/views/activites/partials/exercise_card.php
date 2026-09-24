<?php
/**
 * Partial : carte d'exercice avec image
 * Variables attendues : $ex (array), $ec (string couleur), $diffColors (array), $delay (int), $categoryKey (string)
 */
$dc = $diffColors[$ex['diff']] ?? '#8DA88F';
$imageUrl = ExerciseModel::getExerciseImageUrl($ex['nom'], $categoryKey);
?>
<div class="ex-card" style="animation-delay:<?= $delay ?>ms;" data-cat-color="<?= htmlspecialchars($ec) ?>">

  <!-- Image -->
  <div class="ex-card-image">
    <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>"
         alt="<?= htmlspecialchars($ex['nom']) ?>"
         loading="lazy"
         onerror="this.onerror=null;this.src='<?= View::base('assets/images/exercises/squat.jpg') ?>';">
  </div>

  <div class="ex-card-top">
    <div class="ex-card-ico" style="background:<?= $ec ?>12;border:1px solid <?= $ec ?>22;">
      <?= $ex['icon'] ?>
    </div>
    <div>
      <div class="ex-card-name"><?= htmlspecialchars($ex['nom']) ?></div>
      <div class="ex-card-mus"><?= htmlspecialchars($ex['muscles']) ?></div>
    </div>
  </div>

  <div class="ex-card-body">
    <div class="ex-sets-box">
      <i class="fas fa-repeat" style="color:<?= $ec ?>"></i>
      <div class="ex-sets-inner">
        <span class="ex-sets-label">Séries &amp; répétitions</span>
        <div class="ex-sets-val" style="color:<?= $ec ?>;"><?= htmlspecialchars($ex['sets']) ?></div>
      </div>
    </div>
    <div class="ex-tips" style="border-color:<?= $ec ?>;">
      <i class="fas fa-circle-info" style="color:<?= $ec ?>;"></i><?= htmlspecialchars($ex['tips']) ?>
    </div>
  </div>

  <div class="ex-card-foot">
    <span class="diff-badge"
          style="background:<?= $dc ?>18;color:<?= $dc ?>;border-color:<?= $dc ?>28;">
      <?= htmlspecialchars($ex['diff']) ?>
    </span>
    <span class="ex-time"><i class="fas fa-clock"></i><?= htmlspecialchars($ex['time']) ?></span>
  </div>

  <button class="ex-log-btn"
          data-nom="<?= htmlspecialchars($ex['nom'], ENT_QUOTES) ?>"
          data-category="<?= htmlspecialchars($categoryKey, ENT_QUOTES) ?>"
          data-duration="<?= (int)filter_var($ex['time'], FILTER_SANITIZE_NUMBER_INT) ?>"
          style="border-color:<?= $ec ?>44;background:<?= $ec ?>12;color:<?= $ec ?>;">
    <i class="fas fa-check-circle"></i> J'ai fait cet exercice
  </button>

</div>