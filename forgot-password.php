<?php
require_once 'includes/auth.php';
require_once 'includes/mailer.php';

if (mn_is_logged_in()) {
    header('Location: profil.php');
    exit;
}

$error   = '';
$success = false;
$email_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf  = isset($_POST['csrf'])  ? $_POST['csrf']  : '';
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $email_val = $email;

    if (!mn_csrf_verify($csrf)) {
        $error = 'Requête invalide, veuillez réessayer.';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Veuillez saisir une adresse email valide.';
    } else {
        $pdo  = mn_db();
        $stmt = $pdo->prepare('SELECT id, prenom, google_id FROM mn_users WHERE email = ?');
        $stmt->execute(array($email));
        $user = $stmt->fetch();

        if ($user) {
            if ($user['google_id'] && empty($user['password'])) {
                $error = 'Ce compte utilise la connexion Google. Aucun mot de passe à réinitialiser.';
            } else {
                /* Générer un token sécurisé */
                $token      = bin2hex(openssl_random_pseudo_bytes(32));
                $expires_at = date('Y-m-d H:i:s', time() + 3600);

                /* Supprimer les anciens tokens pour cet email */
                $pdo->prepare('DELETE FROM mn_password_resets WHERE email = ?')
                    ->execute(array($email));

                $pdo->prepare(
                    'INSERT INTO mn_password_resets (email, token, expires_at) VALUES (?, ?, ?)'
                )->execute(array($email, $token, $expires_at));

                mn_mail_password_reset($email, $user['prenom'], $token);
                $success = true;
            }
        } else {
            /* Même réponse pour ne pas révéler si l'email existe */
            $success = true;
        }
    }
}

$active = '';
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mot de passe oublié — Maison Nord</title>
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('mn-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="mn-auth-body">

<?php include 'includes/nav.php'; ?>

<main class="mn-auth-main">
  <div class="mn-auth-card">
    <div class="mn-auth-header">
      <div class="section-eyebrow center">Espace client</div>
      <h1 class="mn-auth-title">Mot de passe oublié</h1>
      <p class="mn-auth-sub">Saisissez votre email pour recevoir un lien de réinitialisation.</p>
    </div>

    <?php if ($success): ?>
    <div class="success-msg" role="alert">
      <i class="bi bi-check-circle-fill"></i>
      <div>
        <div class="success-msg-title">Email envoyé</div>
        <div class="success-msg-sub">Si un compte existe pour cette adresse, vous recevrez un lien sous quelques minutes.</div>
      </div>
    </div>
    <div class="mn-auth-switch mt-4">
      <a href="login.php"><i class="bi bi-arrow-left"></i> Retour à la connexion</a>
    </div>

    <?php else: ?>

    <?php if ($error): ?>
    <div class="mn-auth-error">
      <i class="bi bi-exclamation-circle"></i>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="" novalidate>
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(mn_csrf_token()) ?>">

      <div class="mn-form-group">
        <label class="form-label-mn" for="email">Adresse email</label>
        <input type="email" name="email" id="email" class="form-control-mn"
               value="<?= htmlspecialchars($email_val) ?>"
               placeholder="votre@email.com" required autocomplete="email">
      </div>

      <button type="submit" class="btn-mn w-100 justify-content-center mt-2">
        <span>Envoyer le lien</span><i class="bi bi-envelope"></i>
      </button>
    </form>

    <div class="mn-auth-switch">
      <a href="login.php"><i class="bi bi-arrow-left"></i> Retour à la connexion</a>
    </div>

    <?php endif; ?>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/products.php"></script>
<script src="assets/js/cart.js?v=2"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
