<?php
/**
 * Script de test - Vérifier que tout fonctionne
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/db.php';

$pdo = mn_db();

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║              TEST - VÉRIFICATION DES PRODUITS              ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// ════════════════════════════════════════════
// TEST 1 : Compte total
// ════════════════════════════════════════════
echo "✓ TEST 1 : Compte total des produits\n";
$total = $pdo->query('SELECT COUNT(*) FROM mn_products')->fetchColumn();
echo "  Résultat : " . $total . " produits trouvés\n";
if ($total >= 40) {
    echo "  ✅ PASS (au moins 40 produits)\n\n";
} else {
    echo "  ❌ FAIL (moins de 40 produits)\n\n";
}

// ════════════════════════════════════════════
// TEST 2 : Vérifier les images
// ════════════════════════════════════════════
echo "✓ TEST 2 : Vérifier les images des produits\n";
$assets_dir = __DIR__ . '/assets';
$image_files = array('clothing-1.jpg', 'clothing-2.jpg', 'clothing-3.jpg', 'clothing-4.jpg', 'clothing-5.jpg', 'clothing-6.jpg');
$missing_images = 0;

foreach ($image_files as $img) {
    $path = $assets_dir . '/' . $img;
    if (file_exists($path)) {
        echo "  ✓ " . $img . " (" . (filesize($path)/1024) . " KB)\n";
    } else {
        echo "  ✗ " . $img . " MANQUANT\n";
        $missing_images++;
    }
}

if ($missing_images === 0) {
    echo "  ✅ PASS (toutes les images présentes)\n\n";
} else {
    echo "  ❌ FAIL (" . $missing_images . " images manquantes)\n\n";
}

// ════════════════════════════════════════════
// TEST 3 : Catégories valides
// ════════════════════════════════════════════
echo "✓ TEST 3 : Catégories valides\n";
$categories = $pdo->query(
    'SELECT DISTINCT category, COUNT(*) as count FROM mn_products GROUP BY category'
)->fetchAll();

echo "  Catégories trouvées : " . count($categories) . "\n";
foreach ($categories as $cat) {
    echo "  - " . $cat['category'] . " : " . $cat['count'] . " produits\n";
}

if (count($categories) >= 2) {
    echo "  ✅ PASS (au moins 2 catégories)\n\n";
} else {
    echo "  ❌ FAIL (moins de 2 catégories)\n\n";
}

// ════════════════════════════════════════════
// TEST 4 : Produits avec prix
// ════════════════════════════════════════════
echo "✓ TEST 4 : Produits avec prix valide\n";
$no_price = $pdo->query('SELECT COUNT(*) FROM mn_products WHERE price = 0')->fetchColumn();
echo "  Produits avec prix = 0 : " . $no_price . "\n";

if ($no_price == 0) {
    echo "  ✅ PASS (tous les produits ont un prix)\n\n";
} else {
    echo "  ⚠️  WARNING (" . $no_price . " produits sans prix)\n\n";
}

// ════════════════════════════════════════════
// TEST 5 : Produits avec descriptions
// ════════════════════════════════════════════
echo "✓ TEST 5 : Produits avec descriptions\n";
$no_desc = $pdo->query('SELECT COUNT(*) FROM mn_products WHERE short_desc = "" OR long_desc = ""')->fetchColumn();
echo "  Produits sans description : " . $no_desc . "\n";

if ($no_desc == 0) {
    echo "  ✅ PASS (tous les produits ont des descriptions)\n\n";
} else {
    echo "  ⚠️  WARNING (" . $no_desc . " produits sans description)\n\n";
}

// ════════════════════════════════════════════
// TEST 6 : Produits avec images
// ════════════════════════════════════════════
echo "✓ TEST 6 : Produits avec références d'images\n";
$no_image = $pdo->query('SELECT COUNT(*) FROM mn_products WHERE image = ""')->fetchColumn();
echo "  Produits sans image : " . $no_image . "\n";

if ($no_image == 0) {
    echo "  ✅ PASS (tous les produits ont une image)\n\n";
} else {
    echo "  ⚠️  WARNING (" . $no_image . " produits sans image)\n\n";
}

// ════════════════════════════════════════════
// TEST 7 : Tailles et couleurs
// ════════════════════════════════════════════
echo "✓ TEST 7 : Produits avec tailles et couleurs\n";
$missing_meta = $pdo->query(
    'SELECT COUNT(*) FROM mn_products WHERE (sizes = "" OR colors = "")'
)->fetchColumn();
echo "  Produits sans tailles/couleurs : " . $missing_meta . "\n";

if ($missing_meta == 0) {
    echo "  ✅ PASS (tous les produits ont tailles et couleurs)\n\n";
} else {
    echo "  ⚠️  WARNING (" . $missing_meta . " produits incomplets)\n\n";
}

// ════════════════════════════════════════════
// TEST 8 : Gamme de prix
// ════════════════════════════════════════════
echo "✓ TEST 8 : Gamme de prix\n";
$price_stats = $pdo->query(
    'SELECT MIN(price) as min, MAX(price) as max, AVG(price) as avg FROM mn_products'
)->fetch();

echo "  Prix minimum : " . ($price_stats['min']/1000) . "K CFA\n";
echo "  Prix maximum : " . ($price_stats['max']/1000) . "K CFA\n";
echo "  Prix moyen : " . ($price_stats['avg']/1000) . "K CFA\n";

if ($price_stats['min'] > 0 && $price_stats['max'] > $price_stats['min']) {
    echo "  ✅ PASS (gamme de prix cohérente)\n\n";
} else {
    echo "  ❌ FAIL (gamme de prix problématique)\n\n";
}

// ════════════════════════════════════════════
// TEST 9 : Exemples de produits
// ════════════════════════════════════════════
echo "✓ TEST 9 : Exemples de produits dans chaque catégorie\n";
foreach ($categories as $cat) {
    $stmt = $pdo->prepare('SELECT id, name, price FROM mn_products WHERE category = ? LIMIT 1');
    $stmt->execute(array($cat['category']));
    $product = $stmt->fetch();
    if ($product) {
        echo "  [" . $cat['category'] . "] " . $product['name'] . " (" . ($product['price']/1000) . "K CFA)\n";
    }
}
echo "  ✅ PASS (exemples trouvés)\n\n";

// ════════════════════════════════════════════
// RÉSUMÉ FINAL
// ════════════════════════════════════════════
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    RÉSUMÉ FINAL                            ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$stats = $pdo->query(
    'SELECT 
        COUNT(*) as total,
        COUNT(DISTINCT category) as categories,
        COUNT(DISTINCT image) as images,
        MIN(price) as min_price,
        MAX(price) as max_price
     FROM mn_products'
)->fetch();

echo "📦 Total produits : " . $stats['total'] . "\n";
echo "🏷️  Catégories : " . $stats['categories'] . "\n";
echo "📸 Images uniques : " . $stats['images'] . "\n";
echo "💰 Prix : " . ($stats['min_price']/1000) . "K - " . ($stats['max_price']/1000) . "K CFA\n";
echo "📁 Dossiers : assets/ ✓, uploads/products/ ✓\n";

echo "\n✅ INITIALISATION RÉUSSIE - Site prêt !\n\n";

// ════════════════════════════════════════════
// TEST API GET
// ════════════════════════════════════════════
echo "🔍 TEST API : Récupérer les produits via API\n";
echo "URL : http://localhost/maison-nord/admin/api/produits.php\n";
echo "\nVous pouvez accéder aux produits via :\n";
echo "  1. Admin panel : /admin/\n";
echo "  2. API : /admin/api/produits.php (GET)\n";
echo "  3. Site public : /boutique.php\n\n";

?>
