<?php
// ==========================================
// BACKEND LOGIC: IMPACT CRUD
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

// 2. HANDLE ADD IMPACT (CREATE)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_impact'])) {
    $app_id = $_POST['application_id'];
    $task_desc = $conn->real_escape_string($_POST['affected_task']);
    $type = $_POST['impact_type']; // Meeting, Task, or Event

    // We combine Type and Desc for storage since table has 1 column for task
    // Format: "Type|Description" (e.g., "Meeting|Weekly Sync with Boss")
    $final_string = $type . "|" . $task_desc;

    $sql = "INSERT INTO leave_impacts (application_id, affected_task) VALUES ('$app_id', '$final_string')";
    if($conn->query($sql)) {
        header("Location: impact.php?app_id=" . $app_id); // Refresh to show
        exit();
    }
}

// 3. HANDLE DELETE IMPACT (DELETE)
if (isset($_GET['delete_id']) && isset($_GET['app_id'])) {
    $del_id = $_GET['delete_id'];
    $app_id = $_GET['app_id'];
    $conn->query("DELETE FROM leave_impacts WHERE impact_id = '$del_id'");
    header("Location: impact.php?app_id=" . $app_id);
    exit();
}

// 4. FETCH APPLICATIONS (For Dropdown)
$apps_sql = "SELECT * FROM intern_leave_applications WHERE student_id = '$student_id' ORDER BY start_date DESC";
$apps_res = $conn->query($apps_sql);

// 5. FETCH IMPACTS (If an app is selected)
$selected_app_id = isset($_GET['app_id']) ? $_GET['app_id'] : '';
$impacts = [];
$selected_app_details = null;

if ($selected_app_id) {
    // Get App Details
    $app_details_sql = "SELECT * FROM intern_leave_applications WHERE application_id = '$selected_app_id'";
    $selected_app_details = $conn->query($app_details_sql)->fetch_assoc();

    // Get Impacts
    $impact_res = $conn->query("SELECT * FROM leave_impacts WHERE application_id = '$selected_app_id'");
    while($row = $impact_res->fetch_assoc()) {
        $impacts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impact Analysis | InternLeave</title>
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
        /* CSS STYLES */
        :root {
            --pastel-green-light: #f1f8f6;
            --pastel-green-main: #a7d7c5;
            --pastel-green-dark: #5c8d89;
            --white: #ffffff;
            --text-dark: #2d3436;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --input-bg: #fafafa;
            --input-border: #eee;
        }

        /* DARK MODE OVERRIDES */
        [data-theme="dark"] {
            --pastel-green-light: #1a1f1e;
            --white: #252b2a;
            --text-dark: #e1f2eb;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            --input-bg: #2f3635;
            --input-border: #3a4240;
        }

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; }

        /* MARQUEE & NAV */
        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.2rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }
        
        .nav-links { display: flex; gap: 8px; align-items: center; }
        .nav-links a { text-decoration: none; color: #666; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        [data-theme="dark"] .nav-links a:hover, [data-theme="dark"] .nav-links a.active { background: rgba(255,255,255,0.1); }

        .logout-link { background: #ffeded !important; color: #ff6b6b !important; border: 1px solid #ffcccc; cursor: pointer; }

        /* LAYOUT */
        .main-wrapper { 
            display: grid; grid-template-columns: 350px 1fr; gap: 30px; 
            max-width: 1200px; margin: 40px auto; padding: 0 20px; 
        }

        /* LEFT: SELECTOR & ADD FORM */
        .control-panel { 
            background: var(--white); padding: 30px; border-radius: 25px; 
            box-shadow: var(--soft-shadow); height: fit-content; position: sticky; top: 100px; 
        }
        
        .control-panel h2 { margin-top: 0; color: var(--pastel-green-dark); font-size: 1.5rem; }
        .control-panel p { color: #888; font-size: 0.9rem; margin-bottom: 20px; }

        select, input, textarea { 
            width: 100%; padding: 12px; border: 2px solid var(--input-border); border-radius: 12px; 
            margin-bottom: 15px; font-family: inherit; outline: none; background: var(--input-bg);
            color: var(--text-dark);
        }
        select:focus, input:focus, textarea:focus { border-color: var(--pastel-green-main); background: var(--white); }

        .btn-load { width: 100%; background: var(--text-dark); color: white; padding: 12px; border: none; border-radius: 12px; cursor: pointer; font-weight: 700; margin-bottom: 30px; }
        [data-theme="dark"] .btn-load { background: var(--pastel-green-dark); color: white; }

        .btn-add { width: 100%; background: var(--pastel-green-dark); color: white; padding: 12px; border: none; border-radius: 12px; cursor: pointer; font-weight: 700; }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(92,141,137,0.3); }

        /* RIGHT: IMPACT LIST */
        .impact-container { 
            background: var(--white); padding: 40px; border-radius: 25px; 
            box-shadow: var(--soft-shadow); min-height: 500px; 
        }
        .header-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid var(--input-border); padding-bottom: 20px; }
        .header-area h1 { margin: 0; font-size: 1.8rem; color: var(--text-dark); }
        .leave-badge { background: #e1f2eb; color: var(--pastel-green-dark); padding: 5px 15px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; }
        [data-theme="dark"] .leave-badge { background: rgba(92, 141, 137, 0.2); }

        /* IMPACT CARDS */
        .impact-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        
        .impact-card { 
            padding: 20px; border-radius: 15px; background: var(--white); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid var(--input-border);
            position: relative; overflow: hidden; transition: 0.3s;
        }
        .impact-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        
        /* Color Coding */
        .border-Meeting { border-top: 5px solid #f59e0b; }
        .border-Task { border-top: 5px solid #3b82f6; }
        .border-Event { border-top: 5px solid #ec4899; }

        .icon-circle { 
            width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            margin-bottom: 15px; font-size: 1.2rem;
        }
        .icon-Meeting { background: #fef3c7; color: #d97706; }
        .icon-Task { background: #dbeafe; color: #2563eb; }
        .icon-Event { background: #fce7f3; color: #db2777; }

        .card-title { font-weight: 700; color: var(--text-dark); margin-bottom: 5px; }
        .card-desc { color: #777; font-size: 0.9rem; line-height: 1.5; }
        [data-theme="dark"] .card-desc { color: #ccc; }

        .delete-btn { 
            position: absolute; top: 15px; right: 15px; color: #ff6b6b; 
            background: #fff5f5; width: 30px; height: 30px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; text-decoration: none; 
        }
        .delete-btn:hover { background: #ff6b6b; color: white; }
        [data-theme="dark"] .delete-btn { background: #3a2525; }

        .empty-state { text-align: center; color: #aaa; margin-top: 50px; }

        /* LOGOUT MODAL */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);
            display: none; justify-content: center; align-items: center; z-index: 2000;
        }
        .modal-box {
            background: var(--white); padding: 40px; border-radius: 30px;
            width: 90%; max-width: 400px; text-align: center;
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
            animation: popIn 0.3s ease-out;
        }
        @keyframes popIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .btn-yes { background: #ff6b6b; color: white; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; margin-left:10px; }
        .btn-no { background: #eee; color: #555; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; }

        @media (max-width: 900px) { .main-wrapper { grid-template-columns: 1fr; } .control-panel { position: static; } }
    </style>
</head>
<body>

    <div class="marquee-container">
        <div class="marquee-text"><span>✨ Welcome to InternLeave – A seamless digital hub for internship leave management.</span></div>
    </div>

    <nav>
        <a href="home.php" class="logo-text"><span class="intern">Intern</span><span class="leave">Leave</span></a>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="apply.php">Apply</a>
            <a href="status.php">Status</a>
            <a href="impact.php" class="active">Impact</a>
            <a href="history.php">History</a> <a href="profilesetting.php">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="main-wrapper">
        
        <div class="control-panel">
            <h2>Select Application</h2>
            <p>Choose a leave request to plan handovers.</p>
            
            <form action="" method="GET">
                <select name="app_id" required>
                    <option value="" disabled selected>-- Select Leave Date --</option>
                    <?php 
                    if ($apps_res->num_rows > 0) {
                        while($r = $apps_res->fetch_assoc()) {
                            $sel = ($r['application_id'] == $selected_app_id) ? 'selected' : '';
                            echo "<option value='".$r['application_id']."' $sel>".$r['leave_type']." (".$r['start_date'].")</option>";
                        }
                    }
                    ?>
                </select>
                <button type="submit" class="btn-load">Load Data</button>
            </form>

            <?php if ($selected_app_id): ?>
                <hr style="border:0; border-top:1px solid #eee; margin: 30px 0;">
                
                <h2>Add Impact</h2>
                <p>What will you miss?</p>
                <form action="" method="POST">
                    <input type="hidden" name="application_id" value="<?php echo $selected_app_id; ?>">
                    <input type="hidden" name="add_impact" value="true">

                    <label style="font-weight:700; font-size:0.85rem; color:#555;">CATEGORY</label>
                    <select name="impact_type">
                        <option value="Meeting">📅 Meeting / Discussion</option>
                        <option value="Task">🔨 Project Task</option>
                        <option value="Event">🚀 Company Event</option>
                    </select>

                    <label style="font-weight:700; font-size:0.85rem; color:#555;">DETAILS</label>
                    <textarea name="affected_task" rows="3" placeholder="e.g. Weekly progress update with Mr. Tan" required></textarea>

                    <button type="submit" class="btn-add">+ Add Item</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="impact-container">
            <?php if ($selected_app_id && $selected_app_details): ?>
                <div class="header-area">
                    <div>
                        <h1>Impact Assessment</h1>
                        <p style="color:#888; margin:5px 0 0;">Planning for: <?php echo $selected_app_details['leave_type']; ?></p>
                    </div>
                    <div class="leave-badge">
                        <?php echo $selected_app_details['total_days']; ?> Day(s) Away
                    </div>
                </div>

                <div class="impact-grid">
                    <?php if (count($impacts) > 0): ?>
                        <?php foreach($impacts as $imp): 
                            // Parse "Type|Desc"
                            $parts = explode('|', $imp['affected_task'], 2);
                            $type = $parts[0] ?? 'Task';
                            $desc = $parts[1] ?? $imp['affected_task'];
                            
                            $icon_class = 'fa-tasks';
                            if($type == 'Meeting') $icon_class = 'fa-users';
                            if($type == 'Event') $icon_class = 'fa-calendar-star';
                        ?>
                            <div class="impact-card border-<?php echo $type; ?>">
                                <div class="icon-circle icon-<?php echo $type; ?>">
                                    <i class="fas <?php echo $icon_class; ?>"></i>
                                </div>
                                <div class="card-title"><?php echo $type; ?></div>
                                <div class="card-desc"><?php echo $desc; ?></div>
                                
                                <a href="impact.php?app_id=<?php echo $selected_app_id; ?>&delete_id=<?php echo $imp['impact_id']; ?>" 
                                   class="delete-btn" onclick="return confirm('Delete this item?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1 / -1; text-align:center; color:#aaa; padding:40px;">
                            <i class="fas fa-check-circle" style="font-size:3rem; color:#e1f2eb; margin-bottom:10px;"></i>
                            <p>No impacts added yet.<br>Use the form on the left to add items.</p>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-hand-point-left" style="font-size:4rem; color:#e1f2eb; margin-bottom:20px;"></i>
                    <h2>Select a Leave Request</h2>
                    <p>Please select one of your applications from the left panel to manage its impact.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <div style="font-size: 4rem; margin-bottom: 10px;">👋</div>
            <h2 style="margin-top:0; color:var(--text-dark);">Leaving so soon?</h2>
            <p style="color:#666;">You will be logged out of your session.</p>
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
        window.onclick = function(e) { if(e.target == document.getElementById('logoutModal')) closeLogout(); }
    </script>

</body>
</html>