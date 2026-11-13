<?php
require_once '../includes/auth.php';
require_once '../includes/fedapay.php';
require_once '../includes/mailer.php';

if (!mn_is_logged_in()) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../checkout.php');
    exit;
}

$csrf = isset($_POST['csrf']) ? $_POST['csrf'] : '';
if (!mn_csrf_verify($csrf)) {
    mn_flash_set('checkout_error', 'Session expirée, veuillez réessayer.');
    header('Location: ../checkout.php');
    exit;
}

$prenom        = trim(isset($_POST['prenom'])        ? $_POST['prenom']        : '');
$nom           = trim(isset($_POST['nom'])           ? $_POST['nom']           : '');
$email         = trim(isset($_POST['email'])         ? $_POST['email']         : '');
$phone         = trim(isset($_POST['phone'])         ? $_POST['phone']         : '');
$address       = trim(isset($_POST['address'])       ? $_POST['address']       : '');
$city          = trim(isset($_POST['city'])          ? $_POST['city']          : '');
$notes         = trim(isset($_POST['notes'])         ? $_POST['notes']         : '');
$cart_json     = isset($_POST['cart_json'])          ? $_POST['cart_json']     : '[]';
$delivery_fee  = max(0, intval(isset($_POST['delivery_fee'])  ? $_POST['delivery_fee']  : 0));
$delivery_zone = trim(isset($_POST['delivery_zone']) ? $_POST['delivery_zone'] : '');

if (empty($prenom) || empty($nom) || empty($email) || empty($phone) || empty($address) || empty($city)) {
    mn_flash_set('checkout_error', 'Veuillez remplir tous les champs obligatoires.');
    header('Location: ../checkout.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    mn_flash_set('checkout_error', 'Adresse email invalide.');
    header('Location: ../checkout.php');
    exit;
}

$items = json_decode($cart_json, true);
if (!is_array($items) || count($items) === 0) {
    mn_flash_set('checkout_error', 'Votre panier est vide.');
    header('Location: ../checkout.php');
    exit;
}

/* Recalculer le total côté serveur (articles + livraison) */
$subtotal = 0;
foreach ($items as $item) {
    if (empty($item['id']) || empty($item['name']) || !isset($item['price']) || !isset($item['qty'])) {
        mn_flash_set('checkout_error', 'Données du panier invalides.');
        header('Location: ../checkout.php');
        exit;
    }
    $subtotal += intval($item['price']) * intval($item['qty']);
}
$total = $subtotal + $delivery_fee;

$pdo = mn_db();

/* Créer la commande en base */
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare(
        'INSERT INTO mn_orders (user_id, total, status, prenom, nom, email, phone, address, city, notes, delivery_fee, delivery_zone)
         VALUES (?, ?, \'pending\', ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute(array($_SESSION['mn_user_id'], $total, $prenom, $nom, $email, $phone, $address, $city, $notes, $delivery_fee, $delivery_zone));
    $order_id = $pdo->lastInsertId();

    $item_stmt = $pdo->prepare(
        'INSERT INTO mn_order_items (order_id, product_id, name, price, size, color, qty)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    foreach ($items as $item) {
        $item_stmt->execute(array(
            $order_id,
            $item['id'],
            $item['name'],
            intval($item['price']),
            isset($item['size'])  ? $item['size']  : '',
            isset($item['color']) ? $item['color'] : '',
            intval($item['qty'])
        ));
    }
    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    mn_flash_set('checkout_error', 'Une erreur est survenue lors de la création de la commande. Réessayez.');
    header('Location: ../checkout.php');
    exit;
}

/* Tenter FedaPay */
$customer = array('prenom' => $prenom, 'nom' => $nom, 'email' => $email, 'phone' => $phone);
$fedapay  = mn_fedapay_create_transaction($total, 'Commande Maison Nord #' . $order_id, $customer, $order_id);

if ($fedapay['success']) {
    /* Sauvegarder l'ID de transaction FedaPay */
    $pdo->prepare('UPDATE mn_orders SET fedapay_id = ? WHERE id = ?')
        ->execute(array($fedapay['transaction_id'], $order_id));

    /* Rediriger vers la page de paiement FedaPay */
    header('Location: ' . $fedapay['payment_url']);
    exit;
}

/*
 * FedaPay non disponible (dev local, sandbox, réseau) — on confirme directement.
 * En production, remplacer cette section par une page d'erreur.
 */
$pdo->prepare('UPDATE mn_orders SET status = \'confirmed\', fedapay_id = ? WHERE id = ?')
    ->execute(array('direct-' . $order_id, $order_id));

/* Vider le panier serveur */
$pdo->prepare('DELETE FROM mn_cart_items WHERE user_id = ?')
    ->execute(array($_SESSION['mn_user_id']));

/* Envoyer l'email de confirmation */
mn_mail_order_confirmation($email, $prenom, $order_id, $total, $items);

header('Location: ../checkout/confirmation.php?order=' . $order_id);
exit;
