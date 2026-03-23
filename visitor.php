<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Check-In</title>
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
            --panel-strong: rgba(255, 255, 255, 0.14);
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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-soft);
            letter-spacing: 0.2px;
        }
        input,
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

        input:focus,
        select:focus {
            border-color: rgba(216, 180, 122, 0.75);
            box-shadow: 0 0 0 4px rgba(216, 180, 122, 0.14);
        }

        input::placeholder {
            color: #7d8592;
        }

        .button-stack {
            margin-top: 22px;
        }

        .submit-btn,
        .google-btn,
        .admin-btn {
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

        .submit-btn {
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, #ffffff, #ececec);
            color: var(--navy);
            margin-bottom: 12px;
        }

        .submit-btn:hover {
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

        .admin-btn {
            background: rgba(216, 180, 122, 0.10);
            border: 1px solid rgba(216, 180, 122, 0.28);
            color: var(--gold);
        }

        .admin-btn:hover {
            transform: translateY(-2px);
            background: rgba(216, 180, 122, 0.16);
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
                font-size: 34px;
            }

            .form-panel {
                padding: 22px 18px;
                border-radius: 24px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
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
                    <div class="eyebrow">Visitor Access</div>
                    <div class="title">Check In</div>
                    <div class="subtitle">Enter your details to record your visit.</div>

                    <form action="process_visitor.php" method="POST">
                        <div class="form-grid">
                            <div class="form-group full">
                                <label for="name">Full Name</label>
                                <input type="text" name="name" id="name" placeholder="Enter your full name" required>
                            </div>
                            <div class="form-group full">
                                <label for="email">Institutional Email</label>
                                <input type="email" name="email" id="email" placeholder="example@neu.edu.ph" required>
                            </div>

                            <div class="form-group">
                                <label for="college">College</label>
                                <select name="college" id="college" required>
                                    <option value="">Select College</option>
                                    <option value="College of Computer Studies">College of Computer Studies</option>
                                    <option value="College of Engineering">College of Engineering</option>
                                    <option value="College of Business Administration">College of Business Administration</option>
                                    <option value="College of Arts and Sciences">College of Arts and Sciences</option>
                                    <option value="College of Education">College of Education</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="purpose">Purpose</label>
                                <select name="purpose" id="purpose" required>
                                    <option value="">Select Purpose</option>
                                    <option value="Reading">Reading</option>
                                    <option value="Studying">Studying</option>
                                    <option value="Researching">Researching</option>
                                    <option value="Using the computer">Using the computer</option>
                                </select>
                            </div>
                        </div>

                        <div class="button-stack">
                            <button type="submit" class="submit-btn">Log Visit</button>
                            <a href="google_login.php?role=visitor" class="google-btn">Continue with Google</a>
                            <a href="admin_login.php" class="admin-btn">Admin Access</a>
                        </div>
                    </form>

                    <div class="footer-note">
                        Only valid institutional email accounts are accepted.
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