<?php
/**
 * Récupère les IDs et noms de produits réels pour correction
 */

require 'includes/db.php';
$pdo = mn_db();

echo "Produits réels en base de données :\n";
echo "════════════════════════════════════════════════════════════\n\n";

$result = $pdo->query('SELECT id, name FROM mn_products ORDER BY id');
$products = $result->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $i => $prod) {
    echo ($i+1) . ". '" . $prod['id'] . "' => 'prod-" . str_pad($i+1, 3, '0', STR_PAD_LEFT) . ".jpg',\n";
}

echo "\n════════════════════════════════════════════════════════════\n";
echo "Total : " . count($products) . " produits\n";
?>
