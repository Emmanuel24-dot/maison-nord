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
                'SELECT id, titre, auteur, extrait, contenu, created_at FROM mn_articles ORDER BY created_at DESC'
            )->fetchAll());
            break;

        case 'POST':
            $raw = file_get_contents('php://input');
            $d   = json_decode($raw, true);
            if (!is_array($d)) $d = array();

            $stmt = $pdo->prepare(
                'INSERT INTO mn_articles (titre, auteur, extrait, contenu) VALUES (?,?,?,?)'
            );
            $stmt->execute(array(
                trim(isset($d['titre'])   ? $d['titre']   : ''),
                trim(isset($d['auteur'])  ? $d['auteur']  : 'Équipe Maison Nord'),
                trim(isset($d['extrait']) ? $d['extrait'] : ''),
                trim(isset($d['contenu']) ? $d['contenu'] : ''),
            ));
            echo json_encode(array('success'=>true,'id'=>$pdo->lastInsertId()));
            break;

        case 'PUT':
            $id  = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $raw = file_get_contents('php://input');
            $d   = json_decode($raw, true);
            if (!is_array($d)) $d = array();

            $pdo->prepare(
                'UPDATE mn_articles SET titre=?, auteur=?, extrait=?, contenu=? WHERE id=?'
            )->execute(array(
                trim(isset($d['titre'])   ? $d['titre']   : ''),
                trim(isset($d['auteur'])  ? $d['auteur']  : ''),
                trim(isset($d['extrait']) ? $d['extrait'] : ''),
                trim(isset($d['contenu']) ? $d['contenu'] : ''),
                $id,
            ));
            echo json_encode(array('success'=>true));
            break;

        case 'DELETE':
            $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $pdo->prepare('DELETE FROM mn_articles WHERE id=?')->execute(array($id));
            echo json_encode(array('success'=>true));
            break;

        default:
            http_response_code(405); echo json_encode(array('error'=>'Méthode non autorisée'));
    }
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(array('error'=>$e->getMessage()));
}
