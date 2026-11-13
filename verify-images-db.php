<?php
require 'includes/db.php';
$pdo = mn_db();

echo "════════════════════════════════════════════════════════════\n";
echo "VÉRIFICATION - IMAGES TÉLÉCHARGÉES DANS LA BDD\n";
echo "════════════════════════════════════════════════════════════\n\n";

$result = $pdo->query('SELECT id, name, image FROM mn_products ORDER BY id LIMIT 10');
$products = $result->fetchAll(PDO::FETCH_ASSOC);

echo "Premiers 10 produits :\n";
foreach ($products as $i => $prod) {
    echo ($i+1) . ". " . str_pad($prod['name'], 25) . " => " . $prod['image'] . "\n";
}

echo "\n📊 Statistiques :\n";
$total = $pdo->query('SELECT COUNT(*) FROM mn_products')->fetchColumn();
$updated = $pdo->query('SELECT COUNT(*) FROM mn_products WHERE image LIKE "uploads/products/prod-%"')->fetchColumn();
$old_images = $pdo->query('SELECT COUNT(*) FROM mn_products WHERE image LIKE "assets/clothing-%"')->fetchColumn();

echo "  • Total produits : " . $total . "\n";
echo "  • Avec nouvelles images (uploads/products/) : " . $updated . "\n";
echo "  • Avec anciennes images (assets/clothing-) : " . $old_images . "\n";

if ($updated == $total) {
    echo "\n✅ SUCCÈS : Tous les produits ont les nouvelles images !\n";
} else {
    echo "\n⚠️ " . ($total - $updated) . " produits ont encore les anciennes images\n";
}

echo "\n════════════════════════════════════════════════════════════\n";
?>
