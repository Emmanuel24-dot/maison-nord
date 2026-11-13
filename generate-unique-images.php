<?php
/**
 * Générateur d'images uniques pour chaque produit
 * Crée 47 images avec des couleurs et styles différents
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('UPLOADS_DIR', __DIR__ . '/uploads/products');

// Vérifier/créer le dossier
if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755, true);
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║      GÉNÉRATION DES 47 IMAGES UNIQUES - PRODUITS          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Palette de couleurs variées pour les vêtements
$colors = array(
    // Femme
    array(230, 180, 150, 'Beige'),           // 1. Manteau Camille
    array(180, 140, 100, 'Camel'),           // 2. Veste Nadia
    array(200, 150, 180, 'Rose poudré'),     // 3. Pull Lila
    array(220, 210, 200, 'Crème'),           // 4. Cardigan Iris
    array(200, 100, 80, 'Terracotta'),       // 5. Robe Soléa
    array(255, 250, 240, 'Blanc cassé'),     // 6. Blouse Margot
    array(210, 190, 160, 'Sable'),           // 7. Jupe Elena
    array(50, 50, 50, 'Noir'),               // 8. Top Aline
    array(255, 255, 255, 'Blanc'),           // 9. T-shirt Sophia
    array(220, 220, 220, 'Gris clair'),      // 10. T-shirt Léa
    array(100, 180, 220, 'Bleu ciel'),       // 11. Short Rose
    array(200, 120, 100, 'Rose'),            // 12. Pull Emma
    array(150, 100, 80, 'Marron'),           // 13. Robe Julia
    array(200, 200, 180, 'Gris chiné'),      // 14. Robe Alice
    array(100, 130, 180, 'Bleu'),            // 15. Chemise Louise
    array(255, 200, 0, 'Or rose'),           // 16. Veste Sport
    array(200, 180, 160, 'Beige clair'),     // 17. Pantalon Lucia
    array(255, 230, 200, 'Crème claire'),    // 18. Jupon Grace
    array(50, 50, 50, 'Noir profond'),       // 19. Legging Isabelle
    array(60, 80, 120, 'Indigo'),            // 20. Veste Denim Nina
    array(240, 200, 220, 'Rose pale'),       // 21. Camisole Béatrice
    array(220, 200, 180, 'Sable clair'),     // 22. Bermuda Sophie
    array(180, 160, 140, 'Marron clair'),    // 23. Cape Véronique
    array(200, 150, 100, 'Orange'),          // 24. Sarong Amélie
    array(180, 200, 150, 'Vert sauge'),      // 25. Gilet Christiane
    
    // Homme
    array(200, 160, 120, 'Camel homme'),     // 26. Manteau Paul
    array(80, 100, 140, 'Bleu marine'),      // 27. Veste Marc
    array(150, 150, 150, 'Gris'),            // 28. Pull Victor
    array(255, 255, 255, 'Blanc pur'),       // 29. Chemise Auguste
    array(80, 100, 120, 'Bleu navire'),      // 30. Jean Louis
    array(100, 100, 100, 'Gris foncé'),      // 31. Pantalon Léon
    array(220, 220, 220, 'Gris perle'),      // 32. T-shirt Théo
    array(60, 60, 60, 'Noir intense'),       // 33. T-shirt Alex
    array(100, 120, 140, 'Gris-bleu'),       // 34. Bermuda Kevin
    array(200, 150, 100, 'Beige chaud'),     // 35. Sweat Jordan
    array(200, 200, 200, 'Gris souris'),     // 36. Chemise Olivier
    array(180, 180, 180, 'Gris moyen'),      // 37. Jogging Luc
    array(140, 120, 100, 'Kaki'),            // 38. Pantalon Richard
    array(220, 200, 180, 'Lin blanc'),       // 39. Veste Lin Thomas
    array(40, 40, 40, 'Noir charbon'),       // 40. Costume Pierre
    array(150, 180, 200, 'Bleu ciel clair'), // 41. Polo Sébastien
    
    // Accessoires
    array(180, 120, 80, 'Cognac'),           // 42. Sac Madeleine
    array(50, 50, 50, 'Noir accessoire'),    // 43. Pochette Léa
    array(160, 100, 60, 'Chocolat'),         // 44. Ceinture Gabriel
    array(200, 150, 100, 'Beige oro'),       // 45. Foulard Clara
    array(100, 80, 60, 'Marron foncé'),      // 46. Portefeuille Henri
    array(220, 200, 180, 'Naturel'),         // 47. Chapeau Simone
);

// Produits associés
$products = array(
    'manteau-camille', 'veste-nadia', 'pull-lila', 'cardigan-iris', 'robe-solea',
    'blouse-margot', 'jupe-elena', 'top-aline', 'tshirt-femme-sophia', 'tshirt-femme-lea',
    'short-femme-rose', 'pull-femme-emma', 'robe-femme-julia', 'robe-femme-alice', 'chemise-femme-louise',
    'veste-sport-femme-marie', 'pantalon-femme-lucia', 'jupon-femme-grace', 'legging-femme-isabelle', 'veste-denim-femme-nina',
    'camisole-femme-beatrice', 'bermuda-femme-sophie', 'cape-femme-veronique', 'sarong-femme-amelie', 'gilet-femme-christiane',
    'manteau-paul', 'veste-marc', 'pull-victor', 'chemise-auguste', 'jean-louis',
    'pantalon-leon', 'tshirt-theo', 'tshirt-homme-alex', 'bermuda-homme-kevin', 'sweat-homme-jordan',
    'chemise-homme-olivier', 'jogging-homme-luc', 'pantalon-homme-richard', 'veste-lin-homme-thomas', 'costume-homme-pierre',
    'polo-homme-sebastien',
    'sac-madeleine', 'pochette-lea', 'ceinture-gabriel', 'foulard-clara', 'portefeuille-henri', 'chapeau-simone',
);

/**
 * Fonction pour créer une image avec dégradé et texte
 */
function createProductImage($filename, $colorRGB, $productName, $colorName) {
    $path = UPLOADS_DIR . '/' . $filename;
    
    // Si l'image existe, on peut la réutiliser
    if (file_exists($path)) {
        return $filename;
    }
    
    $width = 500;
    $height = 600;
    $image = imagecreatetruecolor($width, $height);
    
    // Couleur principal
    $mainColor = imagecolorallocate($image, $colorRGB[0], $colorRGB[1], $colorRGB[2]);
    
    // Couleur plus claire (dégradé haut)
    $lightColor = imagecolorallocate($image, 
        min(255, $colorRGB[0] + 40),
        min(255, $colorRGB[1] + 40),
        min(255, $colorRGB[2] + 40)
    );
    
    // Couleur plus foncée (dégradé bas)
    $darkColor = imagecolorallocate($image,
        max(0, $colorRGB[0] - 40),
        max(0, $colorRGB[1] - 40),
        max(0, $colorRGB[2] - 40)
    );
    
    // Couleur du texte (blanc ou noir selon la luminosité)
    $brightness = ($colorRGB[0] * 299 + $colorRGB[1] * 587 + $colorRGB[2] * 114) / 1000;
    $textColor = imagecolorallocate($image, 
        $brightness > 128 ? 40 : 250,
        $brightness > 128 ? 40 : 250,
        $brightness > 128 ? 40 : 250
    );
    
    // Dégradé de haut en bas
    for ($y = 0; $y < $height; $y++) {
        $ratio = $y / $height;
        $r = $colorRGB[0] + ($ratio * 20) - 10;
        $g = $colorRGB[1] + ($ratio * 20) - 10;
        $b = $colorRGB[2] + ($ratio * 20) - 10;
        
        $lineColor = imagecolorallocate($image, 
            max(0, min(255, $r)),
            max(0, min(255, $g)),
            max(0, min(255, $b))
        );
        imageline($image, 0, $y, $width, $y, $lineColor);
    }
    
    // Bordure subtile
    $borderColor = imagecolorallocate($image,
        max(0, $colorRGB[0] - 60),
        max(0, $colorRGB[1] - 60),
        max(0, $colorRGB[2] - 60)
    );
    imagerectangle($image, 0, 0, $width-1, $height-1, $borderColor);
    
    // Ajouter du texte (nom du produit)
    $text = substr($productName, 0, 25);
    $y_text = $height / 2 - 10;
    
    // Centre le texte
    $text_lines = wordwrap($text, 20, "\n");
    $lines = explode("\n", $text_lines);
    $line_height = 20;
    $start_y = $y_text - (count($lines) * $line_height) / 2;
    
    foreach ($lines as $i => $line) {
        $x = ($width - strlen($line) * 7) / 2;
        imagestring($image, 3, (int)$x, (int)($start_y + $i * $line_height), $line, $textColor);
    }
    
    // Ajouter le nom de la couleur en bas
    imagestring($image, 2, 15, $height - 25, $colorName, $textColor);
    
    // Sauvegarder l'image
    imagejpeg($image, $path, 90);
    imagedestroy($image);
    
    return $filename;
}

// Générer toutes les images
echo "🎨 Création des images uniques...\n\n";

$created = 0;
$mapping = array();

for ($i = 0; $i < count($products); $i++) {
    $prodId = $products[$i];
    $prodName = ucfirst(str_replace('-', ' ', $prodId));
    $color = $colors[$i];
    
    // Nom déterministe : prod-001.jpg, prod-002.jpg, etc.
    $filename = 'prod-' . str_pad(($i+1), 3, '0', STR_PAD_LEFT) . '.jpg';
    
    createProductImage($filename, 
        array($color[0], $color[1], $color[2]), 
        $prodName, 
        $color[3]
    );
    
    $mapping[$prodId] = 'uploads/products/' . $filename;
    
    $created++;
    echo ($i+1) . ". ✓ " . $prodName . " → " . $filename . "\n";
}

echo "\n✅ " . $created . " images créées avec succès !\n";
echo "📁 Dossier : uploads/products/\n\n";

// Sauvegarder le mapping
file_put_contents(UPLOADS_DIR . '/product-images.json', json_encode($mapping, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "📋 Mapping créé : uploads/products/product-images.json\n";
echo "\n════════════════════════════════════════════════════════════\n";
echo "RÉSUMÉ DES IMAGES CRÉÉES :\n";
echo "════════════════════════════════════════════════════════════\n\n";

$images_created = glob(UPLOADS_DIR . '/prod-*.jpg');
sort($images_created);

echo "Total images créées : " . count($images_created) . "\n\n";

// Afficher les 10 premières et 10 dernières
foreach (array_slice($images_created, 0, 5) as $i => $img) {
    echo ($i+1) . ". " . basename($img) . " (" . number_format(filesize($img)/1024, 1) . " KB)\n";
}

if (count($images_created) > 10) {
    echo "...\n";
    foreach (array_slice($images_created, -5) as $i => $img) {
        echo (count($images_created) - 4 + $i) . ". " . basename($img) . " (" . number_format(filesize($img)/1024, 1) . " KB)\n";
    }
}

echo "\n✨ Chaque produit a maintenant sa propre image unique !\n\n";

?>
