<?php
require_once '../../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');
if (!mn_is_logged_in()) { http_response_code(401); echo json_encode(array('error'=>'Non autorisé')); exit; }

$pdo    = mn_db();
$method = $_SERVER['REQUEST_METHOD'];

$toFr = array('pending'=>'en_attente','confirmed'=>'validee','shipped'=>'expediee','delivered'=>'livree','cancelled'=>'annulee');
$toDB = array_flip($toFr);

try {
    switch ($method) {
        case 'GET':
            $orders = $pdo->query(
                'SELECT id, prenom, nom, email, city, total, status, created_at
                 FROM mn_orders ORDER BY created_at DESC'
            )->fetchAll();

            $itemsStmt = $pdo->prepare(
                'SELECT name AS nom, qty AS qte, price AS prix FROM mn_order_items WHERE order_id=?'
            );

            $result = array();
            foreach ($orders as $o) {
                $itemsStmt->execute(array($o['id']));
                $result[] = array(
                    'id'       => $o['id'],
                    'numero'   => 'CMD-'.str_pad($o['id'], 4, '0', STR_PAD_LEFT),
                    'client'   => $o['prenom'].' '.$o['nom'],
                    'email'    => $o['email'],
                    'ville'    => $o['city'],
                    'total'    => $o['total'],
                    'statut'   => isset($toFr[$o['status']]) ? $toFr[$o['status']] : $o['status'],
                    'date'     => substr($o['created_at'], 0, 10),
                    'articles' => $itemsStmt->fetchAll(),
                );
            }
            echo json_encode($result);
            break;

        case 'PUT':
            $id  = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $raw = file_get_contents('php://input');
            $d   = json_decode($raw, true);
            if (!is_array($d)) $d = array();

            if (isset($d['statut'])) {
                $dbStatus = isset($toDB[$d['statut']]) ? $toDB[$d['statut']] : $d['statut'];
                $pdo->prepare('UPDATE mn_orders SET status=? WHERE id=?')->execute(array($dbStatus, $id));
            }
            echo json_encode(array('success'=>true));
            break;

        case 'DELETE':
            $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $pdo->prepare('DELETE FROM mn_orders WHERE id=?')->execute(array($id));
            echo json_encode(array('success'=>true));
            break;

        default:
            http_response_code(405); echo json_encode(array('error'=>'Méthode non autorisée'));
    }
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(array('error'=>$e->getMessage()));
}
