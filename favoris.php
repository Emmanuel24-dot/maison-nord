<?php require_once 'includes/auth.php'; $active = ''; ?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mes favoris — Maison Nord</title>
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('mn-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/styles.css">
<link rel="manifest" href="/maison-nord/manifest.json">
<meta name="theme-color" content="#c89468">
</head>
<body>
<?php include 'includes/nav.php'; ?>

<main style="min-height:80vh;padding:4rem 0 5rem">
  <div class="container">
    <div class="section-eyebrow">Wishlist</div>
    <h1 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:400;color:var(--mn-ink);margin:.25rem 0 .5rem">Mes favoris</h1>
    <p style="color:var(--mn-muted);font-size:.875rem;margin-bottom:2.5rem" id="fav-count"></p>

    <div class="row g-4" id="fav-grid"></div>

    <div id="fav-empty" style="display:none;text-align:center;padding:5rem 1rem">
      <i class="bi bi-heart" style="font-size:3rem;color:var(--mn-border-2);display:block;margin-bottom:1rem"></i>
      <p style="color:var(--mn-muted);margin-bottom:1.5rem">Vous n'avez pas encore de favoris.</p>
      <a href="boutique.php" class="btn-mn"><span>Découvrir la boutique</span><i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</main>

<div class="mn-toast" id="mn-toast" role="status" aria-live="polite">
  <i class="bi bi-heart-fill" style="color:#e55"></i>
  <span id="mn-toast-msg">Retiré des favoris.</span>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/products.php"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/cart.js?v=2"></script>
<script>
var FAV_KEY = 'mn-favorites';

function getFavs() { try { return JSON.parse(localStorage.getItem(FAV_KEY)) || []; } catch(e) { return []; } }
function saveFavs(ids) { localStorage.setItem(FAV_KEY, JSON.stringify(ids)); }

function showToast(msg) {
  var t = document.getElementById('mn-toast');
  document.getElementById('mn-toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, 2800);
}

function removeFav(id) {
  var ids = getFavs().filter(function(x){ return x !== id; });
  saveFavs(ids);
  showToast('Retiré des favoris.');
  render();
}

function render() {
  var ids  = getFavs();
  var grid = document.getElementById('fav-grid');
  var empty = document.getElementById('fav-empty');
  var count = document.getElementById('fav-count');

  if (!ids.length) {
    grid.innerHTML = '';
    empty.style.display = '';
    count.textContent = '';
    return;
  }
  empty.style.display = 'none';
  count.textContent = ids.length + (ids.length > 1 ? ' pièces sauvegardées' : ' pièce sauvegardée');

  grid.innerHTML = ids.map(function(id) {
    var p = window.MN_GET_PRODUCT(id);
    if (!p) return '';
    return '<div class="col-md-6 col-lg-4" id="fav-' + id + '">' +
      '<div class="product-card" style="position:relative">' +
        '<button onclick="removeFav(\'' + id + '\')" style="position:absolute;top:.75rem;right:.75rem;z-index:2;background:var(--mn-surface);border:none;width:2rem;height:2rem;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:var(--mn-shadow-sm)" title="Retirer des favoris" aria-label="Retirer des favoris">' +
          '<i class="bi bi-heart-fill" style="color:#e55;font-size:.85rem"></i>' +
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
            '<button type="button" class="btn-mn btn-mn-sm" onclick="addCart(\'' + p.id + '\')">' +
              '<span>Ajouter</span><i class="bi bi-bag-plus"></i>' +
            '</button>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  }).join('');
}

function addCart(id) {
  var p = window.MN_GET_PRODUCT(id);
  if (!p) return;
  window.MN_CART.add(p, p.sizes[0], p.colors[0], 1);
  showToast(p.name + ' ajouté au panier.');
  window.MN_CART_OPEN();
}

render();
</script>
</body>
</html>
