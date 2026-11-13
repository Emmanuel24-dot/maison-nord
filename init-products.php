<?php
/**
 * Script d'initialisation complet :
 * 1. Crée les images des produits
 * 2. Réinitialise la base de données
 * 3. Seed les produits
 * 4. Affiche un rapport
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('MN_ASSETS_DIR', __DIR__ . '/assets');
define('MN_UPLOADS_DIR', __DIR__ . '/uploads/products');

// ════════════════════════════════════════════
// FONCTION : Créer une image placeholder
// ════════════════════════════════════════════
function create_placeholder_image($filename, $text, $width = 400, $height = 500) {
    $path = MN_ASSETS_DIR . '/' . $filename;
    
    // Si l'image existe déjà, skip
    if (file_exists($path)) {
        return $path;
    }
    
    // Créer l'image
    $image = imagecreatetruecolor($width, $height);
    
    // Définir les couleurs
    $colors = array(
        'clothing-1.jpg' => array(220, 180, 130),  // Beige
        'clothing-2.jpg' => array(100, 120, 150),  // Bleu
        'clothing-3.jpg' => array(160, 100, 100),  // Brun
        'clothing-4.jpg' => array(180, 140, 160),  // Rose
        'clothing-5.jpg' => array(140, 110, 80),   // Marron
        'clothing-6.jpg' => array(80, 100, 120),   // Gris-bleu
    );
    
    $rgb = isset($colors[$filename]) ? $colors[$filename] : array(200, 200, 200);
    $bg_color = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
    $text_color = imagecolorallocate($image, 255, 255, 255);
    
    imagefill($image, 0, 0, $bg_color);
    
    // Ajouter du texte
    $font_size = 5;
    $text_bbox = imageftbbox($font_size, 0, __DIR__ . '/assets/fonts/arial.ttf', $text);
    $text_width = $text_bbox[2] - $text_bbox[0];
    $text_height = $text_bbox[1] - $text_bbox[7];
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2 + $text_height;
    
    // Utiliser imagestring si la police n'est pas disponible
    $line_height = 20;
    $lines = explode("\n", wordwrap($text, 30, "\n"));
    $start_y = ($height - (count($lines) * $line_height)) / 2;
    
    foreach ($lines as $i => $line) {
        $line_x = ($width - strlen($line) * 6) / 2;
        imagestring($image, 3, (int)$line_x, (int)($start_y + $i * $line_height), $line, $text_color);
    }
    
    // Sauvegarder l'image
    imagejpeg($image, $path, 85);
    imagedestroy($image);
    
    return $path;
}

// ════════════════════════════════════════════
// FONCTION : Initialiser les images
// ════════════════════════════════════════════
function init_images() {
    echo "📸 Création des images des produits...\n";
    
    if (!is_dir(MN_ASSETS_DIR)) {
        mkdir(MN_ASSETS_DIR, 0755, true);
    }
    
    $images = array(
        'clothing-1.jpg' => 'Manteau\nCoat',
        'clothing-2.jpg' => 'Chemise\nShirt',
        'clothing-3.jpg' => 'Pull\nSweater',
        'clothing-4.jpg' => 'Robe\nDress',
        'clothing-5.jpg' => 'Accessoires\nAccessories',
        'clothing-6.jpg' => 'Pantalon\nTrousers',
    );
    
    foreach ($images as $filename => $text) {
        $path = create_placeholder_image($filename, $text);
        if (file_exists($path)) {
            echo "  ✓ " . $filename . " (" . filesize($path) . " bytes)\n";
        }
    }
    
    if (!is_dir(MN_UPLOADS_DIR)) {
        mkdir(MN_UPLOADS_DIR, 0755, true);
    }
    
    echo "✓ Dossiers créés : assets/ et uploads/products/\n\n";
}

// ════════════════════════════════════════════
// FONCTION : Réinitialiser la BD
// ════════════════════════════════════════════
function reset_database() {
    echo "🗑️  Réinitialisation de la base de données...\n";
    
    require_once __DIR__ . '/includes/db.php';
    
    $pdo = mn_db();
    
    // Vider les tables
    try {
        $pdo->exec('DELETE FROM mn_products');
        $pdo->exec('DELETE FROM mn_cart_items');
        $pdo->exec('DELETE FROM mn_orders');
        $pdo->exec('DELETE FROM mn_order_items');
        echo "✓ Tables vidées\n";
    } catch (Exception $e) {
        echo "⚠ Erreur lors du nettoyage : " . $e->getMessage() . "\n";
    }
    
    // Re-seed les produits
    require_once __DIR__ . '/includes/seed-products.php';
    mn_seed_products($pdo);
    
    echo "✓ Base de données réinitialisée\n\n";
    
    return $pdo;
}

// ════════════════════════════════════════════
// FONCTION : Afficher les statistiques
// ════════════════════════════════════════════
function show_stats($pdo) {
    echo "📊 STATISTIQUES DES PRODUITS\n";
    echo str_repeat("=", 60) . "\n\n";
    
    $total = $pdo->query('SELECT COUNT(*) FROM mn_products')->fetchColumn();
    echo "Total de produits : " . $total . "\n\n";
    
    $by_category = $pdo->query(
        'SELECT category, COUNT(*) as count, 
                MIN(price) as min_price, MAX(price) as max_price,
                AVG(price) as avg_price
         FROM mn_products 
         GROUP BY category 
         ORDER BY category'
    )->fetchAll();
    
    echo "Par catégorie :\n";
    echo str_repeat("-", 60) . "\n";
    printf("%-15s | %6s | %10s | %10s | %10s\n", "Catégorie", "Nombre", "Min Prix", "Max Prix", "Prix Moyen");
    echo str_repeat("-", 60) . "\n";
    
    foreach ($by_category as $row) {
        printf("%-15s | %6d | %10d | %10d | %10.0f\n",
            $row['category'],
            $row['count'],
            $row['min_price'],
            $row['max_price'],
            $row['avg_price']
        );
    }
    
    echo "\n";
    echo "Produits (premiers 20) :\n";
    echo str_repeat("-", 60) . "\n";
    
    $products = $pdo->query(
        'SELECT id, name, category, price, image 
         FROM mn_products 
         ORDER BY sort_order, name
         LIMIT 20'
    )->fetchAll();
    
    foreach ($products as $i => $prod) {
        echo ($i+1) . ". [" . $prod['category'] . "] " . $prod['name'] . " - " . ($prod['price']/1000) . "K CFA\n";
        echo "   Image: " . $prod['image'] . "\n";
    }
    
    echo "\n";
}

// ════════════════════════════════════════════
// EXÉCUTION PRINCIPALE
// ════════════════════════════════════════════

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║         INITIALISATION COMPLÈTE - MAISON NORD              ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// 1. Créer les images
init_images();

// 2. Réinitialiser la BD
$pdo = reset_database();

// 3. Afficher les stats
show_stats($pdo);

echo "✅ INITIALISATION COMPLÈTE !\n";
echo "Votre site est prêt avec " . $pdo->query('SELECT COUNT(*) FROM mn_products')->fetchColumn() . " produits.\n\n";
?>
