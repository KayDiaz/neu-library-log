<?php
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $college = trim($_POST["college"]);
    $purpose = trim($_POST["purpose"]);

    if (empty($name) || empty($email) || empty($college) || empty($purpose)) {
        die("All fields are required.");
    }

    if (!preg_match('/^[a-zA-Z0-9._%+-]+@neu\.edu\.ph$/', $email)) {
        die("Only valid NEU institutional emails are allowed.");
    }

    $checkUser = $conn->prepare("SELECT id, is_blocked FROM users WHERE email = ?");
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user["is_blocked"] == 1) {
            die("This account has been blocked by the administrator.");
        }

        $user_id = $user["id"];
    } else {
        $role = "visitor";
        $password = "";
        $is_blocked = 0;

        $insertUser = $conn->prepare("INSERT INTO users (name, email, college, role, password, is_blocked) VALUES (?, ?, ?, ?, ?, ?)");
        $insertUser->bind_param("sssssi", $name, $email, $college, $role, $password, $is_blocked);

        if (!$insertUser->execute()) {
            die("Error saving user: " . $conn->error);
        }

        $user_id = $conn->insert_id;
    }

    $visit_date = date("Y-m-d");

    $insertLog = $conn->prepare("INSERT INTO visitor_logs (user_id, purpose, visit_date) VALUES (?, ?, ?)");
    $insertLog->bind_param("iss", $user_id, $purpose, $visit_date);

    if ($insertLog->execute()) {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Visit Logged</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: #f4f6f9;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .box {
                    background: white;
                    padding: 30px;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    text-align: center;
                    width: 380px;
                }
                h2 {
                    color: #1f3c88;
                    margin-bottom: 10px;
                }
                p {
                    color: #333;
                    margin-bottom: 20px;
                }
                a {
                    display: inline-block;
                    text-decoration: none;
                    background: #1f3c88;
                    color: white;
                    padding: 10px 16px;
                    border-radius: 8px;
                }
                a:hover {
                    background: #162d66;
                }
            </style>
        </head>
        <body>
            <div class='box'>
                <h2>Welcome to NEU Library</h2>
                <p>Your visit has been logged successfully.</p>
                <a href='index.php'>Back to Visitor Form</a>
            </div>
        </body>
        </html>
        ";
    } else {
        echo "Error saving visit log: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>