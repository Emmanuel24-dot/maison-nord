<?php
require_once '../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');

if (!mn_is_logged_in()) { http_response_code(401); echo json_encode(array('error'=>'Non autorisé')); exit; }

$id  = intval(isset($_GET['id']) ? $_GET['id'] : 0);
$uid = (int)$_SESSION['mn_user_id'];

$st = mn_db()->prepare('SELECT status, created_at FROM mn_orders WHERE id=? AND user_id=?');
$st->execute(array($id, $uid));
$row = $st->fetch();

if (!$row) { http_response_code(404); echo json_encode(array('error'=>'Commande introuvable')); exit; }
echo json_encode($row);
