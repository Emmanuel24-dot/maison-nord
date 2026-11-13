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
                'SELECT id, name, cities, fee, delay, active, sort_order FROM mn_delivery_zones ORDER BY sort_order, id'
            )->fetchAll();
            echo json_encode($rows);
            break;

        case 'POST':
            $d = json_decode(file_get_contents('php://input'), true);
            if (!is_array($d)) $d = array();

            $name = trim(isset($d['name']) ? $d['name'] : '');
            if (!$name) { http_response_code(400); echo json_encode(array('error'=>'Nom requis')); exit; }

            $cities = trim(isset($d['cities'])    ? $d['cities']    : '');
            $fee    = (int)(isset($d['fee'])       ? $d['fee']       : 0);
            $delay  = trim(isset($d['delay'])      ? $d['delay']     : '24-48h');
            $active = (int)(isset($d['active'])    ? $d['active']    : 1);
            $sort   = (int)(isset($d['sort_order'])? $d['sort_order']: 0);

            $pdo->prepare(
                'INSERT INTO mn_delivery_zones (name, cities, fee, delay, active, sort_order) VALUES (?,?,?,?,?,?)'
            )->execute(array($name, $cities, $fee, $delay, $active, $sort));

            echo json_encode(array('success'=>true, 'id'=>(int)$pdo->lastInsertId()));
            break;

        case 'PUT':
            $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $d  = json_decode(file_get_contents('php://input'), true);
            if (!is_array($d)) $d = array();

            $sets = array(); $vals = array();
            if (isset($d['name']))       { $sets[] = 'name=?';       $vals[] = $d['name']; }
            if (isset($d['cities']))     { $sets[] = 'cities=?';     $vals[] = $d['cities']; }
            if (isset($d['fee']))        { $sets[] = 'fee=?';        $vals[] = (int)$d['fee']; }
            if (isset($d['delay']))      { $sets[] = 'delay=?';      $vals[] = $d['delay']; }
            if (isset($d['active']))     { $sets[] = 'active=?';     $vals[] = (int)$d['active']; }
            if (isset($d['sort_order'])) { $sets[] = 'sort_order=?'; $vals[] = (int)$d['sort_order']; }

            if ($sets) {
                $vals[] = $id;
                $pdo->prepare('UPDATE mn_delivery_zones SET '.implode(',', $sets).' WHERE id=?')->execute($vals);
            }
            echo json_encode(array('success'=>true));
            break;

        case 'DELETE':
            $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $pdo->prepare('DELETE FROM mn_delivery_zones WHERE id=?')->execute(array($id));
            echo json_encode(array('success'=>true));
            break;

        default:
            http_response_code(405); echo json_encode(array('error'=>'Méthode non autorisée'));
    }
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(array('error'=>$e->getMessage()));
}
