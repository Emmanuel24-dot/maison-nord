<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/google-config.php';

/* Stocker l'URL de retour et le state anti-CSRF */
$redirect = isset($_GET['redirect']) ? mn_safe_redirect($_GET['redirect']) : '';
$_SESSION['google_redirect'] = $redirect;
$_SESSION['google_state']    = bin2hex(openssl_random_pseudo_bytes(16));

$params = array(
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => $_SESSION['google_state'],
    'access_type'   => 'online',
    'prompt'        => 'select_account',
);

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
exit;
