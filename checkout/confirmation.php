<?php
require_once '../includes/auth.php';

$order_id = isset($_GET['order']) ? intval($_GET['order']) : 0;
$order    = null;
$items    = array();

if ($order_id) {
    $pdo  = mn_db();
    $stmt = $pdo->prepare('SELECT * FROM mn_orders WHERE id = ?');
    $stmt->execute(array($order_id));
    $order = $stmt->fetch();

    if ($order) {
        $iStmt = $pdo->prepare('SELECT * FROM mn_order_items WHERE order_id = ?');
        $iStmt->execute(array($order_id));
        $items = $iStmt->fetchAll();
    }
}

$subtotal = 0;
foreach ($items as $it) { $subtotal += intval($it['price']) * intval($it['qty']); }
$deliveryFee  = $order ? intval($order['delivery_fee'])  : 0;
$deliveryZone = $order ? $order['delivery_zone'] : '';

$active = '';
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Confirmation — Maison Nord</title>
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('mn-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/styles.css">
<link rel="manifest" href="/maison-nord/manifest.json">
<meta name="theme-color" content="#c89468">
<link rel="apple-touch-icon" href="/maison-nord/assets/icons/icon-192.svg">
<style>
.cfm-hero{text-align:center;padding:4rem 1rem 2.5rem}
.cfm-icon{width:5rem;height:5rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.2rem;margin:0 auto 1.25rem}
.cfm-icon--ok    {background:#dcfce7;color:#16a34a}
.cfm-icon--fail  {background:#fee2e2;color:#dc2626}
.cfm-icon--wait  {background:#fef3c7;color:#d97706}
[data-theme=dark] .cfm-icon--ok   {background:rgba(34,197,94,.15);color:#4ade80}
[data-theme=dark] .cfm-icon--fail {background:rgba(239,68,68,.15);color:#f87171}
[data-theme=dark] .cfm-icon--wait {background:rgba(245,158,11,.15);color:#fbbf24}
.cfm-title{font-family:"Cormorant Garamond",Georgia,serif;font-size:2.4rem;font-weight:400;color:var(--mn-ink);margin-bottom:.5rem}
.cfm-sub{font-size:.9rem;color:var(--mn-muted);line-height:1.6;max-width:480px;margin:0 auto}

.cfm-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin:2.5rem 0}
@media(max-width:640px){.cfm-grid{grid-template-columns:1fr}}

.cfm-card{background:var(--mn-surface);border:1px solid var(--mn-border);border-radius:14px;padding:1.5rem}
.cfm-card-title{font-family:"Cormorant Garamond",Georgia,serif;font-size:1.2rem;font-weight:400;color:var(--mn-ink);margin:0 0 1rem;padding-bottom:.6rem;border-bottom:1px solid var(--mn-border)}
.cfm-row{display:flex;justify-content:space-between;align-items:baseline;padding:.35rem 0;font-size:.875rem}
.cfm-row+.cfm-row{border-top:1px solid var(--mn-border)}
.cfm-row-label{color:var(--mn-muted)}
.cfm-row-val{font-weight:500;color:var(--mn-ink)}
.cfm-grand{font-family:"Cormorant Garamond",Georgia,serif;font-size:1.25rem;font-weight:600;border-top:2px solid var(--mn-border)!important;margin-top:.25rem;padding-top:.6rem!important}
.cfm-info-line{font-size:.875rem;color:var(--mn-ink);line-height:1.8}
.cfm-info-muted{color:var(--mn-muted)}

.cfm-steps{display:flex;justify-content:space-between;position:relative;padding:.5rem 0}
.cfm-steps::before{content:'';position:absolute;top:1rem;left:1rem;right:1rem;height:2px;background:var(--mn-accent,#c89468);z-index:0}
.cfm-step{display:flex;flex-direction:column;align-items:center;gap:.4rem;position:relative;z-index:1;flex:1}
.cfm-step-dot{width:2rem;height:2rem;border-radius:50%;background:var(--mn-accent,#c89468);border:2px solid var(--mn-accent,#c89468);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700}
.cfm-step-dot.inactive{background:var(--mn-surface);color:var(--mn-muted);border-color:var(--mn-border)}
.cfm-step-lbl{font-size:.65rem;letter-spacing:.03em;color:var(--mn-muted);text-align:center;line-height:1.3}
.cfm-step.active .cfm-step-lbl{color:var(--mn-ink);font-weight:600}

.cfm-actions{display:flex;flex-wrap:wrap;gap:.75rem;justify-content:center;padding:2rem 0 3rem}
</style>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<main style="min-height:80vh">
<div class="container">

<?php if (!$order): ?>
  <div class="cfm-hero">
    <div class="cfm-icon cfm-icon--fail"><i class="bi bi-exclamation-circle-fill"></i></div>
    <h1 class="cfm-title">Commande introuvable</h1>
    <p class="cfm-sub">Ce numéro de commande n'existe pas ou vous n'y avez pas accès.</p>
    <div class="cfm-actions">
      <a href="../boutique.php" class="btn-mn"><span>Retour à la boutique</span><i class="bi bi-arrow-right"></i></a>
    </div>
  </div>

<?php elseif ($order['status'] === 'cancelled'): ?>
  <div class="cfm-hero">
    <div class="cfm-icon cfm-icon--fail"><i class="bi bi-x-circle-fill"></i></div>
    <div style="font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--mn-accent,#c89468);margin-bottom:.5rem">Paiement annulé</div>
    <h1 class="cfm-title">Commande non finalisée</h1>
    <p class="cfm-sub">Le paiement a été annulé ou refusé. Votre panier n'a pas été débité — vous pouvez réessayer.</p>
    <div class="cfm-actions">
      <a href="../checkout.php" class="btn-mn"><span>Réessayer</span><i class="bi bi-arrow-right"></i></a>
      <a href="../boutique.php" class="btn-mn-outline"><span>Retour à la boutique</span></a>
    </div>
  </div>

<?php elseif ($order['status'] === 'pending'): ?>
  <div class="cfm-hero">
    <div class="cfm-icon cfm-icon--wait"><i class="bi bi-clock-fill"></i></div>
    <div style="font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--mn-accent,#c89468);margin-bottom:.5rem">En traitement</div>
    <h1 class="cfm-title">Paiement en cours…</h1>
    <p class="cfm-sub">Votre paiement est en cours de vérification. Commande <strong>#<?= str_pad($order['id'],4,'0',STR_PAD_LEFT) ?></strong>.<br>Vous recevrez un email de confirmation.</p>
    <div class="cfm-actions">
      <a href="../mes-commandes.php" class="btn-mn"><span>Suivre mes commandes</span><i class="bi bi-arrow-right"></i></a>
    </div>
  </div>

<?php else: /* confirmed / shipped / delivered */ ?>

  <!-- Hero -->
  <div class="cfm-hero">
    <div class="cfm-icon cfm-icon--ok"><i class="bi bi-check-circle-fill"></i></div>
    <div style="font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--mn-accent,#c89468);margin-bottom:.5rem">Merci !</div>
    <h1 class="cfm-title">Commande confirmée</h1>
    <p class="cfm-sub">
      Votre commande <strong>CMD-<?= str_pad($order['id'],4,'0',STR_PAD_LEFT) ?></strong> a bien été reçue.<br>
      Un email de confirmation a été envoyé à <strong><?= htmlspecialchars($order['email']) ?></strong>.
    </p>
  </div>

  <!-- Suivi simplifié -->
  <div style="max-width:520px;margin:0 auto 2.5rem">
    <div class="cfm-steps">
      <div class="cfm-step active">
        <div class="cfm-step-dot"><i class="bi bi-check" style="font-size:.9rem"></i></div>
        <span class="cfm-step-lbl">Commande<br>reçue</span>
      </div>
      <div class="cfm-step">
        <div class="cfm-step-dot inactive"><i class="bi bi-gear" style="font-size:.8rem"></i></div>
        <span class="cfm-step-lbl">Préparation</span>
      </div>
      <div class="cfm-step">
        <div class="cfm-step-dot inactive"><i class="bi bi-truck" style="font-size:.8rem"></i></div>
        <span class="cfm-step-lbl">Expédition</span>
      </div>
      <div class="cfm-step">
        <div class="cfm-step-dot inactive"><i class="bi bi-house" style="font-size:.8rem"></i></div>
        <span class="cfm-step-lbl">Livraison</span>
      </div>
    </div>
  </div>

  <!-- Grille résumé -->
  <div class="cfm-grid" style="max-width:860px;margin-left:auto;margin-right:auto">

    <!-- Articles commandés -->
    <div class="cfm-card" style="grid-column:1/-1">
      <h2 class="cfm-card-title"><i class="bi bi-bag" style="color:var(--mn-accent,#c89468);margin-right:.5rem"></i>Articles commandés</h2>
      <?php foreach ($items as $item): ?>
      <div class="cfm-row">
        <span class="cfm-row-label">
          <?= htmlspecialchars($item['name']) ?>
          <?php if (!empty($item['size'])  && $item['size']  !== ''): ?> <span style="font-size:.8em">· <?= htmlspecialchars($item['size']) ?></span><?php endif; ?>
          <?php if (!empty($item['color']) && $item['color'] !== ''): ?> <span style="font-size:.8em">· <?= htmlspecialchars($item['color']) ?></span><?php endif; ?>
          × <?= intval($item['qty']) ?>
        </span>
        <span class="cfm-row-val"><?= number_format(intval($item['price']) * intval($item['qty']), 0, ',', ' ') ?> FCFA</span>
      </div>
      <?php endforeach; ?>
      <div class="cfm-row" style="margin-top:.25rem;border-top:none">
        <span class="cfm-row-label">Sous-total articles</span>
        <span class="cfm-row-val"><?= number_format($subtotal, 0, ',', ' ') ?> FCFA</span>
      </div>
      <div class="cfm-row">
        <span class="cfm-row-label">Frais de livraison<?= $deliveryZone ? ' <span style="font-size:.8em;color:var(--mn-muted)">('.htmlspecialchars($deliveryZone).')</span>' : '' ?></span>
        <span class="cfm-row-val"><?= number_format($deliveryFee, 0, ',', ' ') ?> FCFA</span>
      </div>
      <div class="cfm-row cfm-grand">
        <span>Total payé</span>
        <span><?= number_format(intval($order['total']), 0, ',', ' ') ?> FCFA</span>
      </div>
    </div>

    <!-- Adresse livraison -->
    <div class="cfm-card">
      <h2 class="cfm-card-title"><i class="bi bi-geo-alt" style="color:var(--mn-accent,#c89468);margin-right:.5rem"></i>Livraison</h2>
      <div class="cfm-info-line">
        <strong><?= htmlspecialchars($order['prenom'].' '.$order['nom']) ?></strong><br>
        <?= htmlspecialchars($order['address']) ?><br>
        <?= htmlspecialchars($order['city']) ?>
        <?php if ($deliveryZone): ?><br><span class="cfm-info-muted"><?= htmlspecialchars($deliveryZone) ?></span><?php endif; ?>
        <?php if ($order['phone']): ?><br><i class="bi bi-telephone" style="font-size:.8rem"></i> <?= htmlspecialchars($order['phone']) ?><?php endif; ?>
      </div>
      <?php if ($order['notes']): ?>
      <div style="margin-top:.75rem;font-size:.82rem;color:var(--mn-muted);font-style:italic">
        "<?= htmlspecialchars($order['notes']) ?>"
      </div>
      <?php endif; ?>
    </div>

    <!-- Infos commande -->
    <div class="cfm-card">
      <h2 class="cfm-card-title"><i class="bi bi-receipt" style="color:var(--mn-accent,#c89468);margin-right:.5rem"></i>Informations</h2>
      <div style="display:flex;flex-direction:column;gap:.5rem;font-size:.875rem">
        <div><span class="cfm-info-muted">Numéro de commande</span><br><strong>CMD-<?= str_pad($order['id'],4,'0',STR_PAD_LEFT) ?></strong></div>
        <div><span class="cfm-info-muted">Date</span><br><?= date('d M Y à H\hi', strtotime($order['created_at'])) ?></div>
        <div><span class="cfm-info-muted">Email de confirmation</span><br><?= htmlspecialchars($order['email']) ?></div>
        <div><span class="cfm-info-muted">Statut</span><br>
          <span style="display:inline-flex;align-items:center;gap:.3rem;background:#dcfce7;color:#166534;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:.2rem .7rem;border-radius:99px">
            <i class="bi bi-check-circle-fill" style="font-size:.65rem"></i> Confirmée
          </span>
        </div>
      </div>
    </div>

  </div>

  <div class="cfm-actions">
    <a href="../mes-commandes.php" class="btn-mn"><span>Suivre mes commandes</span><i class="bi bi-arrow-right"></i></a>
    <a href="../boutique.php" class="btn-mn-outline"><span>Continuer mes achats</span></a>
  </div>

<?php endif; ?>

</div>
</main>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/products.php"></script>
<script src="../assets/js/cart.js?v=2"></script>
<script src="../assets/js/app.js"></script>
<script>
<?php if ($order && in_array($order['status'], array('confirmed','shipped','delivered'))): ?>
localStorage.removeItem('mn-cart-v1');
if (window.MN_CART) { window.MN_CART.items = []; window.MN_CART._updateUI && window.MN_CART._updateUI(); }
<?php endif; ?>
</script>
</body>
</html>
