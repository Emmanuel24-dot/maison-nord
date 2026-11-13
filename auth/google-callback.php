<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/google-config.php';

function google_error($msg) {
    mn_flash_set('error_google', $msg);
    header('Location: ../login.php');
    exit;
}

/* ── 1. Vérifications initiales ── */
if (isset($_GET['error'])) {
    google_error('Connexion Google annulée.');
}
if (empty($_GET['code']) || empty($_GET['state'])) {
    google_error('Réponse Google invalide.');
}
if (empty($_SESSION['google_state']) || !hash_equals($_SESSION['google_state'], $_GET['state'])) {
    google_error('Erreur de sécurité (state invalide).');
}

$code     = $_GET['code'];
$redirect = isset($_SESSION['google_redirect']) ? $_SESSION['google_redirect'] : '';
unset($_SESSION['google_state'], $_SESSION['google_redirect']);

/* ── 2. Échanger le code contre un access token ── */
$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
    'code'          => $code,
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code',
)));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$tokenRaw  = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError || !$tokenRaw) {
    google_error('Impossible de contacter Google (token).');
}
$tokenData = json_decode($tokenRaw, true);
if (empty($tokenData['access_token'])) {
    google_error('Token Google invalide.');
}

/* ── 3. Récupérer les infos utilisateur ── */
$ch = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $tokenData['access_token']
));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$infoRaw = curl_exec($ch);
curl_close($ch);

$info = json_decode($infoRaw, true);
if (empty($info['sub']) || empty($info['email'])) {
    google_error('Informations Google incomplètes.');
}

$google_id = $info['sub'];
$email     = strtolower(trim($info['email']));
$prenom    = isset($info['given_name'])  ? $info['given_name']  : (isset($info['name']) ? explode(' ', $info['name'])[0] : 'Utilisateur');
$nom       = isset($info['family_name']) ? $info['family_name'] : '';

/* ── 4. Trouver ou créer l'utilisateur ── */
$db = mn_db();

/* Chercher par google_id d'abord, puis par email */
$stmt = $db->prepare('SELECT * FROM mn_users WHERE google_id = ? LIMIT 1');
$stmt->execute(array($google_id));
$user = $stmt->fetch();

if (!$user) {
    $stmt = $db->prepare('SELECT * FROM mn_users WHERE email = ? LIMIT 1');
    $stmt->execute(array($email));
    $user = $stmt->fetch();

    if ($user) {
        /* Lier le compte Google existant */
        $db->prepare('UPDATE mn_users SET google_id = ? WHERE id = ?')
           ->execute(array($google_id, $user['id']));
        $user['google_id'] = $google_id;
    } else {
        /* Créer un nouveau compte */
        $db->prepare(
            'INSERT INTO mn_users (email, password, prenom, nom, google_id)
             VALUES (?, NULL, ?, ?, ?)'
        )->execute(array($email, $prenom, $nom ? $nom : '-', $google_id));
        $stmt = $db->prepare('SELECT * FROM mn_users WHERE id = ?');
        $stmt->execute(array($db->lastInsertId()));
        $user = $stmt->fetch();
    }
}

mn_login_user($user);
header('Location: ' . ($redirect ? '../' . $redirect : '../index.php'));
exit;
