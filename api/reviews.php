<?php
require_once '../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');

$pdo    = mn_db();
$method = $_SERVER['REQUEST_METHOD'];
$pid    = trim(isset($_GET['product_id']) ? $_GET['product_id'] : '');

if ($method === 'GET') {
    if (!$pid) { echo json_encode(array()); exit; }
    $st = $pdo->prepare(
        'SELECT r.id, r.rating, r.comment, r.created_at,
                u.prenom, u.nom
         FROM mn_reviews r
         JOIN mn_users u ON u.id = r.user_id
         WHERE r.product_id = ?
         ORDER BY r.created_at DESC'
    );
    $st->execute(array($pid));
    echo json_encode($st->fetchAll());
    exit;
}

if ($method === 'POST') {
    if (!mn_is_logged_in()) { http_response_code(401); echo json_encode(array('error'=>'Connexion requise')); exit; }
    $d      = json_decode(file_get_contents('php://input'), true);
    $pid    = trim(isset($d['product_id']) ? $d['product_id'] : '');
    $rating = max(1, min(5, intval(isset($d['rating']) ? $d['rating'] : 5)));
    $comment= trim(isset($d['comment']) ? $d['comment'] : '');
    $uid    = (int)$_SESSION['mn_user_id'];
    if (!$pid) { http_response_code(400); echo json_encode(array('error'=>'Produit manquant')); exit; }
    $pdo->prepare(
        'INSERT INTO mn_reviews (product_id, user_id, rating, comment)
         VALUES (?,?,?,?)
         ON DUPLICATE KEY UPDATE rating=VALUES(rating), comment=VALUES(comment), created_at=NOW()'
    )->execute(array($pid, $uid, $rating, $comment ?: null));
    echo json_encode(array('success'=>true));
    exit;
}

http_response_code(405); echo json_encode(array('error'=>'Méthode non autorisée'));
