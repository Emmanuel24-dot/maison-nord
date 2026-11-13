<?php require_once 'includes/auth.php'; $active = 'boutique'; ?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Boutique — Maison Nord</title>
<meta name="description" content="Découvrez la collection Maison Nord à Lomé : femme, homme, accessoires. Filtrez par catégorie et prix.">
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

<header class="boutique-hero">
  <div class="boutique-hero-bg-deco" aria-hidden="true"></div>
  <div class="container">
    <div class="boutique-hero-inner">
      <div class="boutique-hero-eyebrow">Maison Nord &mdash; Collection</div>
      <h1 class="boutique-hero-title">La Boutique</h1>
      <div class="boutique-hero-rule"><span></span><i class="bi bi-diamond-fill"></i><span></span></div>
      <p class="boutique-hero-sub">Essentiels façonnés en Europe · Livraison dans tout le Togo</p>
    </div>
  </div>
</header>

<div class="boutique-filters-bar">
  <div class="container">
    <div class="boutique-filters-inner">
      <div class="boutique-search-wrap">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" id="f-search" class="boutique-search-input" placeholder="Rechercher une pièce…" aria-label="Rechercher">
      </div>
      <div class="boutique-price-wrap">
        <span class="boutique-price-lbl">Max. <strong id="f-price-val">200 000</strong> FCFA</span>
        <input type="range" id="f-price" class="form-range-mn" min="50000" max="200000" step="5000" value="200000" aria-label="Prix maximum">
      </div>
      <select id="f-sort" class="boutique-sort-select" aria-label="Trier">
        <option value="featured">Recommandés</option>
        <option value="asc">Prix ↑</option>
        <option value="desc">Prix ↓</option>
      </select>
    </div>
  </div>
</div>

<section class="pb-5 pt-4">
  <div class="container">
    <div class="boutique-cats-row">
      <div id="f-cats" class="boutique-cats-scroll"></div>
      <span class="product-count" id="product-count" aria-live="polite"></span>
    </div>

    <div class="row g-4" id="product-grid">
      <div class="col-md-6 col-lg-4"><div class="mn-skeleton-card"><div class="mn-sk-img"></div><div class="mn-sk-body"><div class="mn-sk-line mn-sk-w60"></div><div class="mn-sk-line mn-sk-w100"></div><div class="mn-sk-line mn-sk-w80"></div></div></div></div>
      <div class="col-md-6 col-lg-4"><div class="mn-skeleton-card"><div class="mn-sk-img"></div><div class="mn-sk-body"><div class="mn-sk-line mn-sk-w60"></div><div class="mn-sk-line mn-sk-w100"></div><div class="mn-sk-line mn-sk-w80"></div></div></div></div>
      <div class="col-md-6 col-lg-4"><div class="mn-skeleton-card"><div class="mn-sk-img"></div><div class="mn-sk-body"><div class="mn-sk-line mn-sk-w60"></div><div class="mn-sk-line mn-sk-w100"></div><div class="mn-sk-line mn-sk-w80"></div></div></div></div>
      <div class="col-md-6 col-lg-4 d-none d-md-block"><div class="mn-skeleton-card"><div class="mn-sk-img"></div><div class="mn-sk-body"><div class="mn-sk-line mn-sk-w60"></div><div class="mn-sk-line mn-sk-w100"></div><div class="mn-sk-line mn-sk-w80"></div></div></div></div>
      <div class="col-md-6 col-lg-4 d-none d-md-block"><div class="mn-skeleton-card"><div class="mn-sk-img"></div><div class="mn-sk-body"><div class="mn-sk-line mn-sk-w60"></div><div class="mn-sk-line mn-sk-w100"></div><div class="mn-sk-line mn-sk-w80"></div></div></div></div>
      <div class="col-md-6 col-lg-4 d-none d-lg-block"><div class="mn-skeleton-card"><div class="mn-sk-img"></div><div class="mn-sk-body"><div class="mn-sk-line mn-sk-w60"></div><div class="mn-sk-line mn-sk-w100"></div><div class="mn-sk-line mn-sk-w80"></div></div></div></div>
    </div>

    <div id="empty" class="empty-state d-none">
      <i class="bi bi-search" aria-hidden="true"></i>
      <p>Aucune pièce ne correspond à vos critères.</p>
      <button type="button" class="btn-mn-outline" id="clear-filters">Effacer les filtres</button>
    </div>
  </div>
</section>

<section id="rv-section" class="py-5" style="display:none;border-top:1px solid var(--mn-border)">
  <div class="container">
    <div class="section-eyebrow">Historique</div>
    <h3 class="section-title mb-5">Articles récemment vus</h3>
    <div class="row g-4" id="rv-grid"></div>
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
var params = new URLSearchParams(location.search);
var state = { cat: params.get('cat') || 'Tous', search: '', maxPrice: 200000, sort: 'featured' };

function showToast(msg) {
  var t = document.getElementById('mn-toast');
  document.getElementById('mn-toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(function () { t.classList.remove('show'); }, 3200);
}

var cats = ['Tous', 'Femme', 'Homme', 'Accessoires'];
var catsEl = document.getElementById('f-cats');
catsEl.innerHTML = cats.map(function (c) {
  return '<button type="button" class="filter-chip' + (state.cat === c ? ' active' : '') + '" data-cat="' + c + '">' + c + '</button>';
}).join('');
catsEl.querySelectorAll('.filter-chip').forEach(function (b) {
  b.addEventListener('click', function () {
    state.cat = b.dataset.cat;
    catsEl.querySelectorAll('.filter-chip').forEach(function (x) {
      x.classList.toggle('active', x.dataset.cat === state.cat);
    });
    render();
  });
});

document.getElementById('f-search').addEventListener('input', function (e) {
  state.search = e.target.value.toLowerCase(); render();
});
document.getElementById('f-price').addEventListener('input', function (e) {
  state.maxPrice = +e.target.value;
  var formatted = state.maxPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  document.getElementById('f-price-val').textContent = formatted;
  render();
});
document.getElementById('f-sort').addEventListener('change', function (e) { state.sort = e.target.value; render(); });
document.getElementById('clear-filters').addEventListener('click', function () {
  state.cat = 'Tous'; state.search = ''; state.maxPrice = 200000; state.sort = 'featured';
  document.getElementById('f-search').value = '';
  document.getElementById('f-price').value = 200000;
  document.getElementById('f-price-val').textContent = '200 000';
  document.getElementById('f-sort').value = 'featured';
  catsEl.querySelectorAll('.filter-chip').forEach(function (x) { x.classList.toggle('active', x.dataset.cat === 'Tous'); });
  render();
});

function render() {
  var list = window.MN_PRODUCTS.filter(function (p) {
    return (state.cat === 'Tous' || p.category === state.cat) &&
      p.price <= state.maxPrice &&
      (!state.search || p.name.toLowerCase().includes(state.search) || p.short.toLowerCase().includes(state.search));
  });
  if (state.sort === 'asc') list.sort(function (a, b) { return a.price - b.price; });
  if (state.sort === 'desc') list.sort(function (a, b) { return b.price - a.price; });

  document.getElementById('product-count').textContent = list.length + (list.length === 1 ? ' pièce' : ' pièces');
  document.getElementById('empty').classList.toggle('d-none', list.length > 0);
  document.getElementById('product-grid').innerHTML = list.map(function (p) {
    var fav = isFav(p.id);
    return '<div class="col-md-6 col-lg-4">' +
      '<div class="product-card" style="position:relative">' +
        (p.badge ? '<span class="badge-mn">' + p.badge + '</span>' : '') +
        '<button type="button" onclick="toggleFav(\'' + p.id + '\')" data-fav="' + p.id + '" class="mn-fav-btn" aria-label="Favoris">' +
          (fav ? '<i class="bi bi-heart-fill" style="color:#e55"></i>' : '<i class="bi bi-heart"></i>') +
        '</button>' +
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
            '<button type="button" class="btn-mn btn-mn-sm" onclick="addFromGrid(\'' + p.id + '\',this)">' +
              '<span>Ajouter</span><i class="bi bi-bag-plus"></i>' +
            '</button>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  }).join('');
}

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

function addFromGrid(id, btn) {
  var p = window.MN_GET_PRODUCT(id);
  if (!p) return;
  var imgEl = btn ? btn.closest('.product-card').querySelector('.img-wrap img') : null;
  flyToCart(p.image, imgEl);
  window.MN_CART.add(p, p.sizes[0], p.colors[0], 1);
  showToast(p.name + ' ajouté au panier.');
}

/* ── Favoris ── */
var FAV_KEY = 'mn-favorites';
function getFavs() { try { return JSON.parse(localStorage.getItem(FAV_KEY)) || []; } catch(e) { return []; } }
function isFav(id) { return getFavs().indexOf(id) !== -1; }
function toggleFav(id) {
  var ids = getFavs();
  var idx = ids.indexOf(id);
  if (idx === -1) { ids.push(id); showToast('Ajouté aux favoris ♥'); }
  else            { ids.splice(idx, 1); showToast('Retiré des favoris.'); }
  localStorage.setItem(FAV_KEY, JSON.stringify(ids));
  /* Met à jour le bouton sur la carte */
  var btn = document.querySelector('[data-fav="' + id + '"]');
  if (btn) {
    btn.innerHTML = idx === -1
      ? '<i class="bi bi-heart-fill" style="color:#e55"></i>'
      : '<i class="bi bi-heart"></i>';
  }
}

render();

/* Recently viewed */
(function () {
  var KEY = 'mn-recently-viewed';
  var stored = [];
  try { stored = JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) {}
  if (!stored.length) return;
  var rvSection = document.getElementById('rv-section');
  var rvGrid    = document.getElementById('rv-grid');
  var cards = stored.slice(0, 6).map(function (item) {
    var p = window.MN_GET_PRODUCT(item.id) || item;
    return '<div class="col-md-6 col-lg-4">' +
      '<div class="product-card">' +
        '<div class="img-wrap">' +
          '<a href="product-detail.php?id=' + p.id + '"><img src="' + p.image + '" alt="' + p.name + '"></a>' +
          '<div class="img-overlay"><a href="product-detail.php?id=' + p.id + '" class="img-overlay-cta">Voir la pièce <i class="bi bi-arrow-right"></i></a></div>' +
        '</div>' +
        '<div class="body">' +
          '<div class="cat">' + p.category + '</div>' +
          '<div class="name">' + p.name + '</div>' +
          '<div class="d-flex justify-content-between align-items-center">' +
            '<span class="price">' + window.MN_FORMAT_PRICE(p.price) + '</span>' +
            '<button type="button" class="btn-mn btn-mn-sm" onclick="addFromGrid(\'' + p.id + '\',this)">' +
              '<span>Ajouter</span><i class="bi bi-bag-plus"></i>' +
            '</button>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  });
  rvGrid.innerHTML = cards.join('');
  rvSection.style.display = '';
})();
</script>
</body>
</html>
