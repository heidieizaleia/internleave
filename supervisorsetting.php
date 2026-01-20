<?php
// ==========================================
// BACKEND LOGIC: FETCH & UPDATE SUPERVISOR DATA
// ==========================================
session_start();

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. HANDLE FORM SUBMISSION (UPDATE PROFILE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // A. Update Text Data
    $name = $conn->real_escape_string($_POST['full_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']); 

    // Update 'industry_supervisors' table
    $sql_update = "UPDATE industry_supervisors SET supervisor_name='$name', phone_no='$phone', email='$email' WHERE supervisor_id='$supervisor_id'";
    $conn->query($sql_update);

    // B. Handle Image Upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "uploads/supervisor_profiles/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

        $file_ext = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        $new_filename = "supervisor_" . $supervisor_id . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $conn->query("UPDATE industry_supervisors SET profile_image='$target_file' WHERE supervisor_id='$supervisor_id'");
            }
        }
    }

    // Redirect back to THIS page
    echo "<script>alert('✅ Profile Updated Successfully!'); window.location.href='supervisorsetting.php';</script>";
    exit();
}

// 3. FETCH CURRENT DETAILS
$sql = "SELECT * FROM industry_supervisors WHERE supervisor_id = '$supervisor_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Prepare Data for Display
$full_name = $user['supervisor_name'] ?? 'Supervisor Member';
$email = $user['email'] ?? '';
$phone = $user['phone_no'] ?? '';
// FIX: Added '??' check to prevent "Undefined array key" warning
$profile_img = $user['profile_image'] ?? ''; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Settings | InternLeave</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const savedColor = localStorage.getItem('accentColor');
            document.documentElement.setAttribute('data-theme', savedTheme);
            if (savedColor) {
                document.documentElement.style.setProperty('--accent-color', savedColor);
                document.documentElement.style.setProperty('--pastel-green-dark', savedColor);
                document.documentElement.style.setProperty('--pastel-green-main', savedColor); 
            }
        })();
    </script>

    <style>
        /* --- CORE THEME --- */
        :root {
            --pastel-green-light: #f1f8f6;
            --pastel-green-main: #a7d7c5;
            --pastel-green-dark: #5c8d89;
            --white: #ffffff;
            --text-dark: #2d3436;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            
            /* Settings Specific */
            --accent-color: #5c8d89; 
            --bg-color: #f1f8f6;
            --card-bg: #ffffff;
            --text-color: #2d3436;
            --border-color: #eee;
        }

        [data-theme="dark"] {
            --bg-color: #1a1f1e;
            --card-bg: #252b2a;
            --text-color: #e1f2eb;
            --border-color: #3a4240;
            --pastel-green-light: #1a1f1e;
            --white: #252b2a;
            --text-dark: #e1f2eb;
        }

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--bg-color); color: var(--text-color); overflow-x: hidden; }

        /* MARQUEE */
        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        /* NAV */
        nav { background: var(--card-bg); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        
        .logo-text { font-size: 2.2rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-color); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }
        
        .nav-links { display: flex; gap: 8px; align-items: center; }
        .nav-links a { text-decoration: none; color: #888; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        [data-theme="dark"] .nav-links a:hover, [data-theme="dark"] .nav-links a.active { background: rgba(255,255,255,0.1); }
        .logout-link { background: #ffeded !important; color: #ff6b6b !important; border: 1px solid #ffcccc; cursor: pointer; }

        /* LAYOUT */
        .main-wrapper { display: grid; grid-template-columns: 350px 1fr; gap: 30px; max-width: 1200px; margin: 40px auto; padding: 0 20px; }

        /* PROFILE CARD */
        .profile-card { background: var(--card-bg); padding: 40px 30px; border-radius: 25px; text-align: center; box-shadow: var(--soft-shadow); height: fit-content; }
        .avatar-circle { width: 120px; height: 120px; margin: 0 auto 20px; border-radius: 50%; background: var(--accent-color); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; overflow: hidden; position: relative; }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }

        .profile-info h2 { margin: 10px 0 5px; color: var(--accent-color); }
        .profile-info p { color: #888; font-size: 0.9rem; margin-bottom: 20px; }
        .info-row { text-align: left; margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; }
        .info-row label { font-size: 0.8rem; color: #aaa; font-weight: 700; text-transform: uppercase; display: block; }
        .info-row span { font-size: 1rem; color: var(--text-color); font-weight: 600; }

        /* SETTINGS SECTION */
        .settings-section { background: var(--card-bg); padding: 40px; border-radius: 25px; box-shadow: var(--soft-shadow); margin-bottom: 30px; border: 1px solid var(--border-color); }
        .settings-section h2 { margin-top: 0; color: var(--text-color); display: flex; align-items: center; gap: 10px; }
        
        /* FORMS */
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 700; color: var(--text-color); font-size: 0.9rem; }
        input, select { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 10px; background: var(--bg-color); color: var(--text-color); outline: none; font-size: 0.95rem; }
        input:focus { border-color: var(--accent-color); }

        /* BUTTONS */
        .btn { padding: 12px 24px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-primary { background: var(--accent-color); color: white; width: 100%; }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-2px); }
        
        /* UPLOAD AREA */
        .upload-wrapper { display: flex; gap: 15px; align-items: center; margin-bottom: 20px; }
        .upload-btn { padding: 10px 20px; background: var(--bg-color); border: 2px dashed var(--accent-color); border-radius: 10px; cursor: pointer; color: var(--accent-color); font-weight: 700; }
        .upload-btn:hover { background: var(--accent-color); color: white; }

        /* TOGGLES & COLOR PICKER */
        .toggle-row { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--bg-color); border-radius: 12px; margin-bottom: 15px; }
        .switch { position: relative; display: inline-block; width: 50px; height: 26px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--accent-color); }
        input:checked + .slider:before { transform: translateX(24px); }

        .color-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(60px, 1fr)); gap: 15px; margin-top: 15px; }
        .color-option { aspect-ratio: 1; border-radius: 15px; cursor: pointer; border: 3px solid transparent; transition: 0.3s; }
        .color-option:hover { transform: scale(1.1); }
        .color-option.selected { border-color: var(--text-color); transform: scale(1.05); }

        /* LOGOUT MODAL */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .modal-box { background: var(--card-bg); padding: 40px; border-radius: 30px; width: 90%; max-width: 400px; text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,0.2); animation: popIn 0.3s ease-out; }
        @keyframes popIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        @media (max-width: 900px) { .main-wrapper { grid-template-columns: 1fr; } }
    </style>
</head>
<body data-theme="light">

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

    <div class="main-wrapper">
        
        <div class="profile-card">
            <div class="avatar-circle" id="sidebarAvatar">
                <?php if (!empty($profile_img) && file_exists($profile_img)): ?>
                    <img src="<?php echo $profile_img; ?>" alt="Profile">
                <?php else: ?>
                    <i class="fas fa-user-tie"></i>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h2><?php echo $full_name; ?></h2>
                <p>Industry Supervisor</p>
            </div>
            <div class="info-row"><label>Supervisor ID</label><span><?php echo $supervisor_id; ?></span></div>
            <div class="info-row"><label>Role</label><span>Industry Partner</span></div>
            <div class="info-row"><label>Email</label><span style="font-size:0.85rem;"><?php echo $email; ?></span></div>
        </div>

        <div class="content-area">
            
            <div class="settings-section">
                <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                
                <form id="personalInfoForm" method="POST" enctype="multipart/form-data">
                    <div class="upload-wrapper">
                        <label class="upload-btn">
                            Change Photo <input type="file" name="profile_pic" id="profilePicInput" accept="image/*" style="display:none;">
                        </label>
                        <span style="color:#888; font-size:0.8rem;">Max 2MB (JPG/PNG)</span>
                        <div id="previewContainer" style="width:40px; height:40px; border-radius:50%; overflow:hidden; display:none;">
                            <img id="previewImg" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?php echo $full_name; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo $email; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo $phone; ?>">
                    </div>

                    <div class="form-group">
                        <label>Supervisor ID (Locked)</label>
                        <input type="text" value="<?php echo $supervisor_id; ?>" readonly style="cursor:not-allowed; opacity:0.7;">
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>

            <div class="settings-section">
                <h2><i class="fas fa-paint-brush"></i> Appearance</h2>
                
                <div class="toggle-row">
                    <span style="font-weight:600;"><i class="fas fa-moon"></i> Dark Mode</span>
                    <label class="switch"><input type="checkbox" id="darkModeToggle"><span class="slider"></span></label>
                </div>

                <div class="form-group">
                    <label>Accent Color</label>
                    <div class="color-grid">
                        <div class="color-option selected" data-color="#5c8d89" style="background: #5c8d89;"></div>
                        <div class="color-option" data-color="#3b82f6" style="background: #3b82f6;"></div>
                        <div class="color-option" data-color="#ec4899" style="background: #ec4899;"></div>
                        <div class="color-option" data-color="#f59e0b" style="background: #f59e0b;"></div>
                        <div class="color-option" data-color="#8b5cf6" style="background: #8b5cf6;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal-overlay" id="logoutModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:2000; justify-content:center; align-items:center;">
        <div class="modal-box" style="background:var(--card-bg); padding:40px; border-radius:30px; text-align:center;">
            <div style="font-size: 4rem; margin-bottom: 10px;">👋</div>
            <h2 style="margin-top:0; color:var(--text-color);">Leaving so soon?</h2>
            <div style="margin-top:20px;">
                <button onclick="closeLogout()" style="padding:12px 30px; border:none; background:#eee; border-radius:10px; cursor:pointer;">Cancel</button>
                <button onclick="window.location.href='index.php'" style="padding:12px 30px; border:none; background:#ff6b6b; color:white; border-radius:10px; cursor:pointer; margin-left:10px;">Logout</button>
            </div>
        </div>
    </div>

    <script>
        // --- LOGOUT LOGIC ---
        function openLogout() { document.getElementById('logoutModal').style.display = 'flex'; }
        function closeLogout() { document.getElementById('logoutModal').style.display = 'none'; }

        // --- THEME LOGIC ---
        window.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.setAttribute('data-theme', savedTheme);
            document.getElementById('darkModeToggle').checked = savedTheme === 'dark';

            const savedColor = localStorage.getItem('accentColor') || '#5c8d89';
            document.documentElement.style.setProperty('--accent-color', savedColor);
            document.documentElement.style.setProperty('--pastel-green-dark', savedColor);
            document.documentElement.style.setProperty('--pastel-green-main', savedColor);
            updateSelectedColor(savedColor);
        });

        document.getElementById('darkModeToggle').addEventListener('change', function() {
            const theme = this.checked ? 'dark' : 'light';
            document.body.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
        });

        document.querySelectorAll('.color-option').forEach(opt => {
            opt.addEventListener('click', function() {
                const color = this.getAttribute('data-color');
                document.documentElement.style.setProperty('--accent-color', color);
                document.documentElement.style.setProperty('--pastel-green-dark', color);
                document.documentElement.style.setProperty('--pastel-green-main', color);
                localStorage.setItem('accentColor', color);
                updateSelectedColor(color);
            });
        });

        function updateSelectedColor(color) {
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.classList.remove('selected');
                if (opt.getAttribute('data-color') === color) opt.classList.add('selected');
            });
        }

        // --- LIVE PREVIEW FOR IMAGE UPLOAD ---
        document.getElementById('profilePicInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    document.getElementById('previewContainer').style.display = 'block';
                    document.getElementById('previewImg').src = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>