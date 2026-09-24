<?php $pageTitle = 'Admin Dashboard'; ?>
<style>
/* ── Admin Dashboard Styles ───────────────────────────── */
.admin-wrap       { padding: 2rem 2rem 2rem 6rem; min-height:100vh; }
.admin-header     { display:flex; align-items:center; justify-content:space-between; margin-bottom:2.5rem; padding-top:1.5rem; }
.admin-title      { font-size:2rem; font-weight:800; color:#e8f5e9; }
.admin-title span { color:#C8F04A; }
.admin-badge      { background:#C8F04A22; border:1px solid #C8F04A55; color:#C8F04A; font-size:.7rem; padding:.3rem .8rem; border-radius:20px; font-weight:700; letter-spacing:.08em; }

/* Tabs */
.admin-tabs       { display:flex; gap:.5rem; margin-bottom:2rem; border-bottom:1px solid #1e2b1e; padding-bottom:0; }
.admin-tab        { padding:.65rem 1.4rem; border-radius:8px 8px 0 0; font-size:.82rem; font-weight:600; cursor:pointer; border:none; background:transparent; color:#7a9e7a; transition:.2s; border-bottom:2px solid transparent; }
.admin-tab.active { color:#C8F04A; border-bottom-color:#C8F04A; background:#C8F04A0d; }
.admin-tab:hover:not(.active) { color:#b0d4b0; background:#ffffff08; }
.tab-panel        { display:none; }
.tab-panel.active { display:block; }

/* Stat cards */
.stat-grid        { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:2rem; }
.stat-card        { background:#0f1f0f; border:1px solid #1e2b1e; border-radius:14px; padding:1.4rem 1.6rem; position:relative; overflow:hidden; }
.stat-card::before{ content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--accent,#C8F04A); }
.stat-card.orange::before { background:#F0A84A; }
.stat-card.blue::before   { background:#4AF0D8; }
.stat-card.red::before    { background:#f87171; }
.stat-val         { font-size:2.2rem; font-weight:800; color:#e8f5e9; line-height:1; margin-bottom:.3rem; }
.stat-lbl         { font-size:.73rem; color:#7a9e7a; text-transform:uppercase; letter-spacing:.06em; font-weight:600; }
.stat-sub         { font-size:.72rem; color:#4a7a4a; margin-top:.5rem; }

/* Section titles */
.section-title    { font-size:.95rem; font-weight:700; color:#c8e6c9; margin-bottom:1rem; display:flex; align-items:center; gap:.5rem; }
.section-title i  { color:#C8F04A; font-size:.85rem; }

/* Charts row */
.charts-row       { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; margin-bottom:2rem; }
.chart-card       { background:#0f1f0f; border:1px solid #1e2b1e; border-radius:14px; padding:1.4rem; }
.chart-card canvas{ max-height:160px; }

/* Messages list */
.msg-list         { display:flex; flex-direction:column; gap:.6rem; }
.msg-row          { background:#0f1f0f; border:1px solid #1e2b1e; border-radius:10px; padding:1rem 1.3rem; display:flex; align-items:center; gap:1rem; transition:.15s; }
.msg-row:hover    { border-color:#2e3b2e; }
.msg-row.unread   { border-left:3px solid #F0A84A; }
.msg-avatar       { width:36px; height:36px; border-radius:50%; background:#1e3a1e; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.85rem; color:#C8F04A; flex-shrink:0; }
.msg-info         { flex:1; min-width:0; }
.msg-name         { font-size:.85rem; font-weight:600; color:#e8f5e9; }
.msg-preview      { font-size:.75rem; color:#5a8a5a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.msg-date         { font-size:.7rem; color:#3a5a3a; flex-shrink:0; }
.msg-badge        { font-size:.65rem; padding:.2rem .6rem; border-radius:20px; font-weight:700; flex-shrink:0; }
.msg-badge.unread { background:#F0A84A22; color:#F0A84A; border:1px solid #F0A84A44; }
.msg-badge.read   { background:#C8F04A11; color:#7a9e7a; border:1px solid #C8F04A22; }
.view-all-link    { display:inline-flex; align-items:center; gap:.4rem; margin-top:1rem; font-size:.78rem; color:#C8F04A; text-decoration:none; font-weight:600; }
.view-all-link:hover { text-decoration:underline; }

/* Users table */
.admin-table      { width:100%; border-collapse:collapse; font-size:.82rem; }
.admin-table th   { text-align:left; padding:.7rem 1rem; color:#5a8a5a; font-size:.7rem; text-transform:uppercase; letter-spacing:.07em; font-weight:700; border-bottom:1px solid #1e2b1e; }
.admin-table td   { padding:.75rem 1rem; border-bottom:1px solid #111f11; color:#c8e6c9; vertical-align:middle; }
.admin-table tr:hover td { background:#0d190d; }
.role-badge       { display:inline-block; padding:.2rem .65rem; border-radius:20px; font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
.role-admin       { background:#f87171 22; color:#f87171; border:1px solid #f8717144; }
.role-salle       { background:#F0A84A22; color:#F0A84A; border:1px solid #F0A84A44; }
.role-user        { background:#C8F04A11; color:#C8F04A; border:1px solid #C8F04A33; }
.status-actif     { color:#C8F04A; font-size:.72rem; }
.status-inactif   { color:#f87171; font-size:.72rem; }
.table-wrap       { background:#0f1f0f; border:1px solid #1e2b1e; border-radius:14px; overflow:hidden; }
.table-toolbar    { padding:1rem 1.3rem; display:flex; align-items:center; gap:1rem; border-bottom:1px solid #1e2b1e; }
.table-search     { background:#071007; border:1px solid #1e2b1e; border-radius:8px; padding:.5rem .9rem; color:#c8e6c9; font-size:.82rem; width:240px; outline:none; }
.table-search:focus { border-color:#C8F04A55; }

/* Role select form */
.role-select      { background:#071007; border:1px solid #1e2b1e; color:#c8e6c9; border-radius:6px; padding:.25rem .5rem; font-size:.75rem; cursor:pointer; outline:none; }
.role-select:focus{ border-color:#C8F04A55; }
.btn-xs           { padding:.25rem .7rem; border-radius:6px; border:none; font-size:.72rem; font-weight:700; cursor:pointer; transition:.15s; }
.btn-xs-green     { background:#C8F04A22; color:#C8F04A; border:1px solid #C8F04A44; }
.btn-xs-green:hover { background:#C8F04A33; }
.btn-xs-red       { background:#f8717122; color:#f87171; border:1px solid #f8717144; }
.btn-xs-red:hover { background:#f8717133; }

/* Alert bar */
.alert-bar        { padding:.75rem 1.2rem; border-radius:10px; margin-bottom:1.5rem; font-size:.82rem; font-weight:600; }
.alert-success    { background:#C8F04A11; border:1px solid #C8F04A44; color:#C8F04A; }
.alert-error      { background:#f8717111; border:1px solid #f8717144; color:#f87171; }

/* Mini doughnut wrapper */
.mini-donuts      { display:flex; gap:1rem; flex-wrap:wrap; }
.mini-donut-item  { flex:1; min-width:120px; }
.mini-donut-lbl   { font-size:.7rem; color:#5a8a5a; text-align:center; margin-top:.5rem; }

@media(max-width:900px) {
    .admin-wrap   { padding:1.5rem 1rem 1.5rem 5rem; }
    .charts-row   { grid-template-columns:1fr; }
}
</style>

<div class="admin-wrap">

    <div class="admin-header">
        <div>
            <div class="admin-title">Admin <span>Dashboard</span></div>
            <div style="color:#5a8a5a;font-size:.82rem;margin-top:.3rem;">
                <?= date('l d F Y') ?> — Connecté en tant que
                <strong style="color:#C8F04A"><?= htmlspecialchars(Auth::user()['prenom'] . ' ' . Auth::user()['nom']) ?></strong>
            </div>
        </div>
        <span class="admin-badge">⚡ ADMIN</span>
    </div>

    <?php
    $tab = $_GET['tab'] ?? 'overview';
    $success = isset($_GET['success']);
    $error   = $_GET['error'] ?? null;
    $errorMsg = match($error) {
        'invalid_role'  => 'Rôle invalide.',
        'self_demotion' => 'Vous ne pouvez pas retirer votre propre rôle admin.',
        'self_ban'      => 'Vous ne pouvez pas désactiver votre propre compte.',
        default         => null,
    };
    ?>

    <?php if ($success): ?>
    <div class="alert-bar alert-success">✓ Modification enregistrée avec succès.</div>
    <?php elseif ($errorMsg): ?>
    <div class="alert-bar alert-error">⚠ <?= $errorMsg ?></div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="admin-tabs">
        <button class="admin-tab <?= $tab==='overview' ? 'active' : '' ?>" onclick="switchTab('overview')">
            <i class="fas fa-chart-pie"></i> Vue Générale
        </button>
        <button class="admin-tab <?= $tab==='users' ? 'active' : '' ?>" onclick="switchTab('users')">
            <i class="fas fa-users"></i> Utilisateurs
            <span style="background:#C8F04A22;color:#C8F04A;font-size:.65rem;padding:.15rem .5rem;border-radius:20px;margin-left:.4rem;"><?= $totalUsers ?></span>
        </button>
        <button class="admin-tab <?= $tab==='messages' ? 'active' : '' ?>" onclick="switchTab('messages')">
            <i class="fas fa-envelope"></i> Messages
            <?php if ($contactCounts['unread'] > 0): ?>
            <span style="background:#F0A84A22;color:#F0A84A;font-size:.65rem;padding:.15rem .5rem;border-radius:20px;margin-left:.4rem;"><?= $contactCounts['unread'] ?> non lus</span>
            <?php endif; ?>
        </button>
        <button class="admin-tab <?= $tab==='settings' ? 'active' : '' ?>" onclick="switchTab('settings')">
            <i class="fas fa-gear"></i> Paramètres
        </button>
        <button class="admin-tab <?= $tab==='seances' ? 'active' : '' ?>" onclick="switchTab('seances')">
            <i class="fas fa-calendar-check"></i> Séances
            <span style="background:#4AF0D822;color:#4AF0D8;font-size:.65rem;padding:.15rem .5rem;border-radius:20px;margin-left:.4rem;"><?= $totalGymSessions ?? 0 ?></span>
        </button>
    </div>

    <!-- ══ TAB: OVERVIEW ══════════════════════════════════ -->
    <div class="tab-panel <?= $tab==='overview' ? 'active' : '' ?>" id="tab-overview">

        <!-- Top stat cards -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-val"><?= $totalUsers ?></div>
                <div class="stat-lbl">Utilisateurs Total</div>
                <div class="stat-sub">+<?= $newThisMonth ?> ce mois · +<?= $newToday ?> aujourd'hui</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-val"><?= $contactCounts['all'] ?></div>
                <div class="stat-lbl">Messages Contact</div>
                <div class="stat-sub"><?= $contactCounts['unread'] ?> non lus</div>
            </div>

            <div class="stat-card">
                <div class="stat-val"><?= $totalAvis ?></div>
                <div class="stat-lbl">Avis / Reviews</div>
                <div class="stat-sub">Note moyenne : <?= $avgNote ?>/5 ⭐</div>
            </div>
            <div class="stat-card">
                <div class="stat-val"><?= $byRole['admin'] ?? 0 ?></div>
                <div class="stat-lbl">Admins</div>
                <div class="stat-sub"><?= $byRole['salle'] ?? 0 ?> compte(s) salle</div>
            </div>
            <div class="stat-card blue" onclick="switchTab('seances')" style="cursor:pointer;" title="Voir les salles">
                <div class="stat-val"><?= $totalSalles ?? 0 ?></div>
                <div class="stat-lbl">Salles de sport <i class="fas fa-arrow-right" style="font-size:.7rem;margin-left:.3rem;"></i></div>
                <div class="stat-sub"><?= $totalGymSessions ?? 0 ?> séances publiées</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-val"><?= $newThisMonth ?></div>
                <div class="stat-lbl">Inscrits ce mois</div>
                <div class="stat-sub"><?= $newToday ?> aujourd'hui</div>
            </div>
        </div>

        <!-- Charts row -->
        <div class="charts-row">
            <!-- Registrations bar chart -->
            <div class="chart-card">
                <div class="section-title"><i class="fas fa-user-plus"></i> Inscriptions — 7 jours</div>
                <canvas id="regChart"></canvas>
            </div>
            <!-- Objectif donut -->
            <div class="chart-card">
                <div class="section-title"><i class="fas fa-bullseye"></i> Objectifs</div>
                <canvas id="objChart"></canvas>
            </div>
            <!-- Niveau donut -->
            <div class="chart-card">
                <div class="section-title"><i class="fas fa-layer-group"></i> Niveaux</div>
                <canvas id="nivChart"></canvas>
            </div>
        </div>

        <!-- Latest messages preview -->
        <div class="section-title" style="margin-bottom:.8rem;"><i class="fas fa-inbox"></i> Derniers messages</div>
        <?php if (empty($latestMessages)): ?>
        <div style="color:#3a5a3a;font-size:.82rem;padding:1.5rem 0;">Aucun message reçu.</div>
        <?php else: ?>
        <div class="msg-list">
            <?php foreach ($latestMessages as $m): ?>
            <div class="msg-row <?= $m['lu'] ? '' : 'unread' ?>">
                <div class="msg-avatar"><?= strtoupper(substr($m['nom'],0,1)) ?></div>
                <div class="msg-info">
                    <div class="msg-name"><?= htmlspecialchars($m['nom']) ?> <span style="color:#3a5a3a;font-size:.7rem;">&lt;<?= htmlspecialchars($m['email']) ?>&gt;</span></div>
                    <div class="msg-preview"><?= htmlspecialchars(mb_strimwidth($m['message'],0,90,'…')) ?></div>
                </div>
                <span class="msg-badge <?= $m['lu'] ? 'read' : 'unread' ?>"><?= $m['lu'] ? 'Lu' : 'Non lu' ?></span>
                <div class="msg-date"><?= date('d/m H:i', strtotime($m['created_at'])) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <a class="view-all-link" href="<?= View::base('admin/contacts') ?>">Voir tous les messages →</a>
        <?php endif; ?>

        <!-- Latest users -->
        <div class="section-title" style="margin:2rem 0 .8rem;"><i class="fas fa-user-clock"></i> Derniers inscrits</div>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th><th>Nom</th><th>Email</th>
                        <th>Rôle</th><th>Objectif</th><th>Niveau</th><th>Inscrit le</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestUsers as $u): ?>
                    <tr>
                        <td style="color:#3a5a3a"><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['prenom'].' '.$u['nom']) ?></td>
                        <td style="color:#5a8a5a"><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="role-badge role-<?= $u['role'] ?? 'user' ?>"><?= ucfirst($u['role'] ?? 'user') ?></span></td>
                        <td style="color:#7a9e7a"><?= ucfirst($u['objectif'] ?? '—') ?></td>
                        <td style="color:#7a9e7a"><?= ucfirst($u['niveau'] ?? '—') ?></td>
                        <td style="color:#3a5a3a"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══ TAB: USERS ════════════════════════════════════ -->
    <div class="tab-panel <?= $tab==='users' ? 'active' : '' ?>" id="tab-users">

        <div class="stat-grid" style="margin-bottom:1.5rem;">
            <div class="stat-card">
                <div class="stat-val"><?= $totalUsers ?></div>
                <div class="stat-lbl">Total utilisateurs</div>
            </div>
            <div class="stat-card red">
                <div class="stat-val"><?= $byRole['admin'] ?? 0 ?></div>
                <div class="stat-lbl">Admins</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-val"><?= $byRole['salle'] ?? 0 ?></div>
                <div class="stat-lbl">Salles</div>
            </div>
            <div class="stat-card">
                <div class="stat-val"><?= $byRole['user'] ?? $totalUsers ?></div>
                <div class="stat-lbl">Utilisateurs</div>
            </div>
        </div>

        <div class="table-wrap">
            <div class="table-toolbar">
                <input class="table-search" type="text" id="userSearch" placeholder="🔍 Rechercher par nom ou email…" onkeyup="filterUsers()">
                <span style="color:#3a5a3a;font-size:.75rem;margin-left:auto;"><?= $totalUsers ?> utilisateurs</span>
            </div>
            <table class="admin-table" id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Objectif</th>
                        <th>Dernière conn.</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($allUsers as $u):
                    $isMe = ((int)$u['id'] === Auth::userId());
                    $statut = $u['statut'] ?? 'actif';
                ?>
                    <tr>
                        <td style="color:#3a5a3a"><?= $u['id'] ?></td>
                        <td>
                            <div style="font-weight:600;color:#e8f5e9"><?= htmlspecialchars($u['prenom'].' '.$u['nom']) ?></div>
                            <?php if ($isMe): ?><div style="font-size:.65rem;color:#C8F04A;">Vous</div><?php endif; ?>
                        </td>
                        <td style="color:#5a8a5a"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <?php if (!$isMe): ?>
                            <form method="POST" action="<?= View::base('admin/update-role') ?>" style="display:flex;align-items:center;gap:.5rem;">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <select name="role" class="role-select" onchange="this.form.submit()">
                                    <option value="user"  <?= ($u['role']??'user')==='user'  ? 'selected':'' ?>>User</option>
                                    <option value="salle" <?= ($u['role']??'')==='salle' ? 'selected':'' ?>>Salle</option>
                                    <option value="admin" <?= ($u['role']??'')==='admin' ? 'selected':'' ?>>Admin</option>
                                </select>
                            </form>
                            <?php else: ?>
                            <span class="role-badge role-admin">Admin</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-<?= $statut ?>"><?= $statut === 'actif' ? '● Actif' : '○ Inactif' ?></span>
                        </td>
                        <td style="color:#7a9e7a"><?= ucfirst($u['objectif'] ?? '—') ?></td>
                        <td style="color:#3a5a3a;font-size:.75rem;">
                            <?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : '—' ?>
                        </td>
                        <td>
                            <?php if (!$isMe): ?>
                            <form method="POST" action="<?= View::base('admin/toggle-status') ?>" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn-xs <?= $statut==='actif' ? 'btn-xs-red' : 'btn-xs-green' ?>"
                                    onclick="return confirm('<?= $statut==='actif' ? 'Désactiver' : 'Réactiver' ?> cet utilisateur ?')">
                                    <?= $statut==='actif' ? 'Désactiver' : 'Réactiver' ?>
                                </button>
                            </form>
                            <?php else: ?>
                            <span style="color:#2a4a2a;font-size:.72rem;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══ TAB: MESSAGES ═════════════════════════════════ -->
    <div class="tab-panel <?= $tab==='messages' ? 'active' : '' ?>" id="tab-messages">

        <div class="stat-grid" style="margin-bottom:1.5rem;">
            <div class="stat-card">
                <div class="stat-val"><?= $contactCounts['all'] ?></div>
                <div class="stat-lbl">Messages Total</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-val"><?= $contactCounts['unread'] ?></div>
                <div class="stat-lbl">Non lus</div>
            </div>
            <div class="stat-card">
                <div class="stat-val"><?= $contactCounts['read'] ?></div>
                <div class="stat-lbl">Lus</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="section-title" style="margin:0;"><i class="fas fa-inbox"></i> Boîte de réception</div>
            <a href="<?= View::base('admin/contacts') ?>" class="btn-xs btn-xs-green" style="padding:.5rem 1.2rem;font-size:.8rem;">
                Gérer tous les messages →
            </a>
        </div>

        <?php if (empty($latestMessages)): ?>
        <div style="color:#3a5a3a;padding:3rem 0;text-align:center;">
            <div style="font-size:2rem;margin-bottom:.5rem;">📭</div>
            Aucun message reçu pour l'instant.
        </div>
        <?php else: ?>
        <div class="msg-list">
            <?php foreach ($latestMessages as $m): ?>
            <div class="msg-row <?= $m['lu'] ? '' : 'unread' ?>">
                <div class="msg-avatar"><?= strtoupper(substr($m['nom'],0,1)) ?></div>
                <div class="msg-info">
                    <div class="msg-name"><?= htmlspecialchars($m['nom']) ?>
                        <span style="color:#3a5a3a;font-size:.7rem;">&lt;<?= htmlspecialchars($m['email']) ?>&gt;</span>
                        <?php if ($m['telephone']): ?>
                        <span style="color:#4a7a4a;font-size:.7rem;"> · <?= htmlspecialchars($m['telephone']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($m['sujet']): ?>
                    <div style="font-size:.72rem;color:#F0A84A;margin-bottom:.2rem;">Sujet : <?= htmlspecialchars($m['sujet']) ?></div>
                    <?php endif; ?>
                    <div class="msg-preview"><?= htmlspecialchars(mb_strimwidth($m['message'],0,100,'…')) ?></div>
                </div>
                <span class="msg-badge <?= $m['lu'] ? 'read' : 'unread' ?>"><?= $m['lu'] ? '✓ Lu' : '● Non lu' ?></span>
                <div class="msg-date"><?= date('d/m/Y', strtotime($m['created_at'])) ?><br><span style="font-size:.65rem;"><?= date('H:i', strtotime($m['created_at'])) ?></span></div>
                <a href="<?= View::base('admin/contacts') ?>?open=<?= $m['id'] ?>" class="btn-xs btn-xs-green">Ouvrir</a>
            </div>
            <?php endforeach; ?>
        </div>
        <a class="view-all-link" href="<?= View::base('admin/contacts') ?>">Voir et gérer tous les messages →</a>
        <?php endif; ?>
    </div>

    <!-- ══ TAB: SÉANCES ══════════════════════════════════ -->
    <div class="tab-panel <?= $tab==='seances' ? 'active' : '' ?>" id="tab-seances">

        <!-- Salles list -->
        <?php if (!empty($allSalles)): ?>
        <div style="margin-bottom:1.5rem;">
            <div class="section-title" style="margin-bottom:.8rem;"><i class="fas fa-building"></i> Salles partenaires (<?= count($allSalles) ?>)</div>
            <div style="display:flex;flex-wrap:wrap;gap:1rem;">
            <?php foreach ($allSalles as $sl):
                $slPhoto = !empty($sl['cover_photo']) ? View::base($sl['cover_photo']) : null;
                $slName  = htmlspecialchars($sl['gym_name'] ?: $sl['prenom'].' '.$sl['nom']);
            ?>
            <div style="background:#0f1f0f;border:1px solid #1e2b1e;border-radius:14px;overflow:hidden;
                        display:flex;align-items:center;gap:1rem;padding:.8rem 1.2rem;min-width:260px;flex:1;">
                <div style="width:50px;height:50px;border-radius:10px;flex-shrink:0;
                            background:<?= $slPhoto ? "url('$slPhoto') center/cover" : '#1e3a1e' ?>;
                            display:flex;align-items:center;justify-content:center;color:#C8F04A;font-size:1.2rem;">
                    <?php if (!$slPhoto): ?><i class="fas fa-building"></i><?php endif; ?>
                </div>
                <div>
                    <div style="font-weight:700;color:#e0ede0;font-size:.9rem;"><?= $slName ?></div>
                    <?php if ($sl['location']): ?>
                    <div style="font-size:.75rem;color:#5a8a5a;"><i class="fas fa-map-marker-alt" style="color:#4AF0D8;"></i> <?= htmlspecialchars($sl['location']) ?></div>
                    <?php endif; ?>
                    <?php if ($sl['phone']): ?>
                    <div style="font-size:.72rem;color:#3a5a3a;"><i class="fas fa-phone" style="color:#4AF0D8;"></i> <?= htmlspecialchars($sl['phone']) ?></div>
                    <?php endif; ?>
                    <div style="font-size:.72rem;color:#3a5a3a;"><?= htmlspecialchars($sl['email']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (empty($allGymSessions)): ?>
        <div style="text-align:center;padding:3rem;background:#0f1f0f;border:1px dashed #1e2b1e;border-radius:14px;">
            <i class="fas fa-calendar-times" style="font-size:2.5rem;color:#2e5a2e;margin-bottom:1rem;display:block;"></i>
            <p style="color:#5a8a5a;">Aucune séance publiée par les salles pour le moment.</p>
        </div>
        <?php else: ?>

        <div class="table-wrap">
            <div class="table-toolbar" style="justify-content:space-between;">
                <span style="font-size:.82rem;color:#7a9e7a;"><i class="fas fa-calendar-check"></i> <?= $totalGymSessions ?> séance(s)</span>
                <input class="table-search" type="text" id="seanceSearch" placeholder="🔍 Rechercher..." onkeyup="filterSeances()">
            </div>
            <table class="admin-table" id="seanceTable">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Salle</th>
                        <th>Catégorie</th>
                        <th>Date & Heure</th>
                        <th>Prix</th>
                        <th>Places</th>
                        <th>Réservations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $catColors = [
                        'gym'   => '#A3E635','yoga'  => '#818CF8',
                        'padel' => '#34D399','dance' => '#F472B6','rpm' => '#F59E0B',
                    ];
                    foreach ($allGymSessions as $gs):
                        $reserved = (int)$gs['reserved_count'];
                        $capacity = (int)($gs['capacity'] ?? 30);
                        $pct      = $capacity > 0 ? min(100, round(($reserved / $capacity) * 100)) : 0;
                        $barColor = $pct >= 90 ? '#f87171' : ($pct >= 60 ? '#F59E0B' : '#34D399');
                        $color    = $catColors[$gs['category']] ?? '#C8F04A';
                    ?>
                    <tr>
                        <td style="font-weight:600;color:#e0ede0;"><?= htmlspecialchars($gs['title']) ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.6rem;">
                                <?php $sp = $gs['salle_photo'] ?? null; ?>
                                <div style="width:32px;height:32px;border-radius:8px;flex-shrink:0;
                                            background:<?= $sp ? "url('".View::base($sp)."') center/cover" : '#1e3a1e' ?>;
                                            display:flex;align-items:center;justify-content:center;color:#4AF0D8;font-size:.8rem;">
                                    <?php if (!$sp): ?><i class="fas fa-building"></i><?php endif; ?>
                                </div>
                                <div>
                                    <div style="color:#c8e6c9;font-size:.82rem;"><?= htmlspecialchars($gs['salle_name']) ?></div>
                                    <?php if (!empty($gs['salle_location'])): ?>
                                    <div style="color:#3a5a3a;font-size:.7rem;"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($gs['salle_location']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="background:<?= $color ?>18;color:<?= $color ?>;border:1px solid <?= $color ?>33;
                                         padding:.15rem .5rem;border-radius:999px;font-size:.7rem;font-weight:700;">
                                <?= ucfirst($gs['category']) ?>
                            </span>
                        </td>
                        <td style="color:#7a9e7a;font-size:.8rem;">
                            <?= date('d M Y', strtotime($gs['session_date'])) ?> · <?= date('H:i', strtotime($gs['session_time'])) ?>
                        </td>
                        <td style="font-family:monospace;color:#C8F04A;"><?= number_format($gs['price'], 2) ?> DT</td>
                        <td>
                            <div style="font-size:.75rem;color:<?= $barColor ?>;font-weight:700;margin-bottom:.3rem;">
                                <?= $reserved ?>/<?= $capacity ?>
                            </div>
                            <div style="width:80px;background:#1e2b1e;border-radius:999px;height:4px;">
                                <div style="width:<?= $pct ?>%;height:100%;background:<?= $barColor ?>;border-radius:999px;"></div>
                            </div>
                        </td>
                        <td>
                            <span style="background:#C8F04A18;color:#C8F04A;border:1px solid #C8F04A33;
                                         padding:.15rem .5rem;border-radius:999px;font-size:.75rem;">
                                <?= $reserved ?> réservation(s)
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>

    </div>

    <!-- ── Settings tab ──────────────────────────────── -->
    <div class="tab-panel <?= $tab==='settings' ? 'active' : '' ?>" id="tab-settings">
        <div style="max-width:480px;">
            <div class="section-title"><i class="fas fa-gear"></i> Mes informations</div>

            <?php if (isset($_GET['settings_success'])): ?>
            <div style="background:#C8F04A11;border:1px solid #C8F04A44;color:#C8F04A;border-radius:10px;padding:.75rem 1rem;margin-bottom:1.2rem;font-size:.82rem;">
                <i class="fas fa-check-circle"></i> Informations mises à jour.
            </div>
            <?php elseif (isset($_GET['settings_error'])): ?>
            <div style="background:#f8717111;border:1px solid #f8717144;color:#f87171;border-radius:10px;padding:.75rem 1rem;margin-bottom:1.2rem;font-size:.82rem;">
                <i class="fas fa-exclamation-circle"></i>
                <?= $_GET['settings_error'] === 'email_taken' ? 'Cet email est déjà utilisé.' : 'Erreur lors de la mise à jour.' ?>
            </div>
            <?php endif; ?>

            <!-- Name & Email -->
            <form method="POST" action="<?= View::base('admin/settings') ?>"
                  style="background:#0f1f0f;border:1px solid #1e2b1e;border-radius:14px;padding:1.6rem;margin-bottom:1.2rem;">
                <input type="hidden" name="section" value="info">
                <div style="display:flex;flex-direction:column;gap:1rem;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label style="font-size:.72rem;color:#7a9e7a;font-weight:600;display:block;margin-bottom:.4rem;text-transform:uppercase;letter-spacing:.05em;">Prénom</label>
                            <input type="text" name="prenom" value="<?= htmlspecialchars($adminUser['prenom'] ?? '') ?>" required
                                   style="width:100%;box-sizing:border-box;background:#071007;border:1px solid #1e2b1e;border-radius:8px;padding:.6rem .9rem;color:#e8f5e9;font-size:.85rem;outline:none;">
                        </div>
                        <div>
                            <label style="font-size:.72rem;color:#7a9e7a;font-weight:600;display:block;margin-bottom:.4rem;text-transform:uppercase;letter-spacing:.05em;">Nom</label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($adminUser['nom'] ?? '') ?>" required
                                   style="width:100%;box-sizing:border-box;background:#071007;border:1px solid #1e2b1e;border-radius:8px;padding:.6rem .9rem;color:#e8f5e9;font-size:.85rem;outline:none;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:.72rem;color:#7a9e7a;font-weight:600;display:block;margin-bottom:.4rem;text-transform:uppercase;letter-spacing:.05em;">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($adminUser['email'] ?? '') ?>" required
                               style="width:100%;box-sizing:border-box;background:#071007;border:1px solid #1e2b1e;border-radius:8px;padding:.6rem .9rem;color:#e8f5e9;font-size:.85rem;outline:none;">
                    </div>
                    <button type="submit"
                            style="background:#C8F04A;color:#071007;border:none;border-radius:8px;padding:.65rem 1.4rem;font-weight:700;font-size:.82rem;cursor:pointer;align-self:flex-start;">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>

            <!-- Password -->
            <form method="POST" action="<?= View::base('admin/settings') ?>"
                  style="background:#0f1f0f;border:1px solid #1e2b1e;border-radius:14px;padding:1.6rem;">
                <input type="hidden" name="section" value="password">
                <div class="section-title" style="margin-bottom:1rem;"><i class="fas fa-lock"></i> Changer le mot de passe</div>
                <div style="display:flex;flex-direction:column;gap:1rem;">
                    <div>
                        <label style="font-size:.72rem;color:#7a9e7a;font-weight:600;display:block;margin-bottom:.4rem;text-transform:uppercase;letter-spacing:.05em;">Mot de passe actuel</label>
                        <input type="password" name="current_password" required
                               style="width:100%;box-sizing:border-box;background:#071007;border:1px solid #1e2b1e;border-radius:8px;padding:.6rem .9rem;color:#e8f5e9;font-size:.85rem;outline:none;">
                    </div>
                    <div>
                        <label style="font-size:.72rem;color:#7a9e7a;font-weight:600;display:block;margin-bottom:.4rem;text-transform:uppercase;letter-spacing:.05em;">Nouveau mot de passe</label>
                        <input type="password" name="new_password" minlength="8" required
                               style="width:100%;box-sizing:border-box;background:#071007;border:1px solid #1e2b1e;border-radius:8px;padding:.6rem .9rem;color:#e8f5e9;font-size:.85rem;outline:none;">
                    </div>
                    <div>
                        <label style="font-size:.72rem;color:#7a9e7a;font-weight:600;display:block;margin-bottom:.4rem;text-transform:uppercase;letter-spacing:.05em;">Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" minlength="8" required
                               style="width:100%;box-sizing:border-box;background:#071007;border:1px solid #1e2b1e;border-radius:8px;padding:.6rem .9rem;color:#e8f5e9;font-size:.85rem;outline:none;">
                    </div>
                    <button type="submit"
                            style="background:#1e2b1e;color:#C8F04A;border:1px solid #C8F04A44;border-radius:8px;padding:.65rem 1.4rem;font-weight:700;font-size:.82rem;cursor:pointer;align-self:flex-start;">
                        <i class="fas fa-key"></i> Changer le mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>

</div><!-- /admin-wrap -->

<script>
/* ── Tab switching ─────────────────────────────────── */
function switchTab(name) {
    document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelector(`#tab-${name}`).classList.add('active');
    event.currentTarget.classList.add('active');
    history.replaceState(null, '', '?tab=' + name);
}

/* ── User search filter ────────────────────────────── */
function filterSeances() {
    const q = document.getElementById('seanceSearch').value.toLowerCase();
    document.querySelectorAll('#seanceTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
function filterUsers() {
    const q = document.getElementById('userSearch').value.toLowerCase();
    document.querySelectorAll('#usersTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

/* ── Charts ────────────────────────────────────────── */
const chartDefaults = {
    plugins: { legend: { labels: { color:'#7a9e7a', font:{size:11} } } },
    scales:  { x: { ticks:{color:'#5a8a5a'}, grid:{color:'#1e2b1e'} },
               y: { ticks:{color:'#5a8a5a'}, grid:{color:'#1e2b1e'}, beginAtZero:true } }
};

// Registrations bar chart
new Chart(document.getElementById('regChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($regChart,'date')) ?>,
        datasets: [{
            label: 'Inscriptions',
            data: <?= json_encode(array_column($regChart,'count')) ?>,
            backgroundColor: '#C8F04A44',
            borderColor: '#C8F04A',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: { ...chartDefaults, plugins: { legend: { display:false } } }
});

// Objectif donut
new Chart(document.getElementById('objChart'), {
    type: 'doughnut',
    data: {
        labels: ['Perte','Masse','Maintien'],
        datasets: [{
            data: [
                <?= $byObjectif['perte']   ?? 0 ?>,
                <?= $byObjectif['masse']   ?? 0 ?>,
                <?= $byObjectif['maintien']?? 0 ?>,
            ],
            backgroundColor: ['#f8717166','#F0A84A66','#C8F04A66'],
            borderColor:     ['#f87171',  '#F0A84A',  '#C8F04A'],
            borderWidth: 2,
        }]
    },
    options: { plugins: { legend: { labels: { color:'#7a9e7a', font:{size:11} } } }, cutout:'65%' }
});

// Niveau donut
new Chart(document.getElementById('nivChart'), {
    type: 'doughnut',
    data: {
        labels: ['Débutant','Intermédiaire','Avancé'],
        datasets: [{
            data: [
                <?= $byNiveau['debutant']      ?? 0 ?>,
                <?= $byNiveau['intermediaire'] ?? 0 ?>,
                <?= $byNiveau['avance']        ?? 0 ?>,
            ],
            backgroundColor: ['#4AF0D866','#F0A84A66','#C8F04A66'],
            borderColor:     ['#4AF0D8',  '#F0A84A',  '#C8F04A'],
            borderWidth: 2,
        }]
    },
    options: { plugins: { legend: { labels: { color:'#7a9e7a', font:{size:11} } } }, cutout:'65%' }
});
</script>