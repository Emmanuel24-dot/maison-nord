<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/* hash_equals disponible seulement depuis PHP 5.6 */
if (!function_exists('hash_equals')) {
    function hash_equals($known, $user) {
        if (strlen($known) !== strlen($user)) return false;
        $diff = 0;
        for ($i = 0, $len = strlen($known); $i < $len; $i++) {
            $diff |= ord($known[$i]) ^ ord($user[$i]);
        }
        return $diff === 0;
    }
}

function mn_is_logged_in() {
    return !empty($_SESSION['mn_user_id']);
}

function mn_current_user() {
    if (!mn_is_logged_in()) return null;
    return array(
        'id'     => $_SESSION['mn_user_id'],
        'email'  => $_SESSION['mn_email'],
        'prenom' => $_SESSION['mn_prenom'],
        'nom'    => $_SESSION['mn_nom'],
        'google' => !empty($_SESSION['mn_google']),
    );
}

function mn_login_user($user) {
    session_regenerate_id(true);
    $_SESSION['mn_user_id'] = $user['id'];
    $_SESSION['mn_email']   = $user['email'];
    $_SESSION['mn_prenom']  = $user['prenom'];
    $_SESSION['mn_nom']     = $user['nom'];
    $_SESSION['mn_google']  = !empty($user['google_id']);
}

function mn_logout() {
    session_unset();
    session_destroy();
}

function mn_csrf_token() {
    if (empty($_SESSION['mn_csrf'])) {
        $_SESSION['mn_csrf'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
    return $_SESSION['mn_csrf'];
}

function mn_csrf_verify($token) {
    return !empty($_SESSION['mn_csrf']) && hash_equals($_SESSION['mn_csrf'], (string) $token);
}

function mn_safe_redirect($url) {
    return preg_match('/^[a-zA-Z0-9_\-\.\/\?=&%]+$/', $url) ? $url : 'index.php';
}

/* ── Messages flash ── */
function mn_flash_set($key, $message) {
    if (!isset($_SESSION['mn_flash'])) {
        $_SESSION['mn_flash'] = array();
    }
    $_SESSION['mn_flash'][$key] = $message;
}

function mn_flash_get($key) {
    if (!empty($_SESSION['mn_flash'][$key])) {
        $msg = $_SESSION['mn_flash'][$key];
        unset($_SESSION['mn_flash'][$key]);
        return $msg;
    }
    return null;
}
