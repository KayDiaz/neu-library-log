<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$todayQuery = "SELECT COUNT(*) AS total_today FROM visitor_logs WHERE visit_date = CURDATE()";
$todayResult = mysqli_query($conn, $todayQuery);
$todayCount = mysqli_fetch_assoc($todayResult)["total_today"];

$weekQuery = "SELECT COUNT(*) AS total_week FROM visitor_logs WHERE YEARWEEK(visit_date, 1) = YEARWEEK(CURDATE(), 1)";
$weekResult = mysqli_query($conn, $weekQuery);
$weekCount = mysqli_fetch_assoc($weekResult)["total_week"];

$monthQuery = "SELECT COUNT(*) AS total_month FROM visitor_logs WHERE MONTH(visit_date) = MONTH(CURDATE()) AND YEAR(visit_date) = YEAR(CURDATE())";
$monthResult = mysqli_query($conn, $monthQuery);
$monthCount = mysqli_fetch_assoc($monthResult)["total_month"];

$blockedQuery = "SELECT COUNT(*) AS total_blocked FROM users WHERE is_blocked = 1 AND role = 'visitor'";
$blockedResult = mysqli_query($conn, $blockedQuery);
$blockedCount = mysqli_fetch_assoc($blockedResult)["total_blocked"];

$chartLabels = [];
$chartValues = [];

$chartQuery = "
    SELECT u.college, COUNT(v.id) AS total
    FROM visitor_logs v
    JOIN users u ON v.user_id = u.id
    GROUP BY u.college
    ORDER BY total DESC
";
$chartResult = mysqli_query($conn, $chartQuery);

while ($chartRow = mysqli_fetch_assoc($chartResult)) {
    $chartLabels[] = $chartRow['college'];
    $chartValues[] = (int)$chartRow['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NEU Library</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            --gold: #d8b47a;
            --shadow: 0 20px 50px rgba(0, 0, 0, 0.28);
        }

        body {
            min-height: 100vh;
            background:
                linear-gradient(rgba(10, 18, 28, 0.82), rgba(10, 18, 28, 0.82)),
                url('neucampus2.jpg') center center / cover no-repeat;
            color: var(--text-main);
        }

        .layout {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 280px 1fr;
            backdrop-filter: blur(4px);
        }

        .sidebar {
            border-right: 1px solid rgba(255,255,255,0.10);
            background: rgba(0,0,0,0.14);
            padding: 28px 20px;
        }

        .brand {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .brand-sub {
            font-size: 13px;
            color: var(--text-soft);
            margin-bottom: 28px;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .nav a {
            text-decoration: none;
            color: var(--text-soft);
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(255,255,255,0.04);
            border: 1px solid transparent;
            transition: 0.2s ease;
            font-size: 14px;
            font-weight: 600;
        }

        .nav a:hover,
        .nav a.active {
            color: var(--text-main);
            background: rgba(255,255,255,0.10);
            border-color: rgba(216, 180, 122, 0.24);
        }

        .sidebar-footer {
            margin-top: 28px;
        }

        .logout-btn {
            display: block;
            text-decoration: none;
            text-align: center;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(216, 180, 122, 0.10);
            border: 1px solid rgba(216, 180, 122, 0.28);
            color: var(--gold);
            font-weight: 700;
            transition: 0.2s ease;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            background: rgba(216, 180, 122, 0.16);
        }

        .content {
            padding: 28px 28px 36px;
        }

        .page-title {
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .page-sub {
            font-size: 14px;
            color: var(--text-soft);
            margin-bottom: 24px;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
            padding: 22px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 16px;
        }

        .card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 20px;
            padding: 20px;
        }

        .card-label {
            font-size: 13px;
            color: var(--text-soft);
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 34px;
            font-weight: 700;
        }

        .chart-wrap {
            position: relative;
            width: 100%;
            min-height: 360px;
        }

        @media (max-width: 1100px) {
            .cards {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }
        }

        @media (max-width: 900px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: none;
                border-bottom: 1px solid rgba(255,255,255,0.10);
            }
        }

        @media (max-width: 560px) {
            .content {
                padding: 20px 16px 28px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 28px;
            }

            .chart-wrap {
                min-height: 280px;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">NEU Library</div>
        <div class="brand-sub">Admin Dashboard</div>

        <nav class="nav">
            <a href="dashboard.php" class="active">Overview</a>
            <a href="college_stats.php">Visitors per College</a>
            <a href="visitor_records.php">Visitor Records</a>
        </nav>

        <div class="sidebar-footer">
            <a class="logout-btn" href="logout.php">Logout</a>
        </div>
    </aside>

    <main class="content">
        <div class="page-title">Overview</div>
        <div class="page-sub">Welcome, <?php echo htmlspecialchars($_SESSION["admin_name"]); ?></div>

        <div class="panel">
            <div class="section-title">Summary</div>
            <div class="cards">
                <div class="card">
                    <div class="card-label">Visitors Today</div>
                    <div class="card-value"><?php echo $todayCount; ?></div>
                </div>
                <div class="card">
                    <div class="card-label">Visitors This Week</div>
                    <div class="card-value"><?php echo $weekCount; ?></div>
                </div>
                <div class="card">
                    <div class="card-label">Visitors This Month</div>
                    <div class="card-value"><?php echo $monthCount; ?></div>
                </div>
                <div class="card">
                    <div class="card-label">Blocked Users</div>
                    <div class="card-value"><?php echo $blockedCount; ?></div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="section-title">Visitors per College</div>
            <div class="chart-wrap">
                <canvas id="collegeChart"></canvas>
            </div>
        </div>
    </main>
</div>

<script>
const collegeLabels = <?php echo json_encode($chartLabels); ?>;
const collegeValues = <?php echo json_encode($chartValues); ?>;

const ctx = document.getElementById('collegeChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: collegeLabels,
        datasets: [{
            label: 'Visitors',
            data: collegeValues,
            borderWidth: 1,
            backgroundColor: [
                'rgba(255, 255, 255, 0.75)',
                'rgba(216, 180, 122, 0.75)',
                'rgba(143, 227, 175, 0.75)',
                'rgba(255, 155, 155, 0.75)',
                'rgba(173, 216, 230, 0.75)',
                'rgba(255, 218, 185, 0.75)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#ffffff'
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: '#ffffff'
                },
                grid: {
                    color: 'rgba(255,255,255,0.08)'
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#ffffff'
                },
                grid: {
                    color: 'rgba(255,255,255,0.08)'
                }
            }
        }
    }
});
</script>
</body>
</html>