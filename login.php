<?php
require_once 'includes/auth.php';

if (mn_is_logged_in()) {
    header('Location: index.php');
    exit;
}

$redirect   = isset($_GET['redirect'])  ? mn_safe_redirect($_GET['redirect'])  : '';
$error      = '';
$email_val  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf     = isset($_POST['csrf'])     ? $_POST['csrf']     : '';
    $email    = trim(isset($_POST['email'])    ? $_POST['email']    : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $redirect = isset($_POST['redirect']) ? mn_safe_redirect($_POST['redirect']) : '';
    $email_val = $email;

    if (!mn_csrf_verify($csrf)) {
        $error = 'Requête invalide, veuillez réessayer.';
    } elseif (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } else {
        $stmt = mn_db()->prepare('SELECT * FROM mn_users WHERE email = ?');
        $stmt->execute(array($email));
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            mn_login_user($user);
            header('Location: ' . ($redirect ? $redirect : 'index.php'));
            exit;
        } else {
            $error = 'Email ou mot de passe incorrect.';
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
<title>Connexion — Maison Nord</title>
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
      <h1 class="mn-auth-title">Connexion</h1>
      <p class="mn-auth-sub">Accédez à votre espace pour finaliser vos commandes.</p>
    </div>

    <?php if ($error): ?>
    <div class="mn-auth-error">
      <i class="bi bi-exclamation-circle"></i>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="" novalidate>
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(mn_csrf_token()) ?>">
      <?php if ($redirect): ?>
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <?php endif; ?>

      <div class="mn-form-group">
        <label class="form-label-mn" for="email">Adresse email</label>
        <input type="email" name="email" id="email" class="form-control-mn"
               value="<?= htmlspecialchars($email_val) ?>"
               placeholder="votre@email.com" required autocomplete="email">
      </div>

      <div class="mn-form-group">
        <label class="form-label-mn" for="password">Mot de passe</label>
        <input type="password" name="password" id="password" class="form-control-mn"
               placeholder="••••••••" required autocomplete="current-password">
      </div>

      <button type="submit" class="btn-mn w-100 justify-content-center mt-2">
        <span>Se connecter</span><i class="bi bi-arrow-right"></i>
      </button>

      <div class="text-center mt-3">
        <a href="forgot-password.php" class="mn-form-hint" style="text-decoration:underline;font-size:13px;">Mot de passe oublié ?</a>
      </div>
    </form>

    <div class="mn-auth-divider">ou</div>

    <a href="auth/google.php<?= $redirect ? '?redirect=' . urlencode($redirect) : '' ?>" class="btn-google">
      <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
        <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/>
        <path d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
        <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
      </svg>
      Continuer avec Google
    </a>

    <div class="mn-auth-switch">
      Pas encore de compte ?
      <a href="register.php<?= $redirect ? '?redirect=' . urlencode($redirect) : '' ?>">Créer un compte</a>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/products.php"></script>
<script src="assets/js/cart.js?v=2"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
