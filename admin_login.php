<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = isset($_GET['error']) ? $_GET['error'] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access - NEU Library</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        :root {
            --bg-dark: #0f1722;
            --panel: rgba(255, 255, 255, 0.10);
            --panel-border: rgba(255, 255, 255, 0.18);
            --text-main: #ffffff;
            --text-soft: rgba(255, 255, 255, 0.72);
            --text-muted: rgba(255, 255, 255, 0.52);
            --input-bg: rgba(255, 255, 255, 0.92);
            --input-border: rgba(255, 255, 255, 0.14);
            --navy: #18263f;
            --gold: #d8b47a;
            --danger-bg: rgba(255, 99, 99, 0.14);
            --danger-border: rgba(255, 99, 99, 0.28);
            --danger-text: #ffd7d7;
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
            max-width: 560px;
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
            font-size: 42px;
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
            margin-bottom: 28px;
        }

        .error {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 14px;
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger-text);
            font-size: 13px;
            text-align: center;
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

        input {
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

        input:focus {
            border-color: rgba(216, 180, 122, 0.75);
            box-shadow: 0 0 0 4px rgba(216, 180, 122, 0.14);
        }

        input::placeholder {
            color: #7d8592;
        }

        .button-stack {
            margin-top: 22px;
        }

        .login-btn,
        .google-btn,
        .back-btn {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            border-radius: 18px;
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 700;
            transition: 0.22s ease;
        }

        .login-btn {
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, #ffffff, #ececec);
            color: var(--navy);
            margin-bottom: 12px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            background: #ffffff;
        }

        .google-btn {
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255,255,255,0.20);
            color: var(--text-main);
            margin-bottom: 12px;
        }

        .google-btn:hover {
            transform: translateY(-2px);
            background: rgba(255,255,255,0.16);
        }

        .back-btn {
            background: rgba(216, 180, 122, 0.10);
            border: 1px solid rgba(216, 180, 122, 0.28);
            color: var(--gold);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            background: rgba(216, 180, 122, 0.16);
        }

        .divider {
            text-align: center;
            margin: 16px 0 12px;
            color: var(--text-muted);
            font-size: 12px;
            letter-spacing: 0.3px;
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
                font-size: 34px;
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
            <a href="index.php" class="back-link">← Back</a>

            <div class="datetime">
                <div class="time" id="timeDisplay">--:--</div>
                <div class="date" id="dateDisplay">Loading date...</div>
            </div>
        </div>

        <div class="main">
            <div class="form-shell">
                <div class="form-panel">
                    <div class="eyebrow">Administrative Access</div>
                    <div class="title">Admin Login</div>
                    <div class="subtitle">Continue using your admin credentials.</div>

                    <?php if (!empty($error)): ?>
                        <div class="error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form action="process_admin_login.php" method="POST">
                        <div class="form-group">
                            <label for="email">Admin Email</label>
                            <input type="email" name="email" id="email" placeholder="Enter admin email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" placeholder="Enter password" required>
                        </div>

                        <div class="button-stack">
                            <button type="submit" class="login-btn">Login</button>
                            <a href="google_login.php?role=admin" class="google-btn">Continue with Google</a>
                        </div>
                    </form>

                    <div class="divider">or</div>

                    <a href="visitor.php" class="back-btn">Visitor Check-In</a>
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