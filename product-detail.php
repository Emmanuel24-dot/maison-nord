<?php require_once 'includes/auth.php'; $active = 'boutique'; ?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Produit — Maison Nord</title>
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

<section class="py-5" id="product-view"></section>

<section class="py-5" style="border-top:1px solid var(--mn-border)" id="reviews-section" style="display:none">
  <div class="container" style="max-width:800px">
    <div class="section-eyebrow">Communauté</div>
    <h3 class="section-title mb-4">Avis clients</h3>
    <div id="reviews-summary" style="margin-bottom:2rem"></div>
    <div id="reviews-list"></div>
    <div id="review-form-wrap"></div>
  </div>
</section>

<section class="py-5 bg-surface">
  <div class="container">
    <div class="section-eyebrow">Vous aimerez aussi</div>
    <h3 class="section-title mb-5">Pièces similaires</h3>
    <div class="row g-4" id="related"></div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<div class="mn-toast" id="mn-toast" role="status" aria-live="polite">
  <i class="bi bi-check-circle-fill"></i>
  <span id="mn-toast-msg">Pièce ajoutée au panier.</span>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/products.php"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/cart.js?v=2"></script>
<script>
var pid = new URLSearchParams(location.search).get('id');
var p = window.MN_GET_PRODUCT(pid);

/* ── Favoris ── */
var FAV_KEY = 'mn-favorites';
function getFavs() { try { return JSON.parse(localStorage.getItem(FAV_KEY)) || []; } catch(e) { return []; } }
function isFavDetail() { return getFavs().indexOf(pid) !== -1; }
function toggleFavDetail() {
  var ids = getFavs(); var idx = ids.indexOf(pid);
  if (idx === -1) { ids.push(pid); showToast('Ajouté aux favoris ♥'); }
  else            { ids.splice(idx, 1); showToast('Retiré des favoris.'); }
  localStorage.setItem(FAV_KEY, JSON.stringify(ids));
  var btn = document.getElementById('btn-fav');
  if (btn) btn.innerHTML = idx === -1
    ? '<i class="bi bi-heart-fill" style="color:#e55"></i>'
    : '<i class="bi bi-heart" style="color:var(--mn-muted)"></i>';
}
var view = document.getElementById('product-view');

function showToast(msg) {
  var t = document.getElementById('mn-toast');
  document.getElementById('mn-toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(function () { t.classList.remove('show'); }, 3500);
}

if (!p) {
  view.innerHTML = '<div class="container text-center py-5"><h2>Pièce introuvable</h2><a href="boutique.php" class="btn-mn mt-3"><span>Retour à la boutique</span><i class="bi bi-arrow-right"></i></a></div>';
} else {
  document.title = p.name + ' — Maison Nord';
  var size = p.sizes[0];
  var color = p.colors[0];
  var qty = 1;

  var galleryImages = (p.gallery && p.gallery.length) ? p.gallery : [p.image];
  var galleryIdx = 0;

  function flyToCart(imgSrc, fromEl) {
    var cartBtn = document.getElementById('cart-open-btn');
    if (!cartBtn) return;
    var cartRect = cartBtn.getBoundingClientRect();
    var fromRect = fromEl ? fromEl.getBoundingClientRect()
      : { left: window.innerWidth / 2 - 32, top: window.innerHeight / 2 - 32, width: 64, height: 64 };
    var fly = document.createElement('img');
    fly.src = imgSrc;
    fly.className = 'mn-fly-img';
    fly.style.left = (fromRect.left + fromRect.width / 2 - 32) + 'px';
    fly.style.top  = (fromRect.top  + fromRect.height / 2 - 32) + 'px';
    fly.style.opacity = '0.92';
    fly.style.transform = 'scale(1)';
    document.body.appendChild(fly);
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        fly.style.left = (cartRect.left + cartRect.width / 2 - 8) + 'px';
        fly.style.top  = (cartRect.top  + cartRect.height / 2 - 8) + 'px';
        fly.style.opacity = '0';
        fly.style.transform = 'scale(0.12)';
        setTimeout(function () {
          if (fly.parentNode) fly.parentNode.removeChild(fly);
          var badge = document.getElementById('nav-cart-count');
          if (badge) {
            badge.classList.add('mn-badge-pop');
            setTimeout(function () { badge.classList.remove('mn-badge-pop'); }, 420);
          }
        }, 680);
      });
    });
  }

  function render() {
    view.innerHTML =
      '<div class="container">' +
        '<nav class="product-breadcrumb" aria-label="Fil d\'Ariane">' +
          '<a href="index.php">Accueil</a>' +
          '<i class="bi bi-chevron-right"></i>' +
          '<a href="boutique.php">Boutique</a>' +
          '<i class="bi bi-chevron-right"></i>' +
          '<span>' + p.name + '</span>' +
        '</nav>' +
        '<div class="row g-5">' +
          '<div class="col-lg-6">' +
            '<div class="product-gallery">' +
              '<div class="product-gallery-main">' +
                '<img id="gallery-main-img" src="' + galleryImages[galleryIdx] + '" alt="' + p.name + '">' +
              '</div>' +
              (galleryImages.length > 1 ?
                '<div class="product-gallery-thumbs">' +
                galleryImages.map(function (img, i) {
                  return '<img src="' + img + '" class="gallery-thumb' + (i === galleryIdx ? ' active' : '') + '" data-idx="' + i + '" alt="Vue ' + (i + 1) + '">';
                }).join('') +
                '</div>'
              : '') +
            '</div>' +
          '</div>' +
          '<div class="col-lg-6">' +
            '<div class="section-eyebrow">' + p.category + '</div>' +
            '<h1 class="section-title">' + p.name + '</h1>' +
            '<div class="price-lg">' + window.MN_FORMAT_PRICE(p.price) + '</div>' +
            '<p class="product-desc">' + p.long + '</p>' +
            '<div class="mb-3">' +
              '<div class="form-label-mn">Taille</div>' +
              p.sizes.map(function (s) {
                return '<button type="button" class="size-chip' + (s === size ? ' active' : '') + '" data-s="' + s + '" aria-pressed="' + (s === size) + '">' + s + '</button>';
              }).join('') +
            '</div>' +
            '<div class="mb-4">' +
              '<div class="form-label-mn">Couleur</div>' +
              p.colors.map(function (c) {
                return '<button type="button" class="color-chip' + (c === color ? ' active' : '') + '" data-c="' + c + '" aria-pressed="' + (c === color) + '">' + c + '</button>';
              }).join('') +
            '</div>' +
            '<div class="d-flex align-items-center gap-3 mb-3">' +
              '<div class="qty-picker">' +
                '<button type="button" class="qty-btn" id="qty-minus" aria-label="Diminuer la quantité"><i class="bi bi-dash"></i></button>' +
                '<div class="qty-value" aria-live="polite">' + qty + '</div>' +
                '<button type="button" class="qty-btn" id="qty-plus" aria-label="Augmenter la quantité"><i class="bi bi-plus"></i></button>' +
              '</div>' +
              '<button type="button" id="add-cart" class="btn-mn flex-grow-1">' +
                '<span>Commander — ' + window.MN_FORMAT_PRICE(p.price * qty) + '</span>' +
                '<i class="bi bi-bag"></i>' +
              '</button>' +
              '<button type="button" id="btn-fav" onclick="toggleFavDetail()" style="flex-shrink:0;width:2.8rem;height:2.8rem;border-radius:50%;border:1.5px solid var(--mn-border);background:var(--mn-surface);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;transition:border-color .2s" aria-label="Favoris">' +
                (isFavDetail() ? '<i class="bi bi-heart-fill" style="color:#e55"></i>' : '<i class="bi bi-heart" style="color:var(--mn-muted)"></i>') +
              '</button>' +
            '</div>' +
            '<div class="delivery-row">' +
              '<div><i class="bi bi-truck"></i> Livraison à Lomé et environs</div>' +
              '<div><i class="bi bi-arrow-return-left"></i> Retours 30 jours</div>' +
            '</div>' +
            '<div style="border-top:1px solid var(--mn-border);">' +
              '<div class="mn-accordion-item">' +
                '<button type="button" class="mn-accordion-btn">Caractéristiques <i class="bi bi-plus"></i></button>' +
                '<div class="mn-accordion-body"><div class="mn-accordion-body-inner"><ul>' +
                  p.features.map(function (f) { return '<li>' + f + '</li>'; }).join('') +
                '</ul></div></div>' +
              '</div>' +
              '<div class="mn-accordion-item">' +
                '<button type="button" class="mn-accordion-btn">Composition <i class="bi bi-plus"></i></button>' +
                '<div class="mn-accordion-body"><div class="mn-accordion-body-inner">' + p.composition + '</div></div>' +
              '</div>' +
              '<div class="mn-accordion-item">' +
                '<button type="button" class="mn-accordion-btn">Livraison &amp; Retours <i class="bi bi-plus"></i></button>' +
                '<div class="mn-accordion-body"><div class="mn-accordion-body-inner">Livraison disponible à Lomé et dans tout le Togo. Retours gratuits sous 30 jours.</div></div>' +
              '</div>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>';

    view.querySelectorAll('.gallery-thumb').forEach(function (th) {
      th.addEventListener('click', function () {
        galleryIdx = +th.dataset.idx;
        document.getElementById('gallery-main-img').src = galleryImages[galleryIdx];
        view.querySelectorAll('.gallery-thumb').forEach(function (x) { x.classList.remove('active'); });
        th.classList.add('active');
      });
    });
    view.querySelectorAll('.size-chip').forEach(function (el) {
      el.addEventListener('click', function () { size = el.dataset.s; render(); });
    });
    view.querySelectorAll('.color-chip').forEach(function (el) {
      el.addEventListener('click', function () { color = el.dataset.c; render(); });
    });
    document.getElementById('qty-minus').addEventListener('click', function () { if (qty > 1) { qty--; render(); } });
    document.getElementById('qty-plus').addEventListener('click', function () { qty++; render(); });
    document.getElementById('add-cart').addEventListener('click', function () {
      flyToCart(galleryImages[galleryIdx], document.getElementById('gallery-main-img'));
      window.MN_CART.add(p, size, color, qty);
      showToast(p.name + ' (' + size + ', ' + color + ') × ' + qty + ' ajouté au panier.');
    });
    window.MN_INIT_ACCORDIONS();
  }

  render();

  /* Recently viewed — save to localStorage */
  (function () {
    var KEY = 'mn-recently-viewed';
    var stored = [];
    try { stored = JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) {}
    stored = stored.filter(function (x) { return x.id !== p.id; });
    stored.unshift({ id: p.id, name: p.name, image: p.image, price: p.price, category: p.category });
    if (stored.length > 8) stored = stored.slice(0, 8);
    try { localStorage.setItem(KEY, JSON.stringify(stored)); } catch (e) {}
  })();

  /* ── Avis ── */
  loadReviews();

  var related = window.MN_PRODUCTS.filter(function (x) { return x.id !== p.id && x.category === p.category; }).slice(0, 3);
  document.getElementById('related').innerHTML = related.map(function (rp) {
    return '<div class="col-md-4">' +
      '<div class="product-card">' +
        '<div class="img-wrap">' +
          '<a href="product-detail.php?id=' + rp.id + '"><img src="' + rp.image + '" alt="' + rp.name + '"></a>' +
          '<div class="img-overlay"><a href="product-detail.php?id=' + rp.id + '" class="img-overlay-cta">Voir <i class="bi bi-arrow-right"></i></a></div>' +
        '</div>' +
        '<div class="body">' +
          '<div class="cat">' + rp.category + '</div>' +
          '<div class="name">' + rp.name + '</div>' +
          '<div class="price">' + window.MN_FORMAT_PRICE(rp.price) + '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  }).join('');
}

/* ── Chargement & affichage des avis ── */
function starsHtml(n, interactive) {
  var html = '<div class="mn-stars' + (interactive ? ' mn-stars-pick' : '') + '">';
  for (var i = 1; i <= 5; i++) {
    html += '<i class="bi bi-star' + (i <= n ? '-fill lit' : '') + '" data-val="' + i + '"></i>';
  }
  return html + '</div>';
}

function loadReviews() {
  fetch('api/reviews.php?product_id=' + encodeURIComponent(pid))
    .then(function(r){ return r.json(); })
    .then(function(list) {
      var section = document.getElementById('reviews-section');
      section.style.display = '';

      /* Résumé */
      var avg = 0;
      if (list.length) { list.forEach(function(r){ avg += +r.rating; }); avg = avg / list.length; }
      document.getElementById('reviews-summary').innerHTML = list.length
        ? '<div class="mn-avg-stars">' +
            '<span class="mn-avg-val">' + avg.toFixed(1) + '</span>' +
            starsHtml(Math.round(avg), false) +
            '<span class="mn-avg-lbl">' + list.length + ' avis</span>' +
          '</div>'
        : '<p style="color:var(--mn-muted);font-size:.875rem">Aucun avis pour ce produit. Soyez le premier !</p>';

      /* Liste */
      document.getElementById('reviews-list').innerHTML = list.map(function(r) {
        var d = new Date(r.created_at).toLocaleDateString('fr-FR', {day:'2-digit',month:'short',year:'numeric'});
        return '<div class="mn-review-card">' +
          '<div class="mn-review-meta">' +
            starsHtml(r.rating, false) +
            '<span class="mn-review-author">' + esc(r.prenom + ' ' + r.nom.charAt(0) + '.') + '</span>' +
            '<span class="mn-review-date">' + d + '</span>' +
          '</div>' +
          (r.comment ? '<p class="mn-review-body">' + esc(r.comment) + '</p>' : '') +
        '</div>';
      }).join('');

      /* Formulaire si connecté */
      var isLogged = window.MN_AUTH && window.MN_AUTH.logged;
      var formWrap = document.getElementById('review-form-wrap');
      if (isLogged) {
        var selectedRating = 0;
        formWrap.innerHTML =
          '<div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--mn-border)">' +
            '<h4 style="font-family:\'Cormorant Garamond\',Georgia,serif;font-size:1.3rem;font-weight:400;color:var(--mn-ink);margin:0 0 1rem">Laisser un avis</h4>' +
            '<div style="margin-bottom:.75rem">' + starsHtml(0, true) + '</div>' +
            '<textarea id="rv-comment" placeholder="Votre commentaire (facultatif)…" style="width:100%;border:1.5px solid var(--mn-border);border-radius:8px;padding:.75rem 1rem;background:var(--mn-surface);color:var(--mn-ink);font-family:inherit;font-size:.875rem;resize:vertical;min-height:90px;transition:border-color .2s" onfocus="this.style.borderColor=\'var(--mn-accent)\'" onblur="this.style.borderColor=\'var(--mn-border)\'"></textarea>' +
            '<button id="rv-submit" type="button" class="btn-mn btn-mn-sm" style="margin-top:.75rem"><span>Publier</span><i class="bi bi-check2"></i></button>' +
          '</div>';

        /* Étoiles interactives */
        var stars = formWrap.querySelectorAll('.mn-stars-pick .bi');
        stars.forEach(function(s) {
          s.addEventListener('mouseover', function() {
            var v = +s.dataset.val;
            stars.forEach(function(x){ x.className = 'bi bi-star' + (+x.dataset.val <= v ? '-fill lit' : ''); });
          });
          s.addEventListener('mouseout', function() {
            stars.forEach(function(x){ x.className = 'bi bi-star' + (+x.dataset.val <= selectedRating ? '-fill lit' : ''); });
          });
          s.addEventListener('click', function() {
            selectedRating = +s.dataset.val;
            stars.forEach(function(x){ x.className = 'bi bi-star' + (+x.dataset.val <= selectedRating ? '-fill lit' : ''); });
          });
        });

        document.getElementById('rv-submit').addEventListener('click', function() {
          if (!selectedRating) { alert('Sélectionnez une note.'); return; }
          fetch('api/reviews.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: pid, rating: selectedRating, comment: document.getElementById('rv-comment').value.trim() })
          }).then(function(r){ return r.json(); }).then(function(d){
            if (d.error) { alert(d.error); return; }
            loadReviews();
          });
        });
      } else {
        formWrap.innerHTML = '<p style="margin-top:1.5rem;font-size:.82rem;color:var(--mn-muted)"><a href="login.php" style="color:var(--mn-accent)">Connectez-vous</a> pour laisser un avis.</p>';
      }
    });
}

function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>
</body>
</html>
