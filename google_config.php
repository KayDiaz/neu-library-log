<?php
require_once __DIR__ . '/vendor/autoload.php';

$googleClient = new Google_Client();
$googleClient->setClientId('YOUR_GOOGLE_CLIENT_ID');
$googleClient->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$googleClient->setRedirectUri('http://localhost/neu_library_log/google_callback.php');
$googleClient->addScope('email');
$googleClient->addScope('profile');
?>