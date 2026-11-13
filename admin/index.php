<?php
require_once '../includes/auth.php';

if (!mn_is_logged_in()) {
    header('Location: ../login.php');
    exit;
}

$pdo = mn_db();

/* ── Migrations ── */
$migrations = [
    "ALTER TABLE mn_users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'client'",
    "ALTER TABLE mn_users ADD COLUMN blocked TINYINT(1) NOT NULL DEFAULT 0",
    "ALTER TABLE mn_products ADD COLUMN stock INT NOT NULL DEFAULT 0",
    "ALTER TABLE mn_contact_messages ADD COLUMN lu TINYINT(1) NOT NULL DEFAULT 0",
    "CREATE TABLE IF NOT EXISTS mn_articles (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        titre      VARCHAR(255) NOT NULL,
        auteur     VARCHAR(100) NOT NULL DEFAULT 'Équipe Maison Nord',
        extrait    TEXT NOT NULL DEFAULT '',
        contenu    TEXT NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
    "CREATE TABLE IF NOT EXISTS mn_settings (
        id     INT AUTO_INCREMENT PRIMARY KEY,
        cle    VARCHAR(100) NOT NULL UNIQUE,
        valeur TEXT NOT NULL DEFAULT ''
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
];
foreach ($migrations as $sql) {
    try { $pdo->exec($sql); } catch (PDOException $e) {}
}

/* Seed settings si vide */
if ((int)$pdo->query('SELECT COUNT(*) FROM mn_settings')->fetchColumn() === 0) {
    $stmt = $pdo->prepare('INSERT IGNORE INTO mn_settings (cle, valeur) VALUES (?, ?)');
    $seeds = [
        ['siteName',    'Maison Nord'],
        ['email',       'contact@maisonnord.com'],
        ['devise',      'FCFA'],
        ['pays',        'Togo'],
        ['fedapayMode', 'sandbox'],
    ];
    foreach ($seeds as $row) {
        try { $stmt->execute([$row[0], $row[1]]); } catch (PDOException $e) {}
    }
}

/* Premier utilisateur = admin si aucun admin n'existe */
$noAdmin = (int)$pdo->query("SELECT COUNT(*) FROM mn_users WHERE role='admin'")->fetchColumn() === 0;
if ($noAdmin) {
    $pdo->exec("UPDATE mn_users SET role='admin'
                WHERE id=(SELECT id FROM (SELECT MIN(id) AS id FROM mn_users) t)");
}

/* Vérification du rôle */
$me = mn_current_user();
$stmt = $pdo->prepare('SELECT role FROM mn_users WHERE id = ?');
$stmt->execute([$me['id']]);
$role = $stmt->fetchColumn();

if ($role !== 'admin') { ?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Accès refusé</title>
<style>body{font-family:sans-serif;background:#0f172a;color:#fff;display:flex;align-items:center;
justify-content:center;min-height:100vh;margin:0}
.box{text-align:center}.btn{display:inline-block;margin-top:1rem;padding:.75rem 1.5rem;
background:#3b82f6;color:#fff;border-radius:.5rem;text-decoration:none}</style></head>
<body><div class="box"><h1>⛔ Accès refusé</h1>
<p>Vous n'avez pas les droits administrateur.</p>
<a href="../index.php" class="btn">Retour au site</a></div></body></html>
<?php exit; }

$csrf    = mn_csrf_token();
$initials = mb_strtoupper(mb_substr($me['prenom'],0,1) . mb_substr($me['nom'],0,1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?= htmlspecialchars($csrf) ?>">
  <title>Admin — Maison Nord</title>
  <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
  <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

  <div class="app-layout">
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-header-inner">
          <div class="logo-badge">MN</div>
          <div class="sidebar-text">
            <div class="sidebar-brand-title">Maison Nord</div>
            <div class="sidebar-brand-sub">Administration</div>
          </div>
        </div>
      </div>
      <nav class="sidebar-content" id="sidebar-nav"></nav>
      <div class="sidebar-footer">
        <a class="sidebar-btn" href="../logout.php">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
          </svg>
          <span class="sidebar-text">Déconnexion</span>
        </a>
      </div>
    </aside>

    <div class="main-wrapper">
      <header class="topnav">
        <button class="btn btn-ghost btn-icon" id="sidebar-toggle" type="button" aria-label="Menu">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <div class="topnav-separator"></div>
        <span class="topnav-title" id="topnav-title">Tableau de bord</span>
        <div class="topnav-actions">
          <span class="topnav-user">
            <span class="avatar avatar-sm"><?= htmlspecialchars($initials) ?></span>
            <span class="topnav-username sidebar-text"><?= htmlspecialchars($me['prenom']) ?></span>
          </span>
        </div>
      </header>

      <main class="main-content" id="page-content">
        <div class="page-loading">Chargement…</div>
      </main>
    </div>
  </div>

  <div class="modal-overlay" id="modal-overlay">
    <div class="modal" id="modal" role="dialog" aria-modal="true"></div>
  </div>
  <div class="toast-container" id="toast-container"></div>

  <script src="js/store.js"></script>
  <script src="js/toast.js"></script>
  <script src="js/pages.js"></script>
  <script src="js/app.js"></script>
</body>
</html>
