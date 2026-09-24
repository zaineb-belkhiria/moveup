<?php $pageTitle = 'Contact'; ?>

<section class="page-hero">
  <div class="page-hero-inner">
    <span class="section-tag">Contact</span>
    <h1 class="page-hero-title">Contactez-<span class="hl">Nous</span></h1>
    <p class="page-hero-sub">Une question, un besoin ou un partenariat ? Notre équipe à Sousse vous répond sous 24h.</p>
    <div class="location-badge">Sousse, Tunisie</div>
  </div>
</section>

<div class="page-content">

  <?php if (!empty($flash)): ?>
  <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['msg']) ?></div>
  <?php endif; ?>

  <div class="contact-grid reveal">

    <!-- INFO PANEL -->
    <div class="contact-info-panel">
      <div class="contact-panel-title">Nos <span class="hl">coordonnées</span></div>
      <div class="contact-detail"><div class="contact-detail-icon">Mail</div><div><strong>Email</strong><p>moveupsupport@gmail.com</p></div></div>
      <div class="contact-detail"><div class="contact-detail-icon">Tel</div><div><strong>Téléphone</strong><p>+216 56 563 588</p><p>Lun–Ven : 09h – 18h</p></div></div>
      <div class="social-links">
        <a href="#" class="social-link">Instagram</a>
        <a href="#" class="social-link">Facebook</a>
        <a href="#" class="social-link">YouTube</a>
        <a href="#" class="social-link">TikTok</a>
      </div>

    </div>

    <!-- FORM PANEL -->
    <div class="contact-form-panel">
      <div class="contact-panel-title">Envoyez-nous un <span class="hl">message</span></div>
      <form method="POST" action="<?= View::base('contact') ?>" novalidate>
        <div class="form-row">
          <div class="form-group">
            <label>Nom complet <span class="req">*</span></label>
            <input type="text" name="nom" value="<?= htmlspecialchars($old['nom']??'') ?>" placeholder="Votre nom" required/>
          </div>
          <div class="form-group">
            <label>Email <span class="req">*</span></label>
            <input type="email" name="email" value="<?= htmlspecialchars($old['email']??'') ?>" placeholder="votre@email.com" required/>
          </div>
        </div>
        <div class="form-group">
          <label>Téléphone</label>
          <input type="tel" name="telephone" value="<?= htmlspecialchars($old['telephone']??'') ?>" placeholder="+216 XX XXX XXX"/>
        </div>
        <div class="form-group">
          <label>Sujet <span class="req">*</span></label>
          <select name="sujet" required>
            <option value="">Sélectionner un sujet…</option>
            <option value="information"  <?= (($old['sujet']??'')==='information') ?'selected':'' ?>>Demande d'information</option>
            <option value="programme"    <?= (($old['sujet']??'')==='programme')   ?'selected':'' ?>>Question sur un programme</option>
            <option value="reservation"  <?= (($old['sujet']??'')==='reservation') ?'selected':'' ?>>Problème de réservation</option>
            <option value="partenariat"  <?= (($old['sujet']??'')==='partenariat') ?'selected':'' ?>>Partenariat salle</option>
            <option value="autre"        <?= (($old['sujet']??'')==='autre')       ?'selected':'' ?>>Autre</option>
          </select>
        </div>
        <div class="form-group">
          <label>Message <span class="req">*</span></label>
          <textarea name="message" rows="6" placeholder="Décrivez votre demande…" required><?= htmlspecialchars($old['message']??'') ?></textarea>
        </div>
        <button type="submit" class="btn-primary" style="width:100%;border-radius:14px;justify-content:center;">Envoyer le message</button>
      </form>

      <div style="display:grid;grid-template-columns:1fr;gap:12px;margin-top:2rem;">
        <div style="background:rgba(46,125,104,0.08);border:1px solid var(--border);border-radius:16px;padding:1.2rem;text-align:center;">
          <div style="font-family:var(--font-head);font-size:1.8rem;font-weight:900;background:linear-gradient(135deg,var(--gold),var(--gold-dark));-webkit-background-clip:text;background-clip:text;color:transparent;">&lt; 24h</div>
          <div style="font-size:0.72rem;color:var(--muted);margin-top:4px;text-transform:uppercase;letter-spacing:0.14em;">Temps de réponse</div>
        </div>
      </div>
    </div>

  </div>
</div>

<section class="cta-section" style="padding:5rem 3rem;">
  <div class="cta-bg-text">CONTACT</div>
  <h2 class="cta-title reveal">On est <span class="hl">là pour toi</span></h2>
  <p class="cta-sub reveal">Pas encore inscrit ? Rejoins plus de 12 000 athlètes et commence ton programme dès aujourd'hui.</p>
  <div class="cta-actions reveal">
    <a href="<?= View::base('login') ?>" class="btn-primary">S'inscrire gratuitement</a>
    <a href="<?= View::base('cours') ?>" class="btn-secondary">Voir les séances</a>
  </div>
</section>