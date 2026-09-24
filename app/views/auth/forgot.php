<?php $pageTitle = 'Mot de passe oublié'; ?>

<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;">
  <div style="width:100%;max-width:420px;">

    <div style="text-align:center;margin-bottom:2rem;">
      <div style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:900;
                  background:linear-gradient(135deg,var(--green),var(--gold));
                  -webkit-background-clip:text;background-clip:text;color:transparent;">
        MoveUp
      </div>
      <div style="color:var(--muted);font-size:.85rem;margin-top:.3rem;">Réinitialisation du mot de passe</div>
    </div>

    <div style="background:var(--card);border:1px solid var(--border-c);border-radius:20px;padding:2rem;">

      <h2 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.2rem;margin:0 0 .5rem;">
        Mot de passe oublié ?
      </h2>
      <p style="color:var(--muted);font-size:.85rem;margin:0 0 1.5rem;line-height:1.5;">
        Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
      </p>

      <?php if (!empty($flash)): ?>
      <div style="padding:.75rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:.85rem;
                  background:<?= $flash['type']==='success' ? 'rgba(163,230,53,.1)' : 'rgba(248,113,113,.1)' ?>;
                  color:<?= $flash['type']==='success' ? '#a3e635' : '#f87171' ?>;
                  border:1px solid <?= $flash['type']==='success' ? '#a3e63533' : '#f8717133' ?>;">
        <i class="fas fa-<?= $flash['type']==='success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="<?= View::base('password/send') ?>">
        <div style="margin-bottom:1rem;">
          <label style="display:block;font-size:.78rem;font-weight:600;color:var(--muted);margin-bottom:.4rem;">
            Adresse email
          </label>
          <input type="email" name="email" required placeholder="votre@email.com"
                 style="width:100%;padding:.65rem 1rem;background:var(--bg);border:1px solid var(--border-c);
                        border-radius:10px;color:inherit;font-size:.9rem;outline:none;box-sizing:border-box;
                        transition:border-color .2s;"
                 onfocus="this.style.borderColor='var(--green)'"
                 onblur="this.style.borderColor='var(--border-c)'">
        </div>
        <button type="submit"
                style="width:100%;padding:.7rem;background:linear-gradient(135deg,var(--green),var(--green-dark));
                       color:#fff;border:none;border-radius:12px;font-weight:700;font-family:'Syne',sans-serif;
                       font-size:.9rem;cursor:pointer;transition:.2s;">
          Envoyer le lien
        </button>
      </form>

      <p style="text-align:center;margin-top:1.2rem;font-size:.82rem;color:var(--muted);">
        <a href="<?= View::base('login') ?>" style="color:var(--muted);text-decoration:none;">
          ← Retour à la connexion
        </a>
      </p>
    </div>
  </div>
</div>
