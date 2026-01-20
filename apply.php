<?php
// ==========================================
// BACKEND LOGIC: STUDENT APPLY
// ==========================================
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$student_id = $_SESSION['user_id']; 
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// FETCH STUDENT DETAILS
$sql_student = "SELECT * FROM students WHERE student_id = '$student_id'";
$res_student = $conn->query($sql_student);
$student_data = $res_student->fetch_assoc();
$student_name = $student_data['full_name'] ?? 'Student';
$program_code = $student_data['programme_code'] ?? 'N/A';
$profile_img = $student_data['profile_image'] ?? ''; 

// HANDLE FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $conn->real_escape_string($_POST['reason']);
    
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $total_days = $interval->days + 1;

    // --- CRITICAL FIX: Get correct placement_id ---
    // This looks up the existing placement created during registration (index.php)
    $place_sql = "SELECT placement_id FROM internship_placements WHERE student_id = '$student_id' LIMIT 1";
    $place_res = $conn->query($place_sql);

    if ($place_res->num_rows > 0) {
        $row = $place_res->fetch_assoc();
        $placement_id = $row['placement_id'];
    } else {
        // Fallback: If no placement exists, create a default one (Should rarely happen if registration works)
        $conn->query("INSERT IGNORE INTO industry_supervisors (supervisor_id, supervisor_name, company_name) VALUES (1, 'Default Supervisor', 'Pending')");
        $conn->query("INSERT IGNORE INTO staffs (staff_id, staff_name) VALUES (1, 'Default Staff')");
        $insert_place = "INSERT INTO internship_placements (student_id, company_name, supervisor_id, staff_id, start_date, end_date) 
                         VALUES ('$student_id', 'Pending Assignment', 1, 1, CURDATE(), CURDATE())";
        if ($conn->query($insert_place)) {
            $placement_id = $conn->insert_id;
        } else {
            die("<div class='alert error'>System Error: Could not create placement record. " . $conn->error . "</div>");
        }
    }

    // HANDLE FILE UPLOAD
    $target_file = NULL;
    if (isset($_POST['has_file']) && $_POST['has_file'] == 'yes' && !empty($_FILES["supporting_doc"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $file_name = time() . "_" . basename($_FILES["supporting_doc"]["name"]);
        $target_file = $target_dir . $file_name;
        move_uploaded_file($_FILES["supporting_doc"]["tmp_name"], $target_file);
    }

    // INSERT LEAVE APPLICATION
    $sql = "INSERT INTO intern_leave_applications 
            (student_id, placement_id, leave_type, start_date, end_date, total_days, reason, supporting_doc_path, status)
            VALUES ('$student_id', '$placement_id', '$leave_type', '$start_date', '$end_date', '$total_days', '$reason', '$target_file', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('✅ Application Submitted Successfully!'); window.location.href = 'status.php';</script>";
        exit();
    } else {
        $message = "<div class='alert error'>Database Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leave | InternLeave</title>
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
        :root { --pastel-green-light: #f1f8f6; --pastel-green-main: #a7d7c5; --pastel-green-dark: #5c8d89; --white: #ffffff; --text-dark: #2d3436; --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); --input-bg: #fafafa; --input-border: #eee; }
        [data-theme="dark"] { --pastel-green-light: #1a1f1e; --white: #252b2a; --text-dark: #e1f2eb; --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); --input-bg: #2f3635; --input-border: #3a4240; }
        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; scroll-behavior: smooth; }
        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.2rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }
        .nav-links a { text-decoration: none; color: #888; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        [data-theme="dark"] .nav-links a:hover, [data-theme="dark"] .nav-links a.active { background: rgba(255,255,255,0.1); }
        .logout-link { background: #ffeded !important; color: #ff6b6b !important; border: 1px solid #ffcccc; cursor: pointer; }
        .main-wrapper { display: grid; grid-template-columns: 350px 1fr; gap: 30px; max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .profile-card { background: var(--white); padding: 40px 30px; border-radius: 25px; text-align: center; box-shadow: var(--soft-shadow); position: sticky; top: 100px; height: fit-content; }
        .avatar-circle { width: 120px; height: 120px; margin: 0 auto 20px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #aaa; position: relative; z-index: 1; }
        .avatar-circle::before { content: ''; position: absolute; top: -5px; left: -5px; right: -5px; bottom: -5px; border-radius: 50%; z-index: -1; background: linear-gradient(45deg, #ff0000, #ff7300, #fffb00, #48ff00, #00ffd5, #002bff, #7a00ff, #ff00c8); background-size: 400%; animation: spinRGB 20s linear infinite; filter: blur(8px); opacity: 0.5; }
        @keyframes spinRGB { 0% { background-position: 0 0; } 50% { background-position: 100% 0; } 100% { background-position: 0 0; } }
        .profile-info h2 { margin: 10px 0 5px; color: var(--pastel-green-dark); }
        .profile-info p { color: #888; font-size: 0.9rem; margin-bottom: 20px; }
        .info-row { text-align: left; margin-bottom: 15px; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; }
        .info-row label { font-size: 0.8rem; color: #aaa; font-weight: 700; text-transform: uppercase; display: block; }
        .info-row span { font-size: 1rem; color: #555; font-weight: 600; }
        [data-theme="dark"] .info-row span { color: var(--text-dark); }
        .form-container { background: var(--white); padding: 50px; border-radius: 25px; box-shadow: var(--soft-shadow); position: relative; overflow: hidden; }
        .form-container::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 6px; background: linear-gradient(90deg, var(--pastel-green-main), var(--pastel-green-dark)); }
        .form-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .form-header h1 { margin: 0; color: var(--text-dark); font-size: 2rem; }
        .live-date { font-size: 0.9rem; color: #aaa; font-weight: 600; }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; font-weight: 700; color: #444; font-size: 0.9rem; }
        [data-theme="dark"] label { color: #bbb; }
        input, select, textarea { width: 100%; padding: 14px; border: 2px solid var(--input-border); border-radius: 12px; background: var(--input-bg); color: var(--text-dark); font-size: 1rem; outline: none; }
        input:focus, select:focus, textarea:focus { border-color: var(--pastel-green-dark); background: var(--white); box-shadow: 0 5px 15px rgba(92, 141, 137, 0.15); }
        .row { display: flex; gap: 20px; } .col { flex: 1; }
        input[readonly] { background-color: #eee !important; cursor: not-allowed; color: #333 !important; font-weight: 700; border-color: #ccc; }
        [data-theme="dark"] input[readonly] { background-color: #444 !important; color: #fff !important; border-color: #555; }
        .toggle-container { background: var(--pastel-green-light); padding: 15px; border-radius: 12px; border: 1px solid var(--input-border); display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
        .toggle-switch { position: relative; display: inline-block; width: 50px; height: 26px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--pastel-green-dark); }
        input:checked + .slider:before { transform: translateX(24px); }
        .upload-section { display: none; animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .upload-area { border: 2px dashed #ccc; border-radius: 15px; padding: 20px; text-align: center; background: var(--input-bg); cursor: pointer; transition: 0.3s; }
        .upload-area:hover { border-color: var(--pastel-green-dark); background: #e1f2eb; }
        [data-theme="dark"] .upload-area:hover { background: rgba(92, 141, 137, 0.2); }
        .submit-btn { width: 100%; padding: 18px; border: none; border-radius: 15px; color: white; font-size: 1.1rem; font-weight: 700; cursor: pointer; margin-top: 20px; position: relative; overflow: hidden; background: var(--text-dark); z-index: 1; }
        [data-theme="dark"] .submit-btn { background: var(--pastel-green-dark); color: white; }
        .submit-btn::after { content: ''; position: absolute; top: -2px; left: -2px; right: -2px; bottom: -2px; z-index: -1; background: linear-gradient(45deg, #ff0000, #ff7300, #fffb00, #48ff00, #00ffd5, #002bff, #7a00ff, #ff00c8); background-size: 400%; animation: glowing 20s linear infinite; filter: blur(10px); opacity: 0; transition: 0.3s; }
        .submit-btn:hover::after { opacity: 0.7; }
        @keyframes glowing { 0% { background-position: 0 0; } 50% { background-position: 400% 0; } 100% { background-position: 0 0; } }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; text-align: center; }
        .success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .modal-box { background: var(--white); padding: 40px; border-radius: 30px; width: 90%; max-width: 400px; text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,0.2); animation: popIn 0.3s ease-out; }
        @keyframes popIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .modal-btn-row { display: flex; gap: 15px; margin-top: 25px; }
        .btn-confirm { flex: 1; background: #2ecc71; color: white; padding: 12px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; }
        .btn-cancel { flex: 1; background: #eee; color: #555; padding: 12px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; }
        .btn-logout { flex: 1; background: #ff6b6b; color: white; padding: 12px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; }
        footer { background: var(--white); padding: 60px 20px; text-align: center; border-top: 1px solid #eee; margin-top: 40px; }
        [data-theme="dark"] footer { border-top-color: #333; }
        .footer-links { margin-bottom: 20px; }
        .footer-links a { color: #888; text-decoration: none; margin: 0 15px; font-weight: 600; }
        @media (max-width: 900px) { .main-wrapper { grid-template-columns: 1fr; } }
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
            <a href="apply.php" class="active">Apply</a>
            <a href="status.php">Status</a>
            <a href="impact.php">Impact</a>
            <a href="history.php">History</a> <a href="profilesetting.php">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>
    <div class="main-wrapper">
        <div class="profile-card">
            <div class="avatar-circle">
                <?php if (!empty($profile_img) && file_exists($profile_img)): ?>
                    <img src="<?php echo $profile_img; ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($student_name); ?></h2>
                <p><?php echo htmlspecialchars($program_code); ?></p>
            </div>
            <div class="info-row"><label>Student ID</label><span><?php echo $student_id; ?></span></div>
            <div class="info-row"><label>Status</label><span style="color:#2ecc71;">● Active</span></div>
            <div class="info-row"><label>Current Sem</label><span>2024/2025</span></div>
        </div>
        <div class="form-container">
            <div class="form-header">
                <div><h1>Leave Application</h1><p>Fill in the details below.</p></div>
                <div class="live-date" id="currentDate"></div>
            </div>
            <?php if(isset($message)) echo $message; ?>
            <form id="leaveForm" action="" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Student ID (Locked)</label>
                    <input type="text" value="<?php echo $student_id; ?>" readonly>
                </div>
                
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                <div class="form-group">
                    <label>Leave Category</label>
                    <select name="leave_type" required>
                        <option value="" disabled selected>Choose type...</option>
                        <option value="Medical">Medical (MC Required)</option>
                        <option value="Personal">Personal Reason</option>
                        <option value="Emergency">Emergency</option>
                        <option value="Academic">University Event</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col form-group"><label>Start Date</label><input type="date" name="start_date" id="startDate" required onchange="calculateDays()"></div>
                    <div class="col form-group"><label>End Date</label><input type="date" name="end_date" id="endDate" required onchange="calculateDays()"></div>
                </div>
                <div class="form-group"><label>Total Duration</label><input type="text" id="totalDays" name="total_days" readonly style="background: var(--input-bg); color: var(--pastel-green-dark); font-weight: 700;" placeholder="0 Days"></div>
                <div class="form-group"><label>Reason</label><textarea name="reason" rows="4" placeholder="Explain your reason..." required></textarea></div>
                <div class="toggle-container">
                    <label class="switch toggle-switch"><input type="checkbox" name="has_file" value="yes" id="fileToggle" onclick="toggleUpload()"><span class="slider round"></span></label>
                    <span style="font-weight: 600; color: #555;">Attach Document (Optional)</span>
                </div>
                <div class="upload-section" id="uploadBox">
                    <div class="form-group"><div class="upload-area" id="dropZone" onclick="document.getElementById('fileInput').click()"><i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #ccc;"></i><p>Click or Drag & Drop File</p><div id="fileName" style="color: var(--pastel-green-dark); font-weight: 700;"></div><input type="file" name="supporting_doc" id="fileInput" accept=".pdf,.jpg,.png"></div></div>
                </div>
                <button type="button" class="submit-btn" onclick="openSubmitModal()">SUBMIT APPLICATION</button>
            </form>
        </div>
    </div>
    <div class="modal-overlay" id="submitModal">
        <div class="modal-box">
            <div style="font-size: 4rem; margin-bottom: 10px;">📤</div><h2 style="margin-top:0; color:var(--text-dark);">Ready to Submit?</h2><div class="modal-btn-row"><button class="btn-cancel" onclick="closeSubmitModal()">Cancel</button><button class="btn-confirm" onclick="confirmSubmit()">Confirm</button></div>
        </div>
    </div>
    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <div style="font-size: 4rem; margin-bottom: 10px;">👋</div><h2 style="margin-top:0; color:var(--text-dark);">Logout?</h2><div class="modal-btn-row"><button class="btn-cancel" onclick="closeLogout()">Cancel</button><button class="btn-logout" onclick="confirmLogout()">Logout</button></div>
        </div>
    </div>
    <script>
        document.getElementById('currentDate').innerText = new Date().toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
        function openSubmitModal() { document.getElementById('submitModal').style.display = 'flex'; }
        function closeSubmitModal() { document.getElementById('submitModal').style.display = 'none'; }
        function confirmSubmit() { document.getElementById('leaveForm').submit(); }
        function openLogout() { document.getElementById('logoutModal').style.display = 'flex'; }
        function closeLogout() { document.getElementById('logoutModal').style.display = 'none'; }
        function confirmLogout() { window.location.href = 'index.php'; }
        function toggleUpload() { const box = document.getElementById('uploadBox'); if (document.getElementById('fileToggle').checked) { box.style.display = 'block'; } else { box.style.display = 'none'; document.getElementById('fileInput').value = ''; } }
        function calculateDays() {
            const start = new Date(document.getElementById('startDate').value);
            const end = new Date(document.getElementById('endDate').value);
            if (start && end) {
                if (end < start) { alert("Invalid Dates"); document.getElementById('endDate').value = ""; return; }
                const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('totalDays').value = diff + " Day(s)";
            }
        }
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.style.background = '#e1f2eb'; });
        dropZone.addEventListener('dragleave', () => { dropZone.style.background = '#fafafa'; });
        dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.style.background = '#fafafa'; if (e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; document.getElementById('fileName').innerText = "Selected: " + fileInput.files[0].name; } });
        fileInput.addEventListener('change', function() { if (this.files[0]) document.getElementById('fileName').innerText = "Selected: " + this.files[0].name; });
    </script>
</body>
</html>