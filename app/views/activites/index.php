<?php
/** @var array $library  @var array $diffColors  @var string|null $currentCat  @var array|null $currentData */
$pageTitle = 'Activités';
?>

<div class="acts-page">

  <!-- ── BREADCRUMB ── -->
  <div class="acts-breadcrumb">
    <a href="<?= View::base('dashboard') ?>"><i class="fas fa-house"></i> Dashboard</a>
    <span class="sep">›</span>
    <a href="<?= View::base('activites') ?>">Activités</a>
    <?php if ($currentCat): ?>
    <span class="sep">›</span>
    <span class="cur"><?= htmlspecialchars($currentData['label']) ?></span>
    <?php endif; ?>
  </div>

  <!-- ── HERO ── -->
  <?php require __DIR__ . '/partials/hero.php'; ?>

  <!-- ── 2-COLUMN GRID ── -->
  <div class="acts-grid">

    <!-- ── SIDEBAR ── -->
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- ── MAIN CONTENT ── -->
    <main class="acts-main">

      <?php if (!$currentCat): ?>

        <!-- État vide -->
        <?php require __DIR__ . '/partials/empty_state.php'; ?>

      <?php else: ?>

        <!-- Bannière connexion planning -->
        <div class="seance-link-banner">
          <div class="slb-icon"><i class="fas fa-calendar-week"></i></div>
          <div class="slb-text">
            <strong>Connecté au planning</strong>
            <p>Ces exercices font partie de vos séances programmées. Sélectionnez depuis le dashboard pour pré-filtrer.</p>
          </div>
          <a href="<?= View::base('cours') ?>?from_activites=<?= htmlspecialchars($currentCat) ?>" class="slb-btn">
            <i class="fas fa-external-link-alt"></i> Planning
          </a>
        </div>

        <!-- En-tête de la catégorie -->
        <div class="cat-header">
          <div class="cat-header-watermark"><?= strtoupper(htmlspecialchars($currentData['label'])) ?></div>
          <div class="cat-header-icon"
               style="background:<?= $currentData['color'] ?>12;border:1px solid <?= $currentData['color'] ?>22;">
            <?= $currentData['icon'] ?>
          </div>
          <div class="cat-header-info">
            <div class="cat-header-title"><?= htmlspecialchars($currentData['label']) ?></div>
            <div class="cat-header-muscles"><?= htmlspecialchars($currentData['muscles']) ?></div>
          </div>
          <a href="<?= View::base('activites') ?>" class="cat-header-back">
            <i class="fas fa-arrow-left"></i> Toutes les catégories
          </a>
        </div>

        <!-- Barre de recherche -->
        <div class="acts-search">
          <i class="fas fa-search"></i>
          <input type="text" id="acts-search-input" placeholder="Rechercher un exercice… (Alt+F)">
        </div>

        <!-- Compteur de résultats -->
        <div class="result-count" id="result-count">
          <span><?= count($currentData['home']) ?></span> exercices disponibles
        </div>

        <!-- Onglets Domicile / Salle -->
        <div class="loc-tabs">
          <button class="loc-tab active" id="btn-home">
            <i class="fas fa-home"></i> Domicile
            <span class="tab-count">(<?= count($currentData['home']) ?>)</span>
          </button>
          <button class="loc-tab" id="btn-gym">
            <i class="fas fa-dumbbell"></i> Salle de sport
            <span class="tab-count">(<?= count($currentData['gym']) ?>)</span>
          </button>
        </div>

        <!-- ── TAB DOMICILE ── -->
        <div class="tab-pane active ex-grid" id="tab-home">
          <?php foreach ($currentData['home'] as $i => $ex):
            $ec    = $currentData['color'];
            $delay = $i * 65;
            $categoryKey = $currentCat;
            require __DIR__ . '/partials/exercise_card.php';
          endforeach; ?>
        </div>

        <!-- ── TAB SALLE ── -->
        <div class="tab-pane ex-grid" id="tab-gym">
          <?php foreach ($currentData['gym'] as $i => $ex):
            $ec    = $currentData['color'];
            $delay = $i * 65;
            $categoryKey = $currentCat;
            require __DIR__ . '/partials/exercise_card.php';
          endforeach; ?>
        </div>

      <?php endif; ?>

    </main>
  </div><!-- /acts-grid -->

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnHome = document.getElementById('btn-home');
    const btnGym  = document.getElementById('btn-gym');
    const tabHome = document.getElementById('tab-home');
    const tabGym  = document.getElementById('tab-gym');

    if (!btnHome || !btnGym) return;

    btnHome.addEventListener('click', function () {
        btnHome.classList.add('active');
        btnGym.classList.remove('active');
        tabHome.classList.add('active');
        tabGym.classList.remove('active');
    });

    btnGym.addEventListener('click', function () {
        btnGym.classList.add('active');
        btnHome.classList.remove('active');
        tabGym.classList.add('active');
        tabHome.classList.remove('active');
    });

    // ── "J'ai fait cet exercice" ──────────────────────────
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.ex-log-btn');
        if (!btn || btn.disabled) return;

        btn.disabled = true;
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement…';

        const fd = new FormData();
        fd.append('nom',      btn.dataset.nom);
        fd.append('category', btn.dataset.category);
        fd.append('duration', btn.dataset.duration);
        fd.append('date',     new Date().toISOString().slice(0, 10));

        try {
            const res  = await fetch('<?= View::base("activites/log") ?>', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                btn.innerHTML = '<i class="fas fa-check"></i> Enregistré !';
                btn.style.background   = 'var(--lime)18';
                btn.style.borderColor  = 'var(--lime)';
                btn.style.color        = 'var(--lime)';
                showActsToast('✅ ' + btn.dataset.nom + ' enregistré dans ta progression !');
            } else {
                btn.disabled = false;
                btn.innerHTML = original;
                showActsToast('❌ ' + (data.message || 'Erreur'), 'error');
            }
        } catch {
            btn.disabled = false;
            btn.innerHTML = original;
            showActsToast('❌ Erreur réseau', 'error');
        }
    });

    // Search filter
    const searchInput = document.getElementById('acts-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.ex-card').forEach(function (card) {
                const name = card.querySelector('.ex-card-name')?.textContent.toLowerCase() || '';
                card.style.display = name.includes(q) ? '' : 'none';
            });
        });
        document.addEventListener('keydown', function (e) {
            if (e.altKey && e.key === 'f') searchInput.focus();
        });
    }

    function showActsToast(msg, type) {
        type = type || 'success';
        const el = document.createElement('div');
        el.textContent = msg;
        Object.assign(el.style, {
            position: 'fixed', bottom: '1.5rem', right: '1.5rem', zIndex: '9999',
            background: type === 'success' ? '#0f1f0f' : '#1a0505',
            color: type === 'success' ? '#C8F04A' : '#f87171',
            border: '1px solid ' + (type === 'success' ? '#C8F04A55' : '#f8717155'),
            padding: '.65rem 1.25rem', borderRadius: '10px',
            fontWeight: '700', fontSize: '.82rem',
            fontFamily: "'Syne', sans-serif",
            boxShadow: '0 4px 20px rgba(0,0,0,.5)',
            transition: 'opacity .4s'
        });
        document.body.appendChild(el);
        setTimeout(function () { el.style.opacity = '0'; setTimeout(function () { el.remove(); }, 400); }, 3000);
    }
});
</script><!-- /acts-page -->


<!-- Toast container -->
<div id="acts-toast-container"></div>
