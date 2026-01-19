<?php
// ==========================================
// BACKEND LOGIC: FETCH SUPERVISOR'S INTERNS
// ==========================================
session_start();

// 1. SECURITY CHECK: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. FETCH ASSIGNED INTERNS (STUDENTS)
// Joining students with internship_placement to find those assigned to this supervisor
$sql_interns = "SELECT 
                    s.student_id, 
                    s.full_name, 
                    s.programme_code, 
                    s.year_semester,
                    (SELECT COUNT(*) FROM intern_leave_applications WHERE student_id = s.student_id AND status = 'Pending') as pending_count,
                    (SELECT COUNT(*) FROM intern_leave_applications WHERE student_id = s.student_id AND status = 'Approved') as taken_count
                FROM students s
                JOIN internship_placement p ON s.student_id = p.student_id
                WHERE p.supervisor_id = '$supervisor_id'
                ORDER BY s.full_name ASC";

$interns_res = $conn->query($sql_interns);

// 3. HANDLE MODAL DATA FETCH (When an intern is clicked)
$view_student = null;
$view_history = null;

if (isset($_GET['view_id'])) {
    $vid = $_GET['view_id'];
    
    // Fetch specific student details
    $v_sql = "SELECT s.*, p.department FROM students s 
              JOIN internship_placement p ON s.student_id = p.student_id 
              WHERE s.student_id = '$vid' AND p.supervisor_id = '$supervisor_id'";
    $view_student = $conn->query($v_sql)->fetch_assoc();
    
    // Fetch this intern's leave history + impact count
    if ($view_student) {
        $h_sql = "SELECT a.*, 
                 (SELECT COUNT(*) FROM leave_impact WHERE application_id = a.application_id) as impact_count 
                 FROM intern_leave_applications a 
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
    <title>My Interns | InternLeave Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const savedColor = localStorage.getItem('accentColor');
            document.documentElement.setAttribute('data-theme', savedTheme);
            if (savedColor) {
                document.documentElement.style.setProperty('--pastel-green-dark', savedColor);
                document.documentElement.style.setProperty('--pastel-green-main', savedColor); 
            }
        })();

        // Automatically show modal if view_id exists in URL
        window.onload = function() {
            <?php if($view_student): ?>
                document.getElementById('profileModal').style.display = 'flex';
            <?php endif; ?>
        }
    </script>

    <style>
        :root {
            --pastel-green-light: #f1f8f6;
            --pastel-green-main: #a7d7c5;
            --pastel-green-dark: #5c8d89;
            --white: #ffffff;
            --text-dark: #2d3436;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --card-bg: #ffffff;
            --border-color: #f0f0f0;
        }

        [data-theme="dark"] {
            --pastel-green-light: #1a1f1e;
            --white: #252b2a;
            --text-dark: #e1f2eb;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            --card-bg: #252b2a;
            --border-color: #3a4240;
        }

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; }

        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; position: relative; z-index: 1001; }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.4rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }
        
        .nav-links { display: flex; gap: 8px; align-items: center; }
        .nav-links a { text-decoration: none; color: #888; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        .logout-link { background: #ffeded !important; color: #ff6b6b !important; font-weight: 700 !important; border: 1px solid #ffcccc; cursor: pointer; }

        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .section-header { margin: 30px 0; font-size: 1.8rem; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 15px; }

        .intern-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .intern-card { background: var(--card-bg); border-radius: 25px; padding: 30px; text-align: center; box-shadow: var(--soft-shadow); border: 1px solid var(--border-color); cursor: pointer; text-decoration: none; display: block; color: inherit; }
        .intern-card:hover { transform: translateY(-10px); border-color: var(--pastel-green-main); }
        
        .profile-img { width: 100px; height: 100px; background: #e1f2eb; border-radius: 30px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--pastel-green-dark); font-weight: 700; overflow: hidden; }
        .profile-img img { width: 100%; height: 100%; object-fit: cover; }
        
        .stats-row { display: flex; justify-content: center; gap: 20px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color); }
        .stat-item b { display: block; font-size: 1.2rem; color: var(--pastel-green-dark); }
        .stat-item span { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #aaa; }

        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .profile-modal { background: var(--card-bg); width: 90%; max-width: 800px; max-height: 90vh; border-radius: 35px; overflow-y: auto; position: relative; padding: 40px; }
        .close-modal { position: absolute; top: 25px; right: 25px; font-size: 1.5rem; cursor: pointer; color: #ccc; }
        .modal-grid { display: grid; grid-template-columns: 250px 1fr; gap: 40px; }
        .leave-row { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--pastel-green-light); border-radius: 15px; margin-bottom: 10px; }
        .impact-tag { background: #fee2e2; color: #ef4444; font-size: 0.7rem; padding: 4px 8px; border-radius: 6px; font-weight: 700; }
        
        .btn-yes { background: #ff6b6b; color: white; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; }
        .btn-no { background: #eee; color: #555; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="marquee-container">
        <div class="marquee-text">
            <span>📢 Attention Supervisor: There are pending leave applications requiring your decision.</span>
            <span>✅ Tip: Review the "Leave Impact" section to ensure project continuity.</span>
        </div>
    </div>

    <nav>
        <a href="dashboardsupervisor.php" class="logo-text">
            <span class="intern">Intern</span><span class="leave">Leave</span>
        </a>
        <div class="nav-links">
            <a href="dashboardsupervisor.php">Dashboard</a>
            <a href="approval.php">Approvals</a>
            <a href="intern_list.php" class="active">My Interns</a>
            <a href="supervisorsetting.php">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="section-header">
            <i class="fas fa-user-graduate"></i> My Assigned Interns
        </div>

        <div class="intern-grid">
            <?php if ($interns_res && $interns_res->num_rows > 0): ?>
                <?php while($row = $interns_res->fetch_assoc()): 
                    // Generate Initials if no profile image
                    $words = explode(" ", $row['full_name']);
                    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                ?>
                <a href="intern_list.php?view_id=<?php echo $row['student_id']; ?>" class="intern-card">
                    <div class="profile-img">
                        <?php echo $initials; ?>
                    </div>
                    <h3><?php echo $row['full_name']; ?></h3>
                    <p><?php echo $row['programme_code']; ?> - Sem <?php echo $row['year_semester']; ?></p>
                    <div class="stats-row">
                        <div class="stat-item"><b><?php echo $row['pending_count']; ?></b><span>Pending</span></div>
                        <div class="stat-item"><b><?php echo $row['taken_count']; ?></b><span>Taken</span></div>
                    </div>
                </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align:center; padding: 50px; color:#aaa;">
                    <i class="fas fa-users-slash" style="font-size: 3rem; margin-bottom: 10px;"></i>
                    <p>No interns currently assigned to you.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($view_student): ?>
    <div class="modal-overlay" id="profileModal" style="display: flex;">
        <div class="profile-modal">
            <i class="fas fa-times close-modal" onclick="window.location.href='intern_list.php'"></i>
            <div class="modal-grid">
                <div style="text-align: center; border-right: 1px solid var(--border-color); padding-right: 40px;">
                    <div class="profile-img" style="width:120px; height:120px; font-size:3rem; margin: 0 auto 15px;">
                        <?php echo strtoupper(substr($view_student['full_name'], 0, 1)); ?>
                    </div>
                    <h2 style="margin-bottom:5px;"><?php echo $view_student['full_name']; ?></h2>
                    <p style="color:#888; margin-bottom:20px;"><?php echo $view_student['programme_code']; ?></p>
                    
                    <div style="text-align:left; background:var(--pastel-green-light); padding:15px; border-radius:20px; font-size:0.85rem;">
                        <p><b>ID:</b> <?php echo $view_student['student_id']; ?></p>
                        <p><b>Dept:</b> <?php echo $view_student['department'] ?? 'General'; ?></p>
                        <p><b>Email:</b> <?php echo $view_student['email']; ?></p>
                        <p><b>Phone:</b> <?php echo $view_student['phone_no']; ?></p>
                    </div>
                </div>
                <div>
                    <h3 style="margin-top:0;"><i class="fas fa-history"></i> Leave History</h3>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <?php if ($view_history && $view_history->num_rows > 0): ?>
                            <?php while($h = $view_history->fetch_assoc()): ?>
                            <div class="leave-row">
                                <div>
                                    <div style="font-weight:700;"><?php echo $h['leave_type']; ?></div>
                                    <div style="font-size:0.8rem; color:#888;">
                                        <?php echo date('M d', strtotime($h['start_date'])); ?> - <?php echo date('M d', strtotime($h['end_date'])); ?> 
                                        (<?php echo $h['total_days']; ?> Days)
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <?php if($h['impact_count'] > 0): ?>
                                        <span class="impact-tag" title="Tasks Affected"><?php echo $h['impact_count']; ?> Impact</span>
                                    <?php endif; ?>
                                    <span style="color:<?php echo ($h['status'] == 'Approved' ? 'var(--pastel-green-dark)' : '#ff6b6b'); ?>; font-weight:700;">
                                        <?php echo $row['status'] ?? $h['status']; ?>
                                    </span>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="text-align:center; color:#ccc; padding-top:20px;">No leave records found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box" style="background:var(--card-bg); padding:40px; border-radius:30px; text-align:center;">
            <div style="font-size: 4rem; margin-bottom: 10px;">👋</div>
            <h2 style="margin-top:0;">End Session?</h2>
            <div style="margin-top:20px;">
                <button class="btn-no" onclick="closeLogout()">Cancel</button>
                <button class="btn-yes" onclick="window.location.href='index.php'">Logout</button>
            </div>
        </div>
    </div>

    <script>
        function openLogout() { document.getElementById('logoutModal').style.display = 'flex'; }
        function closeLogout() { document.getElementById('logoutModal').style.display = 'none'; }
        
        window.onclick = function(e) {
            if(e.target.className === 'modal-overlay') {
                if(e.target.id === 'logoutModal') closeLogout();
                // If clicking outside the profile modal, go back to main list
                if(e.target.id === 'profileModal') window.location.href = 'intern_list.php';
            }
        }
    </script>
</body>
</html>