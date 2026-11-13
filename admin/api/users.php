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
                'SELECT u.id, u.prenom, u.nom, u.email,
                        COALESCE(u.role,"client") AS role,
                        COALESCE(u.blocked,0)     AS blocked,
                        u.created_at,
                        COUNT(o.id) AS nb_commandes
                 FROM mn_users u
                 LEFT JOIN mn_orders o ON o.user_id = u.id
                 GROUP BY u.id
                 ORDER BY u.created_at DESC'
            )->fetchAll();

            $out = array();
            foreach ($rows as $u) {
                $out[] = array(
                    'id'        => $u['id'],
                    'nom'       => $u['prenom'].' '.$u['nom'],
                    'prenom'    => $u['prenom'],
                    'email'     => $u['email'],
                    'role'      => $u['role'] === 'admin' ? 'Admin' : 'Client',
                    'statut'    => $u['blocked'] ? 'Bloqué' : 'Actif',
                    'inscrit'   => substr($u['created_at'], 0, 10),
                    'commandes' => (int)$u['nb_commandes'],
                );
            }
            echo json_encode($out);
            break;

        case 'PUT':
            $id  = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $raw = file_get_contents('php://input');
            $d   = json_decode($raw, true);
            if (!is_array($d)) $d = array();

            $sets = array(); $vals = array();
            if (isset($d['statut'])) {
                $sets[] = 'blocked=?';
                $vals[] = $d['statut'] === 'Bloqué' ? 1 : 0;
            }
            if (isset($d['role'])) {
                $sets[] = 'role=?';
                $vals[] = strtolower($d['role']);
            }
            if ($sets) {
                $vals[] = $id;
                $pdo->prepare('UPDATE mn_users SET '.implode(',',$sets).' WHERE id=?')->execute($vals);
            }
            echo json_encode(array('success'=>true));
            break;

        case 'DELETE':
            $id  = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
            $own = (int)(isset($_SESSION['mn_user_id']) ? $_SESSION['mn_user_id'] : 0);
            if ($id === $own) {
                http_response_code(400);
                echo json_encode(array('error'=>'Impossible de supprimer votre propre compte'));
                exit;
            }
            $pdo->prepare('DELETE FROM mn_users WHERE id=?')->execute(array($id));
            echo json_encode(array('success'=>true));
            break;

        default:
            http_response_code(405); echo json_encode(array('error'=>'Méthode non autorisée'));
    }
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(array('error'=>$e->getMessage()));
}
