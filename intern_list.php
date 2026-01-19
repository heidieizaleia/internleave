<?php
session_start();

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. FETCH ASSIGNED STUDENTS (INTERNS)
// Note: Using backticks for table names with spaces like `intern_leave _application`
$sql_interns = "SELECT 
                    s.student_id, 
                    s.full_name, 
                    s.programme_code, 
                    s.year_semester,
                    (SELECT COUNT(*) FROM `intern_leave _application` WHERE student_id = s.student_id AND status = 'Pending') as pending_count,
                    (SELECT COUNT(*) FROM `intern_leave _application` WHERE student_id = s.student_id AND status = 'Approved') as taken_count
                FROM student s
                JOIN internship_placement p ON s.student_id = p.student_id
                WHERE p.supervisor_id = '$supervisor_id'
                ORDER BY s.full_name ASC";

$interns_res = $conn->query($sql_interns);

// 3. HANDLE VIEW DETAILS (MODAL LOGIC)
$view_student = null;
$view_history = null;

if (isset($_GET['view_id'])) {
    $vid = $_GET['view_id'];
    
    // Fetch specific student info
    $v_sql = "SELECT s.*, p.department FROM student s 
              JOIN internship_placement p ON s.student_id = p.student_id 
              WHERE s.student_id = '$vid' AND p.supervisor_id = '$supervisor_id'";
    $view_student = $conn->query($v_sql)->fetch_assoc();
    
    // Fetch History & Impact
    if ($view_student) {
        $h_sql = "SELECT a.*, 
                 (SELECT COUNT(*) FROM leave_impact WHERE application_id = a.application_id) as impact_count 
                 FROM `intern_leave _application` a 
                 WHERE a.student_id = '$vid' 
                 ORDER BY a.start_date DESC";
        $view_history = $conn->query($h_sql);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Interns | Supervisor Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root { --pastel-green-light: #f1f8f6; --pastel-green-main: #a7d7c5; --pastel-green-dark: #5c8d89; --white: #ffffff; --text-dark: #2d3436; --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); --card-bg: #ffffff; --border-color: #f0f0f0; }
        [data-theme="dark"] { --pastel-green-light: #1a1f1e; --white: #252b2a; --text-dark: #e1f2eb; --card-bg: #252b2a; --border-color: #3a4240; }
        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; background-color: var(--pastel-green-light); color: var(--text-dark); }
        
        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.4rem; font-weight: 700; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .nav-links a { text-decoration: none; color: #888; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .intern-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .intern-card { background: var(--card-bg); border-radius: 25px; padding: 25px; text-align: center; border: 1px solid var(--border-color); cursor: pointer; text-decoration: none; color: inherit; display: block; }
        .intern-card:hover { transform: translateY(-5px); border-color: var(--pastel-green-main); }
        
        .profile-img { width: 80px; height: 80px; background: #e1f2eb; border-radius: 20px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--pastel-green-dark); font-weight: 700; }
        .stats-row { display: flex; justify-content: center; gap: 15px; margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color); }
        .stat-item b { display: block; color: var(--pastel-green-dark); }
        .stat-item span { font-size: 0.65rem; text-transform: uppercase; color: #aaa; }

        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(5px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .profile-modal { background: var(--card-bg); width: 90%; max-width: 700px; border-radius: 30px; padding: 30px; position: relative; }
        .close-modal { position: absolute; top: 20px; right: 20px; cursor: pointer; color: #ccc; }
        .modal-grid { display: grid; grid-template-columns: 200px 1fr; gap: 30px; }
        .leave-row { background: var(--pastel-green-light); padding: 12px; border-radius: 15px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .impact-badge { background: #ffeded; color: #ff6b6b; padding: 2px 8px; border-radius: 5px; font-size: 0.7rem; font-weight: 700; }
    </style>
</head>
<body>

<div class="marquee-container">
        <div class="marquee-text"><span>✨ Customize your supervisor profile and application preferences here.</span></div>
    </div>

    <nav>
        <a href="dashboardsupervisor.php" class="logo-text"><span class="intern">Intern</span><span class="leave">Leave</span></a>
        
        <div class="nav-links">
            <a href="dashboardsupervisor.php">Dashboard</a>
            <a href="approval.php">Approvals</a>
            <a href="intern_list.php">My Interns</a>
            <a href="supervisorsetting.php" class="active">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h2><i class="fas fa-user-graduate"></i> Assigned Students</h2>
        <div class="intern-grid">
            <?php if ($interns_res && $interns_res->num_rows > 0): ?>
                <?php while($row = $interns_res->fetch_assoc()): 
                    $initials = strtoupper(substr($row['full_name'], 0, 1));
                ?>
                <a href="intern_list.php?view_id=<?php echo $row['student_id']; ?>" class="intern-card">
                    <div class="profile-img"><?php echo $initials; ?></div>
                    <h3><?php echo $row['full_name']; ?></h3>
                    <p style="font-size: 0.85rem; color: #888;"><?php echo $row['programme_code']; ?></p>
                    <div class="stats-row">
                        <div class="stat-item"><b><?php echo $row['pending_count']; ?></b><span>Pending</span></div>
                        <div class="stat-item"><b><?php echo $row['taken_count']; ?></b><span>Approved</span></div>
                    </div>
                </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No students assigned.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($view_student): ?>
    <div class="modal-overlay" style="display: flex;" onclick="window.location.href='intern_list.php'">
        <div class="profile-modal" onclick="event.stopPropagation()">
            <i class="fas fa-times close-modal" onclick="window.location.href='intern_list.php'"></i>
            <div class="modal-grid">
                <div style="text-align: center; border-right: 1px solid var(--border-color);">
                    <div class="profile-img" style="width:100px; height:100px;"><?php echo strtoupper(substr($view_student['full_name'], 0, 1)); ?></div>
                    <h4><?php echo $view_student['full_name']; ?></h4>
                    <p style="font-size:0.8rem; color:#888;"><?php echo $view_student['student_id']; ?></p>
                    <div style="text-align:left; font-size:0.75rem; margin-top:20px; padding:10px; background:var(--pastel-green-light); border-radius:10px;">
                        <p><b>Dept:</b> <?php echo $view_student['department']; ?></p>
                        <p><b>Email:</b> <?php echo $view_student['email']; ?></p>
                        <p><b>Phone:</b> <?php echo $view_student['phone_no']; ?></p>
                    </div>
                </div>
                <div>
                    <h3 style="margin-top:0;">Leave History</h3>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <?php if ($view_history && $view_history->num_rows > 0): ?>
                            <?php while($h = $view_history->fetch_assoc()): ?>
                            <div class="leave-row">
                                <div>
                                    <div style="font-weight:700; font-size:0.9rem;"><?php echo $h['leave_type']; ?></div>
                                    <div style="font-size:0.75rem; color:#888;"><?php echo $h['start_date']; ?> (<?php echo $h['total_days']; ?> days)</div>
                                </div>
                                <div style="text-align:right;">
                                    <?php if($h['impact_count'] > 0): ?>
                                        <span class="impact-badge"><?php echo $h['impact_count']; ?> Impact</span><br>
                                    <?php endif; ?>
                                    <span style="font-size:0.8rem; font-weight:700; color:<?php echo $h['status']=='Approved'?'#5c8d89':'#ff6b6b'; ?>"><?php echo $h['status']; ?></span>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color:#ccc;">No leave history.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>