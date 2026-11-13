<?php
/**
 * Force la mise à jour de la base de données avec les nouvelles images
 */

require 'includes/db.php';
$pdo = mn_db();

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║      MISE À JOUR BASE DE DONNÉES - IMAGES TÉLÉCHARGÉES     ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Mapping des IDs de produits aux noms de fichiers
$products = array(
    'bermuda-sophie' => 'prod-001.jpg',
    'bermuda-kevin' => 'prod-002.jpg',
    'blouse-margot' => 'prod-003.jpg',
    'camisole-beatrice' => 'prod-004.jpg',
    'cape-veronique' => 'prod-005.jpg',
    'cardigan-iris' => 'prod-006.jpg',
    'ceinture-gabriel' => 'prod-007.jpg',
    'chapeau-simone' => 'prod-008.jpg',
    'chemise-auguste' => 'prod-009.jpg',
    'chemise-louise' => 'prod-010.jpg',
    'chemise-homme-olivier' => 'prod-011.jpg',
    'chemise-homme-jerome' => 'prod-012.jpg',
    'cravate-robert' => 'prod-013.jpg',
    'costume-homme-charles' => 'prod-014.jpg',
    'echarpe-christine' => 'prod-015.jpg',
    'gants-christine' => 'prod-016.jpg',
    'gilet-christiane' => 'prod-017.jpg',
    'gilet-sebastien' => 'prod-018.jpg',
    'manteau-camille' => 'prod-019.jpg',
    'manteau-paul' => 'prod-020.jpg',
    'pantalon-femme-alice' => 'prod-021.jpg',
    'pantalon-femme-dominique' => 'prod-022.jpg',
    'pantalon-femme-sylvie' => 'prod-023.jpg',
    'pantalon-homme-baptiste' => 'prod-024.jpg',
    'pantalon-homme-vincent' => 'prod-025.jpg',
    'pantalon-homme-yves' => 'prod-026.jpg',
    'polo-homme-sebastien' => 'prod-027.jpg',
    'pull-femme-emma' => 'prod-028.jpg',
    'pull-lila' => 'prod-029.jpg',
    'pull-victor' => 'prod-030.jpg',
    'robe-arlette' => 'prod-031.jpg',
    'robe-christine' => 'prod-032.jpg',
    'robe-danielle' => 'prod-033.jpg',
    'robe-fernande' => 'prod-034.jpg',
    'robe-georgette' => 'prod-035.jpg',
    'robe-henriette' => 'prod-036.jpg',
    'robe-solea' => 'prod-037.jpg',
    'sarong-valerie' => 'prod-038.jpg',
    'sarong-valerie-bleu' => 'prod-039.jpg',
    'short-augustin' => 'prod-040.jpg',
    'short-charles' => 'prod-041.jpg',
    'short-damien' => 'prod-042.jpg',
    'short-fabrice' => 'prod-043.jpg',
    'sweat-homme-jordan' => 'prod-044.jpg',
    'tshirt-femme-lea' => 'prod-045.jpg',
    'tshirt-femme-sophia' => 'prod-046.jpg',
    'veste-denim-nina' => 'prod-047.jpg',
);

$updated = 0;
$failed = 0;

foreach ($products as $product_id => $filename) {
    $image_path = 'uploads/products/' . $filename;
    $filepath = __DIR__ . '/uploads/products/' . $filename;
    
    // Vérifier que le fichier existe
    if (!file_exists($filepath)) {
        echo "✗ " . $product_id . " - Fichier manquant : " . $filename . "\n";
        $failed++;
        continue;
    }
    
    // Mettre à jour la base de données
    try {
        $stmt = $pdo->prepare('UPDATE mn_products SET image = ? WHERE id = ?');
        if ($stmt->execute(array($image_path, $product_id))) {
            echo "✓ " . str_pad($product_id, 30) . " → " . $filename . "\n";
            $updated++;
        } else {
            echo "✗ " . $product_id . " - Erreur mise à jour BDD\n";
            $failed++;
        }
    } catch (Exception $e) {
        echo "✗ " . $product_id . " - Exception : " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║                  RÉSUMÉ DES MISES À JOUR                  ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";
echo "✓ Mis à jour : " . $updated . "\n";
echo "✗ Erreurs : " . $failed . "\n";

// Vérification finale
$total = $pdo->query('SELECT COUNT(*) FROM mn_products')->fetchColumn();
$with_new_images = $pdo->query('SELECT COUNT(*) FROM mn_products WHERE image LIKE "uploads/products/%"')->fetchColumn();

echo "\n📊 Vérification finale :\n";
echo "  • Total produits : " . $total . "\n";
echo "  • Avec nouvelles images : " . $with_new_images . "\n";

if ($with_new_images == $total) {
    echo "\n✅ SUCCÈS ! Tous les produits ont maintenant des images uniques !\n";
} else {
    echo "\n⚠️ " . ($total - $with_new_images) . " produits n'ont pas été mis à jour\n";
}

echo "\n════════════════════════════════════════════════════════════\n";
?>
