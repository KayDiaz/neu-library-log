<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEU Library</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        :root {
            --bg-dark: #0f1722;
            --bg-mid: #182434;
            --panel: rgba(255, 255, 255, 0.10);
            --panel-border: rgba(255, 255, 255, 0.18);
            --text-main: #ffffff;
            --text-soft: rgba(255, 255, 255, 0.72);
            --visitor: #ffffff;
            --admin: #d8b47a;
            --shadow: 0 20px 50px rgba(0, 0, 0, 0.28);
        }

        body {
            min-height: 100vh;
            background:
                linear-gradient(rgba(10, 18, 28, 0.72), rgba(10, 18, 28, 0.72)),
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
            justify-content: flex-end;
            align-items: center;
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

        .center-wrap {
            width: 100%;
            max-width: 980px;
            text-align: center;
        }

        .eyebrow {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.58);
            margin-bottom: 18px;
        }

        .headline {
            font-size: 58px;
            font-weight: 700;
            line-height: 1.05;
            letter-spacing: -1.2px;
            margin-bottom: 18px;
        }

        .subtitle {
            font-size: 17px;
            color: var(--text-soft);
            margin-bottom: 34px;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(260px, 1fr));
            gap: 20px;
            max-width: 760px;
            margin: 0 auto;
        }

        .action-card {
            text-decoration: none;
            color: inherit;
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 28px;
            padding: 28px 24px;
            text-align: left;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            transition: transform 0.22s ease, border-color 0.22s ease, background 0.22s ease;
        }

        .action-card:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.14);
        }

        .action-card.visitor:hover {
            border-color: rgba(255, 255, 255, 0.35);
        }

        .action-card.admin:hover {
            border-color: rgba(216, 180, 122, 0.45);
        }

        .action-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .action-badge {
            min-width: 74px;
            height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .visitor .action-badge {
            background: rgba(255,255,255,0.12);
            color: var(--visitor);
            border: 1px solid rgba(255,255,255,0.18);
        }

        .admin .action-badge {
            background: rgba(216, 180, 122, 0.12);
            color: var(--admin);
            border: 1px solid rgba(216, 180, 122, 0.24);
        }

        .arrow {
            font-size: 24px;
            color: rgba(255,255,255,0.56);
        }

        .action-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .action-text {
            font-size: 14px;
            line-height: 1.7;
            color: var(--text-soft);
        }

        .footer-note {
            margin-top: 26px;
            font-size: 12px;
            color: rgba(255,255,255,0.46);
            letter-spacing: 0.3px;
        }

        @media (max-width: 820px) {
            .headline {
                font-size: 42px;
            }

            .subtitle {
                font-size: 15px;
            }

            .action-grid {
                grid-template-columns: 1fr;
                max-width: 480px;
            }
        }

        @media (max-width: 520px) {
            .page {
                padding: 20px 16px;
            }

            .time {
                font-size: 24px;
            }

            .headline {
                font-size: 34px;
            }

            .action-card {
                padding: 22px 18px;
                border-radius: 22px;
            }

            .action-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="topbar">
            <div class="datetime">
                <div class="time" id="timeDisplay">--:--</div>
                <div class="date" id="dateDisplay">Loading date...</div>
            </div>
        </div>

        <div class="main">
            <div class="center-wrap">
                <div class="eyebrow">NEU Library</div>

                <div class="headline">
                    How would you like to proceed?
                </div>

                <div class="subtitle">
                    Select an option to continue.
                </div>

                <div class="action-grid">
                    <a href="visitor.php" class="action-card visitor">
                        <div class="action-top">
                            <div class="action-badge">Visitor</div>
                            <div class="arrow">→</div>
                        </div>
                        <div class="action-title">Check In</div>
                        <div class="action-text">
                            Record your visit using your institutional email.
                        </div>
                    </a>

                    <a href="admin_login.php" class="action-card admin">
                        <div class="action-top">
                            <div class="action-badge">Admin</div>
                            <div class="arrow">→</div>
                        </div>
                        <div class="action-title">Access Portal</div>
                        <div class="action-text">
                            Continue to the administrative login and monitoring panel.
                        </div>
                    </a>
                </div>

                <div class="footer-note">
                    Secure and monitored system
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