<?php $pageTitle = 'Accueil'; ?>

<!-- ══ HERO ══ -->
<section class="landing-hero">
  <div class="hero-bg-image"><img src="<?= View::base('images/leftimg.jpg') ?>" alt="Athlete MoveUp"/></div>
  <div class="hero-glow"></div>
  <div class="hero-container">
    <div class="hero-left-spacer"></div>
    <div class="hero-right">
      <div class="landing-eyebrow anim-1">Fitness &bull; Santé &bull; Performance</div>
      <h1 class="landing-title anim-2">
        <span class="lt-normal">MOVE</span>
        <span class="lt-gold">YOUR BODY</span>
        <span class="lt-outline">TRANSFORM</span>
      </h1>
      <p class="landing-sub anim-3">MoveUp te propulse vers tes objectifs : perte de poids, prise de masse ou maintien de forme. Des programmes élites conçus par des experts, adaptés à ton niveau.</p>
      <div class="landing-actions anim-3">
        <a href="<?= View::base('cours') ?>" class="btn-primary">Voir les séances</a>
        
      </div>
      <div class="hero-kpi-strip anim-4">
        <div class="hero-kpi"><div class="hero-kpi-val">12K<span style="font-size:1.2rem;color:var(--green-light)">+</span></div><div class="hero-kpi-lbl">Athlètes actifs</div></div>
        <div class="hero-kpi"><div class="hero-kpi-val">48</div><div class="hero-kpi-lbl">Programmes</div></div>
        <div class="hero-kpi"><div class="hero-kpi-val">94<span style="font-size:1.2rem;color:var(--green-light)">%</span></div><div class="hero-kpi-lbl">Complétion</div></div>
        <div class="hero-kpi"><div class="hero-kpi-val">4.9<span style="font-size:1.2rem;color:var(--green-light)">★</span></div><div class="hero-kpi-lbl">Note moyenne</div></div>
      </div>
    </div>
  </div>
</section>

<!-- ══ TICKER ══ -->
<div class="ticker">
  <div class="ticker-inner">
    <?php $items = ['Power Forge','8 Semaines','Ignite HIIT','Sans matériel','Flex & Flow','Mobilité totale','12K+ Athlètes','94% de réussite','Sousse, Tunisie','Rejoins-nous']; ?>
    <?php foreach(array_merge($items,$items) as $i => $it): ?>
      <span class="tick-item <?= $i%3===0?'gold':'' ?>"><?= $it ?><span class="tick-dot"></span></span>
    <?php endforeach; ?>
  </div>
</div>

<!-- ══ STATS STRIP ══ -->
<div class="stats-strip">
  <div class="stat-item reveal"><div class="stat-num">12<span class="g">K+</span></div><div class="stat-desc">Athlètes actifs</div></div>
  <div class="stat-item reveal"><div class="stat-num">48</div><div class="stat-desc">Programmes experts</div></div>
  <div class="stat-item reveal"><div class="stat-num">94<span class="g">%</span></div><div class="stat-desc">Taux de complétion</div></div>
  <div class="stat-item reveal"><div class="stat-num">4.9<span class="g">★</span></div><div class="stat-desc">Note moyenne</div></div>
</div>

<!-- ══ ABOUT ══ -->
<section class="landing-about">
  <div class="section-header">
    <div><span class="section-tag">À propos</span><h2 class="section-title">Qui sommes-<span class="hl">nous</span>&nbsp;?</h2></div>
  </div>
  <div class="about-grid">
    <div class="reveal">
      <p class="about-text">MoveUp est une plateforme fitness tunisienne conçue pour aider chaque athlète à atteindre ses objectifs, qu'il s'agisse de perdre du poids, de prendre de la masse ou de maintenir sa forme.</p>
      <div class="alex-quote">« Chaque rep compte. Chaque séance te rapproche de ta meilleure version. »<span>— L'équipe MoveUp, Sousse</span></div>
      <a href="<?= View::base('cours') ?>" class="btn-primary">Découvrir les programmes</a>
    </div>
    <div class="feature-list reveal">
      <div class="feature-item"><div class="feature-icon">💪</div><div><div class="feature-title">Programmes personnalisés</div><div class="feature-desc">Adaptés à ton objectif, ton niveau et ton rythme de vie.</div></div></div>
      <div class="feature-item"><div class="feature-icon">📊</div><div><div class="feature-title">Suivi de progression</div><div class="feature-desc">Suivi du poids, des séances et des objectifs en temps réel.</div></div></div>
      <div class="feature-item"><div class="feature-icon">🏋️</div><div><div class="feature-title">Salles partenaires à Sousse</div><div class="feature-desc">Accès à nos salles partenaires dans toute la région.</div></div></div>
    </div>
  </div>
</section>

<!-- ══ HOW IT WORKS ══ -->
<section class="landing-how">
  <div class="section-header">
    <div><span class="section-tag">Comment ça marche</span><h2 class="section-title">4 étapes vers ton <span class="hl">objectif</span></h2></div>
  </div>
  <div class="steps-grid">
    <div class="step-card reveal"><span class="step-num">01</span><span class="step-icon">👤</span><div class="step-title">Crée ton profil</div><p class="step-desc">Inscris-toi en 2 minutes. Renseigne ton niveau, ton objectif et tes mensurations.</p><div class="step-connector"></div></div>
    <div class="step-card reveal"><span class="step-num">02</span><span class="step-icon">🗂️</span><div class="step-title">Choisis ton programme</div><p class="step-desc">Perte de poids, prise de masse ou maintien — sélectionne le programme fait pour toi.</p><div class="step-connector"></div></div>
    <div class="step-card reveal"><span class="step-num">03</span><span class="step-icon">📅</span><div class="step-title">Réserve tes séances</div><p class="step-desc">Choisis ta salle partenaire et réserve les créneaux qui te conviennent.</p><div class="step-connector"></div></div>
    <div class="step-card reveal"><span class="step-num">04</span><span class="step-icon">🚀</span><div class="step-title">Transforme-toi</div><p class="step-desc">Entraîne-toi, suis tes progrès et atteins tes objectifs avec MoveUp.</p></div>
  </div>
</section>

<!-- ══ PROGRAMMES ══ -->
<section class="programs-section">
  <div class="section-header">
    <div><span class="section-tag">Programmes</span><h2 class="section-title">Choisis ton <span class="hl">plan de bataille</span></h2></div>
  </div>
  <div class="programs-grid">
    <?php if (!empty($programmes)): ?>
      <?php foreach ($programmes as $i => $p): ?>
      <div class="program-card <?= $i===0?'featured':'' ?> reveal">
        <div class="p-tag <?= $p['type'] ?>"><?= ucfirst($p['type']) ?></div>
        <div class="p-num">0<?= $i+1 ?></div>
        <div class="p-title"><?= htmlspecialchars($p['nom']) ?></div>
        <p class="p-desc"><?= htmlspecialchars(mb_strimwidth($p['description']??'',0,120,'…')) ?></p>
        <div class="p-meta">
          <div><div class="pm-lbl">Durée</div><div class="pm-val"><?= $p['duree_semaines'] ?> semaines</div></div>
          <div><div class="pm-lbl">Séances/sem.</div><div class="pm-val"><?= $p['seances_par_semaine'] ?>×</div></div>
          <div><div class="pm-lbl">Niveau</div><div class="pm-val"><?= ucfirst($p['niveau_requis']) ?></div></div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <?php foreach([['Power Forge','masse','8','5','Avancé','Développe une force brute avec des cycles de surcharge progressive.'],['Ignite HIIT','perte','6','4','Intermédiaire','Brûle les graisses avec des intervalles à haute intensité.'],['Flex & Flow','maintien','4','3','Tous niveaux','Libère ta mobilité et préviens les blessures.']] as $i=>$p): ?>
      <div class="program-card <?= $i===0?'featured':'' ?> reveal">
        <div class="p-tag <?= $p[1] ?>"><?= ucfirst($p[1]) ?></div>
        <div class="p-num">0<?= $i+1 ?></div>
        <div class="p-title"><?= $p[0] ?></div>
        <p class="p-desc"><?= $p[5] ?></p>
        <div class="p-meta">
          <div><div class="pm-lbl">Durée</div><div class="pm-val"><?= $p[2] ?> sem.</div></div>
          <div><div class="pm-lbl">Séances/sem.</div><div class="pm-val"><?= $p[3] ?>×</div></div>
          <div><div class="pm-lbl">Niveau</div><div class="pm-val"><?= $p[4] ?></div></div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- ══ AVIS ══ -->
<?php if (!empty($avis)): ?>
<section class="section-avis">
  <div class="section-header">
    <div><span class="section-tag">Avis clients</span><h2 class="section-title">Ce que dit la <span class="hl">communauté</span></h2></div>
    <a href="<?= View::base('reviews/submit') ?>" class="view-all">Laisser un avis →</a>
  </div>
  <div class="reviews-grid">
    <?php foreach($avis as $a): ?>
    <div class="review-card reveal">
      <div class="review-header">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--green-dark));display:flex;align-items:center;justify-content:center;font-weight:900;color:#fff;"><?= strtoupper(substr($a['prenom'],0,1)) ?></div>
          <strong><?= htmlspecialchars($a['prenom']) ?></strong>
        </div>
        <span style="color:var(--gold);"><?= str_repeat('★',(int)$a['note']) ?><span style="color:var(--border-solid);"><?= str_repeat('★',5-(int)$a['note']) ?></span></span>
      </div>
      <p style="font-size:.85rem;color:var(--muted);line-height:1.7;margin-top:.8rem;"><?= htmlspecialchars($a['commentaire']) ?></p>
      <small style="color:var(--muted2);font-size:.68rem;margin-top:.8rem;display:block;"><?= date('d M Y',strtotime($a['created_at'])) ?></small>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ══ CTA ══ -->
<section class="cta-section">
  <div class="cta-bg-text">MOVEUP</div>
  <div class="cta-badge reveal">Rejoins 12 000+ athlètes</div>
  <h2 class="cta-title reveal">Prêt à <span class="hl">Move Up&nbsp;?</span></h2>
  <p class="cta-sub reveal">Ton prochain niveau commence aujourd'hui. Inscris-toi gratuitement et démarre ton programme en moins de 2 minutes.</p>
  <div class="cta-actions reveal">
    <a href="<?= View::base('login') ?>" class="btn-primary">S'inscrire gratuitement</a>
    <a href="<?= View::base('contact') ?>" class="btn-secondary">Nous contacter</a>
  </div>
</section>