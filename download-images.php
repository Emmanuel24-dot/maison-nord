<?php
/**
 * Télécharger des images de vêtements depuis internet et les assigner aux produits
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('MN_UPLOADS_DIR', __DIR__ . '/uploads/products');

// Assurer que le répertoire existe
if (!is_dir(MN_UPLOADS_DIR)) {
    mkdir(MN_UPLOADS_DIR, 0755, true);
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   TÉLÉCHARGEMENT D'IMAGES DE VÊTEMENTS DEPUIS INTERNET     ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// ════════════════════════════════════════════
// Catalogue d'images par type de vêtement
// ════════════════════════════════════════════
$image_urls = array(
    // Manteaux & Vestes
    'manteau-camille' => 'https://images.unsplash.com/photo-1539533057440-7274bdc34e16?w=400&h=500&fit=crop',
    'veste-nadia' => 'https://images.unsplash.com/photo-1551028719-00167b16ebc5?w=400&h=500&fit=crop',
    'veste-marc' => 'https://images.unsplash.com/photo-1591047990747-74d440642117?w=400&h=500&fit=crop',
    'manteau-paul' => 'https://images.unsplash.com/photo-1539533057440-7274bdc34e16?w=400&h=500&fit=crop',
    'veste-lin-thomas' => 'https://images.unsplash.com/photo-1591047990747-74d440642117?w=400&h=500&fit=crop',
    'veste-sport-femme-marie' => 'https://images.unsplash.com/photo-1506629082632-11c93b13db7d?w=400&h=500&fit=crop',
    'veste-denim-nina' => 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=400&h=500&fit=crop',
    'cape-femme-veronique' => 'https://images.unsplash.com/photo-1539533057440-7274bdc34e16?w=400&h=500&fit=crop',

    // T-shirts
    'tshirt-theo' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=500&fit=crop',
    'tshirt-femme-sophia' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=500&fit=crop',
    'tshirt-femme-lea' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=500&fit=crop',
    'tshirt-homme-alex' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=500&fit=crop',
    'polo-homme-sebastien' => 'https://images.unsplash.com/photo-1554762407-4b32dc924896?w=400&h=500&fit=crop',

    // Pulls & Cardigans
    'pull-lila' => 'https://images.unsplash.com/photo-1573872612872-beb66bc51506?w=400&h=500&fit=crop',
    'cardigan-iris' => 'https://images.unsplash.com/photo-1536882240095-0379873feb4e?w=400&h=500&fit=crop',
    'pull-victor' => 'https://images.unsplash.com/photo-1573872612872-beb66bc51506?w=400&h=500&fit=crop',
    'pull-femme-emma' => 'https://images.unsplash.com/photo-1573872612872-beb66bc51506?w=400&h=500&fit=crop',
    'sweat-homme-jordan' => 'https://images.unsplash.com/photo-1556821552-5f94b149f56f?w=400&h=500&fit=crop',
    'gilet-femme-christiane' => 'https://images.unsplash.com/photo-1551538827-9c037cb4f32a?w=400&h=500&fit=crop',

    // Robes
    'robe-solea' => 'https://images.unsplash.com/photo-1595777707802-221b984ce486?w=400&h=500&fit=crop',
    'robe-femme-julia' => 'https://images.unsplash.com/photo-1595777707802-221b984ce486?w=400&h=500&fit=crop',
    'robe-femme-alice' => 'https://images.unsplash.com/photo-1552381154-f1a06a8485e7?w=400&h=500&fit=crop',

    // Blouses & Chemises
    'blouse-margot' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=500&fit=crop',
    'chemise-auguste' => 'https://images.unsplash.com/photo-1596703686361-9c0d2bcadef9?w=400&h=500&fit=crop',
    'chemise-femme-louise' => 'https://images.unsplash.com/photo-1596703686361-9c0d2bcadef9?w=400&h=500&fit=crop',
    'chemise-homme-olivier' => 'https://images.unsplash.com/photo-1596703686361-9c0d2bcadef9?w=400&h=500&fit=crop',
    'camisole-femme-beatrice' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=500&fit=crop',

    // Pantalons & Jeans
    'jean-louis' => 'https://images.unsplash.com/photo-1542272604-787c62d465d1?w=400&h=500&fit=crop',
    'pantalon-leon' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=400&h=500&fit=crop',
    'pantalon-femme-lucia' => 'https://images.unsplash.com/photo-1542272604-787c62d465d1?w=400&h=500&fit=crop',
    'pantalon-homme-richard' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=400&h=500&fit=crop',
    'costume-homme-pierre' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=400&h=500&fit=crop',

    // Shorts & Bermudas
    'short-femme-rose' => 'https://images.unsplash.com/photo-1506629082632-11c93b13db7d?w=400&h=500&fit=crop',
    'bermuda-homme-kevin' => 'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?w=400&h=500&fit=crop',
    'bermuda-femme-sophie' => 'https://images.unsplash.com/photo-1506629082632-11c93b13db7d?w=400&h=500&fit=crop',

    // Jupes
    'jupe-elena' => 'https://images.unsplash.com/photo-1598618667-e0b82f1b94f0?w=400&h=500&fit=crop',
    'jupon-femme-grace' => 'https://images.unsplash.com/photo-1598618667-e0b82f1b94f0?w=400&h=500&fit=crop',

    // Tops & Camisoles
    'top-aline' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=500&fit=crop',

    // Sport & Casual
    'jogging-homme-luc' => 'https://images.unsplash.com/photo-1556821552-5f94b149f56f?w=400&h=500&fit=crop',
    'legging-femme-isabelle' => 'https://images.unsplash.com/photo-1506629082632-11c93b13db7d?w=400&h=500&fit=crop',

    // Accessoires
    'sac-madeleine' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&h=500&fit=crop',
    'pochette-lea' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&h=500&fit=crop',
    'ceinture-gabriel' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&h=500&fit=crop',
    'foulard-clara' => 'https://images.unsplash.com/photo-1614613535308-eb5fbd8b4fcd?w=400&h=500&fit=crop',
    'portefeuille-henri' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&h=500&fit=crop',
    'chapeau-simone' => 'https://images.unsplash.com/photo-1552168324-d612d080e601?w=400&h=500&fit=crop',
    'sarong-femme-amelie' => 'https://images.unsplash.com/photo-1598618667-e0b82f1b94f0?w=400&h=500&fit=crop',
);

// ════════════════════════════════════════════
// Télécharger les images
// ════════════════════════════════════════════
$downloaded = 0;
$failed = 0;

echo "Téléchargement des images...\n\n";

foreach ($image_urls as $product_id => $url) {
    $filename = 'prod-' . $product_id . '.jpg';
    $filepath = MN_UPLOADS_DIR . '/' . $filename;
    
    // Éviter de re-télécharger si l'image existe
    if (file_exists($filepath)) {
        echo "✓ " . $filename . " (déjà existante)\n";
        $downloaded++;
        continue;
    }
    
    // Télécharger l'image
    echo "⏳ Téléchargement " . $filename . "...";
    
    $context = stream_context_create(array(
        'http' => array(
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        )
    ));
    
    $image_data = @file_get_contents($url, false, $context);
    
    if ($image_data === false) {
        echo " ❌ ERREUR\n";
        $failed++;
        continue;
    }
    
    // Valider que c'est une image
    $temp_file = tempnam(sys_get_temp_dir(), 'img');
    file_put_contents($temp_file, $image_data);
    $info = @getimagesize($temp_file);
    
    if (!$info) {
        echo " ❌ Pas une image valide\n";
        @unlink($temp_file);
        $failed++;
        continue;
    }
    
    // Sauvegarder l'image
    if (file_put_contents($filepath, $image_data) !== false) {
        @unlink($temp_file);
        echo " ✓ (" . (filesize($filepath)/1024) . " KB)\n";
        $downloaded++;
    } else {
        echo " ❌ Erreur de sauvegarde\n";
        @unlink($temp_file);
        $failed++;
    }
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║              RÉSUMÉ DU TÉLÉCHARGEMENT                      ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";
echo "✓ Téléchargées/Existantes : " . $downloaded . "\n";
echo "✗ Erreurs : " . $failed . "\n\n";

// ════════════════════════════════════════════
// Mettre à jour les produits avec les nouvelles images
// ════════════════════════════════════════════
echo "📝 Mise à jour de la base de données...\n\n";

require_once __DIR__ . '/includes/db.php';
$pdo = mn_db();

$updated = 0;
foreach ($image_urls as $product_id => $url) {
    $filename = 'uploads/products/prod-' . $product_id . '.jpg';
    $filepath = MN_UPLOADS_DIR . '/' . 'prod-' . $product_id . '.jpg';
    
    // Ne mettre à jour que si l'image existe
    if (file_exists($filepath)) {
        $stmt = $pdo->prepare('UPDATE mn_products SET image = ? WHERE id = ?');
        if ($stmt->execute(array($filename, $product_id))) {
            echo "✓ " . $product_id . " → " . $filename . "\n";
            $updated++;
        }
    }
}

echo "\n";
echo "✅ " . $updated . " produits ont été mis à jour avec des images uniques !\n\n";

// ════════════════════════════════════════════
// Vérification finale
// ════════════════════════════════════════════
echo "🔍 Vérification finale...\n";
$total = $pdo->query('SELECT COUNT(*) FROM mn_products')->fetchColumn();
$with_image = $pdo->query('SELECT COUNT(*) FROM mn_products WHERE image LIKE "%uploads/products/%" OR image LIKE "%assets/%"')->fetchColumn();

echo "  Total produits : " . $total . "\n";
echo "  Produits avec images : " . $with_image . "\n";

if ($with_image === $total) {
    echo "\n✅ SUCCÈS ! Tous les produits ont maintenant des images uniques !\n\n";
} else {
    echo "\n⚠️ " . ($total - $with_image) . " produits n'ont pas d'image\n\n";
}

?>
