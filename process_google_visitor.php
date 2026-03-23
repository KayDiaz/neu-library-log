<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['google_visitor_user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = (int)$_SESSION['google_visitor_user_id'];
$college = trim($_POST['college'] ?? '');
$purpose = trim($_POST['purpose'] ?? '');

if ($college === '' || $purpose === '') {
    die("College and purpose are required.");
}

$update = $conn->prepare("UPDATE users SET college = ? WHERE id = ?");
$update->bind_param("si", $college, $user_id);
$update->execute();

$visit_date = date("Y-m-d");
$insert = $conn->prepare("INSERT INTO visitor_logs (user_id, purpose, visit_date) VALUES (?, ?, ?)");
$insert->bind_param("iss", $user_id, $purpose, $visit_date);
$insert->execute();

unset($_SESSION['google_visitor_user_id'], $_SESSION['google_visitor_name'], $_SESSION['google_visitor_email']);

echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Visit Logged</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        :root {
            --panel: rgba(255, 255, 255, 0.10);
            --panel-border: rgba(255, 255, 255, 0.18);
            --text-main: #ffffff;
            --text-soft: rgba(255, 255, 255, 0.72);
            --text-muted: rgba(255, 255, 255, 0.52);
            --navy: #18263f;
            --gold: #d8b47a;
            --shadow: 0 20px 50px rgba(0, 0, 0, 0.28);
        }

        body {
            min-height: 100vh;
            background:
                linear-gradient(rgba(10, 18, 28, 0.76), rgba(10, 18, 28, 0.76)),
                url('neucampus2.jpg') center center / cover no-repeat;
            color: var(--text-main);
        }

        .page {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
            backdrop-filter: blur(4px);
        }

        .success-panel {
            width: 100%;
            max-width: 460px;
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 30px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
            padding: 30px 26px;
            text-align: center;
        }

        .eyebrow {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.58);
            margin-bottom: 14px;
        }

        .title {
            font-size: 38px;
            font-weight: 700;
            line-height: 1.08;
            margin-bottom: 12px;
            letter-spacing: -1px;
        }

        .message {
            font-size: 15px;
            color: var(--text-soft);
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .back-btn {
            width: 100%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            border-radius: 18px;
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 700;
            transition: 0.22s ease;
            background: linear-gradient(135deg, #ffffff, #ececec);
            color: var(--navy);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            background: #ffffff;
        }

        .footer-note {
            margin-top: 16px;
            font-size: 12px;
            color: var(--text-muted);
            letter-spacing: 0.2px;
        }

        @media (max-width: 600px) {
            .success-panel {
                padding: 24px 18px;
                border-radius: 24px;
            }

            .title {
                font-size: 32px;
            }
            .message {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class='page'>
        <div class='success-panel'>
            <div class='eyebrow'>Visitor Entry Recorded</div>
            <div class='title'>Welcome to NEU Library</div>
            <div class='message'>
                Your visit has been logged successfully.
            </div>
            <a href='landing.php' class='back-btn'>Exit</a>
            <div class='footer-note'>
                Thank you for using the library check-in system.
            </div>
        </div>
    </div>
</body>
</html>
";
?>