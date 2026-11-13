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
                'SELECT id, prenom, nom, email, subject AS sujet, message AS contenu,
                        COALESCE(lu,0) AS lu, created_at
                 FROM mn_contact_messages ORDER BY lu ASC, created_at DESC'
            )->fetchAll();

            $out = array();
            foreach ($rows as $m) {
                $out[] = array(
                    'id'         => $m['id'],
                    'expediteur' => $m['prenom'].' '.$m['nom'],
                    'email'      => $m['email'],
                    'sujet'      => $m['sujet'],
                    'contenu'    => $m['contenu'],
                    'lu'         => (bool)$m['lu'],
                    'date'       => substr($m['created_at'], 0, 10),
                );
            }
            echo json_encode($out);
            break;

        case 'PUT':
            $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $raw = file_get_contents('php://input');
            $d   = json_decode($raw, true);
            if (!is_array($d)) $d = array();

            if (isset($d['lu'])) {
                $pdo->prepare('UPDATE mn_contact_messages SET lu=? WHERE id=?')
                    ->execute(array($d['lu'] ? 1 : 0, $id));
            }
            if (!empty($d['all_read'])) {
                $pdo->exec('UPDATE mn_contact_messages SET lu=1');
            }
            echo json_encode(array('success'=>true));
            break;

        case 'DELETE':
            $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $pdo->prepare('DELETE FROM mn_contact_messages WHERE id=?')->execute(array($id));
            echo json_encode(array('success'=>true));
            break;

        default:
            http_response_code(405); echo json_encode(array('error'=>'Méthode non autorisée'));
    }
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(array('error'=>$e->getMessage()));
}
