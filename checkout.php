<?php
require_once 'includes/auth.php';

if (!mn_is_logged_in()) {
    header('Location: login.php?redirect=' . urlencode('checkout.php'));
    exit;
}

$user  = mn_current_user();
$error = mn_flash_get('checkout_error');

/* Zones de livraison */
$zonesStmt = mn_db()->query('SELECT id, name, cities, fee, delay FROM mn_delivery_zones WHERE active=1 ORDER BY sort_order, id');
$zones = $zonesStmt->fetchAll();

/* Adresses sauvegardées */
$addrStmt = mn_db()->prepare(
    'SELECT id, label, prenom, nom, phone, address, city, is_default FROM mn_addresses WHERE user_id=? ORDER BY is_default DESC, id ASC'
);
$addrStmt->execute(array($user['id']));
$savedAddresses = $addrStmt->fetchAll();

$active = '';
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Commander — Maison Nord</title>
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('mn-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/styles.css">
<link rel="manifest" href="/maison-nord/manifest.json">
<meta name="theme-color" content="#c89468">
<link rel="apple-touch-icon" href="/maison-nord/assets/icons/icon-192.svg">
<style>
/* ── Adresses sauvegardées ── */
.co-addr-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.75rem;margin-bottom:1.25rem}
.co-addr-card{border:2px solid var(--mn-border);border-radius:10px;padding:.875rem 1rem;cursor:pointer;transition:border-color .2s,background .2s;font-size:.82rem;line-height:1.5;color:var(--mn-muted)}
.co-addr-card:hover{border-color:var(--mn-accent,#c89468)}
.co-addr-card.selected{border-color:var(--mn-accent,#c89468);background:rgba(200,148,104,.07)}
.co-addr-card-name{font-weight:700;font-size:.85rem;color:var(--mn-ink);margin-bottom:.2rem}
.co-addr-card-label{display:inline-block;font-size:.65rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;background:var(--mn-accent,#c89468);color:#fff;padding:.1rem .5rem;border-radius:99px;margin-bottom:.4rem}
/* ── Zone livraison ── */
.co-zone-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.75rem;margin-bottom:1.25rem}
.co-zone-card{border:2px solid var(--mn-border);border-radius:10px;padding:.875rem 1rem;cursor:pointer;transition:border-color .2s,background .2s}
.co-zone-card:hover{border-color:var(--mn-accent,#c89468)}
.co-zone-card.selected{border-color:var(--mn-accent,#c89468);background:rgba(200,148,104,.07)}
.co-zone-name{font-weight:700;font-size:.875rem;color:var(--mn-ink);margin-bottom:.2rem}
.co-zone-cities{font-size:.75rem;color:var(--mn-muted);margin-bottom:.35rem;line-height:1.4}
.co-zone-fee{font-family:"Cormorant Garamond",Georgia,serif;font-size:1.1rem;font-weight:600;color:var(--mn-ink)}
.co-zone-delay{font-size:.72rem;color:var(--mn-muted)}
/* ── Récapitulatif ── */
.co-total-block{border-top:1px solid var(--mn-border);margin-top:.75rem;padding-top:.75rem}
.co-total-row{display:flex;justify-content:space-between;align-items:baseline;padding:.3rem 0;font-size:.9rem}
.co-total-row.grand{font-family:"Cormorant Garamond",Georgia,serif;font-size:1.3rem;font-weight:600;padding-top:.6rem;border-top:1px solid var(--mn-border-2);margin-top:.3rem}
/* ── Méthodes paiement ── */
.co-payment-methods{display:flex;flex-wrap:wrap;gap:.5rem;margin:.75rem 0 1.25rem}
.co-pm-badge{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .8rem;border-radius:8px;border:1px solid var(--mn-border);font-size:.75rem;font-weight:600;color:var(--mn-ink);background:var(--mn-surface)}
</style>
</head>
<body>
<?php include 'includes/nav.php'; ?>

<main class="mn-checkout-main">
<div class="container">

  <div class="mn-checkout-header reveal">
    <div class="section-eyebrow center">Finalisation</div>
    <h1 class="section-title text-center">Votre commande</h1>
  </div>

  <?php if ($error): ?>
  <div class="mn-auth-error mb-4">
    <i class="bi bi-exclamation-circle"></i>
    <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <div class="row g-5 mn-checkout-row">

    <!-- ── Formulaire ── -->
    <div class="col-lg-7 reveal">
      <div class="mn-profile-card">
        <h2 class="mn-profile-card-title">Informations de livraison</h2>

        <form id="checkout-form" method="POST" action="api/checkout-process.php" novalidate>
          <input type="hidden" name="csrf"          value="<?= htmlspecialchars(mn_csrf_token()) ?>">
          <input type="hidden" name="cart_json"     id="checkout-cart-json" value="">
          <input type="hidden" name="delivery_fee"  id="co-fee-input"  value="0">
          <input type="hidden" name="delivery_zone" id="co-zone-input" value="">

          <!-- Adresses sauvegardées -->
          <?php if (!empty($savedAddresses)): ?>
          <div style="margin-bottom:1.25rem">
            <p class="form-label-mn" style="margin-bottom:.6rem">Mes adresses enregistrées</p>
            <div class="co-addr-grid">
              <?php foreach ($savedAddresses as $addr): ?>
              <div class="co-addr-card<?= $addr['is_default'] ? ' selected' : '' ?>"
                   data-addr='<?= htmlspecialchars(json_encode(array(
                       'prenom'  => $addr['prenom']  ?: $user['prenom'],
                       'nom'     => $addr['nom']     ?: $user['nom'],
                       'phone'   => $addr['phone'],
                       'address' => $addr['address'],
                       'city'    => $addr['city'],
                   )), ENT_QUOTES) ?>'>
                <div class="co-addr-card-label"><?= htmlspecialchars($addr['label']) ?></div>
                <div class="co-addr-card-name">
                  <?= htmlspecialchars(($addr['prenom'] ?: $user['prenom']).' '.($addr['nom'] ?: $user['nom'])) ?>
                </div>
                <?= htmlspecialchars($addr['address']) ?><br>
                <?= htmlspecialchars($addr['city']) ?>
                <?php if ($addr['phone']): ?><br><?= htmlspecialchars($addr['phone']) ?><?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
            <p class="form-label-mn" style="margin-bottom:.6rem;margin-top:.25rem">Ou saisir une nouvelle adresse</p>
          </div>
          <?php endif; ?>

          <!-- Champs identité -->
          <div class="row g-3">
            <div class="col-6">
              <div class="mn-form-group">
                <label class="form-label-mn">Prénom</label>
                <input type="text" name="prenom" id="co-prenom" class="form-control-mn"
                       value="<?= htmlspecialchars($user['prenom']) ?>" required>
              </div>
            </div>
            <div class="col-6">
              <div class="mn-form-group">
                <label class="form-label-mn">Nom</label>
                <input type="text" name="nom" id="co-nom" class="form-control-mn"
                       value="<?= htmlspecialchars($user['nom']) ?>" required>
              </div>
            </div>
          </div>

          <div class="mn-form-group">
            <label class="form-label-mn">Adresse email</label>
            <input type="email" name="email" class="form-control-mn"
                   value="<?= htmlspecialchars($user['email']) ?>" required>
          </div>

          <div class="mn-form-group">
            <label class="form-label-mn">Téléphone <span class="mn-form-hint">(Mobile Money)</span></label>
            <input type="tel" name="phone" id="co-phone" class="form-control-mn"
                   placeholder="+228 90 00 00 00" required autocomplete="tel">
          </div>

          <div class="mn-form-group">
            <label class="form-label-mn">Adresse de livraison</label>
            <input type="text" name="address" id="co-address" class="form-control-mn"
                   placeholder="Rue, quartier, repère..." required>
          </div>

          <div class="mn-form-group">
            <label class="form-label-mn">Ville</label>
            <input type="text" name="city" id="co-city" class="form-control-mn"
                   value="Lomé" required>
          </div>

          <!-- Zones de livraison -->
          <?php if (!empty($zones)): ?>
          <div class="mn-form-group">
            <label class="form-label-mn">Zone de livraison</label>
            <div class="co-zone-grid">
              <?php foreach ($zones as $i => $zone): ?>
              <div class="co-zone-card<?= $i === 0 ? ' selected' : '' ?>"
                   data-zone-id="<?= $zone['id'] ?>"
                   data-zone-name="<?= htmlspecialchars($zone['name']) ?>"
                   data-zone-fee="<?= (int)$zone['fee'] ?>"
                   data-zone-delay="<?= htmlspecialchars($zone['delay']) ?>">
                <div class="co-zone-name"><?= htmlspecialchars($zone['name']) ?></div>
                <div class="co-zone-cities"><?= htmlspecialchars($zone['cities']) ?></div>
                <div class="co-zone-fee"><?= number_format($zone['fee'], 0, ',', ' ') ?> FCFA</div>
                <div class="co-zone-delay"><i class="bi bi-clock" style="font-size:.65rem"></i> <?= htmlspecialchars($zone['delay']) ?></div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <div class="mn-form-group">
            <label class="form-label-mn">Instructions <span class="mn-form-hint">(optionnel)</span></label>
            <textarea name="notes" class="form-control-mn" rows="2"
                      placeholder="Instructions particulières, interphone..."></textarea>
          </div>

          <!-- Méthodes de paiement acceptées -->
          <div style="margin-bottom:1.25rem">
            <p class="form-label-mn" style="margin-bottom:.5rem">Méthodes de paiement acceptées</p>
            <div class="co-payment-methods">
              <span class="co-pm-badge"><i class="bi bi-phone-fill" style="color:#ff6600"></i> Moov Money</span>
              <span class="co-pm-badge"><i class="bi bi-phone-fill" style="color:#00a651"></i> T-Money</span>
              <span class="co-pm-badge"><i class="bi bi-phone-fill" style="color:#ff8000"></i> Orange Money</span>
              <span class="co-pm-badge"><i class="bi bi-credit-card-fill" style="color:#1a56db"></i> Carte bancaire</span>
            </div>
          </div>

          <button type="submit" class="btn-mn w-100 justify-content-center" id="checkout-submit-btn">
            <span>Procéder au paiement</span><i class="bi bi-lock"></i>
          </button>

          <p class="mn-checkout-secure">
            <i class="bi bi-shield-check"></i>
            Paiement 100% sécurisé via FedaPay
          </p>
        </form>
      </div>
    </div>

    <!-- ── Récapitulatif ── -->
    <div class="col-lg-5 reveal reveal-delay-2">
      <div class="mn-checkout-summary" style="position:sticky;top:6rem">
        <h2 class="mn-profile-card-title">Récapitulatif</h2>
        <div id="checkout-items">
          <div class="text-center py-4" style="color:var(--mn-muted);">
            <i class="bi bi-bag" style="font-size:2rem;"></i>
            <p class="mt-2">Chargement du panier…</p>
          </div>
        </div>
        <div class="co-total-block">
          <div class="co-total-row">
            <span>Sous-total articles</span>
            <span id="co-subtotal">—</span>
          </div>
          <div class="co-total-row">
            <span>Frais de livraison <span id="co-zone-label" style="font-size:.75rem;color:var(--mn-muted)"></span></span>
            <span id="co-fee-display">—</span>
          </div>
          <div class="co-total-row grand">
            <span>Total</span>
            <span id="checkout-total">—</span>
          </div>
        </div>
        <div id="co-delivery-eta" style="margin-top:.75rem;font-size:.8rem;color:var(--mn-muted);display:flex;align-items:center;gap:.4rem">
          <i class="bi bi-truck"></i>
          <span id="co-eta-text">Sélectionnez une zone</span>
        </div>
      </div>
    </div>

  </div>
</div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/products.php"></script>
<script src="assets/js/cart.js?v=2"></script>
<script src="assets/js/app.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var items = window.MN_CART ? window.MN_CART.items : [];
  if (!items || items.length === 0) { window.location.href = 'boutique.php'; return; }

  var fmt = window.MN_FORMAT_PRICE || function(n){ return Number(n).toLocaleString('fr-FR') + ' FCFA'; };
  var subtotal = 0;
  var deliveryFee = 0;

  /* ── Afficher articles ── */
  var html = '';
  items.forEach(function (item) {
    subtotal += (item.price || 0) * (item.qty || 1);
    html += '<div class="mn-checkout-item">' +
      '<img src="' + (item.image||'') + '" alt="' + (item.name||'') + '" class="mn-checkout-item-img">' +
      '<div class="mn-checkout-item-info">' +
        '<div class="mn-checkout-item-name">' + (item.name||'') + '</div>' +
        '<div class="mn-checkout-item-meta">' + (item.size||'') + (item.size&&item.color?' · ':'') + (item.color||'') + ' × ' + (item.qty||1) + '</div>' +
      '</div>' +
      '<div class="mn-checkout-item-price">' + fmt(item.price * item.qty) + '</div>' +
    '</div>';
  });
  document.getElementById('checkout-items').innerHTML = html;
  document.getElementById('checkout-cart-json').value = JSON.stringify(items);

  function updateTotal() {
    document.getElementById('co-subtotal').textContent  = fmt(subtotal);
    document.getElementById('co-fee-display').textContent = fmt(deliveryFee);
    document.getElementById('checkout-total').textContent = fmt(subtotal + deliveryFee);
    document.getElementById('co-fee-input').value = deliveryFee;
  }
  updateTotal();

  /* ── Zones livraison ── */
  var zoneCards = document.querySelectorAll('.co-zone-card');
  if (zoneCards.length > 0) {
    /* Sélectionner la première par défaut */
    var firstZone = zoneCards[0];
    deliveryFee = parseInt(firstZone.dataset.zoneFee) || 0;
    document.getElementById('co-fee-input').value  = deliveryFee;
    document.getElementById('co-zone-input').value = firstZone.dataset.zoneName || '';
    document.getElementById('co-zone-label').textContent = '· ' + (firstZone.dataset.zoneName || '');
    document.getElementById('co-eta-text').textContent   = 'Délai estimé : ' + (firstZone.dataset.zoneDelay || '');
    updateTotal();
  }

  zoneCards.forEach(function (card) {
    card.addEventListener('click', function () {
      zoneCards.forEach(function (c) { c.classList.remove('selected'); });
      card.classList.add('selected');
      deliveryFee = parseInt(card.dataset.zoneFee) || 0;
      document.getElementById('co-fee-input').value  = deliveryFee;
      document.getElementById('co-zone-input').value = card.dataset.zoneName || '';
      document.getElementById('co-zone-label').textContent = '· ' + (card.dataset.zoneName || '');
      document.getElementById('co-eta-text').textContent   = 'Délai estimé : ' + (card.dataset.zoneDelay || '');
      updateTotal();
    });
  });

  /* ── Adresses sauvegardées ── */
  var addrCards = document.querySelectorAll('.co-addr-card');
  addrCards.forEach(function (card) {
    card.addEventListener('click', function () {
      addrCards.forEach(function (c) { c.classList.remove('selected'); });
      card.classList.add('selected');
      try {
        var a = JSON.parse(card.dataset.addr || '{}');
        if (a.prenom)  document.getElementById('co-prenom').value  = a.prenom;
        if (a.nom)     document.getElementById('co-nom').value     = a.nom;
        if (a.phone)   document.getElementById('co-phone').value   = a.phone;
        if (a.address) document.getElementById('co-address').value = a.address;
        if (a.city)    document.getElementById('co-city').value    = a.city;
      } catch(e) { /* JSON invalide */ }
    });
  });

  /* ── Soumission ── */
  document.getElementById('checkout-form').addEventListener('submit', function () {
    var btn = document.getElementById('checkout-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<span>Redirection vers le paiement…</span><i class="bi bi-hourglass-split"></i>';
  });
});
</script>
</body>
</html>
