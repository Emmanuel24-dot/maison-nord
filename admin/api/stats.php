<?php
require_once '../../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');
if (!mn_is_logged_in()) { http_response_code(401); echo json_encode(array('error'=>'Non autorisé')); exit; }

$pdo = mn_db();

try {
    /* ── KPIs de base ── */
    $nbProduits  = (int)$pdo->query('SELECT COUNT(*) FROM mn_products WHERE active=1')->fetchColumn();
    $nbCommandes = (int)$pdo->query('SELECT COUNT(*) FROM mn_orders')->fetchColumn();
    $nbUsers     = (int)$pdo->query('SELECT COUNT(*) FROM mn_users')->fetchColumn();
    $revenue     = (int)$pdo->query("SELECT COALESCE(SUM(total),0) FROM mn_orders WHERE status NOT IN ('cancelled')")->fetchColumn();
    $nbAttente   = (int)$pdo->query("SELECT COUNT(*) FROM mn_orders WHERE status='pending'")->fetchColumn();
    $nbLivrees   = (int)$pdo->query("SELECT COUNT(*) FROM mn_orders WHERE status='delivered'")->fetchColumn();
    $nbNonLus    = (int)$pdo->query('SELECT COUNT(*) FROM mn_contact_messages WHERE COALESCE(lu,0)=0')->fetchColumn();

    $statusMap = array('pending'=>'en_attente','confirmed'=>'validee','shipped'=>'expediee','delivered'=>'livree','cancelled'=>'annulee');

    /* ── Revenus par mois (6 derniers mois) ── */
    $monthRows = $pdo->query(
        "SELECT DATE_FORMAT(created_at,'%Y-%m') AS mois,
                SUM(total) AS ca,
                COUNT(*) AS nb
         FROM mn_orders
         WHERE status NOT IN ('cancelled')
           AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
         GROUP BY mois
         ORDER BY mois ASC"
    )->fetchAll();

    $monthMap = array();
    foreach ($monthRows as $r) { $monthMap[$r['mois']] = array('ca'=>(int)$r['ca'],'nb'=>(int)$r['nb']); }

    $monthNames = array('Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc');
    $monthly = array();
    for ($i = 5; $i >= 0; $i--) {
        $ts  = strtotime("-$i month");
        $key = date('Y-m', $ts);
        $monthly[] = array(
            'mois'  => $key,
            'label' => $monthNames[(int)date('n', $ts) - 1],
            'ca'    => isset($monthMap[$key]) ? $monthMap[$key]['ca'] : 0,
            'nb'    => isset($monthMap[$key]) ? $monthMap[$key]['nb'] : 0,
        );
    }

    /* ── Top 5 produits ── */
    $topProdRows = $pdo->query(
        "SELECT oi.product_id, oi.name,
                SUM(oi.qty) AS total_qty,
                SUM(oi.qty * oi.price) AS total_ca,
                COUNT(DISTINCT oi.order_id) AS nb_orders
         FROM mn_order_items oi
         JOIN mn_orders o ON o.id = oi.order_id
         WHERE o.status NOT IN ('cancelled')
         GROUP BY oi.product_id, oi.name
         ORDER BY total_qty DESC
         LIMIT 5"
    )->fetchAll();

    $topProduits = array();
    foreach ($topProdRows as $r) {
        $topProduits[] = array(
            'name'      => $r['name'],
            'total_qty' => (int)$r['total_qty'],
            'total_ca'  => (int)$r['total_ca'],
            'nb_orders' => (int)$r['nb_orders'],
        );
    }

    /* ── Top 5 clients ── */
    $topClientsRows = $pdo->query(
        "SELECT o.user_id, u.prenom, u.nom, u.email,
                COUNT(o.id) AS nb_orders,
                SUM(o.total) AS total_ca
         FROM mn_orders o
         JOIN mn_users u ON u.id = o.user_id
         WHERE o.status NOT IN ('cancelled')
         GROUP BY o.user_id, u.prenom, u.nom, u.email
         ORDER BY total_ca DESC
         LIMIT 5"
    )->fetchAll();

    $topClients = array();
    foreach ($topClientsRows as $r) {
        $topClients[] = array(
            'name'      => $r['prenom'].' '.$r['nom'],
            'email'     => $r['email'],
            'nb_orders' => (int)$r['nb_orders'],
            'total_ca'  => (int)$r['total_ca'],
        );
    }

    /* ── 5 dernières commandes ── */
    $recentCmds = $pdo->query(
        'SELECT id, prenom, nom, total, status, created_at FROM mn_orders ORDER BY created_at DESC LIMIT 5'
    )->fetchAll();
    $tmp = array();
    foreach ($recentCmds as $r) {
        $tmp[] = array(
            'id'     => $r['id'],
            'client' => $r['prenom'].' '.$r['nom'],
            'total'  => $r['total'],
            'statut' => isset($statusMap[$r['status']]) ? $statusMap[$r['status']] : $r['status'],
            'date'   => substr($r['created_at'], 0, 10),
        );
    }
    $recentCmds = $tmp;

    /* ── 4 derniers messages ── */
    $recentMsgs = $pdo->query(
        'SELECT id, prenom, nom, subject, COALESCE(lu,0) AS lu FROM mn_contact_messages ORDER BY lu ASC, created_at DESC LIMIT 4'
    )->fetchAll();
    $tmp2 = array();
    foreach ($recentMsgs as $m) {
        $tmp2[] = array(
            'id'         => $m['id'],
            'expediteur' => $m['prenom'].' '.$m['nom'],
            'sujet'      => $m['subject'],
            'lu'         => (bool)$m['lu'],
        );
    }
    $recentMsgs = $tmp2;

    echo json_encode(array(
        'produits'     => $nbProduits,
        'commandes'    => $nbCommandes,
        'users'        => $nbUsers,
        'revenue'      => $revenue,
        'nb_attente'   => $nbAttente,
        'nb_livrees'   => $nbLivrees,
        'nb_non_lus'   => $nbNonLus,
        'monthly'      => $monthly,
        'top_produits' => $topProduits,
        'top_clients'  => $topClients,
        'recent_cmds'  => $recentCmds,
        'recent_msgs'  => $recentMsgs,
    ));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array('error' => $e->getMessage()));
}
