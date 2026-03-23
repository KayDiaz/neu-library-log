<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['google_visitor_user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['google_visitor_user_id'];
$name = $_SESSION['google_visitor_name'];
$email = $_SESSION['google_visitor_email'];

$stmt = $conn->prepare("SELECT college FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$currentCollege = $user['college'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Visitor Check-In</title>
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
            --input-bg: rgba(255, 255, 255, 0.92);
            --input-border: rgba(255, 255, 255, 0.14);
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
            flex-direction: column;
            padding: 28px 34px;
            backdrop-filter: blur(4px);
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-soft);
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s ease;
        }

        .back-link:hover {
            color: var(--text-main);
        }

        .datetime {
            text-align: right;
        }

        .time {
            font-size: 30px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: 0.5px;
        }

        .date {
            margin-top: 6px;
            font-size: 13px;
            color: var(--text-soft);
            letter-spacing: 0.3px;
        }

        .main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px 0;
        }

        .form-shell {
            width: 100%;
            max-width: 620px;
        }

        .form-panel {
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 30px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
            padding: 28px;
        }

        .eyebrow {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.58);
            margin-bottom: 14px;
            text-align: center;
        }

        .title {
            font-size: 40px;
            font-weight: 700;
            line-height: 1.05;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        .subtitle {
            font-size: 15px;
            color: var(--text-soft);
            text-align: center;
            margin-bottom: 24px;
        }

        .info-box {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 20px;
            padding: 16px;
            margin-bottom: 22px;
        }

        .info-item {
            margin-bottom: 10px;
        }
        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 15px;
            color: var(--text-main);
            word-break: break-word;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-soft);
            letter-spacing: 0.2px;
        }

        select {
            width: 100%;
            padding: 14px 14px;
            border-radius: 16px;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            color: #1f2430;
            font-size: 14px;
            outline: none;
            transition: 0.2s ease;
        }

        select:focus {
            border-color: rgba(216, 180, 122, 0.75);
            box-shadow: 0 0 0 4px rgba(216, 180, 122, 0.14);
        }

        .submit-btn {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            cursor: pointer;
            border-radius: 18px;
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 700;
            transition: 0.22s ease;
            background: linear-gradient(135deg, #ffffff, #ececec);
            color: var(--navy);
            margin-top: 8px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            background: #ffffff;
        }

        .footer-note {
            margin-top: 18px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            letter-spacing: 0.2px;
        }

        @media (max-width: 700px) {
            .page {
                padding: 20px 16px;
            }

            .topbar {
                align-items: flex-start;
                gap: 12px;
                flex-direction: column;
            }

            .datetime {
                text-align: left;
            }

            .time {
                font-size: 24px;
            }

            .title {
                font-size: 32px;
            }

            .form-panel {
                padding: 22px 18px;
                border-radius: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="topbar">
            <a href="visitor.php" class="back-link">← Back</a>

            <div class="datetime">
                <div class="time" id="timeDisplay">--:--</div>
                <div class="date" id="dateDisplay">Loading date...</div>
            </div>
        </div>

        <div class="main">
            <div class="form-shell">
                <div class="form-panel">
                    <div class="eyebrow">Google Sign-In</div>
                    <div class="title">Complete Check-In</div>
                    <div class="subtitle">Confirm the remaining details to finish your visit log.</div>

                    <div class="info-box">
                        <div class="info-item">
                            <div class="info-label">Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($name); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
                        </div>
                    </div>
                    <form action="process_google_visitor.php" method="POST">
                        <div class="form-group">
                            <label for="college">College</label>
                            <select name="college" id="college" required>
                                <option value="">Select College</option>
                                <option value="College of Computer Studies" <?php echo $currentCollege === 'College of Computer Studies' ? 'selected' : ''; ?>>College of Computer Studies</option>
                                <option value="College of Engineering" <?php echo $currentCollege === 'College of Engineering' ? 'selected' : ''; ?>>College of Engineering</option>
                                <option value="College of Business Administration" <?php echo $currentCollege === 'College of Business Administration' ? 'selected' : ''; ?>>College of Business Administration</option>
                                <option value="College of Arts and Sciences" <?php echo $currentCollege === 'College of Arts and Sciences' ? 'selected' : ''; ?>>College of Arts and Sciences</option>
                                <option value="College of Education" <?php echo $currentCollege === 'College of Education' ? 'selected' : ''; ?>>College of Education</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="purpose">Purpose of Visit</label>
                            <select name="purpose" id="purpose" required>
                                <option value="">Select Purpose</option>
                                <option value="Reading">Reading</option>
                                <option value="Studying">Studying</option>
                                <option value="Researching">Researching</option>
                                <option value="Using the computer">Using the computer</option>
                            </select>
                        </div>

                        <button type="submit" class="submit-btn">Finish Check-In</button>
                    </form>

                    <div class="footer-note">
                        Your Google account has been verified. Complete the fields above to continue.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateDateTime() {
            const now = new Date();

            const time = now.toLocaleTimeString([], {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });

            const date = now.toLocaleDateString([], {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });

            document.getElementById('timeDisplay').textContent = time;
            document.getElementById('dateDisplay').textContent = date;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</body>
</html>