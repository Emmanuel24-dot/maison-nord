<?php
require_once 'includes/auth.php';

if (mn_is_logged_in()) {
    header('Location: profil.php');
    exit;
}

$token   = isset($_GET['token'])  ? trim($_GET['token'])  : '';
$error   = '';
$success = false;

/* Valider le token */
$pdo      = mn_db();
$reset    = null;

if ($token) {
    $stmt = $pdo->prepare(
        'SELECT * FROM mn_password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()'
    );
    $stmt->execute(array($token));
    $reset = $stmt->fetch();
}

if (!$reset) {
    $error = 'Ce lien est invalide ou a expiré. Veuillez faire une nouvelle demande.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reset) {
    $csrf    = isset($_POST['csrf'])    ? $_POST['csrf']    : '';
    $new     = isset($_POST['new_password'])     ? $_POST['new_password']     : '';
    $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (!mn_csrf_verify($csrf)) {
        $error = 'Requête invalide, veuillez réessayer.';
    } elseif (strlen($new) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($new !== $confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        /* Mettre à jour le mot de passe */
        $pdo->prepare('UPDATE mn_users SET password = ? WHERE email = ?')
            ->execute(array(password_hash($new, PASSWORD_BCRYPT), $reset['email']));

        /* Invalider le token */
        $pdo->prepare('UPDATE mn_password_resets SET used = 1 WHERE id = ?')
            ->execute(array($reset['id']));

        $success = true;
    }
}

$active = '';
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nouveau mot de passe — Maison Nord</title>
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
      <h1 class="mn-auth-title">Nouveau mot de passe</h1>
    </div>

    <?php if ($success): ?>
    <div class="success-msg" role="alert">
      <i class="bi bi-check-circle-fill"></i>
      <div>
        <div class="success-msg-title">Mot de passe modifié</div>
        <div class="success-msg-sub">Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.</div>
      </div>
    </div>
    <div class="mn-auth-switch mt-4">
      <a href="login.php" class="btn-mn w-100 justify-content-center"><span>Se connecter</span><i class="bi bi-arrow-right"></i></a>
    </div>

    <?php elseif (!$reset): ?>
    <div class="mn-auth-error">
      <i class="bi bi-exclamation-circle"></i>
      <?= htmlspecialchars($error) ?>
    </div>
    <div class="mn-auth-switch">
      <a href="forgot-password.php">Faire une nouvelle demande</a>
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
        <label class="form-label-mn" for="new_password">Nouveau mot de passe <span class="mn-form-hint">(8 car. min.)</span></label>
        <input type="password" name="new_password" id="new_password" class="form-control-mn"
               placeholder="••••••••" required autocomplete="new-password" minlength="8">
      </div>

      <div class="mn-form-group">
        <label class="form-label-mn" for="confirm_password">Confirmer le mot de passe</label>
        <input type="password" name="confirm_password" id="confirm_password" class="form-control-mn"
               placeholder="••••••••" required autocomplete="new-password">
      </div>

      <button type="submit" class="btn-mn w-100 justify-content-center mt-2">
        <span>Enregistrer le nouveau mot de passe</span><i class="bi bi-shield-check"></i>
      </button>
    </form>

    <?php endif; ?>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/products.php"></script>
<script src="assets/js/cart.js?v=2"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
