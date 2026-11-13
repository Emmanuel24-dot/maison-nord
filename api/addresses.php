<?php
require_once '../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');

if (!mn_is_logged_in()) { http_response_code(401); echo json_encode(array('error'=>'Non autorisé')); exit; }

$pdo    = mn_db();
$method = $_SERVER['REQUEST_METHOD'];
$uid    = (int)$_SESSION['mn_user_id'];

try {
    switch ($method) {
        case 'GET':
            $rows = $pdo->prepare(
                'SELECT id, label, prenom, nom, phone, address, city, is_default
                 FROM mn_addresses WHERE user_id=? ORDER BY is_default DESC, id ASC'
            );
            $rows->execute(array($uid));
            echo json_encode($rows->fetchAll());
            break;

        case 'POST':
            $d = json_decode(file_get_contents('php://input'), true);
            if (!is_array($d)) $d = array();

            $address = trim(isset($d['address']) ? $d['address'] : '');
            $city    = trim(isset($d['city'])    ? $d['city']    : '');
            if (!$address || !$city) { http_response_code(400); echo json_encode(array('error'=>'Adresse et ville requises')); exit; }

            $label  = trim(isset($d['label'])  ? $d['label']  : 'Domicile');
            $prenom = trim(isset($d['prenom']) ? $d['prenom'] : '');
            $nom    = trim(isset($d['nom'])    ? $d['nom']    : '');
            $phone  = trim(isset($d['phone'])  ? $d['phone']  : '');
            $isDef  = (int)(isset($d['is_default']) ? $d['is_default'] : 0);

            if ($isDef) {
                $pdo->prepare('UPDATE mn_addresses SET is_default=0 WHERE user_id=?')->execute(array($uid));
            }

            /* Si c'est la première adresse, en faire le défaut */
            $count = $pdo->prepare('SELECT COUNT(*) FROM mn_addresses WHERE user_id=?');
            $count->execute(array($uid));
            if ((int)$count->fetchColumn() === 0) $isDef = 1;

            $pdo->prepare(
                'INSERT INTO mn_addresses (user_id, label, prenom, nom, phone, address, city, is_default)
                 VALUES (?,?,?,?,?,?,?,?)'
            )->execute(array($uid, $label, $prenom, $nom, $phone, $address, $city, $isDef));

            echo json_encode(array('success'=>true, 'id'=>(int)$pdo->lastInsertId()));
            break;

        case 'PUT':
            $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $d  = json_decode(file_get_contents('php://input'), true);
            if (!is_array($d)) $d = array();

            /* Vérifier appartenance */
            $own = $pdo->prepare('SELECT id FROM mn_addresses WHERE id=? AND user_id=?');
            $own->execute(array($id, $uid));
            if (!$own->fetch()) { http_response_code(403); echo json_encode(array('error'=>'Accès refusé')); exit; }

            if (!empty($d['is_default'])) {
                $pdo->prepare('UPDATE mn_addresses SET is_default=0 WHERE user_id=?')->execute(array($uid));
            }

            $sets = array(); $vals = array();
            if (isset($d['label']))      { $sets[] = 'label=?';      $vals[] = $d['label']; }
            if (isset($d['prenom']))     { $sets[] = 'prenom=?';     $vals[] = $d['prenom']; }
            if (isset($d['nom']))        { $sets[] = 'nom=?';        $vals[] = $d['nom']; }
            if (isset($d['phone']))      { $sets[] = 'phone=?';      $vals[] = $d['phone']; }
            if (isset($d['address']))    { $sets[] = 'address=?';    $vals[] = $d['address']; }
            if (isset($d['city']))       { $sets[] = 'city=?';       $vals[] = $d['city']; }
            if (isset($d['is_default'])) { $sets[] = 'is_default=?'; $vals[] = (int)$d['is_default']; }

            if ($sets) {
                $vals[] = $id;
                $pdo->prepare('UPDATE mn_addresses SET '.implode(',', $sets).' WHERE id=?')->execute($vals);
            }
            echo json_encode(array('success'=>true));
            break;

        case 'DELETE':
            $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $pdo->prepare('DELETE FROM mn_addresses WHERE id=? AND user_id=?')->execute(array($id, $uid));
            echo json_encode(array('success'=>true));
            break;

        default:
            http_response_code(405); echo json_encode(array('error'=>'Méthode non autorisée'));
    }
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(array('error'=>$e->getMessage()));
}
