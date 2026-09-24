<?php
/**
 * app/views/admin/create_user.php
 * Form to create an admin or salle account (no fitness fields required).
 */
$pageTitle = 'Créer un compte admin / salle';
$old = $old ?? [];
?>

<div style="max-width:520px; margin:2rem auto; padding:0 1rem;">

  <div style="margin-bottom:1.5rem;">
    <a href="<?= View::base('admin/dashboard') ?>?tab=users"
       style="font-size:13px; color:#6b7280; text-decoration:none;">
      ← Retour au dashboard
    </a>
    <h1 style="font-size:1.4rem; font-weight:600; margin:.5rem 0 0; color:#f0fdf4;">
      Créer un compte admin / salle
    </h1>
    <p style="font-size:13px; color:#9ca3af; margin:.25rem 0 0;">
      Les champs fitness (âge, poids, taille) ne sont pas requis pour ces comptes.
    </p>
  </div>

  <?php if (!empty($flash)): ?>
    <div style="padding:.75rem 1rem; border-radius:8px; margin-bottom:1.25rem; font-size:13px;
      background:<?= $flash['type']==='success' ? 'rgba(200,240,74,.15)' : 'rgba(248,113,113,.15)' ?>;
      color:<?= $flash['type']==='success' ? '#C8F04A' : '#f87171' ?>;
      border:1px solid <?= $flash['type']==='success' ? '#C8F04A' : '#f87171' ?>;">
      <?= htmlspecialchars($flash['msg']) ?>
    </div>
  <?php endif; ?>

  <div style="background:#111a14; border:1px solid #2a3328; border-radius:12px; padding:1.5rem;">

    <form method="POST" action="<?= View::base('admin/create-user') ?>">

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
        <div>
          <label style="display:block; font-size:12px; color:#9ca3af; margin-bottom:4px;">Prénom *</label>
          <input type="text" name="prenom" required
                 value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                 style="width:100%; background:#0d1310; border:1px solid #2a3328; border-radius:8px;
                        padding:.5rem .75rem; color:#f0fdf4; font-size:14px; box-sizing:border-box;">
        </div>
        <div>
          <label style="display:block; font-size:12px; color:#9ca3af; margin-bottom:4px;">Nom *</label>
          <input type="text" name="nom" required
                 value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                 style="width:100%; background:#0d1310; border:1px solid #2a3328; border-radius:8px;
                        padding:.5rem .75rem; color:#f0fdf4; font-size:14px; box-sizing:border-box;">
        </div>
      </div>

      <div style="margin-bottom:1rem;">
        <label style="display:block; font-size:12px; color:#9ca3af; margin-bottom:4px;">Email *</label>
        <input type="email" name="email" required
               value="<?= htmlspecialchars($old['email'] ?? '') ?>"
               style="width:100%; background:#0d1310; border:1px solid #2a3328; border-radius:8px;
                      padding:.5rem .75rem; color:#f0fdf4; font-size:14px; box-sizing:border-box;">
      </div>

      <div style="margin-bottom:1rem;">
        <label style="display:block; font-size:12px; color:#9ca3af; margin-bottom:4px;">Mot de passe *</label>
        <input type="password" name="mot_de_passe" required minlength="8"
               style="width:100%; background:#0d1310; border:1px solid #2a3328; border-radius:8px;
                      padding:.5rem .75rem; color:#f0fdf4; font-size:14px; box-sizing:border-box;">
        <p style="font-size:11px; color:#6b7280; margin:.3rem 0 0;">Minimum 8 caractères</p>
      </div>

      <div style="margin-bottom:1.5rem;">
        <label style="display:block; font-size:12px; color:#9ca3af; margin-bottom:4px;">Rôle *</label>
        <select name="role"
                style="width:100%; background:#0d1310; border:1px solid #2a3328; border-radius:8px;
                       padding:.5rem .75rem; color:#f0fdf4; font-size:14px; box-sizing:border-box;">
          <option value="admin" <?= ($old['role'] ?? 'admin') === 'admin' ? 'selected' : '' ?>>Admin</option>
          <option value="salle" <?= ($old['role'] ?? '') === 'coach' ? 'selected' : '' ?>>Salle</option>
        </select>
      </div>

      <button type="submit"
              style="width:100%; background:#C8F04A; color:#0d1310; border:none; border-radius:8px;
                     padding:.65rem; font-size:14px; font-weight:600; cursor:pointer;">
        Créer le compte
      </button>

    </form>
  </div>
</div>
