<?php
require_once __DIR__ . '/mailer-config.php';

function mn_mail($to, $subject, $html_body, $reply_to = null) {
    $from     = MAIL_FROM_NAME . ' <' . MAIL_FROM . '>';
    $replyTo  = $reply_to ? $reply_to : MAIL_REPLY_TO;
    $boundary = '----=_Part_' . md5(uniqid('mn', true));

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '"' . "\r\n";
    $headers .= 'From: ' . $from . "\r\n";
    $headers .= 'Reply-To: ' . $replyTo . "\r\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();

    $text_body = html_entity_decode(strip_tags($html_body), ENT_QUOTES, 'UTF-8');

    $body  = '--' . $boundary . "\r\n";
    $body .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
    $body .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
    $body .= chunk_split(base64_encode($text_body)) . "\r\n";
    $body .= '--' . $boundary . "\r\n";
    $body .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $body .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
    $body .= chunk_split(base64_encode($html_body)) . "\r\n";
    $body .= '--' . $boundary . '--';

    $encoded_subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    return @mail($to, $encoded_subject, $body, $headers);
}

function mn_email_wrap($title, $content) {
    return '<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>' . htmlspecialchars($title) . '</title>
<style>
body{margin:0;padding:0;background:#f4f1ec;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;color:#1a1a1a}
.wrap{max-width:580px;margin:40px auto;background:#fff;border-radius:4px;overflow:hidden}
.header{background:#1a1a1a;padding:32px 40px;text-align:center}
.header h1{margin:0;color:#f4f1ec;font-size:20px;letter-spacing:4px;text-transform:uppercase;font-weight:400}
.body{padding:40px}
.body p{margin:0 0 16px;line-height:1.7;font-size:15px;color:#333}
.body h2{margin:0 0 20px;font-size:18px;color:#1a1a1a;font-weight:600}
.btn{display:inline-block;margin:20px 0;padding:14px 28px;background:#1a1a1a;color:#f4f1ec !important;text-decoration:none;font-size:12px;letter-spacing:2px;text-transform:uppercase}
.divider{border:none;border-top:1px solid #e8e2d9;margin:28px 0}
.order-table{width:100%;border-collapse:collapse;margin:16px 0}
.order-table th{text-align:left;padding:8px 0;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#888;border-bottom:1px solid #e8e2d9}
.order-table td{padding:12px 0;font-size:14px;border-bottom:1px solid #f4f1ec;vertical-align:top}
.order-table .total-row td{font-weight:600;font-size:15px;border-bottom:none;padding-top:16px}
.footer{background:#f4f1ec;padding:24px 40px;text-align:center}
.footer p{margin:0;font-size:12px;color:#888;line-height:1.6}
</style>
</head>
<body>
<div class="wrap">
  <div class="header"><h1>Maison Nord</h1></div>
  <div class="body">' . $content . '</div>
  <div class="footer">
    <p>Maison Nord &middot; Bd du 13 Janvier, Lom&eacute;, Togo<br>
    <a href="mailto:bonjour@maisonnord.tg" style="color:#888;">bonjour@maisonnord.tg</a> &middot; +228 22 21 34 56</p>
  </div>
</div>
</body>
</html>';
}

/* ── Email : bienvenue ── */
function mn_mail_welcome($to, $prenom) {
    $content = '
<h2>Bienvenue, ' . htmlspecialchars($prenom) . '&nbsp;!</h2>
<p>Votre compte Maison Nord a bien &eacute;t&eacute; cr&eacute;&eacute;. Nous sommes ravis de vous accueillir.</p>
<p>Vous pouvez d&eacute;sormais passer vos commandes, suivre vos achats et g&eacute;rer vos informations depuis votre espace client.</p>
<p><a href="' . SITE_URL . '/boutique.php" class="btn">D&eacute;couvrir la collection</a></p>
<hr class="divider">
<p style="font-size:13px;color:#888;">Si vous n&rsquo;avez pas cr&eacute;&eacute; ce compte, ignorez simplement cet email.</p>';
    return mn_mail($to, 'Bienvenue chez Maison Nord', mn_email_wrap('Bienvenue', $content));
}

/* ── Email : confirmation commande ── */
function mn_mail_order_confirmation($to, $prenom, $order_id, $total, $items) {
    $rows = '';
    foreach ($items as $item) {
        $rows .= '<tr>
            <td><strong>' . htmlspecialchars($item['name']) . '</strong><br>
                <span style="font-size:12px;color:#888;">' . htmlspecialchars($item['size']) . ' &middot; ' . htmlspecialchars($item['color']) . '</span></td>
            <td style="text-align:center;">' . intval($item['qty']) . '</td>
            <td style="text-align:right;">' . number_format(intval($item['price']) * intval($item['qty']), 0, ',', ' ') . ' FCFA</td>
        </tr>';
    }
    $content = '
<h2>Votre commande a bien &eacute;t&eacute; re&ccedil;ue</h2>
<p>Bonjour ' . htmlspecialchars($prenom) . ',</p>
<p>Merci pour votre commande ! Voici le r&eacute;capitulatif :</p>
<table class="order-table">
  <thead><tr><th>Pi&egrave;ce</th><th style="text-align:center;">Qt&eacute;</th><th style="text-align:right;">Prix</th></tr></thead>
  <tbody>' . $rows . '</tbody>
  <tfoot>
    <tr class="total-row">
      <td colspan="2">Total</td>
      <td style="text-align:right;">' . number_format(intval($total), 0, ',', ' ') . ' FCFA</td>
    </tr>
  </tfoot>
</table>
<hr class="divider">
<p>Num&eacute;ro de commande : <strong>#' . intval($order_id) . '</strong></p>
<p>Notre &eacute;quipe vous contactera sous 24h pour confirmer les d&eacute;tails de livraison.</p>
<p><a href="' . SITE_URL . '/checkout/confirmation.php?order=' . intval($order_id) . '" class="btn">Voir ma commande</a></p>';
    return mn_mail($to, 'Confirmation de commande #' . $order_id . ' — Maison Nord', mn_email_wrap('Confirmation de commande', $content));
}

/* ── Email : réinitialisation mot de passe ── */
function mn_mail_password_reset($to, $prenom, $token) {
    $link = SITE_URL . '/reset-password.php?token=' . urlencode($token);
    $content = '
<h2>R&eacute;initialisation de votre mot de passe</h2>
<p>Bonjour ' . htmlspecialchars($prenom) . ',</p>
<p>Vous avez demand&eacute; &agrave; r&eacute;initialiser votre mot de passe Maison Nord. Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.</p>
<p><a href="' . $link . '" class="btn">R&eacute;initialiser mon mot de passe</a></p>
<hr class="divider">
<p style="font-size:13px;color:#888;">Ce lien est valable pendant <strong>1 heure</strong>. Si vous n&rsquo;avez pas fait cette demande, ignorez cet email &mdash; votre mot de passe reste inchang&eacute;.</p>
<p style="font-size:12px;color:#aaa;">Lien : <a href="' . htmlspecialchars($link) . '" style="color:#aaa;">' . htmlspecialchars($link) . '</a></p>';
    return mn_mail($to, 'Réinitialisation de votre mot de passe — Maison Nord', mn_email_wrap('Réinitialisation du mot de passe', $content));
}

/* ── Email : réponse automatique contact ── */
function mn_mail_contact_auto_reply($to, $prenom, $subject) {
    $content = '
<h2>Nous avons bien re&ccedil;u votre message</h2>
<p>Bonjour ' . htmlspecialchars($prenom) . ',</p>
<p>Merci de nous avoir contact&eacute;s. Votre message concernant &laquo;&nbsp;<strong>' . htmlspecialchars($subject) . '</strong>&nbsp;&raquo; a bien &eacute;t&eacute; re&ccedil;u.</p>
<p>Notre &eacute;quipe vous r&eacute;pondra dans les <strong>24 heures</strong> ouvrables.</p>
<p><a href="' . SITE_URL . '/boutique.php" class="btn">Voir la boutique</a></p>
<hr class="divider">
<p style="font-size:13px;color:#888;">Maison Nord &middot; Bd du 13 Janvier, Lom&eacute; &middot; Lun &mdash; Sam, 9h &mdash; 19h</p>';
    return mn_mail($to, 'Nous avons reçu votre message — Maison Nord', mn_email_wrap('Message reçu', $content));
}

/* ── Email : notification interne contact ── */
function mn_mail_contact_notification($prenom, $nom, $email, $subject, $message) {
    $content = '
<h2>Nouveau message de contact</h2>
<table class="order-table">
  <tbody>
    <tr><td style="width:30%;color:#888;">Nom</td><td>' . htmlspecialchars($prenom . ' ' . $nom) . '</td></tr>
    <tr><td style="color:#888;">Email</td><td><a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></td></tr>
    <tr><td style="color:#888;">Sujet</td><td>' . htmlspecialchars($subject) . '</td></tr>
    <tr><td style="color:#888;vertical-align:top;">Message</td>
        <td><pre style="white-space:pre-wrap;font-family:inherit;margin:0;font-size:14px;">' . htmlspecialchars($message) . '</pre></td></tr>
  </tbody>
</table>';
    return mn_mail(
        MAIL_REPLY_TO,
        'Contact — ' . $subject . ' (' . $prenom . ' ' . $nom . ')',
        mn_email_wrap('Nouveau message', $content),
        $email
    );
}
