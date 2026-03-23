<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = isset($_GET['role']) ? $_GET['role'] : 'visitor';
$_SESSION['google_login_role'] = $role;

$client_id = "CLIENT_ID";
$redirect_uri = "https://neu-library-visitor-log.infinityfree.me/google_verify.php";
$scope = "email profile";

$url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => 'code',
    'scope' => $scope,
    'access_type' => 'online',
    'prompt' => 'select_account'
]);

header("Location: " . $url);
exit();
?>