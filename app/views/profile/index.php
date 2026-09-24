<?php $pageTitle = 'Mon Profil'; ?>

<main class="profile-main">
<div class="profile-inner">

<div style="margin-bottom:1.8rem;display:flex;justify-content:space-between;align-items:center;">
  <div><div class="page-eyebrow">Mon compte</div><div class="page-title">Mon Profil</div></div>
  <button class="mobile-toggle" id="mobileToggle"><i class="fas fa-bars"></i></button>
</div>

<?php if(!empty($flash)): ?>
<div class="flash flash-<?= $flash['type'] === 'success' ? 'ok' : 'err' ?>"><?= htmlspecialchars($flash['msg']) ?></div>
<?php endif; ?>

<!-- HERO -->
<div class="profile-hero">
  <div class="avatar-wrap">
    <div class="avatar" id="avatarEl">
      <?php $photo = $user['profile_photo'] ?? null; ?>
      <?php if($photo && file_exists(__DIR__ . '/../../../public' . $photo)): ?>
       <img src="<?= View::base(ltrim($photo, '/')) ?>?v=<?= time() ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
      <?php else: ?>
        <?= strtoupper(substr($user['prenom']??'U',0,1)) ?>
      <?php endif; ?>
    </div>
    <label class="avatar-cam" for="photoInput"><i class="fas fa-camera"></i></label>
  </div>
  <div class="hero-info">
    <div class="hero-name"><?= htmlspecialchars($user['prenom']??'') ?> <?= htmlspecialchars($user['nom']??'') ?></div>
    <div class="hero-email"><?= htmlspecialchars($user['email']??'') ?></div>
    <div class="hero-stats">
      <div class="hero-stat"><span class="hero-stat-num"><?= $total_workouts ?></span><span class="hero-stat-lbl">Séances</span></div>
      <div class="hero-stat"><span class="hero-stat-num"><?= $week_workouts ?></span><span class="hero-stat-lbl">7 jours</span></div>
      <div class="hero-stat"><span class="hero-stat-num"><?= $bmi ?></span><span class="hero-stat-lbl"><?= $bmi_label ?></span></div>
      <div class="hero-stat"><span class="hero-stat-num"><?= $score ?></span><span class="hero-stat-lbl">Score</span></div>
    </div>
    <div style="margin-top:1rem;opacity:.8;font-size:.92rem"><?= $smart_tip ?></div>
  </div>
</div>

<form method="POST" action="<?= View::base('profile') ?>" enctype="multipart/form-data">
  <input type="file" name="profile_photo" id="photoInput" accept="image/*" style="display:none" onchange="previewPhoto(this)">

  <div class="section">
    <div class="section-head"><i class="fas fa-user"></i> Informations personnelles</div>
    <div class="section-body">
      <div class="form-grid">
        <div class="field"><label>Prénom</label><input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']??'') ?>" required></div>
        <div class="field"><label>Nom</label><input type="text" name="nom" value="<?= htmlspecialchars($user['nom']??'') ?>" required></div>
        <div class="field"><label>Email</label><input type="email" value="<?= htmlspecialchars($user['email']??'') ?>" disabled></div>
        <div class="field"><label>Âge</label><input type="number" name="age" value="<?= $age ?>" min="14" max="100"></div>
      </div>
    </div>
  </div>

  <div class="section">
    <div class="section-head"><i class="fas fa-heartbeat"></i> Statistiques physiques</div>
    <div class="section-body">
      <div class="form-grid">
        <div class="field"><label>Poids (kg)</label><input type="number" step="0.1" name="poids" value="<?= $poids ?>"></div>
        <div class="field"><label>Taille (cm)</label><input type="number" name="taille" value="<?= $taille ?>"></div>
      </div>
    </div>
  </div>

  <div class="section">
    <div class="section-head"><i class="fas fa-bullseye"></i> Objectifs fitness</div>
    <div class="section-body">
      <div class="form-grid">
        <div class="field">
          <label>Objectif</label>
          <select name="objectif">
            <option value="perte"   <?= $objectif==='perte'   ?'selected':'' ?>>Perte</option>
            <option value="maintien"<?= $objectif==='maintien'?'selected':'' ?>>Maintien</option>
            <option value="masse"   <?= $objectif==='masse'   ?'selected':'' ?>>Masse</option>
          </select>
        </div>
        <div class="field">
          <label>Niveau</label>
          <select name="niveau">
            <option value="debutant"     <?= $niveau==='debutant'     ?'selected':'' ?>>Débutant</option>
            <option value="intermediaire"<?= $niveau==='intermediaire'?'selected':'' ?>>Intermédiaire</option>
            <option value="avance"       <?= $niveau==='avance'       ?'selected':'' ?>>Avancé</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <button type="submit" class="save-btn"><i class="fas fa-check"></i> Enregistrer les modifications</button>
</form>

</div>
</main>

<script>
function previewPhoto(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const sidebar = document.getElementById('liveSidebarAvatar');
      if (sidebar) sidebar.innerHTML = '<img src="' + e.target.result + '" alt="Photo">';
      const avatar = document.getElementById('avatarEl');
      if (avatar) avatar.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
