<?php
require_once '../../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');
if (!mn_is_logged_in()) { http_response_code(401); echo json_encode(array('error'=>'Non autorisé')); exit; }

$pdo    = mn_db();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $rows = $pdo->query(
                'SELECT id, name AS nom, category AS categorie, price AS prix,
                        COALESCE(stock,0) AS stock, active,
                        COALESCE(image,"") AS image
                 FROM mn_products ORDER BY sort_order, name'
            )->fetchAll();
            echo json_encode($rows);
            break;

        case 'POST':
            $raw = file_get_contents('php://input');
            $d   = json_decode($raw, true);
            if (!is_array($d)) $d = array();

            $nom = trim(isset($d['nom']) ? $d['nom'] : '');
            if (!$nom) { http_response_code(400); echo json_encode(array('error'=>'Nom requis')); exit; }

            $id  = 'prod-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($nom)) . '-' . time();
            $cat = trim(isset($d['categorie']) ? $d['categorie'] : 'divers');
            $prix = (int)(isset($d['prix'])   ? $d['prix']   : 0);
            $stk  = (int)(isset($d['stock'])  ? $d['stock']  : 0);
            $img  = trim(isset($d['image'])   ? $d['image']  : '');

            $pdo->prepare(
                'INSERT INTO mn_products (id, name, category, price, stock, image, short_desc, long_desc, features, active)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)'
            )->execute(array($id, $nom, $cat, $prix, $stk, $img, '', '', ''));

            echo json_encode(array(
                'success'   => true,
                'id'        => $id,
                'nom'       => $nom,
                'categorie' => $cat,
                'prix'      => $prix,
                'stock'     => $stk,
                'image'     => $img,
                'active'    => 1,
            ));
            break;

        case 'PUT':
            $id  = isset($_GET['id']) ? $_GET['id'] : '';
            $raw = file_get_contents('php://input');
            $d   = json_decode($raw, true);
            if (!is_array($d)) $d = array();

            $sets = array(); $vals = array();
            if (isset($d['nom']))       { $sets[] = 'name=?';     $vals[] = $d['nom']; }
            if (isset($d['categorie'])){ $sets[] = 'category=?'; $vals[] = $d['categorie']; }
            if (isset($d['prix']))      { $sets[] = 'price=?';    $vals[] = (int)$d['prix']; }
            if (isset($d['stock']))     { $sets[] = 'stock=?';    $vals[] = (int)$d['stock']; }
            if (isset($d['active']))    { $sets[] = 'active=?';   $vals[] = (int)$d['active']; }
            if (array_key_exists('image', $d)) { $sets[] = 'image=?'; $vals[] = trim($d['image']); }

            if ($sets) {
                $vals[] = $id;
                $pdo->prepare('UPDATE mn_products SET ' . implode(',', $sets) . ' WHERE id=?')->execute($vals);
            }
            echo json_encode(array('success'=>true));
            break;

        case 'DELETE':
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $pdo->prepare('DELETE FROM mn_products WHERE id=?')->execute(array($id));
            echo json_encode(array('success'=>true));
            break;

        default:
            http_response_code(405); echo json_encode(array('error'=>'Méthode non autorisée'));
    }
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(array('error'=>$e->getMessage()));
}
