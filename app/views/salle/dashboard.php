<?php
$pageTitle  = 'Mon Espace Salle';
$base       = rtrim(View::base(''), '/');
$gymName    = htmlspecialchars($profile['gym_name']    ?? '');
$location   = htmlspecialchars($profile['location']    ?? '');
$description= htmlspecialchars($profile['description'] ?? '');
$phone      = htmlspecialchars($profile['phone']       ?? '');
$cover      = $profile['cover_photo'] ?? null;
$coverUrl   = $cover ? View::base($cover) : null;

$catColors  = ['gym'=>'#A3E635','yoga'=>'#818CF8','padel'=>'#34D399','dance'=>'#F472B6','rpm'=>'#F59E0B'];
$catIcons   = ['gym'=>'fas fa-dumbbell','yoga'=>'fas fa-spa','padel'=>'fas fa-table-tennis-paddle-ball','dance'=>'fas fa-music','rpm'=>'fas fa-bicycle'];

$totalReservations = array_sum(array_map('count', $reservations));
?>

<main class="main">

<!-- ── Profile banner ───────────────────────────────── -->
<div style="background:var(--card2);border:1px solid var(--border);border-radius:20px;overflow:hidden;margin-bottom:2rem;">

  <!-- Cover photo -->
  <div style="height:180px;position:relative;
              background:<?= $coverUrl ? "url('$coverUrl') center/cover no-repeat" : 'linear-gradient(135deg,#0d1f10,#0a1a0a)' ?>;">
    <?php if (!$coverUrl): ?>
    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                flex-direction:column;gap:.5rem;color:var(--muted);">
      <i class="fas fa-image" style="font-size:2.5rem;"></i>
      <span style="font-size:.75rem;">Aucune photo de couverture</span>
    </div>
    <?php endif; ?>
    <!-- Edit button overlay -->
    <button onclick="document.getElementById('profileModal').style.display='flex'"
            style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,.6);
                   border:1px solid var(--border);color:var(--text);border-radius:8px;
                   padding:.4rem .8rem;cursor:pointer;font-size:.75rem;font-family:'Syne',sans-serif;
                   display:flex;align-items:center;gap:.4rem;">
      <i class="fas fa-pen"></i> Modifier le profil
    </button>
  </div>

  <!-- Info row -->
  <div style="padding:1.4rem 1.8rem;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
    <div>
      <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:900;margin:0 0 .3rem;">
        <?= $gymName ?: htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>
      </h2>
      <?php if ($location): ?>
      <div style="font-size:.82rem;color:var(--muted);margin-bottom:.2rem;">
        <i class="fas fa-map-marker-alt" style="color:var(--lime);"></i> <?= $location ?>
      </div>
      <?php endif; ?>
      <?php if ($phone): ?>
      <div style="font-size:.82rem;color:var(--muted);margin-bottom:.2rem;">
        <i class="fas fa-phone" style="color:var(--lime);"></i> <?= $phone ?>
      </div>
      <?php endif; ?>
      <?php if ($description): ?>
      <div style="font-size:.8rem;color:var(--muted);margin-top:.4rem;max-width:500px;line-height:1.5;">
        <?= $description ?>
      </div>
      <?php endif; ?>
      <?php if (!$gymName && !$location): ?>
      <div style="font-size:.8rem;color:var(--muted);font-style:italic;">
        Complétez votre profil pour apparaître correctement sur la plateforme.
      </div>
      <?php endif; ?>
    </div>
    <a href="<?= $base ?>/cours/publish"
       style="display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.2rem;
              background:var(--lime);color:var(--bg);border-radius:10px;font-weight:700;
              font-family:'Syne',sans-serif;font-size:.82rem;text-decoration:none;white-space:nowrap;">
      <i class="fas fa-plus"></i> Publier une séance
    </a>
  </div>

  <?php if (isset($_GET['updated'])): ?>
  <div style="margin:0 1.5rem 1rem;padding:.6rem 1rem;background:rgba(52,211,153,.08);
              border:1px solid #34D39933;border-radius:8px;color:#34D399;font-size:.8rem;">
    <i class="fas fa-check-circle"></i> Profil mis à jour.
  </div>
  <?php endif; ?>
</div>

<!-- ── Stats ─────────────────────────────────────────── -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.2rem;margin-bottom:2rem;">
  <div style="background:var(--card2);border:1px solid var(--border);border-radius:16px;padding:1.4rem;">
    <div style="color:var(--muted);font-size:.7rem;font-family:'Syne',sans-serif;letter-spacing:.1em;margin-bottom:.5rem;">SÉANCES ACTIVES</div>
    <div style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:900;color:var(--lime);"><?= $totalSeances ?></div>
  </div>
  <div style="background:var(--card2);border:1px solid var(--border);border-radius:16px;padding:1.4rem;">
    <div style="color:var(--muted);font-size:.7rem;font-family:'Syne',sans-serif;letter-spacing:.1em;margin-bottom:.5rem;">RÉSERVATIONS</div>
    <div style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:900;color:var(--orange);"><?= $totalReservations ?></div>
  </div>
  <div style="background:var(--card2);border:1px solid var(--border);border-radius:16px;padding:1.4rem;">
    <div style="color:var(--muted);font-size:.7rem;font-family:'Syne',sans-serif;letter-spacing:.1em;margin-bottom:.5rem;">REVENUS POTENTIELS</div>
    <div style="font-family:'JetBrains Mono',monospace;font-size:1.9rem;font-weight:900;color:#34D399;"><?= number_format($totalRevenue, 2) ?> DT</div>
  </div>
</div>

<!-- ── Sessions ──────────────────────────────────────── -->
<div style="font-family:'Syne',sans-serif;font-weight:800;font-size:.78rem;color:var(--muted);
            letter-spacing:.1em;text-transform:uppercase;margin-bottom:1rem;">
  <i class="fas fa-calendar-check"></i> Mes séances
  <span style="font-size:.68rem;font-weight:400;text-transform:none;letter-spacing:0;margin-left:.5rem;color:var(--muted);">
    (les séances passées sont supprimées automatiquement)
  </span>
</div>

<?php if (empty($mySeances)): ?>
<div style="text-align:center;padding:4rem 2rem;background:var(--card2);border:1px dashed var(--border);border-radius:16px;">
  <i class="fas fa-calendar-plus" style="font-size:2.5rem;color:var(--lime);margin-bottom:1rem;display:block;"></i>
  <p style="color:var(--muted);margin-bottom:1.2rem;">Aucune séance active.</p>
  <a href="<?= $base ?>/cours/publish"
     style="display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.3rem;
            background:var(--lime);color:var(--bg);border-radius:10px;font-weight:700;
            font-family:'Syne',sans-serif;font-size:.82rem;text-decoration:none;">
    <i class="fas fa-plus"></i> Publier une séance
  </a>
</div>

<?php else: ?>
<div style="display:flex;flex-direction:column;gap:1.2rem;">
  <?php foreach ($mySeances as $s):
    $sid      = (int)$s['id'];
    $color    = $catColors[$s['category']] ?? 'var(--lime)';
    $icon     = $catIcons[$s['category']]  ?? 'fas fa-dumbbell';
    $reserved = (int)$s['reserved_count'];
    $capacity = (int)$s['capacity'];
    $pct      = $capacity > 0 ? min(100, round(($reserved/$capacity)*100)) : 0;
    $barColor = $pct>=90?'#f87171':($pct>=60?'#F59E0B':'#34D399');
    $bookers  = $reservations[$sid] ?? [];
    $isDefault = ($s['image_path']==='default_gym.jpg' || empty($s['image_path']));
    $imgSrc   = $isDefault
        ? 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=200&q=60'
        : View::base($s['image_path']);
  ?>
  <div style="background:var(--card2);border:1px solid var(--border);border-radius:16px;overflow:hidden;">

    <div style="display:flex;gap:1rem;padding:1.2rem;align-items:flex-start;flex-wrap:wrap;">
      <!-- Thumbnail -->
      <div style="width:80px;height:80px;border-radius:12px;flex-shrink:0;
                  background:url('<?= htmlspecialchars($imgSrc) ?>') center/cover;"></div>

      <!-- Details -->
      <div style="flex:1;min-width:180px;">
        <span style="font-size:.65rem;font-weight:700;font-family:'Syne',sans-serif;
                     padding:.15rem .5rem;border-radius:999px;display:inline-block;margin-bottom:.4rem;
                     background:<?= $color ?>18;color:<?= $color ?>;border:1px solid <?= $color ?>33;">
          <i class="<?= $icon ?>"></i> <?= ucfirst($s['category']) ?>
        </span>
        <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:.95rem;margin-bottom:.3rem;">
          <?= htmlspecialchars($s['title']) ?>
        </div>
        <div style="font-size:.75rem;color:var(--muted);display:flex;gap:1rem;flex-wrap:wrap;">
          <span><i class="fas fa-calendar-day" style="color:<?= $color ?>"></i> <?= date('d M Y', strtotime($s['session_date'])) ?></span>
          <span><i class="fas fa-clock" style="color:<?= $color ?>"></i> <?= date('H:i', strtotime($s['session_time'])) ?></span>
          <span><i class="fas fa-tag" style="color:<?= $color ?>"></i> <?= number_format($s['price'], 2) ?> DT</span>
        </div>
      </div>

      <!-- Capacity + delete -->
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.6rem;flex-shrink:0;">
        <form method="POST" action="<?= $base ?>/cours/delete"
              onsubmit="return confirm('Supprimer cette séance ?')" style="margin:0;">
          <input type="hidden" name="id" value="<?= $sid ?>">
          <button type="submit" style="background:transparent;border:1px solid #f8717133;color:#f87171;
                         border-radius:8px;padding:.35rem .65rem;cursor:pointer;font-size:.8rem;">
            <i class="fas fa-trash"></i>
          </button>
        </form>
        <div style="text-align:right;">
          <div style="font-family:'JetBrains Mono',monospace;font-size:.78rem;color:<?= $barColor ?>;font-weight:700;">
            <?= $reserved ?>/<?= $capacity ?> places
          </div>
          <div style="width:100px;background:var(--border);border-radius:999px;height:5px;margin-top:.3rem;">
            <div style="width:<?= $pct ?>%;height:100%;background:<?= $barColor ?>;border-radius:999px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Participants -->
    <?php if (!empty($bookers)): ?>
    <div style="border-top:1px solid var(--border);padding:1rem 1.2rem;">
      <div style="font-size:.7rem;font-family:'Syne',sans-serif;font-weight:700;color:var(--muted);
                  letter-spacing:.08em;text-transform:uppercase;margin-bottom:.6rem;">
        <i class="fas fa-users"></i> Participants (<?= count($bookers) ?>)
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
        <?php foreach ($bookers as $b): ?>
        <div style="display:flex;align-items:center;gap:.5rem;background:var(--card);
                    border:1px solid var(--border);border-radius:999px;padding:.3rem .8rem .3rem .4rem;font-size:.75rem;">
          <div style="width:24px;height:24px;border-radius:50%;background:var(--lime);color:#000;
                      display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.65rem;
                      font-family:'Syne',sans-serif;flex-shrink:0;">
            <?= strtoupper(substr($b['prenom'],0,1)) ?>
          </div>
          <span style="color:var(--text);"><?= htmlspecialchars($b['prenom'].' '.$b['nom']) ?></span>
          <span style="color:var(--muted);font-size:.7rem;"><?= htmlspecialchars($b['email']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php else: ?>
    <div style="border-top:1px solid var(--border);padding:.7rem 1.2rem;font-size:.78rem;color:var(--muted);font-style:italic;">
      Aucune réservation pour cette séance.
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

</main>

<!-- ── Profile edit modal ─────────────────────────────── -->
<div id="profileModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);
     z-index:2000;align-items:center;justify-content:center;">
  <div style="background:var(--card2);border:1px solid var(--border);border-radius:20px;
              padding:2rem;max-width:500px;width:90%;max-height:90vh;overflow-y:auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
      <h3 style="font-family:'Syne',sans-serif;font-weight:900;margin:0;font-size:1.1rem;">Profil de la salle</h3>
      <button onclick="document.getElementById('profileModal').style.display='none'"
              style="background:none;border:none;color:var(--muted);font-size:1.5rem;cursor:pointer;line-height:1;">&times;</button>
    </div>

    <form method="POST" action="<?= $base ?>/salle/profile" enctype="multipart/form-data"
          style="display:flex;flex-direction:column;gap:1rem;">

      <div class="sp-field">
        <label>Nom de la salle *</label>
        <input type="text" name="gym_name" value="<?= $gymName ?>" placeholder="ex: FitZone Sfax" class="sp-input" required>
      </div>

      <div class="sp-field">
        <label>Localisation</label>
        <input type="text" name="location" value="<?= $location ?>" placeholder="ex: Route Tunis Km 3, Sfax" class="sp-input">
      </div>

      <div class="sp-field">
        <label>Téléphone</label>
        <input type="text" name="phone" value="<?= $phone ?>" placeholder="ex: +216 74 000 000" class="sp-input">
      </div>

      <div class="sp-field">
        <label>Description</label>
        <textarea name="description" class="sp-input" rows="3"
                  placeholder="Décrivez votre salle, équipements, horaires..."
                  style="resize:vertical;"><?= $description ?></textarea>
      </div>

      <div class="sp-field">
        <label>Photo de couverture</label>
        <?php if ($coverUrl): ?>
        <img src="<?= $coverUrl ?>" style="width:100%;height:100px;object-fit:cover;border-radius:10px;margin-bottom:.5rem;">
        <?php endif; ?>
        <input type="file" name="cover_photo" accept="image/*" class="sp-input">
      </div>

      <button type="submit"
              style="padding:.7rem;background:var(--lime);color:var(--bg);border:none;
                     border-radius:10px;font-weight:700;font-family:'Syne',sans-serif;
                     font-size:.85rem;cursor:pointer;margin-top:.3rem;">
        <i class="fas fa-save"></i> Enregistrer le profil
      </button>
    </form>
  </div>
</div>

<style>
.sp-field       { display:flex;flex-direction:column;gap:.35rem; }
.sp-field label { font-size:.75rem;font-weight:600;font-family:'Syne',sans-serif;color:var(--muted); }
.sp-input       { background:var(--card);border:1px solid var(--border);border-radius:10px;
                  padding:.65rem 1rem;color:inherit;font-size:.85rem;outline:none;
                  transition:border-color .2s;width:100%;box-sizing:border-box;font-family:inherit; }
.sp-input:focus { border-color:var(--lime); }
@media(max-width:900px){ div[style*="grid-template-columns:repeat(3"] { grid-template-columns:1fr !important; } }
</style>
