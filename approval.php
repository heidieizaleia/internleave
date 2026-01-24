<?php
// ==========================================
// BACKEND LOGIC: LEAVE APPROVALS & TASK ASSIGNMENT
// ==========================================
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$supervisor_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// --- 1. HANDLE APPROVAL/REJECTION ACTION ---
if (isset($_GET['action']) && isset($_GET['app_id'])) {
    $app_id = $_GET['app_id'];
    $status = ($_GET['action'] == 'approve') ? 'Approved' : 'Rejected';
    
    $update_sql = "UPDATE intern_leave_applications SET status = '$status' WHERE application_id = '$app_id'";
    
    if ($conn->query($update_sql)) {
        try {
            $log_sql = "INSERT INTO leave_approval (application_id, supervisor_id, approval_status, approved_at) 
                        VALUES ('$app_id', '$supervisor_id', '$status', NOW())";
            @$conn->query($log_sql); 
        } catch (Exception $e) { }
        
        header("Location: approval.php?msg=success");
        exit();
    }
}

// --- 2. HANDLE TASK REASSIGNMENT (NEW FEATURE) ---
if (isset($_POST['assign_task'])) {
    $imp_id = $_POST['impact_id'];
    $app_id = $_POST['view_app_id']; // To keep modal open
    $worker_name = $conn->real_escape_string($_POST['worker_name']);

    $conn->query("UPDATE leave_impacts SET assigned_to = '$worker_name' WHERE impact_id = '$imp_id'");
    
    // Refresh page keeping the modal open
    header("Location: approval.php?view_app=" . $app_id); 
    exit();
}

// --- 3. FETCH DATA ---
$pending_sql = "SELECT a.*, s.full_name, s.student_id 
                FROM intern_leave_applications a
                JOIN students s ON a.student_id = s.student_id
                JOIN internship_placements p ON a.placement_id = p.placement_id
                WHERE p.supervisor_id = '$supervisor_id' AND a.status = 'Pending'
                ORDER BY a.submitted_at ASC";
$pending_res = $conn->query($pending_sql);

$history_sql = "SELECT a.*, s.full_name, s.student_id 
                FROM intern_leave_applications a
                JOIN students s ON a.student_id = s.student_id
                JOIN internship_placements p ON a.placement_id = p.placement_id
                WHERE p.supervisor_id = '$supervisor_id' AND a.status != 'Pending'
                ORDER BY a.submitted_at DESC LIMIT 10";
$history_res = $conn->query($history_sql);

// --- 4. MODAL LOGIC: FETCH DETAILS & IMPACTS ---
$show_modal = false;
$modal_data = null;
$impact_list = null;

if (isset($_GET['view_app'])) {
    $vid = $_GET['view_app'];
    $show_modal = true;

    // Get Application Info
    $modal_sql = "SELECT a.*, s.full_name FROM intern_leave_applications a 
                  JOIN students s ON a.student_id = s.student_id 
                  WHERE a.application_id = '$vid'";
    $modal_data = $conn->query($modal_sql)->fetch_assoc();

    // Get Impacts for this application
    $impact_sql = "SELECT * FROM leave_impacts WHERE application_id = '$vid'";
    $impact_list = $conn->query($impact_sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Approvals | InternLeave Portal</title>
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
    </script>
    <style>
        :root { --pastel-green-light: #f1f8f6; --pastel-green-main: #a7d7c5; --pastel-green-dark: #5c8d89; --white: #ffffff; --text-dark: #2d3436; --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); --card-bg: #ffffff; --border-color: #f0f0f0; --danger: #ff6b6b; --success: #10b981; }
        [data-theme="dark"] { --pastel-green-light: #1a1f1e; --white: #252b2a; --text-dark: #e1f2eb; --card-bg: #252b2a; --border-color: #3a4240; }
        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; }
        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; position: relative; z-index: 1001; overflow: hidden;}
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 25s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.4rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }
        .nav-links { display: flex; gap: 8px; align-items: center; }
        .nav-links a { text-decoration: none; color: #888; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        .logout-link { background: #ffeded !important; color: #ff6b6b !important; font-weight: 700 !important; border: 1px solid #ffcccc; cursor: pointer; }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .section-header { margin: 40px 0 20px; font-size: 1.5rem; color: var(--pastel-green-dark); border-bottom: 2px solid var(--pastel-green-main); padding-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .approval-card { background: var(--card-bg); border-radius: 20px; padding: 25px; margin-bottom: 20px; box-shadow: var(--soft-shadow); border: 1px solid var(--border-color); display: grid; grid-template-columns: 80px 1fr 180px; gap: 20px; align-items: center; }
        .student-avatar { width: 70px; height: 70px; background: var(--pastel-green-light); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: var(--pastel-green-dark); }
        .details-box h3 { margin: 0; font-size: 1.2rem; color: var(--text-dark); }
        .meta { font-size: 0.85rem; color: #888; display: flex; gap: 15px; margin-top: 5px; margin-bottom: 8px; }
        .btn-link { color: var(--pastel-green-dark); text-decoration: underline; font-size: 0.85rem; font-weight: 700; cursor: pointer; background: none; border: none; padding: 0; }
        .action-group { display: flex; flex-direction: column; gap: 10px; }
        .btn { border: none; padding: 10px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; text-align: center; }
        .btn-approve { background: var(--success); color: white; }
        .btn-reject { background: #fff1f1; color: var(--danger); border: 1px solid #ffcccc; }
        .status-pill { padding: 5px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-Approved { background: #ecfdf5; color: #047857; }
        .status-Rejected { background: #fef2f2; color: #dc2626; }
        
        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .modal-box { background: var(--card-bg); padding: 40px; border-radius: 30px; width: 90%; max-width: 550px; text-align: center; max-height: 90vh; overflow-y: auto; }
        .doc-preview { border: 2px dashed var(--border-color); padding: 15px; border-radius: 15px; display: flex; align-items: center; gap: 15px; background: var(--pastel-green-light); text-decoration: none; color: var(--text-dark); margin: 15px 0; text-align: left; cursor: pointer; }
        .doc-preview:hover { border-color: var(--pastel-green-dark); background: #e1f2eb; }
        .btn-yes { background: #ff6b6b; color: white; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; margin-left:10px; }
        .btn-no { background: #eee; color: #555; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; }

        /* Assign Task Styles */
        .impact-item { background: var(--pastel-green-light); padding: 15px; border-radius: 15px; margin-bottom: 10px; text-align: left; border: 1px solid var(--border-color); }
        .impact-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .tag { background: #fff; padding: 2px 8px; border-radius: 5px; font-size: 0.7rem; font-weight: 700; color: var(--pastel-green-dark); border: 1px solid var(--pastel-green-dark); }
        .assign-form { display: flex; gap: 10px; margin-top: 10px; }
        .assign-input { flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 8px; font-size: 0.85rem; }
        .assign-btn { background: var(--pastel-green-dark); color: white; border: none; padding: 8px 15px; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .assigned-label { display: block; margin-top: 5px; font-size: 0.8rem; color: #047857; font-weight: 700; }
    </style>
</head>
<body>
    <div class="marquee-container">
        <div class="marquee-text"><span>📢 Attention Supervisor: Review documents thoroughly before making a decision.</span></div>
    </div>
    <nav>
        <a href="dashboardsupervisor.php" class="logo-text"><span class="intern">Intern</span><span class="leave">Leave</span></a>
        <div class="nav-links">
            <a href="dashboardsupervisor.php">Dashboard</a>
            <a href="approval.php" class="active">Approvals</a>
            <a href="intern_list.php">My Interns</a>
            <a href="supervisorsetting.php">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>
    <div class="container">
        <div class="section-header"><i class="fas fa-hourglass-half"></i> Pending Queue</div>
        <div id="pending-list">
            <?php if ($pending_res->num_rows > 0): ?>
                <?php while($row = $pending_res->fetch_assoc()): ?>
                    <div class="approval-card">
                        <div class="student-avatar"><?php echo strtoupper(substr($row['full_name'], 0, 2)); ?></div>
                        <div class="details-box">
                            <h3><?php echo $row['full_name']; ?></h3>
                            <div class="meta"><span><i class="far fa-calendar"></i> <?php echo $row['total_days']; ?> Day(s)</span><span><i class="fas fa-tag"></i> <?php echo $row['leave_type']; ?></span></div>
                            <a href="approval.php?view_app=<?php echo $row['application_id']; ?>" class="btn-link">Review Details & Impacts</a>
                        </div>
                        <div class="action-group">
                            <a href="approval.php?action=approve&app_id=<?php echo $row['application_id']; ?>" class="btn btn-approve">Approve</a>
                            <a href="approval.php?action=reject&app_id=<?php echo $row['application_id']; ?>" class="btn btn-reject">Reject</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; padding:20px; color:#888;">No pending applications.</p>
            <?php endif; ?>
        </div>
        
        <div class="section-header"><i class="fas fa-history"></i> Decision History</div>
        <div id="history-list">
            <?php while($row = $history_res->fetch_assoc()): ?>
                <div class="approval-card" style="border-left: 5px solid <?php echo ($row['status'] == 'Approved') ? 'var(--success)' : 'var(--danger)'; ?>;">
                    <div class="student-avatar"><?php echo strtoupper(substr($row['full_name'], 0, 2)); ?></div>
                    <div class="details-box">
                        <div style="display: flex; align-items: center; gap: 10px;"><h3><?php echo $row['full_name']; ?></h3><span class="status-pill status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></div>
                        <div class="meta"><span><i class="far fa-calendar"></i> <?php echo $row['total_days']; ?> Day(s)</span></div>
                        <a href="approval.php?view_app=<?php echo $row['application_id']; ?>" class="btn-link">View Details</a>
                    </div>
                    <div class="action-group" style="align-items: center; justify-content: center; opacity: 0.6;">
                        <i class="fas <?php echo ($row['status'] == 'Approved') ? 'fa-check-circle' : 'fa-times-circle'; ?>" style="font-size: 1.5rem; color: <?php echo ($row['status'] == 'Approved') ? 'var(--success)' : 'var(--danger)'; ?>;"></i>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php if ($show_modal && $modal_data): ?>
    <div class="modal-overlay" style="display: flex;" onclick="window.location.href='approval.php'">
        <div class="modal-box" onclick="event.stopPropagation()">
            <h2 style="margin-top:0; color:var(--text-dark);">Application Details</h2>
            <p style="color:#888;"><?php echo $modal_data['full_name']; ?> • <?php echo $modal_data['leave_type']; ?></p>
            
            <div style="text-align:left; margin-bottom:15px;">
                <strong>Reason:</strong>
                <p style="color:#666; font-style: italic; background: var(--pastel-green-light); padding:10px; border-radius:10px; margin-top:5px;">
                    "<?php echo $modal_data['reason']; ?>"
                </p>
            </div>

            <?php if ($modal_data['supporting_doc_path']): ?>
            <a href="<?php echo $modal_data['supporting_doc_path']; ?>" target="_blank" class="doc-preview">
                <i class="fas fa-file-pdf" style="font-size:2rem; color:var(--pastel-green-dark);"></i>
                <div><div style="font-weight:700;">Supporting Document</div><div style="font-size:0.8rem; color:#888;">Click to view file</div></div>
            </a>
            <?php endif; ?>

            <hr style="border:0; border-top:1px solid #eee; margin: 20px 0;">
            
            <h3 style="margin:0 0 15px 0; color:var(--text-dark); text-align:left;">
                <i class="fas fa-tasks"></i> Task Reassignment
            </h3>

            <?php if ($impact_list && $impact_list->num_rows > 0): ?>
                <?php while($imp = $impact_list->fetch_assoc()): 
                    $parts = explode('|', $imp['affected_task'], 2);
                    $type = $parts[0] ?? 'Task';
                    $desc = $parts[1] ?? $imp['affected_task'];
                ?>
                <div class="impact-item">
                    <div class="impact-header">
                        <span style="font-weight:700; color:#333;"><?php echo $desc; ?></span>
                        <span class="tag"><?php echo $type; ?></span>
                    </div>
                    
                    <?php if(!empty($imp['assigned_to'])): ?>
                        <span class="assigned-label">
                            <i class="fas fa-check"></i> Covered by: <?php echo $imp['assigned_to']; ?>
                        </span>
                    <?php else: ?>
                        <form method="POST" class="assign-form">
                            <input type="hidden" name="assign_task" value="1">
                            <input type="hidden" name="impact_id" value="<?php echo $imp['impact_id']; ?>">
                            <input type="hidden" name="view_app_id" value="<?php echo $modal_data['application_id']; ?>">
                            <input type="text" name="worker_name" class="assign-input" placeholder="Assign to who?" required>
                            <button type="submit" class="assign-btn">Assign</button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color:#aaa; font-style:italic;">No specific impacts listed by student.</p>
            <?php endif; ?>

            <button class="btn-no" onclick="window.location.href='approval.php'" style="width:100%; margin-top:20px;">Close</button>
        </div>
    </div>
    <?php endif; ?>

    <div class="modal-overlay" id="logoutModal" style="display:none;">
        <div class="modal-box">
            <div style="font-size: 4rem; margin-bottom: 10px;">👋</div>
            <h2 style="margin-top:0; color:var(--text-dark);">End Session?</h2>
            <div style="margin-top:20px;">
                <button class="btn-no" onclick="closeLogout()">Cancel</button>
                <button class="btn-yes" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>

    <script>
        function openLogout() { document.getElementById('logoutModal').style.display = 'flex'; }
        function closeLogout() { document.getElementById('logoutModal').style.display = 'none'; }
        function confirmLogout() { window.location.href = 'index.php'; }
        
        // Don't auto-close the PHP modal on click, only logout modal
        window.onclick = function(e) { if(e.target.id == 'logoutModal') closeLogout(); }
    </script>
</body>
</html>