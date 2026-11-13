<?php
/*
 * Google OAuth — Configuration
 * ─────────────────────────────────────────────────
 * 1. Aller sur https://console.cloud.google.com/
 * 2. Créer un projet → API & Services → Identifiants
 * 3. Créer un ID client OAuth 2.0 (type : Application Web)
 * 4. Ajouter l'URI de redirection autorisée :
 *    http://localhost/maison-nord/auth/google-callback.php
 * 5. Copier Client ID et Client Secret ci-dessous
 */
define('GOOGLE_CLIENT_ID',     'VOTRE_CLIENT_ID.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'VOTRE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI',  'http://localhost/maison-nord/auth/google-callback.php');
