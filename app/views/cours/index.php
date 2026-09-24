<?php
$pageTitle = 'Séances';
$user      = Auth::user();
$userId    = (int) $user['id'];
$base      = rtrim(View::base(''), '/');

$filters = [
    'all'   => 'All',   'gym'   => 'Gym',
    'yoga'  => 'Pilates/Yoga', 'padel' => 'Padel',
    'dance' => 'Dance', 'rpm'   => 'RPM',
];
$catColors = [
    'gym'   => '#A3E635', 'yoga'  => '#818CF8',
    'padel' => '#34D399', 'dance' => '#F472B6', 'rpm'   => '#F59E0B',
];
$catIcons = [
    'gym'   => 'fas fa-dumbbell',       'yoga'  => 'fas fa-spa',
    'padel' => 'fas fa-table-tennis-paddle-ball',
    'dance' => 'fas fa-music',          'rpm'   => 'fas fa-bicycle',
];
?>

<main class="main">

<!-- Hero -->
<div style="margin-bottom:1.5rem;">
  <div style="font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:var(--lime);font-family:'Syne',sans-serif;margin-bottom:.4rem;">Réservation</div>
  <h1 style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:900;margin:0 0 .3rem;">Réserve ta <span style="color:var(--lime);">séance</span></h1>
  <p style="color:var(--muted);font-size:.85rem;">Clubs disponibles à proximité</p>
</div>

<!-- Filters -->
<div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.5rem;">
  <?php foreach ($filters as $key => $label): ?>
  <a href="<?= $base ?>/cours?type=<?= $key === 'all' ? '' : $key ?>"
     style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;border-radius:999px;
            text-decoration:none;font-size:.78rem;font-family:'Syne',sans-serif;font-weight:700;transition:all .2s;
            <?= ($category === $key || ($key === 'all' && ($category === '' || $category === 'all')))
                ? 'background:var(--lime);color:var(--bg);border:1px solid var(--border-h);'
                : 'border:1px solid var(--border);color:var(--muted);background:transparent;' ?>">
    <?= $label ?>
  </a>
  <?php endforeach; ?>

  <?php if (in_array($user['role'] ?? '', ['admin','salle'])): ?>
  <a href="<?= $base ?>/cours/publish"
     style="margin-left:auto;display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;
            border-radius:999px;background:var(--lime);color:var(--bg);border:none;
            font-size:.78rem;font-family:'Syne',sans-serif;font-weight:700;text-decoration:none;">
    <i class="fas fa-plus"></i> Publier
  </a>
  <?php endif; ?>
</div>

<!-- Session cards -->
<?php if (empty($seances)): ?>
<div style="text-align:center;padding:4rem 2rem;background:var(--card2);border:1px dashed var(--border);border-radius:16px;">
  <i class="fas fa-calendar-times" style="font-size:3rem;color:var(--lime);margin-bottom:1rem;display:block;"></i>
  <p style="color:var(--muted);">Aucune séance disponible pour le moment.</p>
</div>
<?php else: ?>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:1.2rem;">
  <?php foreach ($seances as $i => $s):
    $color        = $catColors[$s['category']] ?? 'var(--lime)';
    $icon         = $catIcons[$s['category']]  ?? 'fas fa-dumbbell';
    $reserved     = (int) $s['reserved_count'];
    $capacity     = (int) $s['capacity'];
    $isFull       = $reserved >= $capacity;
    $pct          = $capacity > 0 ? min(100, round(($reserved / $capacity) * 100)) : 0;
    $userReserved = (bool) $s['user_reserved'];
    $isDefault    = ($s['image_path'] === 'default_gym.jpg' || empty($s['image_path']));
    $imgSrc       = $isDefault
        ? 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=600&q=80'
        : View::base($s['image_path']);
    $barColor     = $pct >= 90 ? '#f87171' : ($pct >= 60 ? '#F59E0B' : '#34D399');
  ?>
  <div class="cours-card" style="background:var(--card2);border:1px solid var(--border);border-radius:16px;
              overflow:hidden;transition:all .3s ease;animation:fadeInUp .5s ease <?= $i*60 ?>ms both;">

    <!-- Image -->
    <div style="height:170px;background-image:url('<?= htmlspecialchars($imgSrc) ?>');
                background-size:cover;background-position:center;position:relative;">
      <span style="position:absolute;top:8px;left:8px;padding:.2rem .6rem;border-radius:999px;
                   font-size:.65rem;font-weight:700;font-family:'Syne',sans-serif;
                   background:<?= $color ?>18;color:<?= $color ?>;border:1px solid <?= $color ?>44;">
        <i class="<?= $icon ?>"></i> <?= ucfirst($s['category']) ?>
      </span>
      <?php if ($isFull): ?>
      <span style="position:absolute;top:8px;right:8px;padding:.2rem .6rem;border-radius:999px;
                   font-size:.65rem;font-weight:700;font-family:'Syne',sans-serif;
                   background:rgba(248,113,113,.15);color:#f87171;border:1px solid #f8717144;">
        COMPLET
      </span>
      <?php endif; ?>
    </div>

    <!-- Body -->
    <div style="padding:1.2rem;">
      <div style="font-size:.65rem;color:var(--muted);font-family:'Syne',sans-serif;margin-bottom:.3rem;">
        <?= date('d M Y', strtotime($s['session_date'])) ?> · <?= date('H:i', strtotime($s['session_time'])) ?>
      </div>
      <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;margin:.2rem 0 .4rem;">
        <?= htmlspecialchars($s['title']) ?>
      </h3>
      <p style="color:var(--muted);font-size:.78rem;margin-bottom:.8rem;">
        📍 <?= htmlspecialchars($s['gym_name'] ?? '') ?>
      </p>

      <!-- Capacity bar -->
      <div style="margin-bottom:.9rem;">
        <div style="display:flex;justify-content:space-between;font-size:.7rem;color:var(--muted);margin-bottom:.3rem;font-family:'JetBrains Mono',monospace;">
          <span>Places</span>
          <span style="color:<?= $barColor ?>;font-weight:700;"><?= $reserved ?> / <?= $capacity ?></span>
        </div>
        <div style="background:var(--border);border-radius:999px;height:6px;overflow:hidden;">
          <div style="width:<?= $pct ?>%;height:100%;background:<?= $barColor ?>;border-radius:999px;transition:width .4s;"></div>
        </div>
      </div>

      <!-- Footer -->
      <div style="display:flex;align-items:center;gap:.6rem;">
        <span style="font-family:'JetBrains Mono',monospace;font-weight:700;font-size:1rem;
                     color:<?= $color ?>;margin-right:auto;">
          <?= number_format($s['price'], 2) ?> DT
        </span>

        <?php if ($userReserved): ?>
        <button onclick="cancelReservation(<?= $s['id'] ?>, this)"
                style="background:rgba(248,113,113,.1);border:1px solid #f8717133;color:#f87171;
                       padding:.35rem .9rem;border-radius:8px;font-family:'Syne',sans-serif;
                       font-weight:700;font-size:.75rem;cursor:pointer;transition:.2s;">
          Annuler
        </button>
        <?php elseif ($isFull): ?>
        <button disabled
                style="background:var(--border);border:none;color:var(--muted);
                       padding:.35rem .9rem;border-radius:8px;font-family:'Syne',sans-serif;
                       font-weight:700;font-size:.75rem;cursor:not-allowed;">
          Complet
        </button>
        <?php else: ?>
        <button onclick="reserveSession(<?= $s['id'] ?>, this)"
                style="background:var(--lime);border:none;color:var(--bg);
                       padding:.35rem .9rem;border-radius:8px;font-family:'Syne',sans-serif;
                       font-weight:700;font-size:.75rem;cursor:pointer;transition:.2s;">
          Réserver
        </button>
        <?php endif; ?>

        <?php if (in_array($user['role'] ?? '', ['admin','salle'])): ?>
        <form method="POST" action="<?= $base ?>/cours/delete"
              onsubmit="return confirm('Supprimer ?')" style="margin:0;">
          <input type="hidden" name="id" value="<?= $s['id'] ?>">
          <button type="submit"
                  style="background:transparent;border:1px solid #f8717133;color:#f87171;
                         border-radius:8px;padding:.35rem .6rem;cursor:pointer;font-size:.8rem;">
            <i class="fas fa-trash"></i>
          </button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

</main>

<!-- Toast -->
<div id="cours-toast" style="display:none;position:fixed;bottom:2rem;right:2rem;z-index:9999;
  background:var(--card2);border:1px solid var(--border-h);border-radius:12px;padding:1rem 1.5rem;
  color:var(--lime);font-family:'Syne',sans-serif;font-weight:700;font-size:.9rem;
  box-shadow:0 8px 32px rgba(0,0,0,.5);">
  <i class="fas fa-check-circle"></i> <span id="cours-toast-msg"></span>
</div>

<style>
.cours-card:hover { border-color:var(--border-h) !important; transform:translateY(-5px); }
@keyframes fadeInUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
</style>

<script>
const BASE = '<?= $base ?>';

function showToast(msg, color = 'var(--lime)') {
    const t = document.getElementById('cours-toast');
    const m = document.getElementById('cours-toast-msg');
    t.style.color = color;
    m.textContent = msg;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}

async function reserveSession(id, btn) {
    btn.disabled = true;
    btn.textContent = '...';
    const fd = new FormData();
    fd.append('session_id', id);
    try {
        const r = await fetch(BASE + '/cours/reserve', { method: 'POST', body: fd });
        const d = await r.json();
        if (d.status === 'ok') {
            showToast('Réservation confirmée !');
            setTimeout(() => location.reload(), 1000);
        } else if (d.status === 'already') {
            showToast('Déjà réservé !', 'var(--orange)');
            btn.disabled = false;
            btn.textContent = 'Réserver';
        } else if (d.status === 'full') {
            showToast('Séance complète !', '#f87171');
            setTimeout(() => location.reload(), 1000);
        }
    } catch {
        showToast('Erreur réseau', '#f87171');
        btn.disabled = false;
        btn.textContent = 'Réserver';
    }
}

async function cancelReservation(id, btn) {
    if (!confirm('Annuler votre réservation ?')) return;
    btn.disabled = true;
    btn.textContent = '...';
    const fd = new FormData();
    fd.append('session_id', id);
    try {
        const r = await fetch(BASE + '/cours/cancel', { method: 'POST', body: fd });
        const d = await r.json();
        if (d.status === 'ok') {
            showToast('Réservation annulée', 'var(--orange)');
            setTimeout(() => location.reload(), 1000);
        }
    } catch {
        showToast('Erreur réseau', '#f87171');
        btn.disabled = false;
        btn.textContent = 'Annuler';
    }
}
</script>
