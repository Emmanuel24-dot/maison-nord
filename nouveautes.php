<?php require_once 'includes/auth.php'; $active = 'nouveautes'; ?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nouveautés — Maison Nord</title>
<meta name="description" content="Découvrez les nouvelles pièces Maison Nord : les dernières additions à notre collection.">
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

<header class="nouv-hero">
  <div class="nouv-hero-deco" aria-hidden="true"></div>
  <div class="container">
    <div class="nouv-hero-inner">
      <div class="nouv-hero-eyebrow">Maison Nord &mdash; Collection</div>
      <h1 class="nouv-hero-title">Nouveautés</h1>
      <p class="nouv-hero-sub">Les dernières pièces ajoutées à notre collection</p>
    </div>
  </div>
</header>

<section class="pb-5 pt-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <span class="product-count" id="nouv-count" aria-live="polite"></span>
      <select id="nouv-sort" class="boutique-sort-select" style="width:auto" aria-label="Trier">
        <option value="recent">Plus récents</option>
        <option value="asc">Prix ↑</option>
        <option value="desc">Prix ↓</option>
      </select>
    </div>

    <div class="row g-4" id="nouv-grid">
      <div class="col-md-6 col-lg-4"><div class="mn-skeleton-card"><div class="mn-sk-img"></div><div class="mn-sk-body"><div class="mn-sk-line mn-sk-w60"></div><div class="mn-sk-line mn-sk-w100"></div><div class="mn-sk-line mn-sk-w80"></div></div></div></div>
      <div class="col-md-6 col-lg-4"><div class="mn-skeleton-card"><div class="mn-sk-img"></div><div class="mn-sk-body"><div class="mn-sk-line mn-sk-w60"></div><div class="mn-sk-line mn-sk-w100"></div><div class="mn-sk-line mn-sk-w80"></div></div></div></div>
      <div class="col-md-6 col-lg-4 d-none d-md-block"><div class="mn-skeleton-card"><div class="mn-sk-img"></div><div class="mn-sk-body"><div class="mn-sk-line mn-sk-w60"></div><div class="mn-sk-line mn-sk-w100"></div><div class="mn-sk-line mn-sk-w80"></div></div></div></div>
    </div>

    <div id="nouv-empty" class="empty-state d-none">
      <i class="bi bi-stars" aria-hidden="true"></i>
      <p>Aucune nouveauté pour le moment.<br>Revenez bientôt !</p>
      <a href="boutique.php" class="btn-mn-outline">Voir toute la collection <i class="bi bi-arrow-right"></i></a>
    </div>
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
var sortVal = 'recent';

function showToast(msg) {
  var t = document.getElementById('mn-toast');
  document.getElementById('mn-toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(function () { t.classList.remove('show'); }, 3200);
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

function isNew(p) {
  if (p.badge === 'Nouveau') return true;
  if (!p.created_at) return false;
  var d = new Date(p.created_at);
  var ago30 = new Date();
  ago30.setDate(ago30.getDate() - 30);
  return d >= ago30;
}

function addFromGrid(id, btn) {
  var p = window.MN_GET_PRODUCT(id);
  if (!p) return;
  var imgEl = btn ? btn.closest('.product-card').querySelector('.img-wrap img') : null;
  flyToCart(p.image, imgEl);
  window.MN_CART.add(p, p.sizes[0], p.colors[0], 1);
  showToast(p.name + ' ajouté au panier.');
}

var FAV_KEY = 'mn-favorites';
function getFavs() { try { return JSON.parse(localStorage.getItem(FAV_KEY)) || []; } catch(e) { return []; } }
function isFav(id) { return getFavs().indexOf(id) !== -1; }
function toggleFav(id) {
  var ids = getFavs();
  var idx = ids.indexOf(id);
  if (idx === -1) { ids.push(id); showToast('Ajouté aux favoris ♥'); }
  else            { ids.splice(idx, 1); showToast('Retiré des favoris.'); }
  localStorage.setItem(FAV_KEY, JSON.stringify(ids));
  var btn = document.querySelector('[data-fav="' + id + '"]');
  if (btn) {
    btn.innerHTML = idx === -1
      ? '<i class="bi bi-heart-fill" style="color:#e55"></i>'
      : '<i class="bi bi-heart"></i>';
  }
}

function render() {
  var list = window.MN_PRODUCTS.filter(isNew);
  if (sortVal === 'asc')  list.sort(function (a, b) { return a.price - b.price; });
  if (sortVal === 'desc') list.sort(function (a, b) { return b.price - a.price; });
  if (sortVal === 'recent') list.sort(function (a, b) {
    return new Date(b.created_at || 0) - new Date(a.created_at || 0);
  });

  document.getElementById('nouv-count').textContent = list.length + (list.length === 1 ? ' pièce' : ' pièces');
  document.getElementById('nouv-empty').classList.toggle('d-none', list.length > 0);
  document.getElementById('nouv-grid').innerHTML = list.map(function (p) {
    var fav = isFav(p.id);
    return '<div class="col-md-6 col-lg-4">' +
      '<div class="product-card" style="position:relative">' +
        '<span class="badge-mn">Nouveau</span>' +
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

document.getElementById('nouv-sort').addEventListener('change', function (e) { sortVal = e.target.value; render(); });

render();
</script>
</body>
</html>
