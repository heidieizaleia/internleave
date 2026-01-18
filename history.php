<?php
// ==========================================
// BACKEND LOGIC: FETCH HISTORY
// ==========================================
session_start();

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. FETCH SUMMARY STATS
// Count total days of APPROVED leave only
$stats_sql = "SELECT SUM(total_days) as total_taken, COUNT(*) as total_apps 
              FROM intern_leave_applications 
              WHERE student_id = '$student_id' AND status = 'Approved'";
$stats = $conn->query($stats_sql)->fetch_assoc();
$days_taken = $stats['total_taken'] ?? 0;
$count_approved = $stats['total_apps'] ?? 0;

// 3. FETCH ALL HISTORY RECORDS
$history_sql = "SELECT * FROM intern_leave_applications 
                WHERE student_id = '$student_id' 
                ORDER BY start_date DESC"; // Newest dates first
$result = $conn->query($history_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave History | InternLeave</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* --- THEME (MATCHING OTHERS) --- */
        :root {
            --pastel-green-light: #f1f8f6;
            --pastel-green-main: #a7d7c5;
            --pastel-green-dark: #5c8d89;
            --white: #ffffff;
            --text-dark: #2d3436;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --approved-bg: #d1fae5; --approved-text: #065f46;
            --pending-bg: #fef3c7; --pending-text: #92400e;
            --rejected-bg: #fee2e2; --rejected-text: #991b1b;
        }

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); }

        /* MARQUEE */
        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        /* NAV */
        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.2rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }
        .nav-links { display: flex; gap: 8px; align-items: center; }
        .nav-links a { text-decoration: none; color: #666; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        .logout-link { background: #ffeded !important; color: #ff6b6b !important; border: 1px solid #ffcccc; cursor: pointer; }

        /* LAYOUT */
        .container { max-width: 1000px; margin: 50px auto; padding: 0 20px; }

        /* DASHBOARD SUMMARY CARDS */
        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 25px; border-radius: 20px; box-shadow: var(--soft-shadow); display: flex; align-items: center; gap: 20px; }
        .stat-icon { width: 60px; height: 60px; border-radius: 50%; background: #e1f2eb; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--pastel-green-dark); }
        .stat-info h3 { margin: 0; font-size: 2rem; color: var(--text-dark); }
        .stat-info p { margin: 0; color: #888; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; }

        /* HISTORY TABLE STYLE */
        .history-panel { background: white; border-radius: 25px; box-shadow: var(--soft-shadow); overflow: hidden; padding: 30px; }
        .panel-header { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .panel-header h2 { margin: 0; color: var(--text-dark); }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; color: #888; font-weight: 700; font-size: 0.85rem; border-bottom: 2px solid #f0f0f0; text-transform: uppercase; }
        td { padding: 20px 15px; border-bottom: 1px solid #f9f9f9; font-size: 0.95rem; color: #555; vertical-align: middle; }
        
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #fcfcfc; }

        /* BADGES */
        .badge { padding: 6px 12px; border-radius: 20px; font-weight: 700; font-size: 0.8rem; display: inline-block; }
        .st-Approved { background: var(--approved-bg); color: var(--approved-text); }
        .st-Pending { background: var(--pending-bg); color: var(--pending-text); }
        .st-Rejected { background: var(--rejected-bg); color: var(--rejected-text); }

        .date-range { font-weight: 700; color: var(--text-dark); display: block; margin-bottom: 3px; }
        .days-count { font-size: 0.85rem; color: #aaa; }

        .empty-state { text-align: center; padding: 50px; color: #aaa; }

        @media (max-width: 768px) {
            .stats-row { grid-template-columns: 1fr; }
            th, td { padding: 10px; font-size: 0.85rem; }
            .date-range { font-size: 0.85rem; }
        }
    </style>
</head>
<body>

    <div class="marquee-container">
        <div class="marquee-text"><span>✨ Your complete internship leave record. Ensure all dates match your logbook.</span></div>
    </div>

    <nav>
        <a href="home.php" class="logo-text"><span class="intern">Intern</span><span class="leave">Leave</span></a>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="apply.php">Apply</a>
            <a href="status.php">Status</a>
            <a href="impact.php">Impact</a>
            <a href="history.php" class="active">History</a> <a href="profilesetting.php">Settings</a>
            <a class="logout-link" onclick="window.location.href='index.php'">Logout</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3><?php echo $days_taken; ?></h3>
                    <p>Total Days Taken</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="stat-info">
                    <h3><?php echo $count_approved; ?></h3>
                    <p>Approved Requests</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#fff7ed; color:#f97316;"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-info">
                    <h3><?php echo max(0, 14 - $days_taken); ?></h3>
                    <p>Days Remaining</p>
                </div>
            </div>
        </div>

        <div class="history-panel">
            <div class="panel-header">
                <h2>Leave Record Log</h2>
                <button onclick="window.print()" style="background:none; border:1px solid #ddd; padding:8px 15px; border-radius:10px; cursor:pointer; color:#666;">
                    <i class="fas fa-print"></i> Print Record
                </button>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Type / Reason</th>
                            <th>Duration</th>
                            <th>Applied On</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span style="font-weight:700; color:var(--pastel-green-dark); display:block; margin-bottom:4px;">
                                        <?php echo $row['leave_type']; ?>
                                    </span>
                                    <span style="color:#999; font-size:0.85rem; font-style:italic;">
                                        "<?php echo substr($row['reason'], 0, 30) . (strlen($row['reason']) > 30 ? '...' : ''); ?>"
                                    </span>
                                </td>
                                <td>
                                    <span class="date-range">
                                        <?php echo date('d M', strtotime($row['start_date'])); ?> - <?php echo date('d M Y', strtotime($row['end_date'])); ?>
                                    </span>
                                    <span class="days-count"><?php echo $row['total_days']; ?> Day(s)</span>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($row['submitted_at'])); ?>
                                </td>
                                <td>
                                    <span class="badge st-<?php echo $row['status']; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-history" style="font-size:3rem; margin-bottom:15px; opacity:0.2;"></i>
                    <p>No history found.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>