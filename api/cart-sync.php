<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!mn_is_logged_in()) {
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

$user_id = $_SESSION['mn_user_id'];

/* ── GET : retourner le panier sauvegardé ── */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = mn_db()->prepare(
        'SELECT product_id AS id, name, price, image, size, color, qty
         FROM mn_cart_items WHERE user_id = ?'
    );
    $stmt->execute(array($user_id));
    echo json_encode(array('items' => $stmt->fetchAll()));
    exit;
}

/* ── POST : sauvegarder le panier ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data) || !array_key_exists('items', $data)) {
        echo json_encode(array('error' => 'Invalid payload'));
        exit;
    }

    $db = mn_db();
    $db->beginTransaction();
    try {
        $db->prepare('DELETE FROM mn_cart_items WHERE user_id = ?')
           ->execute(array($user_id));

        if (!empty($data['items'])) {
            $ins = $db->prepare(
                'INSERT INTO mn_cart_items
                    (user_id, product_id, size, color, qty, name, price, image)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            foreach ($data['items'] as $item) {
                if (empty($item['id'])) continue;
                $ins->execute(array(
                    $user_id,
                    substr((string) $item['id'],    0, 100),
                    substr((string)(isset($item['size'])  ? $item['size']  : ''), 0, 50),
                    substr((string)(isset($item['color']) ? $item['color'] : ''), 0, 100),
                    max(1, (int)(isset($item['qty'])   ? $item['qty']   : 1)),
                    substr((string)(isset($item['name'])  ? $item['name']  : ''), 0, 255),
                    (int)(isset($item['price']) ? $item['price'] : 0),
                    substr((string)(isset($item['image']) ? $item['image'] : ''), 0, 500)
                ));
            }
        }
        $db->commit();
        echo json_encode(array('success' => true));
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(array('error' => 'Database error'));
    }
    exit;
}

echo json_encode(array('error' => 'Method not allowed'));
