<?php $pageTitle = 'Dashboard'; ?>

<main class="main">

    <!-- ══ HERO BANNER ══ -->
    <div style="position:relative;border-radius:20px;overflow:hidden;margin-bottom:1.25rem;min-height:180px;display:flex;align-items:flex-end" class="hero-banner-wrap">
        <div style="position:absolute;inset:0;background-image:url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=1400&q=80');background-size:cover;background-position:center 30%;transition:transform .6s ease" id="heroBg"></div>
        <div style="position:absolute;inset:0;background:linear-gradient(to right,rgba(8,12,9,.92) 0%,rgba(8,12,9,.6) 55%,rgba(8,12,9,.15) 100%)"></div>
        <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--lime),var(--orange),transparent)"></div>
        <div style="position:relative;z-index:2;padding:2rem 2.5rem;width:100%">
            <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap">
                <div>
                    <div style="font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:var(--lime);font-family:'Syne',sans-serif;margin-bottom:.4rem;opacity:.85">Tableau de bord</div>
                    <div class="greeting-name" style="margin-bottom:.5rem">Bonjour, <em><?= $prenom ?></em> 👋</div>
                    <div class="topbar-meta" style="margin-top:0">
                        <div class="meta-pill"><i class="fas fa-bullseye"></i><?= $obj_label ?></div>
                        <div class="meta-pill"><i class="fas fa-layer-group"></i><?= $level_label ?></div>
                        <div class="meta-pill"><i class="fas fa-fire"></i><?= $today_fr ?> · <?= $today_plan[0] ?></div>
                    </div>
                </div>
                <div style="text-align:right">
                    <div class="date-block" style="margin-bottom:.5rem"><?= date('D d M Y') ?></div>
                    <button class="mobile-toggle" id="mobileToggle"><i class="fas fa-bars"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ STATS STRIP ══ -->
    <div class="stats-strip">
        <div class="stat-tile" data-emoji="⚖️">
            <div class="stat-eye">Poids actuel</div>
            <div class="stat-val"><?= $poids_actuel ?><sup>kg</sup></div>
            <div class="stat-foot">Objectif → <?= $goal_weight ?> kg</div>
        </div>
        <div class="stat-tile" data-emoji="🔥">
            <div class="stat-eye">Calories auj.</div>
            <div class="stat-val" style="color:var(--orange)"><?= number_format($today_calories) ?><sup>kcal</sup></div>
            <div class="stat-foot">Goal <?= number_format($calorie_goal) ?> kcal · <?= $cal_pct ?>%</div>
        </div>
        <div class="stat-tile" data-emoji="💪">
            <div class="stat-eye">Séances / sem</div>
            <div class="stat-val" style="color:var(--lime)"><?= $workouts_week ?><sup style="opacity:.35">/5</sup></div>
            <div class="stat-foot"><?= $total_workouts ?> séances au total</div>
        </div>
        <div class="stat-tile" data-emoji="❤️">
            <div class="stat-eye">IMC</div>
            <div class="stat-val" style="color:<?= $bmi_status[1] ?>"><?= $bmi ?></div>
            <div class="stat-foot"><?= $bmi_status[0] ?> · <?= $taille ?>cm</div>
        </div>
    </div>

    <!-- ══ CONSEILS PERSONNALISÉS ══ -->
    <?php if (!empty($conseils)): ?>
    <div style="margin-bottom:1.25rem">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem">
            <div>
                <div style="font-size:.6rem;letter-spacing:.15em;text-transform:uppercase;color:var(--muted);font-family:'Syne',sans-serif;margin-bottom:.2rem">Basé sur tes données</div>
                <div style="font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:900;letter-spacing:-.02em">Recommandations</div>
            </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:.75rem">
        <?php foreach($conseils as $c): ?>
            <div class="conseil-card" style="--conseil-col:<?= $c['col'] ?>">
                <div style="position:absolute;left:0;top:0;bottom:0;width:3px;background:<?= $c['col'] ?>;border-radius:3px 0 0 3px"></div>
                <div style="width:38px;height:38px;border-radius:10px;background:<?= $c['col'] ?>18;border:1px solid <?= $c['col'] ?>30;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-left:.25rem">
                    <i class="<?= $c['ico'] ?>" style="color:<?= $c['col'] ?>;font-size:.85rem"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:.85rem;margin-bottom:.3rem"><?= htmlspecialchars($c['titre']) ?></div>
                    <div style="font-size:.75rem;color:var(--soft);line-height:1.55"><?= htmlspecialchars($c['texte']) ?></div>
                </div>
                <div style="flex-shrink:0;background:<?= $c['col'] ?>18;border:1px solid <?= $c['col'] ?>35;border-radius:20px;padding:.2rem .65rem;font-family:'Syne',sans-serif;font-weight:700;font-size:.58rem;letter-spacing:.08em;text-transform:uppercase;color:<?= $c['col'] ?>;white-space:nowrap">
                    <?= htmlspecialchars($c['badge']) ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ══ RINGS ══ -->
    <div class="rings-row">
        <div class="ring-card" style="--ring-glow:rgba(240,168,74,.06)">
            <div class="ring-wrap">
                <svg width="80" height="80" viewBox="0 0 80 80">
                    <circle class="ring-track" cx="40" cy="40" r="<?= $r_val ?>" stroke-width="6"/>
                    <circle class="ring-fill" cx="40" cy="40" r="<?= $r_val ?>" stroke-width="6" stroke="<?= $today_calories >= $calorie_goal ? '#C8F04A' : '#F0A84A' ?>" stroke-dasharray="<?= $circ ?>" stroke-dashoffset="<?= $cal_offset ?>"/>
                </svg>
                <div class="ring-label"><span class="ring-pct" style="color:var(--orange)"><?= $cal_pct ?>%</span></div>
            </div>
            <div class="ring-info">
                <div class="ring-eyebrow">Apport calorique</div>
                <div class="ring-val" style="color:var(--orange)"><?= number_format($today_calories) ?><sup>kcal</sup></div>
                <div class="ring-sub">Goal: <?= number_format($calorie_goal) ?> kcal</div>
            </div>
            <button class="ring-btn orange" onclick="openModal('mealModal')">+ Repas</button>
        </div>
        <div class="ring-card" style="--ring-glow:rgba(200,240,74,.06)">
            <div class="ring-wrap">
                <svg width="80" height="80" viewBox="0 0 80 80">
                    <circle class="ring-track" cx="40" cy="40" r="<?= $r_val ?>" stroke-width="6"/>
                    <circle class="ring-fill" cx="40" cy="40" r="<?= $r_val ?>" stroke-width="6" stroke="#C8F04A" stroke-dasharray="<?= $circ ?>" stroke-dashoffset="<?= $wrk_offset ?>"/>
                </svg>
                <div class="ring-label"><span class="ring-pct" style="color:var(--lime)"><?= $workout_pct ?>%</span></div>
            </div>
            <div class="ring-info">
                <div class="ring-eyebrow">Objectif hebdo</div>
                <div class="ring-val" style="color:var(--lime)"><?= $workouts_week ?><sup>/5</sup></div>
                <div class="ring-sub"><?= $total_workouts ?> séances · <?= 5-$workouts_week>0?(5-$workouts_week).' restante(s)':'Objectif atteint 🎉' ?></div>
            </div>
            <button class="ring-btn" onclick="window.location.href='<?= View::base("cours") ?>?type=<?= urlencode($today_plan[0]) ?>'">Démarrer</button>
        </div>
    </div>

    <!-- ══ BENTO GRID ══ -->
    <div class="bento">
        <!-- Weekly plan -->
        <div class="bc span-2-rows">
            <div class="bc-head">
                <div class="bc-title"><i class="fas fa-calendar-week"></i>Programme Semaine</div>
                <a class="bc-link" href="<?= View::base('planning') ?>">Planning →</a>
            </div>
            <div class="bc-body" style="padding:.875rem 1rem">
                <?php
                // Map session name → activites category filter
                $planCategoryMap = [
                    'Lower Body'   => 'legs',
                    'Upper Body'   => 'chest',
                    'Full Body'    => 'abs',
                    'Cardio HIIT'  => 'cardio',
                    'Cardio'       => 'cardio',
                    'Cardio Extra' => 'cardio',
                    'Cardio Modéré'=> 'cardio',
                    'Endurance'    => 'cardio',
                    'Push'         => 'chest',
                    'Pull'         => 'back',
                    'Legs'         => 'legs',
                    'Upper Power'  => 'shoulders',
                    'Upper Volume' => 'chest',
                    'Lower Power'  => 'legs',
                    'Mobilité'     => 'abs',
                    'Repos Actif'  => 'abs',
                    'Quick Win'    => 'abs',
                    'Marche Active'=> 'cardio',
                ];
                foreach($weekly_plan as $jour => $p): $isToday = ($jour === $today_fr);
                    $cat = $planCategoryMap[$p[0]] ?? null;
                    $href = $cat
                        ? View::base('activites') . '?category=' . $cat
                        : '#'; // Repos — lien désactivé
                ?>
                <a class="day-link" href="<?= $href ?>" <?= $cat ? '' : 'style="pointer-events:none;opacity:.45"' ?>>
                    <div class="day-row <?= $isToday ? 'today' : '' ?>">
                        <div class="day-pip" style="background:<?= $p[2] ?>"></div>
                        <div class="day-lbl"><?= strtoupper(substr($jour,0,3)) ?></div>
                        <div class="day-txt">
                            <div class="day-name"><?= htmlspecialchars($p[0]) ?></div>
                            <div class="day-sub"><?= htmlspecialchars($p[3]) ?></div>
                        </div>
                        <div class="day-dur"><?= $p[1] > 0 ? $p[1].'min' : 'Repos' ?></div>
                        <?php if($isToday): ?><span class="today-pill">TODAY</span><?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <!-- Progress summary -->
            <div style="padding:1rem 1.25rem;border-top:1px solid var(--border)">
                <div style="font-family:'Syne',sans-serif;font-size:.6rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-bottom:1rem">Progression semaine</div>
                <div style="margin-bottom:.75rem">
                    <div style="display:flex;justify-content:space-between;font-size:.68rem;margin-bottom:.35rem"><span style="color:var(--soft)">Séances cette semaine</span><span style="font-family:'JetBrains Mono',monospace;color:var(--lime)"><?= $workouts_week ?>/5</span></div>
                    <div style="background:rgba(255,255,255,.05);border-radius:6px;height:5px;overflow:hidden"><div style="height:100%;width:<?= min(100,round($workouts_week/5*100)) ?>%;background:linear-gradient(90deg,var(--lime),var(--teal));border-radius:6px"></div></div>
                </div>
                <div style="margin-bottom:.875rem">
                    <div style="display:flex;justify-content:space-between;font-size:.68rem;margin-bottom:.35rem"><span style="color:var(--soft)">Calories aujourd'hui</span><span style="font-family:'JetBrains Mono',monospace;color:var(--orange)"><?= $cal_pct ?>%</span></div>
                    <div style="background:rgba(255,255,255,.05);border-radius:6px;height:5px;overflow:hidden"><div style="height:100%;width:<?= $cal_pct ?>%;background:linear-gradient(90deg,var(--orange),#f87171);border-radius:6px"></div></div>
                </div>
                <div style="display:flex;align-items:center;gap:.5rem;padding:.6rem .875rem;border-radius:10px;background:<?= $bmi_status[1] ?>18;border:1px solid <?= $bmi_status[1] ?>35">
                    <i class="fas fa-heart-pulse" style="color:<?= $bmi_status[1] ?>;font-size:.72rem"></i>
                    <span style="font-size:.7rem;color:var(--soft)">IMC</span>
                    <span style="font-family:'JetBrains Mono',monospace;font-size:.9rem;font-weight:600;color:<?= $bmi_status[1] ?>"><?= $bmi ?></span>
                    <span style="font-size:.65rem;font-family:'Syne',sans-serif;font-weight:700;color:<?= $bmi_status[1] ?>;margin-left:auto"><?= $bmi_status[0] ?></span>
                </div>
            </div>
        </div>

        <!-- Nutrition -->
        <div class="bc">
            <div class="bc-head">
                <div class="bc-title"><i class="fas fa-apple-alt"></i>Nutrition du jour</div>
                
            </div>
            <div class="bc-body">
                <div style="display:flex;justify-content:space-between;font-size:.72rem;margin-bottom:6px">
                    <span><?= number_format($today_calories) ?> kcal</span>
                    <span style="color:var(--muted)"><?= $cal_pct ?>% · <?= number_format($calorie_goal) ?></span>
                </div>
                <div class="prog-bar-wrap"><div class="prog-bar" style="width:<?= $cal_pct ?>%"></div></div>
                <?php if(!empty($today_meals)): foreach($today_meals as $m): ?>
                <div class="meal-row"><span class="meal-n"><?= htmlspecialchars($m['meal_name']) ?></span><span class="meal-c"><?= $m['calories'] ?? '?' ?></span></div>
                <?php endforeach; else: ?>
                <div class="empty-state"><i class="fas fa-utensils"></i>Aucun repas</div>
                <?php endif; ?>
                <form class="quick-add" id="quickAddMealForm">
                    <input type="text" name="meal_name" placeholder="Ajouter un repas…" required>
                    <input type="number" name="calories" placeholder="kcal" class="cal" step="10">
                    <button type="submit"><i class="fas fa-plus"></i></button>
                </form>
            </div>
        </div>

        <!-- Health / BMI -->
        <div class="bc">
            <div class="bc-head"><div class="bc-title"><i class="fas fa-heartbeat"></i>Santé</div></div>
            <div class="bc-body">
                <div style="text-align:center;padding:.5rem 0">
                    <div class="bmi-big" style="color:<?= $bmi_status[1] ?>"><?= $bmi ?></div>
                    <span class="bmi-tag" style="background:<?= $bmi_status[1] ?>22;color:<?= $bmi_status[1] ?>"><?= $bmi_status[0] ?></span>
                </div>
                <div class="bmi-details">
                    <div class="bdi-val"><?= $poids_actuel ?><small style="font-size:.7rem;opacity:.5">kg</small><div class="bdi-lbl">Poids</div></div>
                    <div class="bdi-val"><?= $taille ?><small style="font-size:.7rem;opacity:.5">cm</small><div class="bdi-lbl">Taille</div></div>
                    <div class="bdi-val"><?= $goal_weight ?><small style="font-size:.7rem;opacity:.5">kg</small><div class="bdi-lbl">Objectif</div></div>
                </div>
                <?php if(!empty($weight_history)): ?>
                <div class="chart-wrap" style="margin-top:1rem"><canvas id="weightChart"></canvas></div>
                <?php endif; ?>
                <button onclick="openModal('weightModal')" style="margin-top:1rem;width:100%;background:transparent;border:1px solid var(--border);border-radius:9px;padding:.6rem;color:var(--soft);font-size:.72rem;cursor:pointer;font-family:'Syne',sans-serif;font-weight:600;letter-spacing:.04em;text-transform:uppercase;transition:all .2s">Mettre à jour le poids</button>
            </div>
        </div>

        <!-- Reservations -->
        <div class="bc">
            <div class="bc-head">
                <div class="bc-title"><i class="fas fa-calendar-check"></i>Prochaines séances</div>
                <a class="bc-link" href="<?= View::base('reservation') ?>">Réserver →</a>
            </div>
            <div class="bc-body" style="padding:.75rem 1.4rem">
                <?php if(empty($my_reservations)): ?>
                <div class="empty-state"><i class="far fa-calendar"></i>Aucune réservation</div>
                <?php else: foreach($my_reservations as $r): ?>
                <div class="res-row">
                    <div class="res-date-block">
                        <div class="res-name"><?= htmlspecialchars($r['title']) ?></div>
                        <div class="res-meta"><?= date('H:i', strtotime($r['session_time'])) ?> · <?= ucfirst($r['category']) ?></div>
                    </div>
                    <div class="res-info">
                        <div class="res-name"><?= date('d/m/Y', strtotime($r['session_date'])) ?></div>
                        <div class="res-meta"><?= $r['price'] > 0 ? $r['price'].' DT' : 'Gratuit' ?></div>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Activity feed -->
        <div class="bc">
            <div class="bc-head"><div class="bc-title"><i class="fas fa-bolt"></i>Activité récente</div></div>
            <div style="max-height:280px;overflow-y:auto">
                <?php if(empty($activities)): ?>
                <div class="empty-state"><i class="fas fa-clock"></i>Aucune activité</div>
                <?php else: foreach($activities as $a): ?>
                <div class="feed-row">
                    <div class="feed-dot <?= $a['type']==='workout'?'fd-w':($a['type']==='meal'?'fd-n':'fd-m') ?>">
                        <i class="fas <?= $a['type']==='workout'?'fa-dumbbell':($a['type']==='meal'?'fa-utensils':'fa-weight-scale') ?>"></i>
                    </div>
                    <div class="feed-txt">
                        <div class="feed-msg"><?= htmlspecialchars($a['message']) ?></div>
                        <div class="feed-d"><?= $a['d'] ?></div>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <!-- ══ ACTION CARDS ══ -->
    <div class="actions-row">
        <div class="act-btn act-btn-lime" onclick="window.location.href='<?= View::base("cours") ?>?type=<?= urlencode($today_plan[0]) ?>'" style="background-image:url('https://images.unsplash.com/photo-1549060279-7e168fcee0c2?w=800&q=80');background-size:cover;background-position:center top">
            <div class="act-arrow act-arrow-lime"><i class="fas fa-arrow-up-right"></i></div>
            <div class="act-btn-content">
                <div class="act-badge act-badge-lime"><i class="fas fa-bolt"></i> Aujourd'hui</div>
                <div class="act-title">Lancer la séance</div>
                <div class="act-sub"><?= $today_plan[0] ?> · <?= $today_plan[1] ?> min</div>
            </div>
        </div>
        <div class="act-btn act-btn-orange" onclick="openModal('mealModal')" style="background-image:url('https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&q=80');background-size:cover;background-position:center">
            <div class="act-arrow act-arrow-orange"><i class="fas fa-arrow-up-right"></i></div>
            <div class="act-btn-content">
                <div class="act-badge act-badge-orange"><i class="fas fa-fire"></i> <?= $cal_pct ?>% objectif</div>
                <div class="act-title">Ajouter un repas</div>
                <div class="act-sub"><?= number_format($today_calories) ?> / <?= number_format($calorie_goal) ?> kcal</div>
            </div>
        </div>
        <div class="act-btn act-btn-teal" onclick="openModal('weightModal')" style="background-image:url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&q=80');background-size:cover;background-position:center">
            <div class="act-arrow act-arrow-teal"><i class="fas fa-arrow-up-right"></i></div>
            <div class="act-btn-content">
                <div class="act-badge act-badge-teal"><i class="fas fa-chart-line"></i> Suivi</div>
                <div class="act-title">Mettre à jour le poids</div>
                <div class="act-sub">Actuel · <?= $poids_actuel ?> kg → <?= $goal_weight ?> kg</div>
            </div>
        </div>
    </div>

    <!-- ══ UPDATES ══ -->
    <div class="updates-row">
        <?php foreach($updates as [$ico,$col,$tag,$title,$desc,$url]): ?>
        <?php if($url): ?><a href="<?= $url ?>" style="text-decoration:none;display:contents;"><?php endif; ?>
        <div class="upd-card" style="--upd-c:<?= $col ?><?= $url ? ';cursor:pointer' : '' ?>">
            <div class="upd-ico-wrap"><?= $ico ?></div>
            <div class="upd-body">
                <div class="upd-tag"><?= $tag ?></div>
                <div class="upd-title"><?= $title ?></div>
                <div class="upd-desc"><?= $desc ?></div>
            </div>
        </div>
        <?php if($url): ?></a><?php endif; ?>
        <?php endforeach; ?>
    </div>

</main>

<!-- ══ MODALS ══ -->
<div class="modal" id="weightModal">
    <div class="modal-box">
        <div class="modal-head"><div class="modal-title">Mettre à jour le poids</div><button class="modal-close" onclick="closeModal('weightModal')">&times;</button></div>
        <form id="updateWeightForm">
            <div class="field"><label>Poids (kg)</label><input type="number" name="poids" step="0.1" min="30" max="200" value="<?= $poids_actuel ?>" required></div>
            <div class="field"><label>Date</label><input type="date" name="date" value="<?= date('Y-m-d') ?>" required></div>
            <button type="submit" class="btn-lime">Enregistrer</button>
        </form>
    </div>
</div>

<div class="modal" id="mealModal">
    <div class="modal-box">
        <div class="modal-head"><div class="modal-title">Ajouter un repas</div><button class="modal-close" onclick="closeModal('mealModal')">&times;</button></div>
        <form id="fullAddMealForm">
            <div class="field"><label>Nom du repas</label><input type="text" name="meal_name" placeholder="Ex: Déjeuner" required></div>
            <div class="field"><label>Calories (kcal)</label><input type="number" name="calories" placeholder="450" step="10"></div>
            <button type="submit" class="btn-lime">Enregistrer</button>
        </form>
    </div>
</div>

<script>
// Hero parallax
const heroBg = document.getElementById('heroBg');
if (heroBg) { window.addEventListener('scroll', () => { heroBg.style.transform = `translateY(${window.scrollY * 0.25}px)`; }, { passive: true }); }

// Meal forms
async function addMeal(form) {
    const fd = new FormData(form);
    fd.append('action', 'add_meal');
    const r = await fetch('<?= View::base("nutrition/add") ?>', { method: 'POST', body: fd });
    const d = await r.json();
    if (d.success) location.reload();
    else alert(d.error || 'Erreur');
}
document.getElementById('quickAddMealForm')?.addEventListener('submit', e => { e.preventDefault(); addMeal(e.target); });
document.getElementById('fullAddMealForm')?.addEventListener('submit',  e => { e.preventDefault(); addMeal(e.target); closeModal('mealModal'); });

// Weight form
document.getElementById('updateWeightForm')?.addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'update_weight');
    const r = await fetch('<?= View::base("weight/update") ?>', { method: 'POST', body: fd });
    const d = await r.json();
    if (d.success) location.reload();
    else alert(d.error || 'Erreur');
});

// Weight chart
<?php if (!empty($weight_history)): ?>
const ctx = document.getElementById('weightChart');
if (ctx) {
    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $weight_labels ?>,
            datasets: [{
                data: <?= $weight_values ?>,
                borderColor: '#C8F04A', borderWidth: 2,
                pointBackgroundColor: '#C8F04A', pointRadius: 3,
                tension: .4, fill: true,
                backgroundColor: c => {
                    const g = c.chart.ctx.createLinearGradient(0,0,0,100);
                    g.addColorStop(0,'rgba(200,240,74,.12)'); g.addColorStop(1,'rgba(200,240,74,0)');
                    return g;
                }
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#131A14', borderColor: 'rgba(200,240,74,.2)', borderWidth: 1, titleColor: '#C8F04A', bodyColor: '#E4EDDF' } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: '#3D5240', font: { size: 9 } } },
                y: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: '#3D5240', font: { size: 9 } } }
            }
        }
    });
}
<?php endif; ?>
</script>
