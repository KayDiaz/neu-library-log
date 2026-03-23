<?php
session_start();
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if ($password === $admin["password"]) {
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_name"] = $admin["name"];
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: admin_login.php?error=Invalid password");
            exit();
        }
    } else {
        header("Location: admin_login.php?error=Admin account not found");
        exit();
    }
} else {
    echo "Invalid request.";
}
?>