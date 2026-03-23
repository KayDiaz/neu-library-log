<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$collegeQuery = "
    SELECT u.college, COUNT(v.id) AS total
    FROM visitor_logs v
    JOIN users u ON v.user_id = u.id
    WHERE u.college IS NOT NULL AND u.college != ''
    GROUP BY u.college
    ORDER BY total DESC
";
$collegeResult = mysqli_query($conn, $collegeQuery);

$chartLabels = [];
$chartValues = [];

if ($collegeResult && mysqli_num_rows($collegeResult) > 0) {
    mysqli_data_seek($collegeResult, 0);
    while ($row = mysqli_fetch_assoc($collegeResult)) {
        $chartLabels[] = $row["college"];
        $chartValues[] = (int)$row["total"];
    }
    mysqli_data_seek($collegeResult, 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors per College - NEU Library</title>
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

        .chart-wrap {
            position: relative;
            width: 100%;
            min-height: 420px;
        }

        .college-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .college-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 18px;
            padding: 18px;
        }

        .college-name {
            font-size: 14px;
            color: var(--text-soft);
            margin-bottom: 10px;
        }

        .college-value {
            font-size: 28px;
            font-weight: 700;
        }

        .empty {
            color: var(--text-soft);
            font-size: 14px;
            padding: 14px 0 4px;
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

            .page-title {
                font-size: 28px;
            }

            .chart-wrap {
                min-height: 320px;
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
            <a href="dashboard.php">Overview</a>
            <a href="college_stats.php" class="active">Visitors per College</a>
            <a href="visitor_records.php">Visitor Records</a>
        </nav>

        <div class="sidebar-footer">
            <a class="logout-btn" href="logout.php">Logout</a>
        </div>
    </aside>

    <main class="content">
        <div class="page-title">Visitors per College</div>
        <div class="page-sub">Welcome, <?php echo htmlspecialchars($_SESSION["admin_name"]); ?></div>

        <div class="panel">
            <div class="section-title">College Distribution</div>
            <div class="chart-wrap">
                <canvas id="collegePieChart"></canvas>
            </div>
        </div>

        <div class="panel">
            <div class="section-title">College Statistics</div>
            <div class="college-grid">
                <?php if ($collegeResult && mysqli_num_rows($collegeResult) > 0): ?>
                    <?php while ($college = mysqli_fetch_assoc($collegeResult)): ?>
                        <div class="college-card">
                            <div class="college-name"><?php echo htmlspecialchars($college["college"]); ?></div>
                            <div class="college-value"><?php echo $college["total"]; ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty">No college data found.</div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
const collegeLabels = <?php echo json_encode($chartLabels); ?>;
const collegeValues = <?php echo json_encode($chartValues); ?>;

const pieCtx = document.getElementById('collegePieChart').getContext('2d');

new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: collegeLabels,
        datasets: [{
            data: collegeValues,
            backgroundColor: [
                'rgba(255, 255, 255, 0.78)',
                'rgba(216, 180, 122, 0.82)',
                'rgba(143, 227, 175, 0.82)',
                'rgba(255, 155, 155, 0.82)',
                'rgba(173, 216, 230, 0.82)',
                'rgba(255, 218, 185, 0.82)',
                'rgba(196, 181, 253, 0.82)',
                'rgba(253, 230, 138, 0.82)'
            ],
            borderColor: 'rgba(15, 23, 34, 0.35)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#ffffff',
                    padding: 18
                }
            }
        }
    }
});
</script>
</body>
</html>