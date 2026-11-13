<?php
require_once '../includes/auth.php';
require_once '../includes/fedapay.php';
require_once '../includes/mailer.php';

$order_id = isset($_GET['order']) ? intval($_GET['order']) : 0;
if (!$order_id) {
    header('Location: ../index.php');
    exit;
}

$pdo  = mn_db();
$stmt = $pdo->prepare('SELECT * FROM mn_orders WHERE id = ?');
$stmt->execute(array($order_id));
$order = $stmt->fetch();

if (!$order) {
    header('Location: ../index.php');
    exit;
}

/* Ne pas retraiter une commande déjà confirmée */
if ($order['status'] === 'confirmed') {
    header('Location: confirmation.php?order=' . $order_id);
    exit;
}

/* Vérifier le statut auprès de FedaPay */
if ($order['fedapay_id']) {
    $txn = mn_fedapay_get_transaction($order['fedapay_id']);
    if ($txn && isset($txn['status'])) {
        $fedapay_status = $txn['status'];
        $new_status     = 'pending';

        if ($fedapay_status === 'approved') {
            $new_status = 'confirmed';
        } elseif (in_array($fedapay_status, array('declined', 'canceled'))) {
            $new_status = 'cancelled';
        }

        $pdo->prepare('UPDATE mn_orders SET status = ? WHERE id = ?')
            ->execute(array($new_status, $order_id));

        if ($new_status === 'confirmed') {
            /* Vider le panier serveur */
            $pdo->prepare('DELETE FROM mn_cart_items WHERE user_id = ?')
                ->execute(array($order['user_id']));

            /* Email de confirmation */
            $items_stmt = $pdo->prepare('SELECT * FROM mn_order_items WHERE order_id = ?');
            $items_stmt->execute(array($order_id));
            $items = $items_stmt->fetchAll();
            mn_mail_order_confirmation($order['email'], $order['prenom'], $order_id, $order['total'], $items);
        }
    }
}

header('Location: confirmation.php?order=' . $order_id);
exit;
