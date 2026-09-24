<?php $pageTitle = 'Laisser un avis'; ?>

<main class="main">

<section class="page-hero" style="padding:9rem 3rem 3rem;">
    <div class="page-hero-inner">
        <span class="section-tag">Avis</span>
        <h1 class="page-hero-title">Ton <span class="hl">expérience</span></h1>
        <p class="page-hero-sub">Partage ton ressenti pour aider la communauté MoveUp.</p>
    </div>
</section>

<div class="page-content" style="max-width:600px;">

    <?php if (!empty($flash)): ?>
    <div style="margin-bottom:1.5rem;padding:1rem 1.5rem;border-radius:12px;
        background:<?= $flash['type']==='error' ? 'rgba(248,113,113,.1)' : 'rgba(200,240,74,.1)' ?>;
        border:1px solid <?= $flash['type']==='error' ? 'rgba(248,113,113,.3)' : 'rgba(200,240,74,.3)' ?>;
        color:<?= $flash['type']==='error' ? '#f87171' : '#C8F04A' ?>;">
        <?= htmlspecialchars($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <div style="background:var(--card2);border:1px solid var(--border);border-radius:24px;padding:2.5rem;">

        <form method="POST" action="<?= View::base('reviews/submit') ?>" class="review-form">
            <input type="hidden" name="type"      value="general">
            <input type="hidden" name="target_id" value="">
            <input type="hidden" name="note"      id="noteInput">

            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-family:'Syne',sans-serif;font-weight:700;font-size:.8rem;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.75rem;">
                    Note <span style="color:#f87171;">*</span>
                </label>
                <div id="starRating" style="display:flex;gap:8px;">
                    <?php for($i=1;$i<=5;$i++): ?>
                    <span data-value="<?= $i ?>"
                          style="font-size:2.2rem;cursor:pointer;color:rgba(255,255,255,.15);transition:color .15s;user-select:none;">★</span>
                    <?php endfor; ?>
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-family:'Syne',sans-serif;font-weight:700;font-size:.8rem;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.75rem;">
                    Commentaire <span style="color:#f87171;">*</span>
                </label>
                <textarea name="commentaire" rows="5"
                          style="width:100%;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:12px;padding:1rem;color:var(--text);font-family:'DM Sans',sans-serif;font-size:.9rem;resize:vertical;outline:none;box-sizing:border-box;"
                          placeholder="Décris ton expérience avec MoveUp…" required></textarea>
            </div>

            <button type="submit"
                    style="width:100%;background:linear-gradient(135deg,var(--green),var(--green-dark));border:none;border-radius:14px;padding:1rem;color:#fff;font-family:'Syne',sans-serif;font-weight:800;font-size:.9rem;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;transition:opacity .2s;">
                Publier mon avis
            </button>
        </form>

    </div>
</div>

</main>

<script>
(function() {
    const stars  = document.querySelectorAll('#starRating span');
    const input  = document.getElementById('noteInput');
    const GOLD   = '#F0A84A';
    const DIM    = 'rgba(255,255,255,.15)';

    function paint(upTo) {
        stars.forEach((s, i) => { s.style.color = i < upTo ? GOLD : DIM; });
    }

    stars.forEach((star, idx) => {
        star.addEventListener('click', () => {
            input.value = star.dataset.value;
            paint(idx + 1);
        });
        star.addEventListener('mouseover', () => paint(idx + 1));
        star.addEventListener('mouseout',  () => paint(input.value ? parseInt(input.value) : 0));
    });

    document.querySelector('.review-form').addEventListener('submit', function(e) {
        if (!input.value) {
            e.preventDefault();
            alert('Choisis une note ⭐');
        }
    });
})();
</script>
