<?php
// ==========================================
// BACKEND LOGIC: READ & DELETE
// ==========================================
session_start();

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// 2. DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$msg = "";

// 3. HANDLE CANCELLATION (DELETE CRUD)
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    
    // Security: Ensure we only delete OUR own Pending request
    $del_sql = "DELETE FROM intern_leave_applications 
                WHERE application_id = '$cancel_id' AND student_id = '$student_id' AND status = 'Pending'";
    
    if ($conn->query($del_sql) === TRUE) {
        $msg = "<div class='alert success'>✅ Application cancelled successfully.</div>";
    } else {
        $msg = "<div class='alert error'>Error cancelling: " . $conn->error . "</div>";
    }
}

// 4. FETCH DATA (READ CRUD)
$sql = "SELECT * FROM intern_leave_applications WHERE student_id = '$student_id' ORDER BY submitted_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Status | InternLeave</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* --- THEME STYLES --- */
        :root {
            --pastel-green-light: #f1f8f6;
            --pastel-green-main: #a7d7c5;
            --pastel-green-dark: #5c8d89;
            --white: #ffffff;
            --text-dark: #2d3436;
            --pending: #f59e0b;
            --approved: #10b981;
            --rejected: #ef4444;
        }

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); }

        /* NAV & MARQUEE */
        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 5px 20px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.2rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }
        .nav-links { display: flex; gap: 8px; align-items: center; }
        .nav-links a { text-decoration: none; color: #666; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        .logout-link { background: #ffeded !important; color: #ff6b6b !important; border: 1px solid #ffcccc; cursor: pointer; }

        /* CONTAINER */
        .container { max-width: 900px; margin: 50px auto; padding: 0 20px; }
        
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 2.5rem; color: var(--text-dark); margin: 0; }
        .btn-new { background: var(--pastel-green-dark); color: white; text-decoration: none; padding: 12px 25px; border-radius: 30px; font-weight: 700; box-shadow: 0 5px 15px rgba(92, 141, 137, 0.3); }
        .btn-new:hover { transform: translateY(-3px); }

        /* STATUS CARDS */
        .status-card {
            background: white; padding: 30px; border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 25px;
            display: flex; justify-content: space-between; align-items: center;
            border-left: 10px solid #ccc; position: relative; overflow: hidden;
        }

        .card-Pending { border-left-color: var(--pending); }
        .card-Approved { border-left-color: var(--approved); }
        .card-Rejected { border-left-color: var(--rejected); }

        .info h3 { margin: 0 0 5px; color: #333; font-size: 1.4rem; }
        .info .meta { color: #888; font-size: 0.9rem; margin-top: 5px; }
        .info .reason { margin-top: 10px; color: #555; background: #f9f9f9; padding: 10px; border-radius: 10px; display: inline-block; font-size: 0.95rem; }

        .status-badge { padding: 8px 20px; border-radius: 30px; font-weight: 700; font-size: 0.9rem; color: white; text-transform: uppercase; letter-spacing: 1px; text-align: center; width: 120px; display: block; }
        .bg-Pending { background: var(--pending); }
        .bg-Approved { background: var(--approved); }
        .bg-Rejected { background: var(--rejected); }

        .actions { text-align: right; margin-top: 15px; }
        .btn-cancel { background: #fff5f5; color: #ef4444; border: 1px solid #fee2e2; padding: 8px 15px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.85rem; transition: 0.2s; }
        .btn-cancel:hover { background: #ef4444; color: white; }

        .empty-state { text-align: center; padding: 60px; color: #aaa; }
        .alert { padding: 15px; border-radius: 15px; margin-bottom: 20px; text-align: center; font-weight: 700; }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #991b1b; }

        @media (max-width: 600px) { .status-card { flex-direction: column; text-align: center; gap: 20px; } .actions { text-align: center; } }
    </style>
</head>
<body>

    <div class="marquee-container">
        <div class="marquee-text"><span>✨ Track your applications in real-time. Contact your supervisor for urgent approvals.</span></div>
    </div>

    <nav>
        <a href="home.php" class="logo-text"><span class="intern">Intern</span><span class="leave">Leave</span></a>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="apply.php">Apply</a>
            <a href="status.php" class="active">Status</a>
            <a href="impact.php">Impact</a>
            <a href="history.php">History</a> <a href="profilesetting.php">Settings</a>
            <a class="logout-link" onclick="window.location.href='index.php'">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Application History</h1>
            <a href="apply.php" class="btn-new">+ New Application</a>
        </div>

        <?php echo $msg; ?>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="status-card card-<?php echo $row['status']; ?>">
                    <div class="info">
                        <h3><?php echo $row['leave_type']; ?></h3>
                        <div class="meta">
                            <i class="far fa-calendar-alt"></i> 
                            <?php echo date('d M Y', strtotime($row['start_date'])); ?> 
                            <span style="margin:0 5px;">➔</span> 
                            <?php echo date('d M Y', strtotime($row['end_date'])); ?>
                            <span style="font-weight:700; color:var(--pastel-green-dark); margin-left:10px;">
                                (<?php echo $row['total_days']; ?> Days)
                            </span>
                        </div>
                        <div class="reason">"<?php echo $row['reason']; ?>"</div>
                    </div>
                    <div style="min-width: 140px; text-align: right;">
                        <span class="status-badge bg-<?php echo $row['status']; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                        <?php if($row['status'] == 'Pending'): ?>
                            <div class="actions">
                                <a href="status.php?cancel_id=<?php echo $row['application_id']; ?>" 
                                   class="btn-cancel" onclick="return confirm('Are you sure you want to cancel?');">
                                   <i class="fas fa-trash"></i> Cancel
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-folder-open" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
                <h3>No Applications Found</h3>
                <p>You haven't submitted any leave requests yet.</p>
                <a href="apply.php" class="btn-new" style="margin-top:20px; display:inline-block;">Apply Now</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>