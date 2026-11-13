<?php
require_once '../../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');
if (!mn_is_logged_in()) { http_response_code(401); echo json_encode(array('error'=>'Non autorisé')); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(array('error'=>'Méthode non autorisée')); exit;
}

if (empty($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
    http_response_code(400); echo json_encode(array('error'=>'Aucun fichier envoyé')); exit;
}

$file = $_FILES['image'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    $msgs = array(
        UPLOAD_ERR_INI_SIZE   => 'Fichier trop volumineux (limite php.ini)',
        UPLOAD_ERR_FORM_SIZE  => 'Fichier trop volumineux',
        UPLOAD_ERR_PARTIAL    => 'Upload incomplet',
        UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
        UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire sur le disque',
    );
    $msg = isset($msgs[$file['error']]) ? $msgs[$file['error']] : 'Erreur upload '.$file['error'];
    http_response_code(400); echo json_encode(array('error'=>$msg)); exit;
}

/* Validate image via getimagesize (checks actual file content) */
$info = @getimagesize($file['tmp_name']);
if (!$info) {
    http_response_code(400); echo json_encode(array('error'=>'Fichier image invalide. Utilisez JPG, PNG ou WEBP.')); exit;
}

$mimeToExt = array('image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif');
if (!isset($mimeToExt[$info['mime']])) {
    http_response_code(400); echo json_encode(array('error'=>'Format non supporté. Utilisez JPG, PNG, WEBP ou GIF.')); exit;
}

/* Max 5 MB */
if ($file['size'] > 5 * 1024 * 1024) {
    http_response_code(400); echo json_encode(array('error'=>'Image trop grande (max 5 Mo)')); exit;
}

$uploadDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500); echo json_encode(array('error'=>'Impossible de créer le dossier uploads')); exit;
    }
}

$ext      = $mimeToExt[$info['mime']];
$filename = 'prod-' . time() . '-' . mt_rand(1000, 9999) . '.' . $ext;
$dest     = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500); echo json_encode(array('error'=>'Échec de l\'enregistrement du fichier')); exit;
}

echo json_encode(array('success'=>true, 'url'=>'uploads/products/' . $filename));
