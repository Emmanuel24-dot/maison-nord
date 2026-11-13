<?php require_once 'includes/auth.php'; $active = ''; ?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>À propos — Maison Nord</title>
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('mn-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/styles.css">
<link rel="manifest" href="/maison-nord/manifest.json">
<meta name="theme-color" content="#c89468">
<style>
.ap-hero{padding:6rem 0 4rem;text-align:center;border-bottom:1px solid var(--mn-border)}
.ap-hero-title{font-family:"Cormorant Garamond",Georgia,serif;font-size:clamp(3rem,7vw,5.5rem);font-weight:400;color:var(--mn-ink);line-height:1;margin:.5rem 0 1.5rem;letter-spacing:-.02em}
.ap-hero-sub{font-size:.95rem;color:var(--mn-muted);max-width:500px;margin:0 auto;line-height:1.8}
.ap-section{padding:5rem 0}
.ap-section+.ap-section{border-top:1px solid var(--mn-border)}
.ap-label{font-size:.68rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--mn-accent);margin-bottom:1rem}
.ap-title{font-family:"Cormorant Garamond",Georgia,serif;font-size:clamp(2rem,4vw,3rem);font-weight:400;color:var(--mn-ink);margin:0 0 1.5rem;line-height:1.15}
.ap-body{font-size:.9rem;color:var(--mn-muted);line-height:2;max-width:520px}
.ap-valeurs{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-top:3rem}
.ap-valeur-card{background:var(--mn-surface);border:1px solid var(--mn-border);border-radius:14px;padding:1.75rem}
.ap-valeur-icon{width:2.5rem;height:2.5rem;border-radius:50%;background:rgba(200,148,104,.12);display:flex;align-items:center;justify-content:center;color:var(--mn-accent);font-size:1rem;margin-bottom:1rem}
.ap-valeur-title{font-family:"Cormorant Garamond",Georgia,serif;font-size:1.2rem;font-weight:400;color:var(--mn-ink);margin:0 0 .5rem}
.ap-valeur-desc{font-size:.82rem;color:var(--mn-muted);line-height:1.7;margin:0}
.ap-img{border-radius:18px;overflow:hidden;aspect-ratio:4/3;background:var(--mn-surface-alt)}
.ap-img img{width:100%;height:100%;object-fit:cover;display:block}
.ap-chiffres{display:flex;gap:3rem;flex-wrap:wrap;margin-top:2.5rem}
.ap-chiffre-val{font-family:"Cormorant Garamond",Georgia,serif;font-size:3rem;font-weight:400;color:var(--mn-ink);line-height:1}
.ap-chiffre-lbl{font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:var(--mn-muted);margin-top:.25rem}
.ap-team{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.25rem;margin-top:2.5rem}
.ap-team-card{text-align:center}
.ap-team-avatar{width:72px;height:72px;border-radius:50%;background:var(--mn-grad-accent);color:#fff;display:flex;align-items:center;justify-content:center;font-family:"Cormorant Garamond",Georgia,serif;font-size:1.6rem;margin:0 auto 1rem}
.ap-team-name{font-weight:600;font-size:.875rem;color:var(--mn-ink);margin:0}
.ap-team-role{font-size:.78rem;color:var(--mn-muted);margin:.25rem 0 0}
@media(max-width:767px){
  .ap-valeurs{grid-template-columns:1fr}
  .ap-chiffres{gap:2rem}
  .ap-hero{padding:4rem 0 3rem}
}
</style>
</head>
<body>
<div id="scroll-progress"></div>
<?php include 'includes/nav.php'; ?>

<div class="ap-hero">
  <div class="container">
    <div class="section-eyebrow center">Notre histoire</div>
    <h1 class="ap-hero-title">Maison Nord</h1>
    <p class="ap-hero-sub">Une maison de mode fondée à Lomé, Togo, avec l'ambition de proposer un vestiaire essentiel, sobre et durable.</p>
  </div>
</div>

<!-- Histoire -->
<section class="ap-section">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <div class="ap-label">Depuis 2020</div>
        <h2 class="ap-title">Née d'une conviction : le beau devrait durer.</h2>
        <p class="ap-body">
          Maison Nord est née d'un constat simple : trop de vêtements sont conçus pour être remplacés.
          Nous avons voulu faire l'inverse — créer des pièces pensées pour traverser les saisons,
          les années, et les humeurs.
        </p>
        <p class="ap-body" style="margin-top:1rem">
          Chaque collection est le résultat d'un dialogue entre des matières nobles sourcées en Europe
          et l'identité visuelle de Lomé — une ville qui assume son propre sens du style.
        </p>
        <div class="ap-chiffres">
          <div><div class="ap-chiffre-val">4+</div><div class="ap-chiffre-lbl">Ans d'existence</div></div>
          <div><div class="ap-chiffre-val">800+</div><div class="ap-chiffre-lbl">Clients satisfaits</div></div>
          <div><div class="ap-chiffre-val">100%</div><div class="ap-chiffre-lbl">Qualité contrôlée</div></div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="ap-img">
          <img src="assets/atelier-1.jpg" alt="L'atelier Maison Nord">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Valeurs -->
<section class="ap-section" style="background:var(--mn-surface-alt)">
  <div class="container">
    <div class="ap-label">Ce qui nous guide</div>
    <h2 class="ap-title">Nos valeurs</h2>
    <div class="ap-valeurs">
      <div class="ap-valeur-card reveal">
        <div class="ap-valeur-icon"><i class="bi bi-gem"></i></div>
        <h3 class="ap-valeur-title">Qualité sans compromis</h3>
        <p class="ap-valeur-desc">Chaque pièce est soumise à un contrôle qualité rigoureux avant d'être livrée. Nous n'expédions que ce que nous porterions nous-mêmes.</p>
      </div>
      <div class="ap-valeur-card reveal">
        <div class="ap-valeur-icon"><i class="bi bi-infinity"></i></div>
        <h3 class="ap-valeur-title">L'intemporel avant tout</h3>
        <p class="ap-valeur-desc">Nous refusons la tendance pour la tendance. Nos collections sont dessinées pour s'intégrer naturellement à votre garde-robe existante.</p>
      </div>
      <div class="ap-valeur-card reveal">
        <div class="ap-valeur-icon"><i class="bi bi-people"></i></div>
        <h3 class="ap-valeur-title">Ancré à Lomé</h3>
        <p class="ap-valeur-desc">Nous livrons partout au Togo. Notre service client est joignable 7j/7 et toutes nos décisions sont prises à Lomé, pour nos clients au Togo.</p>
      </div>
    </div>
  </div>
</section>

<!-- Matières -->
<section class="ap-section">
  <div class="container">
    <div class="row g-5 align-items-center flex-lg-row-reverse">
      <div class="col-lg-6">
        <div class="ap-label">Savoir-faire</div>
        <h2 class="ap-title">Des matières choisies avec exigence.</h2>
        <p class="ap-body">
          Lin, coton pima, laine mérinos, soie — nos matières sont sourcées auprès de fournisseurs
          européens sélectionnés pour leur traçabilité et la qualité de leur filature.
        </p>
        <p class="ap-body" style="margin-top:1rem">
          Chaque fiche produit détaille la composition exacte et les conseils d'entretien.
          Nous croyons que vous méritez de savoir ce que vous portez.
        </p>
      </div>
      <div class="col-lg-6">
        <div class="ap-img">
          <img src="assets/carousel-2.jpg" alt="Matières Maison Nord">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Équipe -->
<section class="ap-section" style="background:var(--mn-surface-alt)">
  <div class="container">
    <div class="ap-label">Qui nous sommes</div>
    <h2 class="ap-title">L'équipe</h2>
    <div class="ap-team">
      <div class="ap-team-card">
        <div class="ap-team-avatar">A</div>
        <p class="ap-team-name">Ama K.</p>
        <p class="ap-team-role">Fondatrice & Directrice artistique</p>
      </div>
      <div class="ap-team-card">
        <div class="ap-team-avatar">K</div>
        <p class="ap-team-name">Koffi M.</p>
        <p class="ap-team-role">Responsable logistique</p>
      </div>
      <div class="ap-team-card">
        <div class="ap-team-avatar">E</div>
        <p class="ap-team-name">Eyram D.</p>
        <p class="ap-team-role">Service client</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="ap-section" style="text-align:center">
  <div class="container">
    <h2 class="ap-title" style="margin:0 auto 1rem">Prêt à explorer la collection ?</h2>
    <p class="ap-body" style="margin:0 auto 2rem;text-align:center">Des pièces livrées à votre porte, partout au Togo.</p>
    <a href="boutique.php" class="btn-mn"><span>Voir la boutique</span><i class="bi bi-arrow-right"></i></a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/cart.js?v=2"></script>
</body>
</html>
