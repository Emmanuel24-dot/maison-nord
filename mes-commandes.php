<?php
require_once 'includes/auth.php';

if (!mn_is_logged_in()) {
    header('Location: login.php?redirect=' . urlencode('mes-commandes.php'));
    exit;
}

$user = mn_current_user();

/* ── Commandes du client ── */
$oStmt = mn_db()->prepare(
    'SELECT o.id, o.total, o.status, o.created_at,
            oi.name AS item_name, oi.qty, oi.price, oi.size, oi.color
     FROM mn_orders o
     LEFT JOIN mn_order_items oi ON oi.order_id = o.id
     WHERE o.user_id = ?
     ORDER BY o.created_at DESC, oi.id ASC'
);
$oStmt->execute(array($user['id']));
$oRows = $oStmt->fetchAll();

$orders = array();
foreach ($oRows as $oRow) {
    $oid = $oRow['id'];
    if (!isset($orders[$oid])) {
        $orders[$oid] = array(
            'id'         => $oid,
            'total'      => $oRow['total'],
            'status'     => $oRow['status'],
            'created_at' => $oRow['created_at'],
            'items'      => array(),
        );
    }
    if ($oRow['item_name']) {
        $orders[$oid]['items'][] = array(
            'name'  => $oRow['item_name'],
            'qty'   => $oRow['qty'],
            'price' => $oRow['price'],
            'size'  => $oRow['size'],
            'color' => $oRow['color'],
        );
    }
}

$statusMap = array(
    'pending'   => array('label' => 'En attente'),
    'confirmed' => array('label' => 'Validée'),
    'shipped'   => array('label' => 'Expédiée'),
    'delivered' => array('label' => 'Livrée'),
    'cancelled' => array('label' => 'Annulée'),
);
$trackSteps  = array('pending', 'confirmed', 'shipped', 'delivered');
$trackLabels = array('En attente', 'Validée', 'Expédiée', 'Livrée');

$active = '';
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mes commandes — Maison Nord</title>
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('mn-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/styles.css">
<link rel="manifest" href="/maison-nord/manifest.json">
<meta name="theme-color" content="#c89468">
<link rel="apple-touch-icon" href="/maison-nord/assets/icons/icon-192.svg">
<style>
/* ── Page header ── */
.mc-hero{padding:4rem 0 2.5rem;border-bottom:1px solid var(--mn-border)}
.mc-hero-inner{display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:1rem}
.mc-hero-title{font-family:"Cormorant Garamond",Georgia,serif;font-size:2.4rem;font-weight:400;color:var(--mn-ink);margin:0;letter-spacing:-.02em;line-height:1}
.mc-hero-sub{font-size:.8rem;color:var(--mn-muted);margin-top:.4rem;letter-spacing:.03em}
.mc-back-link{display:inline-flex;align-items:center;gap:.4rem;font-size:.8rem;color:var(--mn-muted);text-decoration:none;transition:color .2s}
.mc-back-link:hover{color:var(--mn-accent,#c89468)}

/* ── Filtres ── */
.mc-filters{display:flex;flex-wrap:wrap;gap:.5rem;padding:1.75rem 0 1.25rem}
.mc-filter-btn{display:inline-flex;align-items:center;gap:.35rem;padding:.45rem 1.1rem;border-radius:99px;border:1px solid var(--mn-border);background:transparent;font-size:.78rem;font-weight:500;color:var(--mn-muted);cursor:pointer;transition:all .2s;white-space:nowrap}
.mc-filter-btn:hover{border-color:var(--mn-accent,#c89468);color:var(--mn-accent,#c89468)}
.mc-filter-btn.active{background:var(--mn-accent,#c89468);border-color:var(--mn-accent,#c89468);color:#fff}
.mc-filter-count{font-size:.68rem;opacity:.75}

/* ── Résultats ── */
.mc-results-info{font-size:.8rem;color:var(--mn-muted);margin-bottom:1.25rem}
.mc-no-results{text-align:center;padding:4rem 2rem;color:var(--mn-muted)}
.mc-no-results svg{opacity:.15;display:block;margin:0 auto 1rem}
.mc-no-results p{margin:0;font-size:.9rem}

/* ── Cartes commandes ── */
.mn-ocard{background:var(--mn-surface);border:1px solid var(--mn-border);border-radius:16px;padding:1.75rem;margin-bottom:1.25rem;box-shadow:var(--mn-shadow-sm);transition:box-shadow .25s,border-color .25s}
.mn-ocard:hover{box-shadow:var(--mn-shadow-md);border-color:var(--mn-border-2)}
.mn-ocard-head{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem}
.mn-ocard-left{display:flex;align-items:baseline;gap:.6rem;flex-wrap:wrap}
.mn-ocard-num{font-weight:700;font-size:.85rem;letter-spacing:.07em;text-transform:uppercase;color:var(--mn-ink)}
.mn-ocard-date{font-size:.78rem;color:var(--mn-muted)}
.mn-ocard-right{display:flex;align-items:center;gap:.85rem;flex-wrap:wrap}
.mn-ocard-total{font-family:"Cormorant Garamond",Georgia,serif;font-size:1.3rem;font-weight:600;color:var(--mn-ink);line-height:1}
.mn-ocard-total em{font-size:.72rem;font-style:normal;opacity:.6}

/* Badges statut */
.mn-ocard-badge{display:inline-flex;align-items:center;padding:.3rem .9rem;border-radius:99px;font-size:.68rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;white-space:nowrap}
.mn-badge-pending  {background:#fef3c7;color:#92400e}
.mn-badge-confirmed{background:#dbeafe;color:#1e40af}
.mn-badge-shipped  {background:#ede9fe;color:#5b21b6}
.mn-badge-delivered{background:#dcfce7;color:#166534}
.mn-badge-cancelled{background:#fee2e2;color:#991b1b}
[data-theme=dark] .mn-badge-pending  {background:rgba(245,158,11,.18);color:#fde68a}
[data-theme=dark] .mn-badge-confirmed{background:rgba(59,130,246,.18);color:#bfdbfe}
[data-theme=dark] .mn-badge-shipped  {background:rgba(139,92,246,.18);color:#ddd6fe}
[data-theme=dark] .mn-badge-delivered{background:rgba(34,197,94,.18);color:#bbf7d0}
[data-theme=dark] .mn-badge-cancelled{background:rgba(239,68,68,.18);color:#fecaca}

/* Annulée */
.mn-ocard-cancelled{display:flex;align-items:center;gap:.5rem;padding:.75rem 1rem;border-radius:10px;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.15);font-size:.83rem;color:#b91c1c;margin-bottom:1.25rem}
[data-theme=dark] .mn-ocard-cancelled{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.2);color:#fca5a5}

/* Stepper */
.mn-otrack-wrap{position:relative;margin-bottom:1.75rem}
.mn-otrack-rail{position:absolute;top:1rem;left:1rem;right:1rem;height:2px;background:var(--mn-border,#e5e7eb);border-radius:2px;z-index:0}
.mn-otrack-fill{height:100%;max-width:100%;background:var(--mn-accent,#c89468);border-radius:2px;transition:width .6s ease}
.mn-otrack-steps{position:relative;z-index:1;display:flex;justify-content:space-between}
.mn-otrack-step{display:flex;flex-direction:column;align-items:center;gap:.4rem}
.mn-otrack-dot{width:2rem;height:2rem;border-radius:50%;border:2px solid var(--mn-border,#e5e7eb);background:var(--mn-surface,#fff);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .3s}
.mn-otrack-step.done .mn-otrack-dot{background:var(--mn-accent,#c89468);border-color:var(--mn-accent,#c89468);color:#fff;box-shadow:0 0 0 4px rgba(200,148,104,.15)}
.mn-otrack-step.current .mn-otrack-dot{border-color:var(--mn-accent,#c89468);box-shadow:0 0 0 5px rgba(200,148,104,.18)}
.mn-otrack-pulse{width:8px;height:8px;border-radius:50%;background:var(--mn-accent,#c89468);animation:otp 2s ease-in-out infinite}
@keyframes otp{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.45;transform:scale(.7)}}
.mn-otrack-lbl{font-size:.67rem;letter-spacing:.04em;color:var(--mn-muted,#6b7280);text-align:center;line-height:1.3}
.mn-otrack-step.done .mn-otrack-lbl,.mn-otrack-step.current .mn-otrack-lbl{color:var(--mn-ink,#1a1a1a);font-weight:600}

/* Articles */
.mn-ocard-items{border-top:1px solid var(--mn-border);padding-top:1rem;margin-top:.25rem}
.mn-ocard-item{display:flex;align-items:baseline;gap:.75rem;padding:.45rem 0;font-size:.875rem}
.mn-ocard-item+.mn-ocard-item{border-top:1px solid var(--mn-border)}
.mn-ocard-item-name{flex:1;color:var(--mn-ink)}
.mn-ocard-item-meta{color:var(--mn-muted);font-size:.8em}
.mn-ocard-item-qty{color:var(--mn-muted);white-space:nowrap;flex-shrink:0}
.mn-ocard-item-price{white-space:nowrap;flex-shrink:0;font-weight:500}
.mn-ocard-item-sum{margin-top:.5rem;padding-top:.75rem;border-top:2px solid var(--mn-border)!important;font-size:.95rem}

/* Toggle articles */
.mn-ocard-toggle{display:inline-flex;align-items:center;gap:.35rem;font-size:.78rem;color:var(--mn-muted);background:none;border:none;padding:0;cursor:pointer;transition:color .2s;margin-top:.5rem}
.mn-ocard-toggle:hover{color:var(--mn-accent,#c89468)}
.mn-ocard-toggle svg{transition:transform .25s}
.mn-ocard-toggle.open svg{transform:rotate(180deg)}

@media(max-width:576px){
  .mc-hero{padding:3rem 0 2rem}
  .mc-hero-title{font-size:1.9rem}
  .mn-ocard{padding:1.25rem}
  .mn-ocard-head{flex-direction:column;align-items:flex-start}
  .mn-otrack-lbl{font-size:.6rem}
  .mn-otrack-dot{width:1.6rem;height:1.6rem}
}
</style>
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main style="min-height:80vh;padding-bottom:4rem">
  <div class="container">

    <!-- ── Hero ── -->
    <div class="mc-hero">
      <div class="mc-hero-inner">
        <div>
          <h1 class="mc-hero-title">Mes commandes</h1>
          <p class="mc-hero-sub">
            <?= count($orders) ?> commande<?= count($orders) > 1 ? 's' : '' ?> au total
          </p>
        </div>
        <a href="profil.php" class="mc-back-link">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
          Mon profil
        </a>
      </div>
    </div>

    <?php if (empty($orders)): ?>
    <!-- ── État vide ── -->
    <div class="mc-no-results" style="margin-top:3rem">
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
        <path d="M3 6h18M16 10a4 4 0 0 1-8 0"/>
      </svg>
      <p style="font-size:1.05rem;margin-bottom:.5rem">Vous n'avez encore passé aucune commande.</p>
      <p style="font-size:.85rem;margin-bottom:1.5rem">Découvrez notre sélection et faites votre premier achat.</p>
      <a href="boutique.php" class="btn-mn">Découvrir la boutique</a>
    </div>

    <?php else: ?>

    <!-- ── Filtres ── -->
    <?php
    $counts = array('all' => count($orders), 'pending' => 0, 'confirmed' => 0, 'shipped' => 0, 'delivered' => 0, 'cancelled' => 0);
    foreach ($orders as $o) {
        $s = $o['status'];
        if (isset($counts[$s])) $counts[$s]++;
    }
    $filters = array(
        'all'       => 'Toutes',
        'pending'   => 'En attente',
        'confirmed' => 'Validées',
        'shipped'   => 'Expédiées',
        'delivered' => 'Livrées',
        'cancelled' => 'Annulées',
    );
    ?>
    <div class="mc-filters" role="group" aria-label="Filtrer par statut">
      <?php foreach ($filters as $fkey => $flabel):
        if ($fkey !== 'all' && $counts[$fkey] === 0) continue; ?>
      <button class="mc-filter-btn<?= $fkey === 'all' ? ' active' : '' ?>"
              data-filter="<?= $fkey ?>"
              type="button">
        <?= $flabel ?>
        <span class="mc-filter-count">(<?= $counts[$fkey] ?>)</span>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- ── Liste ── -->
    <div id="mc-orders-list">
      <?php foreach ($orders as $order):
        $st     = isset($statusMap[$order['status']]) ? $statusMap[$order['status']] : array('label' => $order['status']);
        $curIdx = array_search($order['status'], $trackSteps);
        if ($curIdx === false) $curIdx = -1;
        $fillPct = $curIdx <= 0 ? 0 : round($curIdx / (count($trackSteps) - 1) * 100);
        $dateStr = date('d M Y', strtotime($order['created_at']));
        $itemCount = count($order['items']);
      ?>
      <article class="mn-ocard" data-status="<?= $order['status'] ?>" data-order-id="<?= $order['id'] ?>">

        <!-- En-tête -->
        <div class="mn-ocard-head">
          <div class="mn-ocard-left">
            <span class="mn-ocard-num">CMD-<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></span>
            <span class="mn-ocard-date"><?= $dateStr ?></span>
          </div>
          <div class="mn-ocard-right">
            <span class="mn-ocard-badge mn-badge-<?= $order['status'] ?>"><?= htmlspecialchars($st['label']) ?></span>
            <span class="mn-ocard-total"><?= number_format($order['total'], 0, ',', ' ') ?> <em>FCFA</em></span>
          </div>
        </div>

        <?php if ($order['status'] === 'cancelled'): ?>
        <!-- Annulée -->
        <div class="mn-ocard-cancelled">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/>
          </svg>
          Cette commande a été annulée.
        </div>

        <?php else: ?>
        <!-- Stepper -->
        <div class="mn-otrack-wrap">
          <div class="mn-otrack-rail"><div class="mn-otrack-fill" style="width:<?= $fillPct ?>%"></div></div>
          <div class="mn-otrack-steps">
            <?php foreach ($trackSteps as $si => $step):
              $cls = $si < $curIdx ? 'done' : ($si === $curIdx ? 'current' : '');
            ?>
            <div class="mn-otrack-step <?= $cls ?>">
              <div class="mn-otrack-dot">
                <?php if ($si < $curIdx): ?>
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m5 12 5 5 9-10"/></svg>
                <?php elseif ($si === $curIdx): ?>
                  <span class="mn-otrack-pulse"></span>
                <?php endif; ?>
              </div>
              <span class="mn-otrack-lbl"><?= $trackLabels[$si] ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Articles (avec toggle) -->
        <?php if ($itemCount > 0): ?>
        <button class="mn-ocard-toggle open" type="button" aria-expanded="true" data-toggle-items>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
          <?= $itemCount ?> article<?= $itemCount > 1 ? 's' : '' ?>
        </button>
        <div class="mn-ocard-items" data-items-body>
          <?php foreach ($order['items'] as $item): ?>
          <div class="mn-ocard-item">
            <span class="mn-ocard-item-name">
              <?= htmlspecialchars($item['name']) ?>
              <?php if (!empty($item['size'])):  ?><span class="mn-ocard-item-meta"> · <?= htmlspecialchars($item['size'])  ?></span><?php endif; ?>
              <?php if (!empty($item['color'])): ?><span class="mn-ocard-item-meta"> · <?= htmlspecialchars($item['color']) ?></span><?php endif; ?>
            </span>
            <span class="mn-ocard-item-qty">× <?= (int)$item['qty'] ?></span>
            <span class="mn-ocard-item-price"><?= number_format((int)$item['price'] * (int)$item['qty'], 0, ',', ' ') ?> FCFA</span>
          </div>
          <?php endforeach; ?>
          <div class="mn-ocard-item mn-ocard-item-sum">
            <strong>Total</strong>
            <span></span>
            <strong><?= number_format($order['total'], 0, ',', ' ') ?> FCFA</strong>
          </div>
        </div>
        <?php endif; ?>

      </article>
      <?php endforeach; ?>
    </div><!-- /mc-orders-list -->

    <div class="mc-no-results" id="mc-empty-filter" style="display:none;margin-top:1rem">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
      <p>Aucune commande pour ce filtre.</p>
    </div>

    <?php endif; ?>

  </div>
</main>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/products.php"></script>
<script src="assets/js/cart.js?v=2"></script>
<script src="assets/js/app.js"></script>
<script>
(function () {
  /* ── Filtre par statut ── */
  var filterBtns = document.querySelectorAll('.mc-filter-btn');
  var cards      = document.querySelectorAll('#mc-orders-list .mn-ocard');
  var noResult   = document.getElementById('mc-empty-filter');

  filterBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var f = btn.dataset.filter;
      filterBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var visible = 0;
      cards.forEach(function (card) {
        var show = f === 'all' || card.dataset.status === f;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
      });
      if (noResult) noResult.style.display = visible === 0 ? 'block' : 'none';
    });
  });

  /* ── Toggle articles ── */
  document.querySelectorAll('[data-toggle-items]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var body = btn.nextElementSibling;
      var open = btn.classList.toggle('open');
      if (body) body.style.display = open ? '' : 'none';
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });

  /* ── Suivi en temps réel (poll toutes les 30s) ── */
  var statusLabels = {
    pending:   'En attente',
    confirmed: 'Confirmée',
    shipped:   'Expédiée',
    delivered: 'Livrée',
    cancelled: 'Annulée'
  };
  var statusColors = {
    pending:   '#f59e0b',
    confirmed: '#3b82f6',
    shipped:   '#8b5cf6',
    delivered: '#16a34a',
    cancelled: '#dc2626'
  };

  function pollOrders() {
    document.querySelectorAll('#mc-orders-list .mn-ocard[data-order-id]').forEach(function (card) {
      var id = card.dataset.orderId;
      fetch('api/order-status.php?id=' + id)
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.error) return;
          var newStatus = data.status;
          var oldStatus = card.dataset.status;
          if (newStatus !== oldStatus) {
            /* Mettre à jour le badge */
            var badge = card.querySelector('.mn-ocard-badge');
            if (badge) {
              badge.textContent = statusLabels[newStatus] || newStatus;
              badge.style.background = (statusColors[newStatus] || '#888') + '22';
              badge.style.color = statusColors[newStatus] || '#888';
            }
            /* Flash de la carte */
            card.style.transition = 'box-shadow .4s';
            card.style.boxShadow = '0 0 0 3px ' + (statusColors[newStatus] || '#888') + '55';
            setTimeout(function () { card.style.boxShadow = ''; }, 2000);
            card.dataset.status = newStatus;
          }
        })
        .catch(function () {});
    });
  }

  /* Démarre le polling si au moins une commande visible */
  if (document.querySelector('#mc-orders-list .mn-ocard[data-order-id]')) {
    setInterval(pollOrders, 30000);
  }
})();
</script>
</body>
</html>
