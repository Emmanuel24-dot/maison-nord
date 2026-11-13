<?php
header('Content-Type: application/javascript; charset=utf-8');

$products = array();
try {
    require_once __DIR__ . '/../../includes/db.php';
    $stmt = mn_db()->query(
        'SELECT * FROM mn_products WHERE active = 1 ORDER BY sort_order ASC, name ASC'
    );
    foreach ($stmt->fetchAll() as $p) {
        $gallery = isset($p['gallery']) ? json_decode($p['gallery'], true) : null;
        if (!is_array($gallery)) $gallery = array();
        $products[] = array(
            'id'          => $p['id'],
            'name'        => $p['name'],
            'category'    => $p['category'],
            'price'       => (int) $p['price'],
            'image'       => $p['image'],
            'gallery'     => $gallery,
            'short'       => $p['short_desc'],
            'long'        => $p['long_desc'],
            'features'    => json_decode($p['features'],  true) ?: array(),
            'composition' => $p['composition'],
            'sizes'       => json_decode($p['sizes'],     true) ?: array(),
            'colors'      => json_decode($p['colors'],    true) ?: array(),
            'badge'       => $p['badge'] ? $p['badge'] : null,
            'created_at'  => isset($p['created_at']) ? $p['created_at'] : null,
        );
    }
} catch (Exception $e) {
    /* Fallback silencieux — tableau vide */
}

echo 'window.MN_PRODUCTS = ' . json_encode($products, JSON_UNESCAPED_UNICODE) . ';' . "\n\n";
?>
window.MN_FORMAT_PRICE = function (n) {
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' FCFA';
};

window.MN_GET_PRODUCT = function (id) {
  return window.MN_PRODUCTS.find(function (p) { return p.id === id; });
};
