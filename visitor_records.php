<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$college_filter = isset($_GET["college_filter"]) ? trim($_GET["college_filter"]) : "";
$purpose_filter = isset($_GET["purpose_filter"]) ? trim($_GET["purpose_filter"]) : "";
$date_filter = isset($_GET["date_filter"]) ? trim($_GET["date_filter"]) : "";

$collegeOptionsQuery = "SELECT DISTINCT college FROM users WHERE college IS NOT NULL AND college != '' ORDER BY college ASC";
$collegeOptionsResult = mysqli_query($conn, $collegeOptionsQuery);

$sql = "
    SELECT 
        u.id AS user_id,
        u.name,
        u.email,
        u.college,
        u.is_blocked,
        v.id AS log_id,
        v.purpose,
        v.visit_date
    FROM visitor_logs v
    JOIN users u ON v.user_id = u.id
    WHERE 1=1
";

$params = [];
$types = "";

if (!empty($college_filter)) {
    $sql .= " AND u.college = ?";
    $params[] = $college_filter;
    $types .= "s";
}

if (!empty($purpose_filter)) {
    $sql .= " AND v.purpose = ?";
    $params[] = $purpose_filter;
    $types .= "s";
}

if (!empty($date_filter)) {
    $sql .= " AND v.visit_date = ?";
    $params[] = $date_filter;
    $types .= "s";
}

$sql .= " ORDER BY v.visit_date DESC, v.id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$logsResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Records - NEU Library</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }

        :root {
            --panel: rgba(255, 255, 255, 0.10);
            --panel-border: rgba(255, 255, 255, 0.18);
            --text-main: #ffffff;
            --text-soft: rgba(255, 255, 255, 0.72);
            --gold: #d8b47a;
            --navy: #18263f;
            --green: #8fe3af;
            --red: #ff9b9b;
            --input-bg: rgba(255, 255, 255, 0.92);
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

        .sidebar-footer { margin-top: 28px; }

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

        .content { padding: 28px 28px 36px; }

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
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 1fr 190px 140px;
            gap: 12px;
            margin-bottom: 18px;
        }

        .filter-form input,
        .filter-form select,
        .filter-form button {
            width: 100%;
            padding: 13px 14px;
            border-radius: 14px;
            border: none;
            font-size: 14px;
        }

        .filter-form input,
        .filter-form select {
            background: var(--input-bg);
            color: #1f2430;
        }

        .filter-form button {
            background: linear-gradient(135deg, #ffffff, #ececec);
            color: var(--navy);
            font-weight: 700;
            cursor: pointer;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 920px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            font-size: 14px;
        }

        th {
            color: var(--text-soft);
            font-weight: 700;
        }

        td {
            color: var(--text-main);
        }

        .status-active {
            color: var(--green);
            font-weight: 700;
        }

        .status-blocked {
            color: var(--red);
            font-weight: 700;
        }

        .action-btn {
            display: inline-flex;
            text-decoration: none;
            padding: 9px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            color: white;
        }

        .block-btn {
            background: rgba(255, 99, 99, 0.78);
        }

        .unblock-btn {
            background: rgba(76, 175, 80, 0.82);
        }

        .empty {
            color: var(--text-soft);
            font-size: 14px;
            padding: 14px 0 4px;
        }

        @media (max-width: 900px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar {
                border-right: none;
                border-bottom: 1px solid rgba(255,255,255,0.10);
            }

            .filter-form { grid-template-columns: 1fr; }
        }

        @media (max-width: 560px) {
            .content { padding: 20px 16px 28px; }
            .page-title { font-size: 28px; }
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
            <a href="college_stats.php">Visitors per College</a>
            <a href="visitor_records.php" class="active">Visitor Records</a>
        </nav>
        <div class="sidebar-footer">
            <a class="logout-btn" href="logout.php">Logout</a>
        </div>
    </aside>

    <main class="content">
        <div class="page-title">Visitor Records</div>
        <div class="page-sub">Welcome, <?php echo htmlspecialchars($_SESSION["admin_name"]); ?></div>

        <div class="panel">
            <div class="section-title">Records</div>

            <form class="filter-form" method="GET" action="">
                <select name="college_filter">
                    <option value="">All Colleges</option>
                    <?php while ($collegeOption = mysqli_fetch_assoc($collegeOptionsResult)): ?>
                        <option value="<?php echo htmlspecialchars($collegeOption['college']); ?>"
                            <?php echo ($college_filter === $collegeOption['college']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($collegeOption['college']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <select name="purpose_filter">
                    <option value="">All Purposes</option>
                    <option value="Reading" <?php echo ($purpose_filter === 'Reading') ? 'selected' : ''; ?>>Reading</option>
                    <option value="Studying" <?php echo ($purpose_filter === 'Studying') ? 'selected' : ''; ?>>Studying</option>
                    <option value="Researching" <?php echo ($purpose_filter === 'Researching') ? 'selected' : ''; ?>>Researching</option>
                    <option value="Using the computer" <?php echo ($purpose_filter === 'Using the computer') ? 'selected' : ''; ?>>Using the computer</option>
                </select>

                <input
                    type="date"
                    name="date_filter"
                    value="<?php echo htmlspecialchars($date_filter); ?>"
                >

                <button type="submit">Filter</button>
            </form>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>College</th>
                            <th>Purpose</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($logsResult->num_rows > 0): ?>
                            <?php while ($row = $logsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["college"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["purpose"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["visit_date"]); ?></td>
                                    <td>
                                        <?php if ($row["is_blocked"] == 1): ?>
                                            <span class="status-blocked">Blocked</span>
                                        <?php else: ?>
                                            <span class="status-active">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row["is_blocked"] == 1): ?>
                                            <a class="action-btn unblock-btn" href="toggle_block.php?id=<?php echo $row["user_id"]; ?>&action=unblock">Unblock</a>
                         <?php else: ?>
                                            <a class="action-btn block-btn" href="toggle_block.php?id=<?php echo $row["user_id"]; ?>&action=block">Block</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty">No visitor records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>               