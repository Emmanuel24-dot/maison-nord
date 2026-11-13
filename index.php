<?php require_once 'includes/auth.php'; $active = 'home'; ?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Maison Nord — Vestiaire essentiel façonné en Europe</title>
<meta name="description" content="Maison Nord, boutique de prêt-à-porter premium à Lomé, Togo. Matières nobles, coupes intemporelles, fabrication européenne.">
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('mn-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/styles.css">
<link rel="manifest" href="/maison-nord/manifest.json">
<meta name="theme-color" content="#c89468">
<link rel="apple-touch-icon" href="/maison-nord/assets/icons/icon-192.svg">
</head>
<body>
<div id="scroll-progress"></div>
<?php include 'includes/nav.php'; ?>

<!-- HERO -->
<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="5500">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-label="Slide 1" aria-current="true"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="assets/carousel-1.jpg" alt="Collection Maison Nord">
      <div class="hero-overlay">
        <div class="hero-content">
          <div class="hero-eyebrow">Collection 2026</div>
          <h1>Le vestiaire<br>essentiel</h1>
          <p>Des pièces intemporelles, façonnées en Europe avec des matières nobles.</p>
          <a href="boutique.php" class="btn-mn"><span>Découvrir la boutique</span><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="hero-scroll-hint">Défiler</div>
      </div>
    </div>
    <div class="carousel-item">
      <img src="assets/carousel-2.jpg" alt="Collection automne-hiver 2026">
      <div class="hero-overlay">
        <div class="hero-content">
          <div class="hero-eyebrow">Automne — Hiver 2026</div>
          <h1>Automne<br>2026</h1>
          <p>Une collection pensée pour traverser les saisons sans rien perdre de son allure.</p>
          <a href="boutique.php" class="btn-mn"><span>Voir la collection</span><i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
    </div>
    <div class="carousel-item">
      <img src="assets/carousel-3.jpg" alt="Atelier Maison Nord">
      <div class="hero-overlay">
        <div class="hero-content">
          <div class="hero-eyebrow">Savoir-faire</div>
          <h1>L'atelier</h1>
          <p>Chaque pièce naît d'un savoir-faire transmis et d'un respect profond des matières.</p>
          <a href="boutique.php" class="btn-mn"><span>Commander</span><i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Précédent">
    <i class="bi bi-chevron-left carousel-ctrl-icon"></i>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Suivant">
    <i class="bi bi-chevron-right carousel-ctrl-icon"></i>
  </button>
</div>

<!-- STATS -->
<section class="py-5 bg-surface">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-4 reveal">
        <div class="stat-num" data-count="12" data-suffix="+">12+</div>
        <div class="stat-label">Années d'artisanat</div>
      </div>
      <div class="col-4 reveal reveal-delay-2">
        <div class="stat-num" data-count="6" data-suffix="">6</div>
        <div class="stat-label">Ateliers partenaires</div>
      </div>
      <div class="col-4 reveal reveal-delay-3">
        <div class="stat-num" data-count="100" data-suffix="%">100%</div>
        <div class="stat-label">Fabrication européenne</div>
      </div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section class="py-section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 reveal">
        <div class="about-img-wrap">
          <img src="assets/atelier-1.jpg" alt="Atelier Maison Nord" class="img-fluid">
        </div>
      </div>
      <div class="col-lg-6 reveal reveal-delay-2">
        <div class="about-accent-line"></div>
        <div class="section-eyebrow">La boutique</div>
        <h2 class="section-title">Une garde-robe<br>pensée pour durer</h2>
        <p class="about-desc">Notre boutique réunit une sélection resserrée de pièces conçues pour s'inscrire dans le temps. Manteaux, chemises, mailles, accessoires : chaque modèle est choisi pour sa coupe, sa matière et son intemporalité.</p>
        <a href="boutique.php" class="btn-mn"><span>Entrer dans la boutique</span><i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- TRUST STRIP -->
<section class="trust-strip">
  <div class="container">
    <div class="trust-strip-inner">
      <div class="trust-item">
        <div class="trust-icon"><i class="bi bi-truck"></i></div>
        <div class="trust-text">
          <div class="trust-title">Livraison au Togo</div>
          <div class="trust-sub">Partout dans le pays</div>
        </div>
      </div>
      <div class="trust-item">
        <div class="trust-icon"><i class="bi bi-arrow-counterclockwise"></i></div>
        <div class="trust-text">
          <div class="trust-title">Retours 30 jours</div>
          <div class="trust-sub">Satisfait ou remboursé</div>
        </div>
      </div>
      <div class="trust-item">
        <div class="trust-icon"><i class="bi bi-gem"></i></div>
        <div class="trust-text">
          <div class="trust-title">Qualité premium</div>
          <div class="trust-sub">Matières européennes</div>
        </div>
      </div>
      <div class="trust-item">
        <div class="trust-icon"><i class="bi bi-shield-check"></i></div>
        <div class="trust-text">
          <div class="trust-title">Paiement sécurisé</div>
          <div class="trust-sub">Mobile Money · Carte</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="py-5 bg-surface">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="section-eyebrow center">Sélection</div>
      <h2 class="section-title">Nos pièces phares</h2>
    </div>
    <div class="row g-4" id="featured-products"></div>
    <div class="text-center mt-5 reveal">
      <a href="boutique.php" class="btn-mn-outline"><span>Voir tous les produits</span><i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- MANIFESTE -->
<section class="manifeste-section">
  <div class="container">
    <div class="manifeste-headline reveal">
      <div class="section-eyebrow center manifeste-eyebrow">Notre Manifeste</div>
      <h2 class="manifeste-title">Chaque pièce,<br>une promesse.</h2>
    </div>
    <div class="manifeste-list">
      <div class="manifeste-item reveal">
        <div class="manifeste-icon"><i class="bi bi-gem"></i></div>
        <div class="manifeste-body">
          <h3>Matières d'exception</h3>
          <p>Laines d'Italie, lin européen, cachemire sélectionné à la source. Nous n'utilisons que des fibres nobles, choisies pour leur tenue, leur toucher et leur respect de la peau. Parce qu'une belle pièce commence par une belle matière.</p>
        </div>
      </div>
      <div class="manifeste-item reveal reveal-delay-1">
        <div class="manifeste-icon"><i class="bi bi-scissors"></i></div>
        <div class="manifeste-body">
          <h3>Façonné avec soin</h3>
          <p>Chaque vêtement naît dans un petit atelier, où chaque couture est une signature. Nous travaillons avec des façonniers qui ont fait du détail une vocation. La rapidité n'a ici aucune valeur — seul compte le résultat.</p>
        </div>
      </div>
      <div class="manifeste-item reveal reveal-delay-2">
        <div class="manifeste-icon"><i class="bi bi-clock-history"></i></div>
        <div class="manifeste-body">
          <h3>Conçu pour durer</h3>
          <p>Nous ne croyons pas à la mode jetable. Chaque pièce Maison Nord est pensée pour traverser les années et les tendances. Des coupes intemporelles, des finitions soignées, un service de retouches : investir ici, c'est investir pour toujours.</p>
        </div>
      </div>
    </div>
    <div class="manifeste-cta reveal">
      <a href="boutique.php" class="btn-mn-outline btn-mn-outline-light"><span>Découvrir la collection</span><i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- REVIEWS -->
<section class="py-5 bg-surface">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="section-eyebrow center">Avis clients</div>
      <h2 class="section-title">Ce que disent<br>nos clients</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-8 reveal">
        <div class="review-main-card" id="review-main"></div>
      </div>
      <div class="col-lg-4 reveal reveal-delay-2">
        <div class="d-flex flex-column gap-3" id="review-list"></div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section reveal">
  <div class="container text-center cta-inner">
    <div class="section-eyebrow cta-eyebrow center">Conciergerie</div>
    <h2 class="section-title cta-title">Un conseil, une retouche,<br>une commande sur-mesure ?</h2>
    <p class="cta-desc">Notre équipe est à votre écoute pour vous accompagner dans le choix d'une pièce, organiser un essayage ou répondre à toutes vos questions.</p>
    <a href="contact.php" class="btn-mn btn-mn-accent"><span>Nous contacter</span><i class="bi bi-arrow-right"></i></a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/products.php"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/cart.js?v=2"></script>
<script>
var featured = window.MN_PRODUCTS.slice(0, 3);
document.getElementById('featured-products').innerHTML = featured.map(function (p, i) {
  return '<div class="col-md-4 reveal' + (i > 0 ? ' reveal-delay-' + i : '') + '">' +
    '<div class="product-card">' +
      (p.badge ? '<span class="badge-mn">' + p.badge + '</span>' : '') +
      '<div class="img-wrap">' +
        '<a href="product-detail.php?id=' + p.id + '"><img src="' + p.image + '" alt="' + p.name + '"></a>' +
        '<div class="img-overlay"><a href="product-detail.php?id=' + p.id + '" class="img-overlay-cta">Voir la pièce <i class="bi bi-arrow-right"></i></a></div>' +
      '</div>' +
      '<div class="body">' +
        '<div class="cat">' + p.category + '</div>' +
        '<div class="name">' + p.name + '</div>' +
        '<p class="short">' + p.short + '</p>' +
        '<div class="d-flex justify-content-between align-items-center">' +
          '<span class="price">' + window.MN_FORMAT_PRICE(p.price) + '</span>' +
          '<a href="product-detail.php?id=' + p.id + '" class="btn-mn-outline" style="padding:0.4rem 1rem;font-size:0.72rem;">Détails</a>' +
        '</div>' +
      '</div>' +
    '</div>' +
  '</div>';
}).join('');

var reviews = [
  { quote: "Un vrai souci du détail. Le manteau Camille a remplacé toutes mes autres pièces hivernales.", name: "Élise M.", city: "Lomé", stars: 5 },
  { quote: "Une chemise impeccable, une coupe parfaite. Service après-vente irréprochable.", name: "Thomas R.", city: "Abidjan", stars: 5 },
  { quote: "Le sac Madeleine est sublime, le cuir vieillit magnifiquement.", name: "Camille D.", city: "Cotonou", stars: 5 }
];
function starsHtml(n) { return '★'.repeat(n); }
function renderMain(r) {
  return '<div class="stars">' + starsHtml(r.stars) + '</div>' +
    '<p class="quote">« ' + r.quote + ' »</p>' +
    '<div class="author">' + r.name + '</div>' +
    '<div class="city">' + r.city + '</div>';
}
var listEl = document.getElementById('review-list');
listEl.innerHTML = reviews.map(function (r, i) {
  return '<div class="review-side' + (i === 0 ? ' active' : '') + '" data-i="' + i + '" role="button" tabindex="0">' +
    '<div class="r-stars">' + starsHtml(r.stars) + '</div>' +
    '<div class="r-name">' + r.name + '</div>' +
    '<div class="r-city">' + r.city + '</div>' +
  '</div>';
}).join('');
document.getElementById('review-main').innerHTML = renderMain(reviews[0]);
listEl.querySelectorAll('.review-side').forEach(function (el) {
  el.addEventListener('click', function () {
    listEl.querySelectorAll('.review-side').forEach(function (e) { e.classList.remove('active'); });
    el.classList.add('active');
    document.getElementById('review-main').innerHTML = renderMain(reviews[+el.dataset.i]);
  });
});
</script>
</body>
</html>
