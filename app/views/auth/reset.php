<?php $pageTitle = 'Nouveau mot de passe'; ?>

<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;">
  <div style="width:100%;max-width:420px;">

    <div style="text-align:center;margin-bottom:2rem;">
      <div style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:900;
                  background:linear-gradient(135deg,var(--green),var(--gold));
                  -webkit-background-clip:text;background-clip:text;color:transparent;">
        MoveUp
      </div>
      <div style="color:var(--muted);font-size:.85rem;margin-top:.3rem;">Nouveau mot de passe</div>
    </div>

    <div style="background:var(--card);border:1px solid var(--border-c);border-radius:20px;padding:2rem;">

      <h2 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.2rem;margin:0 0 .5rem;">
        Choisissez un nouveau mot de passe
      </h2>
      <p style="color:var(--muted);font-size:.85rem;margin:0 0 1.5rem;">
        Minimum 8 caractères.
      </p>

      <?php if (!empty($flash)): ?>
      <div style="padding:.75rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:.85rem;
                  background:rgba(248,113,113,.1);color:#f87171;border:1px solid #f8717133;">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash['msg']) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="<?= View::base('password/update') ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div style="margin-bottom:1rem;">
          <label style="display:block;font-size:.78rem;font-weight:600;color:var(--muted);margin-bottom:.4rem;">
            Nouveau mot de passe
          </label>
          <input type="password" name="mot_de_passe" required minlength="8" placeholder="••••••••"
                 style="width:100%;padding:.65rem 1rem;background:var(--bg);border:1px solid var(--border-c);
                        border-radius:10px;color:inherit;font-size:.9rem;outline:none;box-sizing:border-box;
                        transition:border-color .2s;"
                 onfocus="this.style.borderColor='var(--green)'"
                 onblur="this.style.borderColor='var(--border-c)'">
        </div>

        <div style="margin-bottom:1.2rem;">
          <label style="display:block;font-size:.78rem;font-weight:600;color:var(--muted);margin-bottom:.4rem;">
            Confirmer le mot de passe
          </label>
          <input type="password" name="confirmation" required minlength="8" placeholder="••••••••"
                 style="width:100%;padding:.65rem 1rem;background:var(--bg);border:1px solid var(--border-c);
                        border-radius:10px;color:inherit;font-size:.9rem;outline:none;box-sizing:border-box;
                        transition:border-color .2s;"
                 onfocus="this.style.borderColor='var(--green)'"
                 onblur="this.style.borderColor='var(--border-c)'">
        </div>

        <!-- Password strength indicator -->
        <div style="margin-bottom:1.2rem;">
          <div style="height:4px;background:var(--border-c);border-radius:999px;overflow:hidden;">
            <div id="strengthBar" style="height:100%;width:0%;border-radius:999px;transition:all .3s;"></div>
          </div>
          <div id="strengthLabel" style="font-size:.72rem;color:var(--muted);margin-top:.3rem;"></div>
        </div>

        <button type="submit"
                style="width:100%;padding:.7rem;background:linear-gradient(135deg,var(--green),var(--green-dark));
                       color:#fff;border:none;border-radius:12px;font-weight:700;font-family:'Syne',sans-serif;
                       font-size:.9rem;cursor:pointer;transition:.2s;">
          Réinitialiser le mot de passe
        </button>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelector('input[name="mot_de_passe"]').addEventListener('input', function() {
    const val = this.value;
    const bar = document.getElementById('strengthBar');
    const lbl = document.getElementById('strengthLabel');
    let score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^a-zA-Z0-9]/.test(val)) score++;

    const levels = [
        { color: '#f87171', label: 'Très faible', w: '20%' },
        { color: '#F59E0B', label: 'Faible',      w: '40%' },
        { color: '#F59E0B', label: 'Moyen',       w: '60%' },
        { color: '#34D399', label: 'Fort',         w: '80%' },
        { color: '#a3e635', label: 'Très fort',   w: '100%' },
    ];
    const lvl = levels[Math.max(0, score - 1)] || levels[0];
    bar.style.width       = val.length ? lvl.w     : '0%';
    bar.style.background  = val.length ? lvl.color : 'transparent';
    lbl.style.color       = val.length ? lvl.color : 'var(--muted)';
    lbl.textContent       = val.length ? lvl.label : '';
});
</script>
