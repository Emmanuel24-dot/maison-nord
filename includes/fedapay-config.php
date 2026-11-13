<?php
/* FedaPay — https://fedapay.com
 * Clés disponibles sur : https://app.fedapay.com/settings/apikeys
 * Mettre vos vraies clés dans includes/fedapay-config.local.php (ignoré par Git) :
 *   <?php
 *   define('FEDAPAY_SECRET_KEY', 'sk_sandbox_...');
 *   define('FEDAPAY_PUBLIC_KEY', 'pk_sandbox_...');
 * Passer FEDAPAY_SANDBOX à false et utiliser les clés live pour la production.
 */
if (is_file(__DIR__ . '/fedapay-config.local.php')) {
    require_once __DIR__ . '/fedapay-config.local.php';
}
defined('FEDAPAY_SECRET_KEY') || define('FEDAPAY_SECRET_KEY', 'VOTRE_CLE_SECRETE_FEDAPAY');
defined('FEDAPAY_PUBLIC_KEY') || define('FEDAPAY_PUBLIC_KEY', 'VOTRE_CLE_PUBLIQUE_FEDAPAY');
define('FEDAPAY_SANDBOX',      true);
define('FEDAPAY_CURRENCY',     'XOF');
define('FEDAPAY_CALLBACK_URL', 'http://localhost/maison-nord/checkout/callback.php');
define('FEDAPAY_RETURN_URL',   'http://localhost/maison-nord/checkout/confirmation.php');
