<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET["id"]) && isset($_GET["action"])) {
    $user_id = (int)$_GET["id"];
    $action = $_GET["action"];

    if ($action === "block") {
        $stmt = $conn->prepare("UPDATE users SET is_blocked = 1 WHERE id = ? AND role = 'visitor'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    } elseif ($action === "unblock") {
        $stmt = $conn->prepare("UPDATE users SET is_blocked = 0 WHERE id = ? AND role = 'visitor'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
}

header("Location: visitor_records.php");
exit();
?>