<?php require_once 'includes/auth.php'; $active = ''; ?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>FAQ — Maison Nord</title>
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('mn-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/styles.css">
<link rel="manifest" href="/maison-nord/manifest.json">
<meta name="theme-color" content="#c89468">
<style>
.faq-hero{padding:5rem 0 3rem;text-align:center}
.faq-hero-title{font-family:"Cormorant Garamond",Georgia,serif;font-size:clamp(2.5rem,6vw,4rem);font-weight:400;color:var(--mn-ink);margin:.5rem 0 1rem}
.faq-section{max-width:760px;margin:0 auto;padding-bottom:4rem}
.faq-cat-title{font-family:"Cormorant Garamond",Georgia,serif;font-size:1.5rem;font-weight:400;color:var(--mn-ink);margin:2.5rem 0 1rem;display:flex;align-items:center;gap:.6rem}
.faq-cat-title i{color:var(--mn-accent);font-size:1rem}
.faq-item{border-bottom:1px solid var(--mn-border)}
.faq-q{width:100%;text-align:left;background:none;border:none;padding:1.1rem 0;font-size:.9rem;font-weight:600;color:var(--mn-ink);cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:1rem;line-height:1.4}
.faq-q .bi{font-size:.75rem;color:var(--mn-muted);transition:transform .25s;flex-shrink:0}
.faq-item.open .faq-q .bi{transform:rotate(45deg);color:var(--mn-accent)}
.faq-a{display:none;padding:0 0 1.1rem;font-size:.875rem;color:var(--mn-muted);line-height:1.8}
.faq-item.open .faq-a{display:block}
</style>
</head>
<body>
<div id="scroll-progress"></div>
<?php include 'includes/nav.php'; ?>

<div class="faq-hero">
  <div class="container">
    <div class="section-eyebrow center">Aide</div>
    <h1 class="faq-hero-title">Questions fréquentes</h1>
    <p style="color:var(--mn-muted);font-size:.9rem;max-width:420px;margin:0 auto">Tout ce que vous devez savoir avant, pendant et après votre commande.</p>
  </div>
</div>

<div class="container faq-section">

  <?php
  $sections = array(
    array('icon'=>'truck','title'=>'Livraison',
      'qa'=>array(
        array('q'=>'Quelles zones de livraison couvrez-vous ?',
              'a'=>'Nous livrons à Lomé Centre, dans la périphérie de Lomé, dans le Grand Lomé et dans tout l\'intérieur du Togo. Sélectionnez votre zone lors du passage de commande pour voir les frais et délais.'),
        array('q'=>'Quels sont les délais de livraison ?',
              'a'=>'Lomé Centre : 2-4h · Périphérie : 4-8h · Grand Lomé : 1-2 jours · Intérieur Togo : 2-3 jours. Les délais courent après confirmation du paiement.'),
        array('q'=>'Puis-je choisir une heure de livraison ?',
              'a'=>'Pas encore. Notre équipe vous contactera par téléphone pour convenir d\'un créneau de livraison une fois votre commande confirmée.'),
      )
    ),
    array('icon'=>'bag-check','title'=>'Commandes',
      'qa'=>array(
        array('q'=>'Comment suivre ma commande ?',
              'a'=>'Connectez-vous et rendez-vous dans "Mes commandes". Le statut se met à jour automatiquement. Vous recevrez également un email à chaque étape.'),
        array('q'=>'Puis-je modifier ou annuler ma commande ?',
              'a'=>'Contactez-nous via la page Contact dès que possible. Toute modification est possible avant expédition. Après expédition, consultez notre politique de retours.'),
        array('q'=>'Ma commande est en statut "En attente", que faire ?',
              'a'=>'Le statut "En attente" signifie que votre paiement est en cours de vérification. Cela prend généralement moins de 30 minutes. Si le problème persiste, contactez-nous.'),
      )
    ),
    array('icon'=>'credit-card','title'=>'Paiement',
      'qa'=>array(
        array('q'=>'Quels moyens de paiement acceptez-vous ?',
              'a'=>'Moov Money, T-Money, Orange Money, et carte bancaire (Visa/Mastercard) via FedaPay, notre partenaire de paiement sécurisé.'),
        array('q'=>'Mes données bancaires sont-elles sécurisées ?',
              'a'=>'Toutes les transactions sont traitées par FedaPay, certifié PCI-DSS. Nous ne stockons aucune donnée bancaire sur nos serveurs.'),
        array('q'=>'Le paiement a échoué, que faire ?',
              'a'=>'Vérifiez que votre mobile money est activé et approvisionné, puis retentez. Vous pouvez également choisir un autre moyen de paiement sur la page de checkout.'),
      )
    ),
    array('icon'=>'arrow-return-left','title'=>'Retours & Remboursements',
      'qa'=>array(
        array('q'=>'Quelle est votre politique de retours ?',
              'a'=>'Vous disposez de 30 jours après réception pour nous retourner un article dans son état d\'origine. Les articles portés ou lavés ne sont pas éligibles.'),
        array('q'=>'Comment effectuer un retour ?',
              'a'=>'Contactez notre service client via la page Contact en précisant votre numéro de commande. Nous vous indiquerons la marche à suivre pour l\'expédition retour.'),
        array('q'=>'Quand serai-je remboursé ?',
              'a'=>'Le remboursement est traité sous 5 à 10 jours ouvrés après réception de l\'article retourné, par le même moyen de paiement utilisé lors de l\'achat.'),
      )
    ),
    array('icon'=>'rulers','title'=>'Tailles & Produits',
      'qa'=>array(
        array('q'=>'Comment choisir ma taille ?',
              'a'=>'Nos pièces suivent les tailles européennes standard (XS, S, M, L, XL). En cas de doute, nous recommandons de prendre une taille au-dessus. Pour tout conseil, contactez-nous.'),
        array('q'=>'Les photos reflètent-elles fidèlement les produits ?',
              'a'=>'Nous faisons tout pour que les photos soient fidèles. Les couleurs peuvent légèrement varier selon votre écran. En cas de doute, contactez-nous avant d\'acheter.'),
        array('q'=>'Un article épuisé peut-il être réapprovisionné ?',
              'a'=>'Oui. Contactez-nous via le formulaire de contact en mentionnant l\'article souhaité. Nous ferons le nécessaire pour le disponibiliser.'),
      )
    ),
  );
  foreach ($sections as $sec):
  ?>
  <h2 class="faq-cat-title"><i class="bi bi-<?= $sec['icon'] ?>"></i><?= $sec['title'] ?></h2>
  <?php foreach ($sec['qa'] as $item): ?>
  <div class="faq-item">
    <button class="faq-q" type="button"><?= htmlspecialchars($item['q']) ?> <i class="bi bi-plus-lg"></i></button>
    <div class="faq-a"><?= htmlspecialchars($item['a']) ?></div>
  </div>
  <?php endforeach; endforeach; ?>

  <div style="margin-top:3rem;padding:2rem;background:var(--mn-surface-alt);border-radius:14px;text-align:center">
    <p style="font-size:.875rem;color:var(--mn-muted);margin:0 0 1rem">Vous n'avez pas trouvé votre réponse ?</p>
    <a href="contact.php" class="btn-mn"><span>Nous contacter</span><i class="bi bi-arrow-right"></i></a>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/cart.js?v=2"></script>
<script>
document.querySelectorAll('.faq-q').forEach(function(btn){
  btn.addEventListener('click', function(){
    var item = btn.parentElement;
    var wasOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(function(el){ el.classList.remove('open'); });
    if (!wasOpen) item.classList.add('open');
  });
});
</script>
</body>
</html>
