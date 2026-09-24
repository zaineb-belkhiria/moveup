<?php
/**
 * app/views/progression/index.php
 */
$pageTitle = 'Ma progression';

$poids        = $poids        ?? ($user['poids']  ?? 70);
$poids_actuel = $poids_actuel ?? $poids;
$taille       = $taille       ?? ($user['taille'] ?? 175);
$objectif     = $objectif     ?? ($user['objectif'] ?? 'maintien');
$niveau       = $niveau       ?? ($user['niveau']   ?? 'intermediaire');
$consistency  = $consistency  ?? 0;
$motivation   = $motivation   ?? 0;
$readiness    = $readiness    ?? 0;
$streak       = $streak       ?? 0;
$seances_week = $seances_week ?? 0;
$cals_week    = $cals_week    ?? 0;
$weight_history = $weight_history ?? [];
$objectifs    = $objectifs    ?? [];
$badges       = $badges       ?? [];
$muscles      = $muscles      ?? [];
$exercises    = $exercises    ?? [];
$weekly_plan  = $weekly_plan  ?? [];
$conseils     = $conseils     ?? [];

$bmi = $taille > 0 ? round($poids_actuel / pow($taille / 100, 2), 1) : 0;
$bmi_label = match(true) {
    $bmi < 18.5 => 'Insuffisant',
    $bmi < 25   => 'Normal',
    $bmi < 30   => 'Surpoids',
    default     => 'Obésité',
};
$bmi_color = match(true) {
    $bmi < 18.5 => '#60a5fa',
    $bmi < 25   => '#C8F04A',
    $bmi < 30   => '#F0A84A',
    default     => '#f87171',
};

$niveau_label = match($niveau) {
    'debutant' => 'Débutant',
    'avance'   => 'Avancé',
    default    => 'Intermédiaire',
};
$obj_label = match($objectif) {
    'perte' => 'Perte de poids',
    'masse' => 'Prise de masse',
    default => 'Maintien',
};

$goal_weight = match($objectif) {
    'perte' => max(45, round($poids_actuel - 8, 1)),
    'masse' => min(130, round($poids_actuel + 5, 1)),
    default => $poids_actuel,
};

$js_weight = json_encode(array_values($weight_history));
$js_objectifs = json_encode(array_values($objectifs));
$js_badges = json_encode(array_values($badges));
$js_muscles = json_encode(array_values($muscles));
$js_exercises = json_encode(array_values($exercises));
$js_weekly = json_encode(array_values($weekly_plan));
?>

<style>
/* ── Progression page — dark theme matching MoveUp ─────────────── */
.prog-wrap {
    padding: 2rem 2.5rem 3rem 6rem;
    min-height: 100vh;
    font-family: 'Syne', sans-serif;
}

/* Breadcrumb */
.prog-bc { display:flex; align-items:center; gap:6px; font-size:.72rem; color:var(--muted); margin-bottom:1.8rem; }
.prog-bc a { color:var(--muted); text-decoration:none; transition:.15s; }
.prog-bc a:hover { color:var(--lime); }
.prog-bc .sep { opacity:.4; }
.prog-bc .cur { color:#c8e6c9; font-weight:700; }

/* Hero */
.prog-hero {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem;
    margin-bottom: 2.5rem;
}
.prog-hero-left {}
.prog-hero-tag {
    font-size:.6rem; letter-spacing:.18em; text-transform:uppercase;
    color:var(--lime); margin-bottom:.4rem;
}
.prog-hero h1 {
    font-size: 2.2rem; font-weight: 900; color: #e8f5e9;
    line-height: 1.1; margin: 0 0 .4rem;
}
.prog-hero h1 span { color: var(--lime); }
.prog-hero p { color: var(--muted); font-size: .85rem; }
.prog-pills { display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; }
.prog-pill {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.4rem 1rem; border-radius:999px;
    font-size:.72rem; font-weight:700; letter-spacing:.03em;
}
.pill-streak { background:#F0A84A18; color:#F0A84A; border:1px solid #F0A84A44; }
.pill-level  { background:#C8F04A12; color:#C8F04A; border:1px solid #C8F04A44; }
.pill-obj    { background:#4AF0D818; color:#4AF0D8; border:1px solid #4AF0D844; }
.live-indicator {
    display:flex; align-items:center; gap:.4rem;
    font-size:.7rem; color:var(--muted); margin-top:.5rem;
}
.live-dot { width:6px; height:6px; border-radius:50%; background:var(--lime); animation:livepulse 2s infinite; }
@keyframes livepulse { 0%,100%{opacity:1}50%{opacity:.2} }

/* Section label */
.prog-section-label {
    font-size:.6rem; letter-spacing:.18em; text-transform:uppercase;
    color:var(--lime); font-weight:700;
    margin: 2rem 0 1rem;
    display:flex; align-items:center; gap:.5rem;
}
.prog-section-label::after {
    content:''; flex:1; height:1px; background:#1e2b1e;
}

/* KPI grid */
.prog-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.prog-kpi {
    background: #0f1f0f;
    border: 1px solid #1e2b1e;
    border-radius: 14px;
    padding: 1.3rem 1.5rem;
    position: relative;
    overflow: hidden;
    transition: border-color .2s;
}
.prog-kpi:hover { border-color: #2e3b2e; }
.prog-kpi::before {
    content:''; position:absolute; top:0; left:0; right:0; height:2px;
    background: var(--kpi-accent, #C8F04A);
}
.prog-kpi-lbl { font-size:.68rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.5rem; font-weight:600; }
.prog-kpi-val { font-size:1.9rem; font-weight:900; color:#e8f5e9; line-height:1; }
.prog-kpi-sub { font-size:.72rem; color:var(--muted); margin-top:.4rem; }
.prog-kpi-up  { color: #C8F04A; }
.prog-kpi-dn  { color: #f87171; }

/* Two-column */
.prog-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }

/* Cards */
.prog-card {
    background: #0f1f0f;
    border: 1px solid #1e2b1e;
    border-radius: 14px;
    padding: 1.4rem;
    margin-bottom: 1rem;
    transition: border-color .2s;
}
.prog-card:hover { border-color: #2e3b2e; }
.prog-card-title {
    font-size:.72rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.1em; color:#7a9e7a; margin-bottom:1.2rem;
    display:flex; align-items:center; gap:.5rem;
}
.prog-card-title i { color:var(--lime); }

/* Rings */
.prog-rings { display:flex; justify-content:space-around; align-items:center; gap:.5rem; }
.prog-ring-wrap { display:flex; flex-direction:column; align-items:center; gap:.5rem; }
.prog-ring-svg-wrap { position:relative; width:90px; height:90px; }
.prog-ring-center {
    position:absolute; inset:0;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
}
.prog-ring-pct { font-size:1.1rem; font-weight:900; color:#e8f5e9; }
.prog-ring-lbl { font-size:.6rem; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:.06em; }
.prog-ring-name { font-size:.7rem; color:#7a9e7a; font-weight:600; }
.ring-arc { transition: stroke-dashoffset 1.4s cubic-bezier(.2,1,.4,1); }

/* Bars */
.prog-bars { display:flex; flex-direction:column; gap:.85rem; }
.prog-bar-item {}
.prog-bar-head { display:flex; justify-content:space-between; margin-bottom:.3rem; font-size:.75rem; color:#c8e6c9; font-weight:600; }
.prog-bar-head .val { font-family:'JetBrains Mono',monospace; color:var(--lime); }
.prog-bar-track { height:5px; background:#1a2a1a; border-radius:999px; overflow:hidden; }
.prog-bar-fill { height:100%; border-radius:999px; width:0; transition:width 1.3s cubic-bezier(.2,1,.4,1); }

/* Weight chart */
.prog-chart-wrap { position:relative; width:100%; height:180px; margin-bottom:1rem; }

/* Add weight form */
.prog-weight-form {
    display:flex; align-items:center; gap:.6rem; flex-wrap:wrap;
    padding-top:1rem; border-top:1px solid #1e2b1e;
}
.prog-weight-form label { font-size:.72rem; color:var(--muted); white-space:nowrap; font-weight:600; text-transform:uppercase; letter-spacing:.06em; }
.prog-weight-form input {
    padding:.45rem .8rem;
    background:#071007; border:1px solid #1e2b1e;
    border-radius:8px; color:#e8f5e9; font-size:.82rem;
    font-family:'JetBrains Mono',monospace; outline:none; transition:.15s;
}
.prog-weight-form input:focus { border-color:#C8F04A55; }
.prog-weight-form input[type=number] { width:90px; }
.prog-weight-form input[type=date]   { width:135px; }
.prog-weight-form button {
    padding:.45rem 1.1rem; border-radius:8px;
    background:var(--lime); color:#0a140a;
    border:none; font-size:.78rem; font-weight:800;
    font-family:'Syne',sans-serif; cursor:pointer; transition:.15s;
}
.prog-weight-form button:hover { opacity:.88; }

/* Week grid */
.prog-week-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:.5rem; margin-bottom:.8rem; }
.prog-day-col { display:flex; flex-direction:column; align-items:center; gap:.3rem; }
.prog-day-letter { font-size:.65rem; color:var(--muted); font-weight:700; text-transform:uppercase; }
.prog-day-dot {
    width:38px; height:38px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:.62rem; font-weight:700; text-align:center;
    line-height:1.2; transition:.15s;
}
.prog-day-dot:hover { transform:scale(1.06); }
.dd-done  { background:#C8F04A18; color:#C8F04A; border:1px solid #C8F04A44; }
.dd-rest  { background:#F0A84A18; color:#F0A84A; border:1px solid #F0A84A44; }
.dd-skip  { background:#0f1f0f; color:#3a5a3a; border:1px solid #1e2b1e; }
.prog-week-legend { display:flex; gap:1rem; font-size:.68rem; color:var(--muted); }
.prog-swatch { display:inline-block; width:8px; height:8px; border-radius:2px; margin-right:4px; vertical-align:middle; }

/* Goals */
.obj-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:1.1rem; }
.obj-stat { background:#071007; border:1px solid #1e2b1e; border-radius:10px; padding:.75rem 1rem; text-align:center; }
.obj-stat-num { font-size:1.3rem; font-weight:700; color:var(--lime); }
.obj-stat-lbl { font-size:.65rem; color:var(--muted); margin-top:1px; }
.obj-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:1rem; }
.obj-card { background:#071007; border:1px solid #1e2b1e; border-radius:12px; padding:1rem; position:relative; transition:.15s; }
.obj-card:hover { border-color:#2e3b2e; }
.obj-card.atteint { border-color:#C8F04A33; }
.obj-card-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:.75rem; gap:6px; }
.obj-title { font-size:.83rem; font-weight:700; color:#c8e6c9; flex:1; line-height:1.3; }
.obj-type-badge { font-size:.6rem; font-weight:700; padding:.2rem .55rem; border-radius:999px; white-space:nowrap; flex-shrink:0; }
.otb-poids    { background:#C8F04A18; color:#C8F04A; border:1px solid #C8F04A44; }
.otb-seances  { background:#4AF0D818; color:#4AF0D8; border:1px solid #4AF0D844; }
.otb-calories { background:#F0A84A18; color:#F0A84A; border:1px solid #F0A84A44; }
.otb-autre    { background:#7a9e7a18; color:#7a9e7a; border:1px solid #7a9e7a44; }
.obj-progress-row { display:flex; align-items:center; gap:10px; margin-bottom:.5rem; }
.obj-ring { flex-shrink:0; width:50px; height:50px; position:relative; }
.obj-ring-pct { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:.65rem; font-weight:700; }
.obj-info { flex:1; min-width:0; }
.obj-values { font-size:.75rem; color:var(--muted); margin-bottom:3px; }
.obj-values strong { color:#c8e6c9; font-weight:700; }
.obj-deadline { font-size:.68rem; color:var(--muted); display:flex; align-items:center; gap:3px; }
.obj-deadline.urgent { color:#fb923c; }
.obj-deadline.done   { color:#C8F04A; }
.obj-atteint-banner { background:#4AF0D810; border:1px solid #4AF0D833; border-radius:7px; padding:5px 9px; font-size:.68rem; color:#4AF0D8; display:flex; align-items:center; gap:5px; margin-bottom:.5rem; }
.obj-actions { display:flex; gap:6px; margin-top:.7rem; padding-top:.7rem; border-top:1px solid #1e2b1e; }
.obj-btn { font-size:.7rem; padding:4px 10px; border-radius:6px; border:1px solid #1e2b1e; background:transparent; color:#7a9e7a; cursor:pointer; font-family:'Syne',sans-serif; transition:.15s; }
.obj-btn:hover { background:#0f1f0f; color:#c8e6c9; }
.obj-btn.primary { border-color:#C8F04A44; color:#C8F04A; }
.obj-btn.primary:hover { background:#C8F04A0f; }
.obj-btn.danger  { margin-left:auto; border-color:#f8717133; color:#f87171; }
.obj-btn.danger:hover { background:#f8717110; }
.obj-add-wrap { }
.obj-add-toggle { display:flex; align-items:center; gap:7px; padding:.75rem 1.2rem; cursor:pointer; font-size:.78rem; color:#7a9e7a; background:transparent; border:none; width:100%; text-align:left; font-family:'Syne',sans-serif; font-weight:700; transition:.15s; }
.obj-add-toggle:hover { color:var(--lime); }
.obj-add-form { padding:1.2rem; border-top:1px solid #1e2b1e; display:none; }
.obj-add-form.open { display:block; }
.obj-type-picker { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:1rem; }
.obj-type-opt { border:1px solid #1e2b1e; border-radius:8px; padding:.6rem .3rem; text-align:center; cursor:pointer; transition:.15s; }
.obj-type-opt:hover { border-color:#C8F04A55; background:#C8F04A08; }
.obj-type-opt.selected { border-color:#C8F04A; background:#C8F04A12; }
.obj-type-opt-icon { font-size:1.1rem; margin-bottom:2px; }
.obj-type-opt-lbl { font-size:.6rem; color:var(--muted); }
.obj-type-opt.selected .obj-type-opt-lbl { color:#C8F04A; }
.obj-form-row { display:grid; gap:9px; margin-bottom:9px; }
.obj-form-row.cols2 { grid-template-columns:1fr 1fr; }
.obj-form-row.cols4 { grid-template-columns:1fr 1fr 1fr 1fr; }
.obj-field label { display:block; font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--muted); margin-bottom:3px; }
.obj-field input, .obj-field select { width:100%; background:#071007; border:1px solid #1e2b1e; border-radius:7px; color:#e8f5e9; font-size:.8rem; font-family:inherit; outline:none; transition:.15s; padding:.4rem .7rem; }
.obj-field input:focus, .obj-field select:focus { border-color:#C8F04A55; }
.obj-field select option { background:#071007; }
.obj-form-actions { display:flex; gap:.6rem; justify-content:flex-end; margin-top:.85rem; padding-top:.75rem; border-top:1px solid #1e2b1e; }
.obj-cancel { padding:.4rem 1rem; border-radius:7px; background:transparent; border:1px solid #2e3b2e; color:var(--muted); font-size:.75rem; font-weight:700; font-family:'Syne',sans-serif; cursor:pointer; }
.obj-cancel:hover { color:#c8e6c9; border-color:#5a7a5a; }
.obj-save { padding:.4rem 1.1rem; border-radius:7px; background:var(--lime); border:none; color:#0a140a; font-size:.75rem; font-weight:800; font-family:'Syne',sans-serif; cursor:pointer; display:flex; align-items:center; gap:5px; }
.obj-save:hover { opacity:.88; }
.obj-save:disabled { opacity:.5; cursor:not-allowed; }
.obj-update-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9998; align-items:center; justify-content:center; }
.obj-update-overlay.open { display:flex; }
.obj-update-box { background:#0f1f0f; border:1px solid #2e3b2e; border-radius:14px; padding:1.5rem; width:290px; }
.obj-update-title { font-size:.9rem; font-weight:700; color:#c8e6c9; margin-bottom:.3rem; }
.obj-update-sub { font-size:.7rem; color:var(--muted); margin-bottom:.85rem; }
.obj-update-input { width:100%; background:#071007; border:1px solid #1e2b1e; border-radius:9px; padding:.7rem 1rem; font-size:1.3rem; color:#c8e6c9; font-family:'JetBrains Mono',monospace; outline:none; text-align:center; }
.obj-update-input:focus { border-color:#C8F04A55; }
.obj-update-actions { display:flex; gap:8px; justify-content:flex-end; margin-top:1rem; }
.obj-upd-cancel { padding:.4rem 1rem; border-radius:7px; background:transparent; border:1px solid #2e3b2e; color:var(--muted); font-size:.75rem; font-weight:700; font-family:'Syne',sans-serif; cursor:pointer; }
.obj-upd-confirm { padding:.4rem 1.1rem; border-radius:7px; background:var(--lime); border:none; color:#0a140a; font-size:.75rem; font-weight:800; font-family:'Syne',sans-serif; cursor:pointer; }

/* ── Badges ── */
.bdg-section { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.bdg-card {
    display:flex; align-items:center; gap:12px;
    background:#071007; border:1px solid #1e2b1e;
    border-radius:12px; padding:.85rem 1rem;
    position:relative; transition:.2s; cursor:default;
}
.bdg-card.unlocked { border-color:#C8F04A33; }
.bdg-card.unlocked:hover { border-color:#C8F04A66; background:#071f07; }
.bdg-card.locked { opacity:.4; filter:grayscale(.9); }
.bdg-icon-wrap {
    width:44px; height:44px; border-radius:10px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:1.4rem; background:#0f1f0f; border:1px solid #1e2b1e;
}
.bdg-card.unlocked .bdg-icon-wrap { background:#C8F04A12; border-color:#C8F04A33; }
.bdg-info { flex:1; min-width:0; }
.bdg-name { font-size:.82rem; font-weight:700; color:#c8e6c9; margin-bottom:2px; }
.bdg-desc { font-size:.68rem; color:var(--muted); line-height:1.4; }
.bdg-unlocked-tag {
    font-size:.58rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase;
    color:#C8F04A; background:#C8F04A12; border:1px solid #C8F04A33;
    padding:1px 7px; border-radius:20px; white-space:nowrap; margin-top:4px; display:inline-block;
}
.bdg-new-dot { position:absolute; top:8px; right:8px; width:7px; height:7px; border-radius:50%; background:var(--lime); }
.bdg-lock-icon { font-size:.85rem; flex-shrink:0; opacity:.5; }

/* ── Tips ── */
.tip-grid { display:flex; flex-direction:column; gap:8px; margin-bottom:1.5rem; }
.tip-card {
    display:flex; align-items:flex-start; gap:14px;
    background:#071007; border:1px solid #1e2b1e; border-radius:12px;
    padding:1rem 1.1rem; transition:.15s;
}
.tip-card:hover { border-color:#2e3b2e; }
.tip-ico-wrap {
    width:38px; height:38px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:1.1rem;
}
.tip-body { flex:1; min-width:0; }
.tip-badge {
    display:inline-flex; align-items:center; gap:4px;
    font-size:.6rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase;
    padding:2px 8px; border-radius:20px; margin-bottom:5px;
}
.tip-title { font-size:.83rem; font-weight:700; color:#c8e6c9; margin-bottom:3px; }
.tip-text  { font-size:.75rem; color:var(--muted); line-height:1.55; }

/* ── Refresh footer ── */
.prog-refresh {
    display:flex; align-items:center; justify-content:center; gap:7px;
    font-size:.68rem; color:var(--muted); padding:.6rem; margin-top:1rem;
    background:#071007; border:1px solid #1e2b1e; border-radius:10px;
}
/* ── WATER TRACKING ── */
.water-card { padding:1.2rem 1.4rem; }
.water-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem; }
.water-title { font-size:.9rem; font-weight:700; color:#c8e6c9; }
.water-sub { font-size:.72rem; color:var(--muted); margin-top:2px; }
.water-pct-badge { background:#4AF0D818; border:1px solid #4AF0D844; color:#4AF0D8; font-size:.75rem; font-weight:700; padding:3px 10px; border-radius:20px; }
.water-track { background:#1a2e1a; border-radius:8px; height:10px; overflow:hidden; margin-bottom:6px; }
.water-fill { height:100%; border-radius:8px; background:linear-gradient(90deg,#4AF0D8,#60a5fa); transition:width .5s ease; }
.water-labels { display:flex; justify-content:space-between; font-size:.72rem; color:var(--muted); margin-bottom:1rem; }
.water-btns { display:flex; gap:8px; flex-wrap:wrap; }
.water-btn { background:#1a2e1a; border:1px solid #2e3b2e; color:#c8e6c9; font-size:.75rem; padding:7px 14px; border-radius:8px; cursor:pointer; transition:all .2s; }
.water-btn:hover { border-color:#4AF0D8; color:#4AF0D8; background:#4AF0D810; }
.water-btn-custom { border-style:dashed; }
.water-motivation { font-size:.78rem; color:#4AF0D8; margin-top:.8rem; min-height:1.2em; font-style:italic; }
</style>

<div class="prog-wrap">

    <!-- Breadcrumb -->
    <div class="prog-bc">
        <a href="<?= View::base('dashboard') ?>"><i class="fas fa-house"></i> Dashboard</a>
        <span class="sep">›</span>
        <span class="cur">Ma progression</span>
    </div>

    <!-- Hero -->
    <div class="prog-hero">
        <div class="prog-hero-left">
            <div class="prog-hero-tag"><i class="fas fa-chart-line"></i> &nbsp;Suivi personnel</div>
            <h1>Ma <span>progression</span></h1>
            <p>Toutes tes statistiques en un coup d'œil</p>
            <div class="live-indicator">
                <span class="live-dot"></span>
                Mis à jour · <span id="prog-last-update"></span>
            </div>
        </div>
        <div class="prog-pills">
            <span class="prog-pill pill-streak">
                <i class="fas fa-bolt"></i>
                <?= (int)$streak ?> jour<?= $streak > 1 ? 's' : '' ?> de streak
            </span>
            <span class="prog-pill pill-level"><?= htmlspecialchars($niveau_label) ?></span>
            <span class="prog-pill pill-obj"><i class="fas fa-bullseye"></i> <?= htmlspecialchars($obj_label) ?></span>
        </div>
    </div>

    <!-- ── KPIs ── -->
    <div class="prog-section-label"><i class="fas fa-gauge-high"></i> Vue d'ensemble</div>
    <div class="prog-kpi-grid">
        <div class="prog-kpi" style="--kpi-accent:#C8F04A">
            <div class="prog-kpi-lbl">Poids actuel</div>
            <div class="prog-kpi-val"><?= number_format((float)$poids, 1) ?> <span style="font-size:1rem;color:var(--muted)">kg</span></div>
            <div class="prog-kpi-sub">
                Objectif : <strong style="color:var(--lime)"><?= $goal_weight ?> kg</strong>
            </div>
        </div>
        <div class="prog-kpi" style="--kpi-accent:#4AF0D8">
            <div class="prog-kpi-lbl">Séances cette semaine</div>
            <div class="prog-kpi-val"><?= (int)$seances_week ?> <span style="font-size:1rem;color:var(--muted)">/5</span></div>
            <div class="prog-kpi-sub">
                <?= $seances_week >= 5 ? '<span class="prog-kpi-up">🎯 Objectif atteint !</span>' : ((5 - (int)$seances_week) . ' restante' . (5 - (int)$seances_week > 1 ? 's' : '')) ?>
            </div>
        </div>
        <div class="prog-kpi" style="--kpi-accent:#F0A84A">
            <div class="prog-kpi-lbl">Calories brûlées</div>
            <div class="prog-kpi-val"><?= number_format((int)$cals_week) ?></div>
            <div class="prog-kpi-sub">cette semaine</div>
        </div>
        <div class="prog-kpi" style="--kpi-accent:<?= $bmi_color ?>">
            <div class="prog-kpi-lbl">IMC</div>
            <div class="prog-kpi-val" style="color:<?= $bmi_color ?>"><?= $bmi ?></div>
            <div class="prog-kpi-sub"><?= htmlspecialchars($bmi_label) ?></div>
        </div>
    </div>

    <!-- ── SCORES + MUSCLES ── -->
    <div class="prog-row">

        <!-- Scores -->
        <div class="prog-card">
            <div class="prog-card-title"><i class="fas fa-circle-nodes"></i> Scores de forme</div>
            <div class="prog-rings">
                <?php foreach ([
                    [(int)$consistency, '#C8F04A', 'consistance'],
                    [(int)$motivation,  '#F0A84A', 'motivation'],
                    [(int)$readiness,   '#4AF0D8', 'readiness'],
                ] as [$val, $color, $lbl]):
                    $circ = 245.04;
                    $offset = $circ - (min($val, 100) / 100 * $circ);
                ?>
                <div class="prog-ring-wrap">
                    <div class="prog-ring-svg-wrap">
                        <svg width="90" height="90" viewBox="0 0 90 90" role="img" aria-label="<?= $lbl ?> <?= $val ?>%">
                            <circle cx="45" cy="45" r="39" fill="none" stroke="#1a2a1a" stroke-width="7"/>
                            <circle cx="45" cy="45" r="39" fill="none"
                                stroke="<?= $color ?>" stroke-width="7"
                                stroke-linecap="round"
                                stroke-dasharray="<?= $circ ?>"
                                stroke-dashoffset="<?= $offset ?>"
                                transform="rotate(-90 45 45)"
                                class="ring-arc"/>
                        </svg>
                        <div class="prog-ring-center">
                            <span class="prog-ring-pct" style="color:<?= $color ?>"><?= $val ?>%</span>
                        </div>
                    </div>
                    <span class="prog-ring-name"><?= $lbl ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Muscle activity -->
        <div class="prog-card">
            <div class="prog-card-title"><i class="fas fa-dumbbell"></i> Activité musculaire</div>
            <div class="prog-bars" id="prog-muscle-bars">
                <?php if (empty($muscles)): ?>
                <p class="prog-empty">Aucune donnée musculaire encore.<br>Complète des séances pour les voir apparaître.</p>
                <?php else:
                    $muscle_colors = ['#C8F04A','#4AF0D8','#F0A84A','#f87171','#a78bfa','#60a5fa'];
                    foreach ($muscles as $i => $m):
                        $col = $m['color'] ?? $muscle_colors[$i % count($muscle_colors)];
                        $val = (int)($m['fatigue_level'] ?? $m['val'] ?? 0);
                        $nom = htmlspecialchars($m['muscle_group'] ?? $m['nom'] ?? '—');
                ?>
                <div class="prog-bar-item">
                    <div class="prog-bar-head">
                        <span><?= $nom ?></span>
                        <span class="val"><?= $val ?>%</span>
                    </div>
                    <div class="prog-bar-track">
                        <div class="prog-bar-fill" style="background:<?= $col ?>" data-w="<?= $val ?>"></div>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <!-- ── WEIGHT CHART ── -->
    <div class="prog-section-label"><i class="fas fa-weight-scale"></i> Poids</div>
    <div class="prog-card">
        <div class="prog-card-title"><i class="fas fa-chart-area"></i> Évolution du poids</div>
        <div class="prog-chart-wrap">
            <canvas id="weightChart" role="img" aria-label="Courbe d'évolution du poids">Chargement…</canvas>
        </div>
        <div class="prog-weight-form">
            <label>Ajouter</label>
            <input type="number" id="prog-new-weight" min="30" max="300" step="0.1" placeholder="kg">
            <input type="date"   id="prog-new-date" value="<?= date('Y-m-d') ?>">
            <button onclick="progSaveWeight()"><i class="fas fa-plus"></i> Enregistrer</button>
        </div>
    </div>

    <!-- ── WEEK PLAN ── -->
    <div class="prog-section-label"><i class="fas fa-calendar-week"></i> Semaine en cours</div>
    <div class="prog-card">
        <div class="prog-card-title"><i class="fas fa-calendar-check"></i> Planning hebdomadaire</div>
        <div class="prog-week-grid">
            <?php
            $days_letters = ['L','M','M','J','V','S','D'];
            foreach ($weekly_plan as $i => $p):
                $done = !empty($p['done']);
                $rest = !empty($p['rest']);
                $cls  = $done ? 'dd-done' : ($rest ? 'dd-rest' : 'dd-skip');
                $dl   = $days_letters[$i] ?? '?';
                $lbl  = mb_substr($p['label'] ?? '', 0, 6);
            ?>
            <div class="prog-day-col">
                <span class="prog-day-letter"><?= $dl ?></span>
                <div class="prog-day-dot <?= $cls ?>" title="<?= htmlspecialchars($p['label'] ?? '') ?>"><?= htmlspecialchars($lbl) ?></div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($weekly_plan)): ?>
            <div style="grid-column:1/-1;text-align:center;color:var(--muted);font-size:.78rem;padding:.5rem 0">
                Aucun planning pour cette semaine.
            </div>
            <?php endif; ?>
        </div>
        <div class="prog-week-legend">
            <span><span class="prog-swatch" style="background:#C8F04A22;border:1px solid #C8F04A44"></span>Complété</span>
            <span><span class="prog-swatch" style="background:#F0A84A22;border:1px solid #F0A84A44"></span>Repos actif</span>
            <span><span class="prog-swatch" style="background:#0f1f0f;border:1px solid #1e2b1e"></span>Non effectué</span>
        </div>
    </div>

   

        <?php
        $cnt_total   = count($objectifs);
        $cnt_atteint = count(array_filter($objectifs, fn($g) => ($g['statut'] ?? '') === 'atteint'));
        $cnt_cours   = count(array_filter($objectifs, fn($g) => ($g['statut'] ?? '') === 'en_cours'));
        ?>

        <?php if (!empty($objectifs)): ?>
        <div class="obj-stats">
            <div class="obj-stat">
                <div class="obj-stat-num"><?= $cnt_total ?></div>
                <div class="obj-stat-lbl">Total</div>
            </div>
            <div class="obj-stat">
                <div class="obj-stat-num" style="color:#4AF0D8"><?= $cnt_atteint ?></div>
                <div class="obj-stat-lbl">Atteints</div>
            </div>
            <div class="obj-stat">
                <div class="obj-stat-num" style="color:#F0A84A"><?= $cnt_cours ?></div>
                <div class="obj-stat-lbl">En cours</div>
            </div>
        </div>
        <div class="obj-grid">
        <?php foreach ($objectifs as $g):
            $range  = (float)($g['valeur_cible'] ?? 0) - (float)($g['valeur_depart'] ?? 0);
            $done   = (float)($g['valeur_actuelle'] ?? 0) - (float)($g['valeur_depart'] ?? 0);
            $pct    = $range > 0 ? min(100, max(0, (int)round($done / $range * 100))) : 0;
            $gColor = match($g['type'] ?? '') {
                'poids'    => '#C8F04A',
                'seances'  => '#4AF0D8',
                'calories' => '#F0A84A',
                default    => '#7a9e7a',
            };
            $badgeCls = match($g['type'] ?? '') {
                'poids'    => 'otb-poids',
                'seances'  => 'otb-seances',
                'calories' => 'otb-calories',
                default    => 'otb-autre',
            };
            $typeLabel = match($g['type'] ?? '') {
                'poids'    => '⚖ Poids',
                'seances'  => '🏋 Séances',
                'calories' => '🔥 Calories',
                default    => '🎯 Autre',
            };
            $statut   = $g['statut'] ?? 'en_cours';
            $r = 20; $circ = 2 * M_PI * $r;
            $dash = $circ * ($pct / 100); $gap = $circ - $dash;
            $offset = $circ * 0.25;
            $daysLeft = null;
            if (!empty($g['date_echeance'])) {
                $daysLeft = (int)ceil((strtotime($g['date_echeance']) - time()) / 86400);
            }
        ?>
        <div class="obj-card <?= $statut === 'atteint' ? 'atteint' : '' ?>">
            <div class="obj-card-top">
                <span class="obj-title"><?= htmlspecialchars($g['titre']) ?></span>
                <span class="obj-type-badge <?= $badgeCls ?>"><?= $typeLabel ?></span>
            </div>
            <div class="obj-progress-row">
                <div class="obj-ring">
                    <svg width="50" height="50" viewBox="0 0 50 50">
                        <circle cx="25" cy="25" r="<?= $r ?>" fill="none" stroke="<?= $gColor ?>22" stroke-width="4"/>
                        <circle cx="25" cy="25" r="<?= $r ?>" fill="none" stroke="<?= $gColor ?>" stroke-width="4"
                            stroke-dasharray="<?= round($dash,1) ?> <?= round($gap,1) ?>"
                            stroke-dashoffset="<?= round($offset,1) ?>"
                            stroke-linecap="round"/>
                    </svg>
                    <div class="obj-ring-pct" style="color:<?= $gColor ?>"><?= $pct ?>%</div>
                </div>
                <div class="obj-info">
                    <div class="obj-values">
                        <strong><?= number_format((float)$g['valeur_actuelle'], 1) ?></strong>
                        / <?= number_format((float)$g['valeur_cible'], 1) ?> <?= htmlspecialchars($g['unite'] ?? '') ?>
                    </div>
                    <?php if ($statut === 'atteint'): ?>
                        <div class="obj-deadline done">✓ Objectif atteint</div>
                    <?php elseif ($daysLeft !== null): ?>
                        <?php if ($daysLeft < 0): ?>
                            <div class="obj-deadline urgent">⚠ Expiré il y a <?= abs($daysLeft) ?>j</div>
                        <?php elseif ($daysLeft <= 7): ?>
                            <div class="obj-deadline urgent">⚡ <?= $daysLeft ?>j restants</div>
                        <?php else: ?>
                            <div class="obj-deadline">📅 <?= $daysLeft ?>j restants</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($statut === 'atteint'): ?>
            <div class="obj-atteint-banner">✓ Objectif atteint !</div>
            <?php endif; ?>
            <div class="obj-actions">
                <?php if ($statut !== 'atteint'): ?>
                <button class="obj-btn primary"
                    onclick="openObjUpdate(<?= (int)$g['id'] ?>, <?= (float)$g['valeur_actuelle'] ?>, '<?= htmlspecialchars($g['unite'] ?? '') ?>', <?= (float)$g['valeur_cible'] ?>)">
                    ↑ Mise à jour
                </button>
                <?php endif; ?>
                <button class="obj-btn danger" onclick="deleteObj(<?= (int)$g['id'] ?>)">Supprimer</button>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        
        
        <?php endif; ?>

        
        

    <!-- Update modal -->
    <div class="obj-update-overlay" id="obj-update-overlay">
        <div class="obj-update-box">
            <div class="obj-update-title" id="obj-modal-title">Mise à jour</div>
            <div class="obj-update-sub" id="obj-modal-sub">Valeur actuelle</div>
            <input type="number" class="obj-update-input" id="obj-modal-input" step="0.1">
            <div class="obj-update-actions">
                <button class="obj-upd-cancel" onclick="closeObjModal()">Annuler</button>
                <button class="obj-upd-confirm" onclick="confirmObjUpdate()">Mettre à jour</button>
            </div>
        </div>
    </div>

        <!-- ── BADGES ── -->
    <div class="prog-section-label"><i class="fas fa-medal"></i> Récompenses</div>
    <div class="prog-card">
        <div class="prog-card-title" style="display:flex;align-items:center;justify-content:space-between;">
            <span><i class="fas fa-trophy"></i> Badges</span>
            <?php
            $total_badges    = count($badges);
            $unlocked_badges = count(array_filter($badges, fn($b) => !empty($b['unlocked']) || !empty($b['obtenu_le'])));
            ?>
            <span style="font-size:.7rem;font-weight:400;color:var(--muted);">
                <span style="color:var(--lime);font-weight:700;"><?= $unlocked_badges ?></span> / <?= $total_badges ?> obtenus
            </span>
        </div>
        <?php if (empty($badges)): ?>
        <p style="font-size:.8rem;color:var(--muted);padding:.5rem 0;text-align:center;">Aucun badge encore. Commence à t'entraîner !</p>
        <?php else: ?>
        <div class="bdg-section">
        <?php foreach ($badges as $b):
            $unlocked = !empty($b['unlocked']) || !empty($b['obtenu_le']);
            $isNew    = !empty($b['is_new']);
            $obtenu   = !empty($b['obtenu_le']) ? date('d/m/Y', strtotime($b['obtenu_le'])) : null;
        ?>
        <div class="bdg-card <?= $unlocked ? 'unlocked' : 'locked' ?>">
            <?php if ($isNew): ?><span class="bdg-new-dot"></span><?php endif; ?>
            <div class="bdg-icon-wrap"><?= htmlspecialchars($b['icone'] ?? '🏅') ?></div>
            <div class="bdg-info">
                <div class="bdg-name"><?= htmlspecialchars($b['nom']) ?></div>
                <div class="bdg-desc"><?= htmlspecialchars($b['description'] ?? '') ?></div>
                <?php if ($unlocked): ?>
                <span class="bdg-unlocked-tag">
                    <?= $obtenu ? '✓ '.$obtenu : '✓ Débloqué' ?>
                </span>
                <?php endif; ?>
            </div>
            <?php if (!$unlocked): ?><span class="bdg-lock-icon">🔒</span><?php endif; ?>
        </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── EXERCISES ── -->
    <?php if (!empty($exercises)): ?>
    <div class="prog-section-label"><i class="fas fa-list-check"></i> Exercices suivis</div>
    <div class="prog-card">
        <div class="prog-card-title"><i class="fas fa-chart-bar"></i> Progression par exercice</div>
        <div class="prog-bars">
            <?php
            $maxW = max(array_filter(array_column($exercises, 'weight')) ?: [1]);
            foreach ($exercises as $e):
                $w   = (float)($e['weight'] ?? 0);
                $pct = $maxW > 0 ? round($w / $maxW * 100) : 0;
                $lbl = $w > 0
                    ? "{$e['sets']}×{$e['reps']} @ {$w}kg"
                    : "{$e['sets']}×{$e['reps']} poids du corps";
            ?>
            <div class="prog-bar-item">
                <div class="prog-bar-head">
                    <span><?= htmlspecialchars($e['exercise']) ?></span>
                    <span class="prog-ex-meta"><?= htmlspecialchars($lbl) ?></span>
                </div>
                <div class="prog-bar-track">
                    <div class="prog-bar-fill" style="background:var(--lime)" data-w="<?= $pct ?>"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── HYDRATATION ── -->
    <div class="prog-section-label"><i class="fas fa-tint"></i> Hydratation du jour</div>
    <div class="prog-card water-card">
        <div class="water-header">
            <div>
                <div class="water-title">💧 Eau bue aujourd'hui</div>
                <div class="water-sub">Objectif : 2,5 L / jour</div>
            </div>
            <div class="water-pct-badge" id="waterPctBadge">0%</div>
        </div>

        <!-- Progress bar -->
        <div class="water-track">
            <div class="water-fill" id="waterFill" style="width:0%"></div>
        </div>
        <div class="water-labels">
            <span id="waterDrank">0 L bu</span>
            <span>2,5 L</span>
        </div>

        <!-- Quick add buttons -->
        <div class="water-btns">
            <button class="water-btn" onclick="addWater(0.25)">+250 ml</button>
            <button class="water-btn" onclick="addWater(0.5)">+500 ml</button>
            <button class="water-btn" onclick="addWater(1)">+1 L</button>
            <button class="water-btn water-btn-custom" onclick="openWaterCustom()">Personnalisé</button>
        </div>

        <!-- Custom input -->
        <div id="waterCustomWrap" style="display:none;margin-top:10px;display:none;">
            <div style="display:flex;gap:8px;align-items:center;">
                <input type="number" id="waterCustomInput" step="0.1" min="0.1" max="5" placeholder="ex: 0.75"
                    style="flex:1;background:#1a2e1a;border:1px solid #2e3b2e;border-radius:8px;padding:8px 12px;color:#e8f5e9;font-size:.85rem;">
                <span style="color:var(--muted);font-size:.8rem;">L</span>
                <button class="water-btn" onclick="submitCustomWater()">Ajouter</button>
            </div>
        </div>

        <!-- Motivation message -->
        <div class="water-motivation" id="waterMotivation"></div>

        <!-- 7-day mini chart -->
        <div style="margin-top:1.2rem;">
            <div style="font-size:.72rem;color:var(--muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em;">7 derniers jours</div>
            <canvas id="waterChart" height="70"></canvas>
        </div>
    </div>

    <!-- ── CONSEILS ── -->
    

    <!-- Live hint -->
    <div class="prog-refresh">
        <span class="live-dot" style="display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--lime);"></span>
        Mise à jour automatique toutes les 30 secondes
    </div>

</div><!-- /prog-wrap -->


<!-- ══ CHART.JS + SCRIPTS ══════════════════════════════════ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
(function () {
    'use strict';

    const WH = <?= json_encode(array_values($weight_history)) ?>;

    /* Timestamp */
    function tick() {
        const el = document.getElementById('prog-last-update');
        if (el) el.textContent = new Date().toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});
    }
    tick();
    setInterval(tick, 30000);

    /* Animate bar fills */
    function animateBars() {
        document.querySelectorAll('.prog-bar-fill[data-w]').forEach(function(el) {
            const w = el.dataset.w + '%';
            el.style.width = '0';
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { el.style.width = w; });
            });
        });
        document.querySelectorAll('.prog-goal-fill').forEach(function(el) {
            const w = el.style.width;
            el.style.width = '0';
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { el.style.width = w; });
            });
        });
    }
    setTimeout(animateBars, 300);

    /* Weight chart */
    let weightChart = null;
    function buildChart(history) {
        const canvas = document.getElementById('weightChart');
        if (!canvas) return;
        if (weightChart) weightChart.destroy();
        const labels = history.map(function(w) { return w.d || w.date_log || ''; });
        const data   = history.map(function(w) { return parseFloat(w.poids); });
        const minV   = data.length ? Math.min.apply(null, data) : 0;
        const maxV   = data.length ? Math.max.apply(null, data) : 100;
        weightChart = new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Poids (kg)',
                    data: data,
                    borderColor: '#C8F04A',
                    backgroundColor: 'rgba(200,240,74,0.07)',
                    fill: true, tension: 0.4,
                    pointBackgroundColor: '#C8F04A',
                    pointRadius: 4, pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(c) { return c.parsed.y.toFixed(1) + ' kg'; } } }
                },
                scales: {
                    x: {
                        grid: { color: '#1a2a1a' },
                        ticks: { color: '#5a8a5a', font: { size: 11, family: "'JetBrains Mono'" } }
                    },
                    y: {
                        min: Math.max(0, minV - 1.5),
                        max: maxV + 1.5,
                        grid: { color: '#1a2a1a' },
                        ticks: {
                            color: '#5a8a5a',
                            font: { size: 11, family: "'JetBrains Mono'" },
                            callback: function(v) { return v.toFixed(1) + 'kg'; }
                        }
                    }
                }
            }
        });
    }
    buildChart(<?= json_encode(array_values($weight_history)) ?>);

    /* Save weight */
    window.progSaveWeight = function() {
        const w = parseFloat(document.getElementById('prog-new-weight').value);
        const d = document.getElementById('prog-new-date').value || new Date().toISOString().slice(0,10);
        if (!w || w < 30 || w > 300) { progToast('❌ Poids invalide', '#f87171'); return; }
        const fd = new FormData();
        fd.append('poids', w); fd.append('date', d);
        fetch('<?= View::base("weight/update") ?>', { method:'POST', body:fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    const label = new Date(d).toLocaleDateString('fr-FR', {day:'2-digit', month:'2-digit'});
                    <?= "WH" ?>.push({ d: label, poids: w });
                    buildChart(WH);
                    document.getElementById('prog-new-weight').value = '';
                    progToast('✅ Poids enregistré : ' + w.toFixed(1) + ' kg');
                } else {
                    progToast('❌ ' + (data.error || 'Erreur'), '#f87171');
                }
            })
            .catch(function() { progToast('❌ Erreur réseau', '#f87171'); });
    };

    /* Toast */
    function progToast(msg, color) {
        color = color || '#C8F04A';
        const el = document.createElement('div');
        el.textContent = msg;
        Object.assign(el.style, {
            position:'fixed', bottom:'1.5rem', right:'1.5rem', zIndex:'9999',
            background: color === '#C8F04A' ? '#0f1f0f' : '#1a0f0f',
            color: color, padding:'.65rem 1.25rem', borderRadius:'10px',
            fontWeight:'700', fontSize:'.8rem',
            border:'1px solid ' + color + '55',
            boxShadow:'0 4px 20px rgba(0,0,0,.5)',
            fontFamily:"'Syne',sans-serif",
            transition:'opacity .4s'
        });
        document.body.appendChild(el);
        setTimeout(function() { el.style.opacity='0'; setTimeout(function(){ el.remove(); }, 400); }, 3500);
    }

    /* Auto-refresh */
    setInterval(function() { window.location.reload(); }, 30000);

    /* ── Objectifs ── */
    var _objActiveId = null;

    window.toggleObjForm = function() {
        var f = document.getElementById('obj-add-form');
        f.classList.toggle('open');
    };

    window.selectObjType = function(type, el) {
        document.getElementById('obj-type-val').value = type;
        document.querySelectorAll('.obj-type-opt').forEach(function(x) { x.classList.remove('selected'); });
        el.classList.add('selected');
        var units = { poids:'kg', seances:'séances', calories:'kcal', autre:'' };
        document.getElementById('obj-unite').value = units[type] || '';
    };

    window.saveObj = function() {
        var titre    = document.getElementById('obj-titre').value.trim();
        var type     = document.getElementById('obj-type-val').value;
        var depart   = document.getElementById('obj-depart').value || '0';
        var cible    = document.getElementById('obj-cible').value;
        var unite    = document.getElementById('obj-unite').value.trim();
        var echeance = document.getElementById('obj-echeance').value;
        var btn      = document.getElementById('obj-save-btn');

        if (!titre) { progToast('❌ Donne un titre à ton objectif', '#f87171'); return; }
        if (!cible)  { progToast('❌ Indique une valeur cible', '#f87171'); return; }

        btn.disabled  = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement…';

        var fd = new FormData();
        fd.append('titre',           titre);
        fd.append('type',            type);
        fd.append('valeur_depart',   depart);
        fd.append('valeur_cible',    cible);
        fd.append('valeur_actuelle', depart);
        fd.append('unite',           unite);
        fd.append('date_echeance',   echeance);

        fetch('<?= View::base("objectifs/add") ?>', { method:'POST', body:fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    progToast('✅ Objectif créé !');
                    setTimeout(function() { window.location.reload(); }, 900);
                } else {
                    btn.disabled  = false;
                    btn.innerHTML = '<i class="fas fa-check"></i> Enregistrer';
                    progToast('❌ ' + (data.message || 'Erreur'), '#f87171');
                }
            })
            .catch(function() {
                btn.disabled  = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Enregistrer';
                progToast('❌ Erreur réseau', '#f87171');
            });
    };

    window.openObjUpdate = function(id, current, unite, cible) {
        _objActiveId = id;
        document.getElementById('obj-modal-title').textContent = 'Mise à jour de la valeur';
        document.getElementById('obj-modal-sub').textContent   = 'Valeur actuelle (' + unite + ') — objectif : ' + cible;
        document.getElementById('obj-modal-input').value       = current;
        document.getElementById('obj-update-overlay').classList.add('open');
    };

    window.closeObjModal = function() {
        document.getElementById('obj-update-overlay').classList.remove('open');
        _objActiveId = null;
    };

    window.confirmObjUpdate = function() {
        if (!_objActiveId) return;
        var val = document.getElementById('obj-modal-input').value;
        var fd  = new FormData();
        fd.append('id',     _objActiveId);
        fd.append('valeur', val);
        fetch('<?= View::base("objectifs/updateProgress") ?>', { method:'POST', body:fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    closeObjModal();
                    if (data.statut === 'atteint') progToast('🎯 Objectif atteint !');
                    else progToast('✅ Progression mise à jour');
                    setTimeout(function() { window.location.reload(); }, 900);
                } else {
                    progToast('❌ Erreur', '#f87171');
                }
            })
            .catch(function() { progToast('❌ Erreur réseau', '#f87171'); });
    };

    window.deleteObj = function(id) {
        if (!confirm('Supprimer cet objectif ?')) return;
        var fd = new FormData();
        fd.append('id', id);
        fetch('<?= View::base("objectifs/delete") ?>', { method:'POST', body:fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    progToast('🗑 Objectif supprimé');
                    setTimeout(function() { window.location.reload(); }, 900);
                } else {
                    progToast('❌ ' + (data.message || 'Erreur'), '#f87171');
                }
            })
            .catch(function() { progToast('❌ Erreur réseau', '#f87171'); });
    };

    /* Close update modal on overlay click */
    document.getElementById('obj-update-overlay').addEventListener('click', function(e) {
        if (e.target === this) closeObjModal();
    });


})();
</script>

<script>
/* ── WATER TRACKER ── */
(function(){
    const GOAL   = 2.5;
    const BASE   = '<?= View::base("") ?>';
    let today    = new Date().toISOString().slice(0,10);
    let current  = 0;
    let waterChart = null;

    function pct(v){ return Math.min(100, Math.round(v / GOAL * 100)); }

    function motivation(v){
        const p = pct(v);
        if (p === 0)  return 'Commence ta journée avec un verre d\'eau 💧';
        if (p < 25)   return 'Bon début ! Continue à t\'hydrater régulièrement.';
        if (p < 50)   return 'Tu es sur la bonne voie, continue ! 👍';
        if (p < 75)   return 'Super ! Plus que la moitié restante. 💪';
        if (p < 100)  return 'Presque là ! Encore un peu d\'eau. 🚀';
        return '🎉 Objectif atteint ! Excellente hydratation aujourd\'hui !';
    }

    function updateUI(litres){
        current = litres;
        const p = pct(litres);
        document.getElementById('waterFill').style.width   = p + '%';
        document.getElementById('waterDrank').textContent  = litres.toFixed(2).replace('.',',') + ' L bu';
        document.getElementById('waterPctBadge').textContent = p + '%';
        document.getElementById('waterMotivation').textContent = motivation(litres);
    }

    function buildChart(history){
        // history = [{log_date, litres}, ...] newest first — reverse for chart
        const days = [...history].reverse().slice(-7);
        const labels = days.map(d => {
            const dt = new Date(d.log_date + 'T00:00:00');
            return dt.toLocaleDateString('fr-FR', {weekday:'short', day:'numeric'});
        });
        const data = days.map(d => parseFloat(d.litres));

        const ctx = document.getElementById('waterChart').getContext('2d');
        if (waterChart) waterChart.destroy();
        waterChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: data.map(v => v >= GOAL ? '#4AF0D8aa' : '#4AF0D840'),
                    borderColor:     data.map(v => v >= GOAL ? '#4AF0D8'   : '#4AF0D866'),
                    borderWidth: 1,
                    borderRadius: 5,
                }]
            },
            options: {
                plugins: { legend: { display: false },
                    tooltip: { callbacks: { label: c => c.parsed.y.toFixed(2) + ' L' }}},
                scales: {
                    x: { ticks: { color:'#6b7f6b', font:{ size:10 }}, grid:{ display:false }},
                    y: { ticks: { color:'#6b7f6b', font:{ size:10 },
                                  callback: v => v + ' L' },
                         grid: { color:'#1a2e1a' },
                         min: 0, max: Math.max(GOAL + 0.5, ...data, 0.5),
                         suggestedMax: GOAL + 0.3 }
                }
            }
        });

        // Today's value from history
        const todayEntry = history.find(d => d.log_date === today);
        updateUI(todayEntry ? parseFloat(todayEntry.litres) : 0);
    }

    async function loadHistory(){
        try {
            const r = await fetch(BASE + 'water/history');
            const d = await r.json();
            if (d.success) buildChart(d.history);
        } catch(e){ console.warn('water history error', e); }
    }

    window.addWater = async function(litres){
        const newVal = Math.min(20, parseFloat((current + litres).toFixed(2)));
        await saveWater(newVal);
    };

    window.openWaterCustom = function(){
        const w = document.getElementById('waterCustomWrap');
        w.style.display = w.style.display === 'none' ? 'block' : 'none';
    };

    window.submitCustomWater = async function(){
        const v = parseFloat(document.getElementById('waterCustomInput').value || 0);
        if (!v || v <= 0) return;
        const newVal = Math.min(20, parseFloat((current + v).toFixed(2)));
        document.getElementById('waterCustomWrap').style.display = 'none';
        document.getElementById('waterCustomInput').value = '';
        await saveWater(newVal);
    };

    async function saveWater(litres){
        updateUI(litres); // optimistic
        try {
            const fd = new FormData();
            fd.append('litres', litres);
            fd.append('date',   today);
            const r = await fetch(BASE + 'water/log', { method:'POST', body:fd });
            const d = await r.json();
            if (d.success) loadHistory();
        } catch(e){ console.warn('water log error', e); }
    }

    loadHistory();
})();
</script>

