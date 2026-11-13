<?php
require_once 'includes/auth.php';
require_once 'includes/mailer.php';

$active  = 'contact';
$success = false;
$error   = '';

/* Valeurs à réafficher en cas d'erreur */
$val = array('firstname' => '', 'lastname' => '', 'email' => '', 'subject' => '', 'message' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf      = isset($_POST['csrf'])      ? $_POST['csrf']      : '';
    $firstname = trim(isset($_POST['firstname']) ? $_POST['firstname'] : '');
    $lastname  = trim(isset($_POST['lastname'])  ? $_POST['lastname']  : '');
    $email     = trim(isset($_POST['email'])     ? $_POST['email']     : '');
    $subject   = trim(isset($_POST['subject'])   ? $_POST['subject']   : '');
    $message   = trim(isset($_POST['message'])   ? $_POST['message']   : '');

    $val = array(
        'firstname' => $firstname,
        'lastname'  => $lastname,
        'email'     => $email,
        'subject'   => $subject,
        'message'   => $message
    );

    if (!mn_csrf_verify($csrf)) {
        $error = 'Requête invalide, veuillez réessayer.';
    } elseif (empty($firstname) || empty($lastname) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif (mb_strlen($message) < 10) {
        $error = 'Votre message est trop court.';
    } else {
        /* Sauvegarder en base */
        mn_db()->prepare(
            'INSERT INTO mn_contact_messages (prenom, nom, email, subject, message)
             VALUES (?, ?, ?, ?, ?)'
        )->execute(array($firstname, $lastname, $email, $subject, $message));

        /* Envoyer les emails */
        mn_mail_contact_auto_reply($email, $firstname, $subject);
        mn_mail_contact_notification($firstname, $lastname, $email, $subject, $message);

        mn_flash_set('contact_success', '1');
        header('Location: contact.php');
        exit;
    }
}

$success = (bool) mn_flash_get('contact_success');
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contact — Maison Nord</title>
<meta name="description" content="Contactez Maison Nord : conseil, retouches, commandes sur-mesure ou questions sur nos pièces.">
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('mn-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div id="scroll-progress"></div>
<?php include 'includes/nav.php'; ?>

<section class="py-section">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="section-eyebrow center">Conciergerie</div>
      <h1 class="section-title">Nous écrire</h1>
      <p class="contact-subtitle">Une question, un conseil, une commande sur-mesure ? Notre équipe vous répond sous 24h.</p>
    </div>

    <div class="row g-5">
      <div class="col-lg-7 reveal">

        <?php if ($success): ?>
        <div class="success-msg" role="alert">
          <i class="bi bi-check-circle-fill"></i>
          <div>
            <div class="success-msg-title">Message envoyé</div>
            <div class="success-msg-sub">Notre équipe vous répond sous 24h. Un email de confirmation vous a été envoyé.</div>
          </div>
        </div>
        <a href="contact.php" class="btn-mn mt-4"><span>Nouveau message</span><i class="bi bi-arrow-right"></i></a>

        <?php else: ?>

        <?php if ($error): ?>
        <div class="mn-auth-error mb-4">
          <i class="bi bi-exclamation-circle"></i>
          <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form id="contact-form" method="POST" action="" class="row g-4" novalidate>
          <input type="hidden" name="csrf" value="<?= htmlspecialchars(mn_csrf_token()) ?>">

          <div class="col-md-6">
            <label class="form-label-mn" for="firstname">Prénom</label>
            <input required type="text" id="firstname" name="firstname" class="form-control-mn"
                   placeholder="Marie" value="<?= htmlspecialchars($val['firstname']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label-mn" for="lastname">Nom</label>
            <input required type="text" id="lastname" name="lastname" class="form-control-mn"
                   placeholder="Dupont" value="<?= htmlspecialchars($val['lastname']) ?>">
          </div>
          <div class="col-12">
            <label class="form-label-mn" for="email">Email</label>
            <input required type="email" id="email" name="email" class="form-control-mn"
                   placeholder="marie@exemple.tg" value="<?= htmlspecialchars($val['email']) ?>">
          </div>
          <div class="col-12">
            <label class="form-label-mn" for="subject">Sujet</label>
            <select id="subject" name="subject" class="form-control-mn">
              <?php
              $subjects = array('Conseil sur une pièce', 'Commande sur-mesure', 'Retouches', 'Autre');
              foreach ($subjects as $s):
              ?>
              <option<?= $val['subject'] === $s ? ' selected' : '' ?>><?= htmlspecialchars($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label-mn" for="message">Message</label>
            <textarea required id="message" name="message" rows="6" class="form-control-mn"
                      placeholder="Votre message..."><?= htmlspecialchars($val['message']) ?></textarea>
          </div>
          <div class="col-12">
            <button type="submit" class="btn-mn"><span>Envoyer le message</span><i class="bi bi-send"></i></button>
          </div>
        </form>

        <?php endif; ?>
      </div>

      <div class="col-lg-5 reveal reveal-delay-2">
        <div class="contact-info-card">
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-geo-alt"></i></div>
            <div>
              <div class="contact-info-label">Boutique</div>
              <div class="contact-info-value">Bd du 13 Janvier<br>Lomé, Togo<br>Lun — Sam, 9h — 19h</div>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-envelope"></i></div>
            <div>
              <div class="contact-info-label">Email</div>
              <div class="contact-info-value"><a href="mailto:bonjour@maisonnord.tg">bonjour@maisonnord.tg</a></div>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-telephone"></i></div>
            <div>
              <div class="contact-info-label">Téléphone</div>
              <div class="contact-info-value">+228 22 21 34 56</div>
            </div>
          </div>
          <div class="map-wrap mt-4">
            <iframe
              src="https://www.openstreetmap.org/export/embed.html?bbox=1.1900%2C6.1100%2C1.2500%2C6.1700&amp;layer=mapnik&amp;marker=6.1375%2C1.2123"
              title="Carte Maison Nord — Lomé, Togo"
              loading="lazy"
              referrerpolicy="no-referrer"
            ></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/products.php"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/cart.js?v=2"></script>
</body>
</html>
