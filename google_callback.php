<?php
session_start();
include "db_connect.php";
require_once 'google_config.php';

if (!isset($_GET['code'])) {
    die("Google login failed.");
}

$token = $googleClient->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    die("Token error: " . htmlspecialchars($token['error']));
}

$googleClient->setAccessToken($token['access_token']);

$oauth = new Google_Service_Oauth2($googleClient);
$userInfo = $oauth->userinfo->get();

$email = trim($userInfo->email);
$name = trim($userInfo->name);
$requestedRole = $_SESSION['google_login_role'] ?? 'visitor';

if (!preg_match('/@neu\.edu\.ph$/', $email)) {
    die("Only NEU institutional Google accounts are allowed.");
}

if ($requestedRole === 'admin') {
    $stmt = $conn->prepare("SELECT id, name, email, role, is_blocked FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        die("This Google account is not registered as an admin.");
    }

    $admin = $result->fetch_assoc();

    if ((int)$admin['is_blocked'] === 1) {
        die("This admin account is blocked.");
    }

    $_SESSION["admin_id"] = $admin["id"];
    $_SESSION["admin_name"] = $admin["name"];
    header("Location: dashboard.php");
    exit();
}

/* Visitor login flow */
$stmt = $conn->prepare("SELECT id, name, email, role, is_blocked, college FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if ((int)$user['is_blocked'] === 1) {
        die("This account has been blocked by the administrator.");
    }

    $user_id = $user['id'];
} else {
    $defaultCollege = "Not yet selected";
    $role = "visitor";
    $password = "";

    $insert = $conn->prepare("INSERT INTO users (name, email, college, role, password, is_blocked) VALUES (?, ?, ?, ?, ?, 0)");
    $insert->bind_param("sssss", $name, $email, $defaultCollege, $role, $password);
    $insert->execute();
    $user_id = $conn->insert_id;
}

/* Log the visit immediately or redirect to a purpose page */
$_SESSION['google_visitor_user_id'] = $user_id;
$_SESSION['google_visitor_name'] = $name;
$_SESSION['google_visitor_email'] = $email;

header("Location: visitor_purpose.php");
exit();
?>