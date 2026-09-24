<?php $pageTitle = 'Messages — Admin'; ?>
<main class="main">

<section class="page-hero" style="padding:9rem 3rem 3rem;">
  <div class="page-hero-inner">
    <span class="section-tag">Administration</span>
    <h1 class="page-hero-title">Messages <span class="hl">Reçus</span></h1>
    <p class="page-hero-sub">Consultez et gérez les messages envoyés via le formulaire de contact.</p>
  </div>
</section>

<div class="page-content" style="max-width:1100px;">

  <!-- Stats bar -->
  <div class="contacts-stats reveal">
    <div class="cstat"><div class="cstat-val"><?= $counts['all'] ?></div><div class="cstat-lbl">Total</div></div>
    <div class="cstat accent"><div class="cstat-val"><?= $counts['unread'] ?></div><div class="cstat-lbl">Non lus</div></div>
    <div class="cstat"><div class="cstat-val"><?= $counts['read'] ?></div><div class="cstat-lbl">Lus</div></div>
    <?php if ($counts['unread'] > 0): ?>
    <form method="POST" action="<?= View::base('admin/contacts') ?>" style="margin-left:auto;">
      <input type="hidden" name="action" value="mark_all_read">
      <input type="hidden" name="msg_id" value="0">
      <button type="submit" class="btn-secondary" style="padding:0.6rem 1.4rem;font-size:0.78rem;">Tout marquer comme lu</button>
    </form>
    <?php endif; ?>
  </div>

  <!-- Filter tabs -->
  <div class="contacts-tabs reveal">
    <a href="<?= View::base('admin/contacts') ?>?filter=all"    class="ctab <?= $filter==='all'    ? 'active' : '' ?>">Tous <span class="ctab-count"><?= $counts['all'] ?></span></a>
    <a href="<?= View::base('admin/contacts') ?>?filter=unread" class="ctab <?= $filter==='unread' ? 'active' : '' ?>">Non lus <span class="ctab-count unread"><?= $counts['unread'] ?></span></a>
    <a href="<?= View::base('admin/contacts') ?>?filter=read"   class="ctab <?= $filter==='read'   ? 'active' : '' ?>">Lus <span class="ctab-count"><?= $counts['read'] ?></span></a>
  </div>

  <!-- Messages list -->
  <?php if (empty($messages)): ?>
  <div class="empty-state reveal" style="padding:5rem 2rem;">
    <div class="empty-state-icon">📭</div>
    <div class="empty-state-title">Aucun message</div>
    <p><?= $filter === 'unread' ? 'Tous les messages ont été lus.' : "Aucun message reçu pour l'instant." ?></p>
  </div>
  <?php else: ?>
  <div class="contacts-list reveal">
    <?php foreach ($messages as $m):
      $isOpen = ($openId === (int)$m['id']);
      $isRead = $isOpen ? true : (bool)$m['lu'];
    ?>
    <div class="contact-msg-card <?= $isRead ? 'read' : 'unread' ?> <?= $isOpen ? 'open' : '' ?>" id="msg-<?= $m['id'] ?>">

      <div class="cmsg-header" onclick="toggleMsg(<?= $m['id'] ?>)">
        <div class="cmsg-avatar"><?= strtoupper(substr($m['nom'],0,1)) ?></div>
        <div class="cmsg-meta">
          <div class="cmsg-name"><?= htmlspecialchars($m['nom']) ?><?php if(!$isRead): ?><span class="unread-dot"></span><?php endif; ?></div>
          <div class="cmsg-email"><?= htmlspecialchars($m['email']) ?></div>
        </div>
        <div class="cmsg-subject"><?= $m['sujet'] ? htmlspecialchars(ucfirst($m['sujet'])) : '<span style="color:var(--muted2)">—</span>' ?></div>
        <div class="cmsg-preview"><?= htmlspecialchars(mb_strimwidth($m['message'],0,80,'…')) ?></div>
        <div class="cmsg-date"><?= date('d/m/Y',strtotime($m['created_at'])) ?><div style="font-size:.65rem;color:var(--muted2);"><?= date('H:i',strtotime($m['created_at'])) ?></div></div>
        <div class="cmsg-chevron">▾</div>
      </div>

      <div class="cmsg-body" id="body-<?= $m['id'] ?>" style="<?= $isOpen ? '' : 'display:none;' ?>">
        <div class="cmsg-body-inner">
          <div class="cmsg-detail-grid">
            <div class="cmsg-detail-item"><div class="cmsg-detail-label">Nom</div><div class="cmsg-detail-val"><?= htmlspecialchars($m['nom']) ?></div></div>
            <div class="cmsg-detail-item"><div class="cmsg-detail-label">Email</div><div class="cmsg-detail-val"><a href="mailto:<?= htmlspecialchars($m['email']) ?>" style="color:var(--gold);"><?= htmlspecialchars($m['email']) ?></a></div></div>
            <?php if($m['telephone']): ?>
            <div class="cmsg-detail-item"><div class="cmsg-detail-label">Téléphone</div><div class="cmsg-detail-val"><a href="tel:<?= htmlspecialchars($m['telephone']) ?>" style="color:var(--gold);"><?= htmlspecialchars($m['telephone']) ?></a></div></div>
            <?php endif; ?>
            <div class="cmsg-detail-item"><div class="cmsg-detail-label">Sujet</div><div class="cmsg-detail-val"><?= $m['sujet'] ? htmlspecialchars(ucfirst($m['sujet'])) : '—' ?></div></div>
            <div class="cmsg-detail-item"><div class="cmsg-detail-label">Reçu le</div><div class="cmsg-detail-val"><?= date('d/m/Y à H:i',strtotime($m['created_at'])) ?></div></div>
            <div class="cmsg-detail-item"><div class="cmsg-detail-label">Statut</div><div class="cmsg-detail-val" style="color:<?= $isRead ? 'var(--green-light)' : 'var(--gold)' ?>"><?= $isRead ? '✓ Lu' : '● Non lu' ?></div></div>
          </div>
          <div class="cmsg-message-box"><div class="cmsg-detail-label" style="margin-bottom:.6rem;">Message</div><?= nl2br(htmlspecialchars($m['message'])) ?></div>
          <div class="cmsg-actions">
            <a href="mailto:<?= htmlspecialchars($m['email']) ?>?subject=Re: <?= htmlspecialchars($m['sujet'] ?? 'Votre message') ?>" class="btn-primary" style="padding:.6rem 1.6rem;font-size:.82rem;">✉ Répondre</a>
            <form method="POST" action="<?= View::base('admin/contacts') ?>">
              <input type="hidden" name="action" value="<?= $isRead ? 'mark_unread' : 'mark_read' ?>">
              <input type="hidden" name="msg_id" value="<?= $m['id'] ?>">
              <button type="submit" class="btn-secondary" style="padding:.6rem 1.4rem;font-size:.78rem;"><?= $isRead ? 'Marquer non lu' : 'Marquer lu' ?></button>
            </form>
            <form method="POST" action="<?= View::base('admin/contacts') ?>" onsubmit="return confirm('Supprimer ce message définitivement ?');" style="margin-left:auto;">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="msg_id" value="<?= $m['id'] ?>">
              <button type="submit" class="btn-delete">Supprimer</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<script>
function toggleMsg(id) {
  const body = document.getElementById('body-' + id);
  const card = document.getElementById('msg-'  + id);
  const isOpen = body.style.display !== 'none';
  document.querySelectorAll('.cmsg-body').forEach(b => b.style.display = 'none');
  document.querySelectorAll('.contact-msg-card').forEach(c => c.classList.remove('open'));
  if (!isOpen) {
    body.style.display = 'block';
    card.classList.add('open','read');
    card.classList.remove('unread');
    const dot = card.querySelector('.unread-dot');
    if (dot) dot.remove();
  }
}
<?php if ($openId): ?>
document.addEventListener('DOMContentLoaded', () => {
  const card = document.getElementById('msg-<?= $openId ?>');
  if (card) { toggleMsg(<?= $openId ?>); setTimeout(() => card.scrollIntoView({ behavior: 'smooth', block: 'center' }), 300); }
});
<?php endif; ?>
</script>

</main>
