<?php
require_once 'includes/auth.php';
mn_logout();
header('Location: index.php');
exit;
