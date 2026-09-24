<?php
$pageTitle = 'Publier une séance';
?>

<main class="main">
<div style="max-width:540px;margin:0 auto;padding:2rem 1.5rem;">

  <div style="font-size:.6rem;letter-spacing:.18em;text-transform:uppercase;color:var(--lime);
              font-family:'Syne',sans-serif;margin-bottom:.4rem;">Salle</div>
  <h1 style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:900;margin-bottom:2rem;">
    Publier une séance
  </h1>

  <form method="POST" action="<?= View::base('cours/publish') ?>" enctype="multipart/form-data"
        style="display:flex;flex-direction:column;gap:1.1rem;">

    <div class="pub-field">
      <label>Titre</label>
      <input type="text" name="title" placeholder="ex: Padel Intense" required class="pub-input">
    </div>

    <div class="pub-field">
      <label>Catégorie</label>
      <select name="category" required class="pub-input">
        <option value="gym">Gym</option>
        <option value="yoga">Pilates/Yoga</option>
        <option value="padel">Padel</option>
        <option value="dance">Dance</option>
        <option value="rpm">RPM</option>
      </select>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
      <div class="pub-field">
        <label>Date</label>
        <input type="date" name="date" required class="pub-input">
      </div>
      <div class="pub-field">
        <label>Heure</label>
        <input type="time" name="time" required class="pub-input">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
      <div class="pub-field">
        <label>Prix (DT)</label>
        <input type="number" step="0.01" min="0" name="price" placeholder="0.00" required class="pub-input">
      </div>
      <div class="pub-field">
        <label>Nombre de places</label>
        <input type="number" min="1" max="500" name="capacity" placeholder="ex: 30" value="30" required class="pub-input">
      </div>
    </div>

    <div class="pub-field">
      <label>Photo <span style="color:var(--muted);font-size:.75rem;">(optionnel)</span></label>
      <input type="file" name="image" accept="image/*" class="pub-input">
    </div>

    <div style="display:flex;gap:1rem;margin-top:.5rem;">
      <button type="submit"
              style="flex:1;display:flex;align-items:center;justify-content:center;gap:.4rem;
                     padding:.65rem 1.2rem;background:var(--lime);color:var(--text);border:none;
                     border-radius:10px;font-weight:700;font-family:'Syne',sans-serif;
                     font-size:.85rem;cursor:pointer;">
        <i class="fas fa-plus"></i> PUBLIER LA SÉANCE
      </button>
      <a href="<?= View::base('cours') ?>"
         style="display:flex;align-items:center;justify-content:center;padding:.65rem 1.2rem;
                border:1px solid var(--border);border-radius:10px;color:var(--muted);
                text-decoration:none;font-family:'Syne',sans-serif;font-weight:600;font-size:.82rem;">
        Annuler
      </a>
    </div>

  </form>
</div>
</main>

<style>
.pub-field       { display:flex;flex-direction:column;gap:.4rem; }
.pub-field label { font-size:.78rem;font-weight:600;font-family:'Syne',sans-serif;color:var(--muted); }
.pub-input       { background:var(--card2);border:1px solid var(--border);border-radius:10px;
                   padding:.65rem 1rem;color:inherit;font-size:.88rem;outline:none;
                   transition:border-color .2s;width:100%;box-sizing:border-box; }
.pub-input:focus { border-color:var(--lime); }
</style>
