<?php $pageTitle = $tab === 'register' ? 'Inscription' : 'Connexion'; ?>

<section class="page-hero" style="padding:9rem 3rem 3rem;">
    <div class="page-hero-inner">
        <span class="section-tag">Compte</span>
        <h1 class="page-hero-title">
            <?= $tab === 'register' ? 'Crée ton <span class="hl">compte</span>' : 'Bon <span class="hl">retour</span>' ?>
        </h1>
    </div>
</section>

<div class="page-content" style="max-width:560px;">

    <?php if (!empty($flash)): ?>
    <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div style="display:flex;gap:4px;background:var(--card);border:1px solid var(--border);border-radius:40px;padding:4px;margin-bottom:2rem;">
        <a href="<?= View::base('login') ?>"    style="flex:1;text-align:center;padding:0.7rem;border-radius:40px;font-family:var(--font-head);font-size:0.85rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;transition:all 0.2s;<?= $tab==='login'    ? 'background:linear-gradient(135deg,var(--green),var(--green-dark));color:#fff;' : 'color:var(--muted);' ?>">Connexion</a>
        <a href="<?= View::base('register') ?>" style="flex:1;text-align:center;padding:0.7rem;border-radius:40px;font-family:var(--font-head);font-size:0.85rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;transition:all 0.2s;<?= $tab==='register' ? 'background:linear-gradient(135deg,var(--green),var(--green-dark));color:#fff;' : 'color:var(--muted);' ?>">Inscription</a>
    </div>

    <div style="background:var(--card2);border:1px solid var(--border);border-radius:28px;padding:2.5rem;">

        <?php if ($tab === 'login'): ?>
        <!-- LOGIN -->
        <form method="POST" action="<?= View::base('login') ?>">
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">
    <!-- rest of form unchanged -->
            <div class="form-group">
                <label>Email <span class="req">*</span></label>
                <input type="email" name="email" placeholder="votre@email.com" required/>
            </div>
            <div class="form-group">
                <label>Mot de passe <span class="req">*</span></label>
                <div style="position:relative;">
                    <input type="password" name="mot_de_passe" id="login_password" placeholder="••••••••" required style="padding-right:2.8rem;width:100%;box-sizing:border-box;"/>
                    <button type="button" onclick="togglePassword('login_password', this)"
                            style="position:absolute;right:.8rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);padding:0;font-size:1rem;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:.5rem;">
                <input type="checkbox" name="remember" id="remember" style="width:auto;">
                <label for="remember" style="margin:0;font-size:.85rem;color:var(--muted);">Se souvenir de moi</label>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;border-radius:14px;justify-content:center;margin-top:0.5rem;">
                Se connecter
            </button>
        </form>
        <p style="text-align:center;margin-top:1rem;font-size:0.82rem;">
            <a href="<?= View::base('password/forgot') ?>" style="color:var(--muted);text-decoration:none;">
                <i class="fas fa-lock" style="font-size:.75rem;"></i> Mot de passe oublié ?
            </a>
        </p>
        <p style="text-align:center;margin-top:.5rem;font-size:0.85rem;color:var(--muted);">
            Pas encore de compte ? <a href="<?= View::base('register') ?>" style="color:var(--gold);">S'inscrire</a>
        </p>

        <?php else: ?>
        <!-- REGISTER -->
        <form method="POST" action="<?= View::base('register') ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Prénom <span class="req">*</span></label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" placeholder="Votre prénom" required/>
                </div>
                <div class="form-group">
                    <label>Nom <span class="req">*</span></label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" placeholder="Votre nom" required/>
                </div>
            </div>
            <div class="form-group">
                <label>Email <span class="req">*</span></label>
                <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="votre@email.com" required/>
            </div>
            <div class="form-group">
                <label>Mot de passe <span class="req">*</span></label>
                <div style="position:relative;">
                    <input type="password" name="mot_de_passe" id="reg_password" placeholder="Min. 8 caractères" required style="padding-right:2.8rem;width:100%;box-sizing:border-box;"/>
                    <button type="button" onclick="togglePassword('reg_password', this)"
                            style="position:absolute;right:.8rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);padding:0;font-size:1rem;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Genre</label>
                    <select name="genre">
                        <option value="homme">Homme</option>
                        <option value="femme">Femme</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Âge</label>
                    <input type="number" name="age" value="25" min="14" max="99"/>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Poids (kg)</label>
                    <input type="number" name="poids" value="70" min="30" max="200" step="0.1"/>
                </div>
                <div class="form-group">
                    <label>Taille (cm)</label>
                    <input type="number" name="taille" value="175" min="100" max="250"/>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Niveau</label>
                    <select name="niveau">
                        <option value="debutant">Débutant</option>
                        <option value="intermediaire" selected>Intermédiaire</option>
                        <option value="avance">Avancé</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Objectif</label>
                    <select name="objectif">
                        <option value="perte">Perte de poids</option>
                        <option value="masse">Prise de masse</option>
                        <option value="maintien" selected>Maintien</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;border-radius:14px;justify-content:center;margin-top:0.5rem;">
                Créer mon compte
            </button>
        </form>
        <p style="text-align:center;margin-top:1.5rem;font-size:0.85rem;color:var(--muted);">
            Déjà un compte ? <a href="<?= View::base('login') ?>" style="color:var(--gold);">Se connecter</a>
        </p>
        <?php endif; ?>

    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>