<?php
require_once '../../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');
if (!mn_is_logged_in()) { http_response_code(401); echo json_encode(array('error'=>'Non autorisé')); exit; }

$pdo    = mn_db();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            echo json_encode($pdo->query(
                'SELECT id, cle, valeur FROM mn_settings ORDER BY id'
            )->fetchAll());
            break;

        case 'POST':
            $raw = file_get_contents('php://input');
            $d   = json_decode($raw, true);
            if (!is_array($d)) $d = array();

            $pdo->prepare(
                'INSERT INTO mn_settings (cle, valeur) VALUES (?,?)
                 ON DUPLICATE KEY UPDATE valeur=VALUES(valeur)'
            )->execute(array(
                trim(isset($d['cle'])    ? $d['cle']    : ''),
                trim(isset($d['valeur']) ? $d['valeur'] : ''),
            ));
            echo json_encode(array('success'=>true));
            break;

        case 'PUT':
            $id  = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $raw = file_get_contents('php://input');
            $d   = json_decode($raw, true);
            if (!is_array($d)) $d = array();

            $pdo->prepare('UPDATE mn_settings SET valeur=? WHERE id=?')
                ->execute(array(trim(isset($d['valeur']) ? $d['valeur'] : ''), $id));
            echo json_encode(array('success'=>true));
            break;

        case 'DELETE':
            $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $pdo->prepare('DELETE FROM mn_settings WHERE id=?')->execute(array($id));
            echo json_encode(array('success'=>true));
            break;

        default:
            http_response_code(405); echo json_encode(array('error'=>'Méthode non autorisée'));
    }
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(array('error'=>$e->getMessage()));
}
