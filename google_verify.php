<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db_connect.php";

$client_id = "CLIENT_ID";
$client_secret = "SECRET_ID";
$redirect_uri = "https://neu-library-visitor-log.infinityfree.me/google_verify.php";

if (!isset($_GET['code']) || empty($_GET['code'])) {
    header("Location: admin_login.php?error=Google login failed");
    exit();
}

$code = $_GET['code'];
$token_url = "https://oauth2.googleapis.com/token";

$data = [
    'code' => $code,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code'
];

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded",
        'method' => 'POST',
        'content' => http_build_query($data),
        'ignore_errors' => true
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($token_url, false, $context);

if ($result === false) {
    header("Location: admin_login.php?error=Google API error");
    exit();
}

$response = json_decode($result, true);

if (!isset($response['access_token'])) {
    header("Location: admin_login.php?error=No access token");
    exit();
}

$access_token = $response['access_token'];

$user_info = file_get_contents(
    "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . urlencode($access_token)
);

if ($user_info === false) {
    header("Location: admin_login.php?error=Failed to fetch user");
    exit();
}

$user = json_decode($user_info, true);

if (!isset($user['email'])) {
    header("Location: admin_login.php?error=Email not found");
    exit();
}

$email = trim($user['email']);
$name = trim($user['name'] ?? 'Google User');

if (!str_ends_with(strtolower($email), "@neu.edu.ph")) {
    header("Location: admin_login.php?error=Only NEU email allowed");
    exit();
}

$loginType = $_SESSION['google_login_role'] ?? 'visitor';

/* ================= ADMIN FLOW ================= */
if ($loginType === 'admin') {
    $stmt = $conn->prepare("SELECT id, name, email, role, is_blocked FROM users WHERE email = ? AND role = 'admin' LIMIT 1");

    if (!$stmt) {
        header("Location: admin_login.php?error=Database error");
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        header("Location: admin_login.php?error=This Google account is not registered as admin");
        exit();
    }

    $admin = $result->fetch_assoc();

    if ((int)$admin['is_blocked'] === 1) {
        header("Location: admin_login.php?error=Admin account is blocked");
        exit();
    }

    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];

    header("Location: dashboard.php");
    exit();
}

/* ================= VISITOR FLOW ================= */
$stmt = $conn->prepare("SELECT id, name, email, college, is_blocked FROM users WHERE email = ? LIMIT 1");

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $existingUser = $result->fetch_assoc();

    if ((int)$existingUser['is_blocked'] === 1) {
        die("This account has been blocked.");
    }

    $userId = $existingUser['id'];
} else {
    $role = "visitor";
    $college = "";
    $password = "";
    $is_blocked = 0;

    $insert = $conn->prepare("INSERT INTO users (name, email, college, role, password, is_blocked) VALUES (?, ?, ?, ?, ?, ?)");

    $insert->bind_param("sssssi", $name, $email, $college, $role, $password, $is_blocked);

    if (!$insert->execute()) {
        die("Failed to create visitor account.");
    }

    $userId = $conn->insert_id;
}

$_SESSION['google_visitor_user_id'] = $userId;
$_SESSION['google_visitor_name'] = $name;
$_SESSION['google_visitor_email'] = $email;
header("Location: visitor_purpose.php");
exit();
?>