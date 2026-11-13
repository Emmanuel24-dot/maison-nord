<?php
require_once 'includes/auth.php';

if (!mn_is_logged_in()) {
    header('Location: login.php?redirect=' . urlencode('profil.php'));
    exit;
}

/* ── Traitement des formulaires ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf   = isset($_POST['csrf'])   ? $_POST['csrf']   : '';
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if (!mn_csrf_verify($csrf)) {
        header('Location: profil.php');
        exit;
    }

    /* Modifier prénom / nom */
    if ($action === 'update_info') {
        $prenom = trim(isset($_POST['prenom']) ? $_POST['prenom'] : '');
        $nom    = trim(isset($_POST['nom'])    ? $_POST['nom']    : '');
        if (empty($prenom) || empty($nom)) {
            mn_flash_set('error_info', 'Veuillez remplir tous les champs.');
        } else {
            mn_db()->prepare('UPDATE mn_users SET prenom = ?, nom = ? WHERE id = ?')
                   ->execute(array($prenom, $nom, $_SESSION['mn_user_id']));
            $_SESSION['mn_prenom'] = $prenom;
            $_SESSION['mn_nom']    = $nom;
            mn_flash_set('success_info', 'Informations mises à jour.');
        }
        header('Location: profil.php');
        exit;
    }

    /* Changer le mot de passe */
    if ($action === 'change_password') {
        $current = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new     = isset($_POST['new_password'])     ? $_POST['new_password']     : '';
        $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        $row = mn_db()->prepare('SELECT password FROM mn_users WHERE id = ?');
        $row->execute(array($_SESSION['mn_user_id']));
        $row = $row->fetch();

        if (!$row || empty($row['password']) || !password_verify($current, $row['password'])) {
            mn_flash_set('error_password', 'Mot de passe actuel incorrect.');
        } elseif (strlen($new) < 8) {
            mn_flash_set('error_password', 'Le nouveau mot de passe doit contenir au moins 8 caractères.');
        } elseif ($new !== $confirm) {
            mn_flash_set('error_password', 'Les nouveaux mots de passe ne correspondent pas.');
        } else {
            mn_db()->prepare('UPDATE mn_users SET password = ? WHERE id = ?')
                   ->execute(array(password_hash($new, PASSWORD_BCRYPT), $_SESSION['mn_user_id']));
            mn_flash_set('success_password', 'Mot de passe modifié avec succès.');
        }
        header('Location: profil.php');
        exit;
    }

    header('Location: profil.php');
    exit;
}

$user             = mn_current_user();
$success_info     = mn_flash_get('success_info');
$error_info       = mn_flash_get('error_info');
$success_password = mn_flash_get('success_password');
$error_password   = mn_flash_get('error_password');
$error_google     = mn_flash_get('error_google');

/* Date d'inscription */
$stmt = mn_db()->prepare('SELECT created_at FROM mn_users WHERE id = ?');
$stmt->execute(array($user['id']));
$row  = $stmt->fetch();
$since = $row ? date('d/m/Y', strtotime($row['created_at'])) : '';

/* Initiales avatar */
$initiales = mb_strtoupper(mb_substr($user['prenom'], 0, 1)) . mb_strtoupper(mb_substr($user['nom'], 0, 1));

$active = '';
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mon profil — Maison Nord</title>
<script>document.documentElement.setAttribute('data-theme',localStorage.getItem('mn-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/styles.css">
<link rel="manifest" href="/maison-nord/manifest.json">
<meta name="theme-color" content="#c89468">
<link rel="apple-touch-icon" href="/maison-nord/assets/icons/icon-192.svg">
<meta name="csrf-token" content="<?= htmlspecialchars(mn_csrf_token()) ?>">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main class="mn-profile-main">
  <div class="container">

    <?php if ($error_google): ?>
    <div class="mn-auth-error mb-4">
      <i class="bi bi-exclamation-circle"></i>
      <?= htmlspecialchars($error_google) ?>
    </div>
    <?php endif; ?>

    <!-- En-tête profil -->
    <div class="mn-profile-header reveal">
      <div class="mn-profile-avatar"><?= htmlspecialchars($initiales) ?></div>
      <div>
        <h1 class="mn-profile-name"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h1>
        <p class="mn-profile-email"><?= htmlspecialchars($user['email']) ?></p>
        <p class="mn-profile-since">
          <?php if ($user['google']): ?>
          <span class="mn-google-badge"><svg width="13" height="13" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/><path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/><path d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/><path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/></svg> Compte Google</span> ·
          <?php endif; ?>
          Membre depuis le <?= $since ?>
        </p>
      </div>
    </div>

    <div class="row g-4">

      <!-- Informations personnelles -->
      <div class="col-lg-6 reveal reveal-delay-1">
        <div class="mn-profile-card">
          <h2 class="mn-profile-card-title">Informations personnelles</h2>

          <?php if ($success_info): ?>
          <div class="mn-alert-success"><i class="bi bi-check-circle"></i><?= htmlspecialchars($success_info) ?></div>
          <?php endif; ?>
          <?php if ($error_info): ?>
          <div class="mn-auth-error"><i class="bi bi-exclamation-circle"></i><?= htmlspecialchars($error_info) ?></div>
          <?php endif; ?>

          <form method="POST" action="" novalidate>
            <input type="hidden" name="csrf"   value="<?= htmlspecialchars(mn_csrf_token()) ?>">
            <input type="hidden" name="action" value="update_info">

            <div class="row g-3">
              <div class="col-6">
                <div class="mn-form-group">
                  <label class="form-label-mn" for="prenom">Prénom</label>
                  <input type="text" name="prenom" id="prenom" class="form-control-mn"
                         value="<?= htmlspecialchars($user['prenom']) ?>" required>
                </div>
              </div>
              <div class="col-6">
                <div class="mn-form-group">
                  <label class="form-label-mn" for="nom">Nom</label>
                  <input type="text" name="nom" id="nom" class="form-control-mn"
                         value="<?= htmlspecialchars($user['nom']) ?>" required>
                </div>
              </div>
            </div>

            <div class="mn-form-group">
              <label class="form-label-mn">Adresse email</label>
              <input type="email" class="form-control-mn"
                     value="<?= htmlspecialchars($user['email']) ?>" disabled>
              <small class="mn-form-note">L'email ne peut pas être modifié.</small>
            </div>

            <button type="submit" class="btn-mn btn-mn-sm">
              <span>Enregistrer</span><i class="bi bi-check2"></i>
            </button>
          </form>
        </div>
      </div>

      <!-- Sécurité -->
      <div class="col-lg-6 reveal reveal-delay-2">
        <div class="mn-profile-card">
          <h2 class="mn-profile-card-title">Sécurité</h2>

          <?php if ($user['google']): ?>
          <!-- Compte Google : pas de mot de passe -->
          <div class="mn-google-linked">
            <svg width="24" height="24" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/><path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/><path d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/><path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/></svg>
            <div class="mn-google-linked-text">
              <strong>Connecté via Google</strong>
              Votre compte est sécurisé par votre compte Google. Aucun mot de passe Maison Nord distinct.
            </div>
          </div>

          <?php else: ?>
          <!-- Compte email : formulaire mot de passe -->
          <?php if ($success_password): ?>
          <div class="mn-alert-success"><i class="bi bi-check-circle"></i><?= htmlspecialchars($success_password) ?></div>
          <?php endif; ?>
          <?php if ($error_password): ?>
          <div class="mn-auth-error"><i class="bi bi-exclamation-circle"></i><?= htmlspecialchars($error_password) ?></div>
          <?php endif; ?>

          <form method="POST" action="" novalidate>
            <input type="hidden" name="csrf"   value="<?= htmlspecialchars(mn_csrf_token()) ?>">
            <input type="hidden" name="action" value="change_password">

            <div class="mn-form-group">
              <label class="form-label-mn" for="current_password">Mot de passe actuel</label>
              <input type="password" name="current_password" id="current_password"
                     class="form-control-mn" placeholder="••••••••" required autocomplete="current-password">
            </div>
            <div class="mn-form-group">
              <label class="form-label-mn" for="new_password">Nouveau mot de passe <span class="mn-form-hint">(8 car. min.)</span></label>
              <input type="password" name="new_password" id="new_password"
                     class="form-control-mn" placeholder="••••••••" required autocomplete="new-password" minlength="8">
            </div>
            <div class="mn-form-group">
              <label class="form-label-mn" for="confirm_password">Confirmer le nouveau mot de passe</label>
              <input type="password" name="confirm_password" id="confirm_password"
                     class="form-control-mn" placeholder="••••••••" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn-mn btn-mn-sm">
              <span>Changer le mot de passe</span><i class="bi bi-shield-check"></i>
            </button>
          </form>
          <?php endif; ?>
        </div>
      </div>

    </div><!-- /row -->

    <!-- ══ Adresses de livraison ══ -->
    <section style="margin-top:3rem;padding-bottom:3rem">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--mn-border)">
        <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.75rem;font-weight:400;color:var(--mn-ink);margin:0">Adresses de livraison</h2>
        <button class="btn-mn btn-mn-sm" id="btn-add-addr" type="button">
          <i class="bi bi-plus-lg"></i> Ajouter une adresse
        </button>
      </div>

      <div id="addr-list">
        <div style="color:var(--mn-muted);font-size:.9rem;text-align:center;padding:2rem">Chargement…</div>
      </div>

      <!-- Formulaire adresse (masqué par défaut) -->
      <div id="addr-form-wrap" style="display:none;margin-top:1.5rem">
        <div class="mn-profile-card">
          <h3 class="mn-profile-card-title" id="addr-form-title">Nouvelle adresse</h3>
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="mn-form-group">
                <label class="form-label-mn">Étiquette</label>
                <input id="af-label" class="form-control-mn" placeholder="Domicile, Bureau…" value="Domicile">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="mn-form-group">
                <label class="form-label-mn">Téléphone</label>
                <input id="af-phone" class="form-control-mn" placeholder="+228 90 00 00 00" type="tel">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="mn-form-group">
                <label class="form-label-mn">Prénom</label>
                <input id="af-prenom" class="form-control-mn" placeholder="Prénom du destinataire" value="<?= htmlspecialchars($user['prenom']) ?>">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="mn-form-group">
                <label class="form-label-mn">Nom</label>
                <input id="af-nom" class="form-control-mn" placeholder="Nom du destinataire" value="<?= htmlspecialchars($user['nom']) ?>">
              </div>
            </div>
            <div class="col-12">
              <div class="mn-form-group">
                <label class="form-label-mn">Adresse complète</label>
                <input id="af-address" class="form-control-mn" placeholder="Rue, quartier, repère…">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="mn-form-group">
                <label class="form-label-mn">Ville</label>
                <input id="af-city" class="form-control-mn" placeholder="Lomé" value="Lomé">
              </div>
            </div>
            <div class="col-sm-6" style="display:flex;align-items:flex-end;padding-bottom:.25rem">
              <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem;color:var(--mn-ink)">
                <input id="af-default" type="checkbox" style="accent-color:var(--mn-accent,#c89468)">
                Définir comme adresse par défaut
              </label>
            </div>
          </div>
          <div style="display:flex;gap:.75rem;margin-top:1.25rem;flex-wrap:wrap">
            <button class="btn-mn btn-mn-sm" id="btn-addr-save" type="button">Enregistrer</button>
            <button class="btn-mn btn-mn-sm" id="btn-addr-cancel" type="button" style="background:var(--mn-surface);color:var(--mn-ink);border:1px solid var(--mn-border)">Annuler</button>
          </div>
        </div>
      </div>
    </section>

  </div><!-- /container -->
</main>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/products.php"></script>
<script src="assets/js/cart.js?v=2"></script>
<script src="assets/js/app.js"></script>
<script>
(function () {
  var editingId = null;
  var addresses = [];

  function renderAddresses(list) {
    addresses = list;
    var wrap = document.getElementById('addr-list');
    if (!list.length) {
      wrap.innerHTML = '<div style="color:var(--mn-muted);font-size:.875rem;text-align:center;padding:2rem 1rem"><i class="bi bi-geo-alt" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.75rem"></i>Aucune adresse enregistrée.</div>';
      return;
    }
    wrap.innerHTML = list.map(function (a) {
      return '<div style="background:var(--mn-surface);border:1px solid ' + (a.is_default ? 'var(--mn-accent,#c89468)' : 'var(--mn-border)') + ';border-radius:12px;padding:1.25rem;margin-bottom:.875rem;position:relative">' +
        (a.is_default ? '<span style="position:absolute;top:.75rem;right:.75rem;background:var(--mn-accent,#c89468);color:#fff;font-size:.65rem;font-weight:700;letter-spacing:.06em;padding:.2rem .6rem;border-radius:99px;text-transform:uppercase">Défaut</span>' : '') +
        '<div style="font-weight:600;font-size:.875rem;color:var(--mn-ink);margin-bottom:.35rem"><i class="bi bi-geo-alt-fill" style="color:var(--mn-accent,#c89468);margin-right:.35rem"></i>' + esc(a.label) + '</div>' +
        '<div style="font-size:.85rem;color:var(--mn-muted);line-height:1.6">' +
          (a.prenom || a.nom ? esc(a.prenom + ' ' + a.nom) + '<br>' : '') +
          esc(a.address) + '<br>' + esc(a.city) +
          (a.phone ? '<br>' + esc(a.phone) : '') +
        '</div>' +
        '<div style="display:flex;gap:.5rem;margin-top:.875rem;flex-wrap:wrap">' +
          '<button class="btn-mn btn-mn-sm" style="font-size:.75rem;padding:.3rem .8rem;background:var(--mn-surface);color:var(--mn-ink);border:1px solid var(--mn-border)" onclick="editAddr(' + a.id + ')"><i class="bi bi-pencil"></i> Modifier</button>' +
          (!a.is_default ? '<button class="btn-mn btn-mn-sm" style="font-size:.75rem;padding:.3rem .8rem;background:var(--mn-surface);color:var(--mn-ink);border:1px solid var(--mn-border)" onclick="setDefault(' + a.id + ')"><i class="bi bi-star"></i> Par défaut</button>' : '') +
          '<button class="btn-mn btn-mn-sm" style="font-size:.75rem;padding:.3rem .8rem;background:var(--mn-surface);color:#dc2626;border:1px solid #dc2626" onclick="deleteAddr(' + a.id + ')"><i class="bi bi-trash"></i></button>' +
        '</div>' +
      '</div>';
    }).join('');
  }

  function esc(s) { return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  function loadAddresses() {
    fetch('api/addresses.php', { headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content } })
      .then(function (r) { return r.json(); })
      .then(function (data) { renderAddresses(Array.isArray(data) ? data : []); })
      .catch(function () { document.getElementById('addr-list').innerHTML = '<div style="color:var(--mn-muted)">Erreur de chargement.</div>'; });
  }

  function showForm(editing) {
    editingId = editing ? editing.id : null;
    document.getElementById('addr-form-title').textContent = editing ? 'Modifier l\'adresse' : 'Nouvelle adresse';
    document.getElementById('af-label').value   = editing ? editing.label   : 'Domicile';
    document.getElementById('af-prenom').value  = editing ? editing.prenom  : '<?= addslashes($user['prenom']) ?>';
    document.getElementById('af-nom').value     = editing ? editing.nom     : '<?= addslashes($user['nom']) ?>';
    document.getElementById('af-phone').value   = editing ? editing.phone   : '';
    document.getElementById('af-address').value = editing ? editing.address : '';
    document.getElementById('af-city').value    = editing ? editing.city    : 'Lomé';
    document.getElementById('af-default').checked = editing ? !!editing.is_default : false;
    document.getElementById('addr-form-wrap').style.display = '';
    document.getElementById('addr-form-wrap').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  window.editAddr = function (id) { showForm(addresses.find(function (a) { return a.id == id; })); };
  window.setDefault = function (id) {
    fetch('api/addresses.php?id=' + id, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ is_default: 1 })
    }).then(function () { loadAddresses(); });
  };
  window.deleteAddr = function (id) {
    if (!confirm('Supprimer cette adresse ?')) return;
    fetch('api/addresses.php?id=' + id, {
      method: 'DELETE',
      headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content }
    }).then(function () { loadAddresses(); });
  };

  document.getElementById('btn-add-addr').addEventListener('click', function () { showForm(null); });
  document.getElementById('btn-addr-cancel').addEventListener('click', function () {
    document.getElementById('addr-form-wrap').style.display = 'none';
    editingId = null;
  });

  document.getElementById('btn-addr-save').addEventListener('click', function () {
    var payload = {
      label:      document.getElementById('af-label').value.trim()   || 'Domicile',
      prenom:     document.getElementById('af-prenom').value.trim(),
      nom:        document.getElementById('af-nom').value.trim(),
      phone:      document.getElementById('af-phone').value.trim(),
      address:    document.getElementById('af-address').value.trim(),
      city:       document.getElementById('af-city').value.trim(),
      is_default: document.getElementById('af-default').checked ? 1 : 0,
    };
    if (!payload.address || !payload.city) { alert('Veuillez remplir l\'adresse et la ville.'); return; }

    var url    = 'api/addresses.php' + (editingId ? '?id=' + editingId : '');
    var method = editingId ? 'PUT' : 'POST';

    fetch(url, {
      method: method,
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify(payload)
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.error) { alert(data.error); return; }
      document.getElementById('addr-form-wrap').style.display = 'none';
      editingId = null;
      loadAddresses();
    });
  });

  loadAddresses();
})();
</script>
</body>
</html>
