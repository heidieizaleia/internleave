<?php
// ==========================================
// BACKEND LOGIC: STAFF DASHBOARD
// ==========================================
session_start();

// 1. SECURITY CHECK: ENSURE USER IS STAFF
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: index.php");
    exit();
}

$staff_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. FETCH STAFF DETAILS
$sql = "SELECT * FROM staffs WHERE staff_id = '$staff_id'";
$result = $conn->query($sql);
$staff = $result->fetch_assoc();
$staff_name = $staff['staff_name'] ?? 'Faculty Member';

// 3. OPTIONAL: FETCH QUICK STATS (e.g., Total Students on Leave)
// This is just a placeholder example query
$count_sql = "SELECT COUNT(*) as active_leaves FROM intern_leave_applications WHERE status = 'Approved' AND end_date >= CURDATE()";
$count_res = $conn->query($count_sql);
$active_leaves = $count_res->fetch_assoc()['active_leaves'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | InternLeave</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    
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
        /* --- CORE THEME --- */
        :root {
            --pastel-green-light: #f1f8f6;
            --pastel-green-main: #a7d7c5;
            --pastel-green-dark: #5c8d89;
            --white: #ffffff;
            --text-dark: #2d3436;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --card-bg: #ffffff;
        }

        /* DARK MODE OVERRIDES */
        [data-theme="dark"] {
            --pastel-green-light: #1a1f1e;
            --white: #252b2a;
            --text-dark: #e1f2eb;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            --card-bg: #252b2a;
        }

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; scroll-behavior: smooth; }

        /* MARQUEE */
        .marquee-container {
            background: var(--pastel-green-dark);
            color: white;
            padding: 12px 0;
            font-weight: 600;
            font-size: 0.9rem;
            position: relative;
            z-index: 1001;
        }
        .marquee-text {
            display: inline-block;
            white-space: nowrap;
            animation: marqueeMove 30s linear infinite;
        }
        @keyframes marqueeMove {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        /* NAVIGATION */
        nav {
            background: var(--white);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--soft-shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-text { font-size: 2.4rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }

        .nav-links { display: flex; gap: 8px; align-items: center; }
        .nav-links a {
            text-decoration: none;
            color: #888;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 10px 14px;
            border-radius: 12px;
        }
        .nav-links a:hover, .nav-links a.active {
            background: #e1f2eb;
            color: var(--pastel-green-dark);
        }
        [data-theme="dark"] .nav-links a:hover, 
        [data-theme="dark"] .nav-links a.active { background: rgba(255,255,255,0.1); }

        .logout-link {
            background: #ffeded !important;
            color: #ff6b6b !important;
            font-weight: 700 !important;
            border: 1px solid #ffcccc;
            cursor: pointer;
        }
        .logout-link:hover { background: #ff6b6b !important; color: white !important; }

        /* HERO BANNER */
        .hero-banner {
            width: 100%;
            height: 500px;
            background: linear-gradient(135deg, var(--pastel-green-main) 0%, var(--pastel-green-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .bubble { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.2); animation: float 6s infinite ease-in-out; }
        .b1 { width: 120px; height: 120px; top: 15%; left: 10%; }
        .b2 { width: 180px; height: 180px; bottom: 10%; right: 10%; animation-delay: 2s; }
        .b3 { width: 80px; height: 80px; bottom: 20%; left: 40%; animation-delay: 4s; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-25px); } 100% { transform: translateY(0px); } }

        .hero-content { position: relative; z-index: 10; text-align: center; }

        .welcome-title {
            font-size: 5.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -3px;
            background: linear-gradient(to right, var(--text-dark), var(--pastel-green-dark), var(--text-dark));
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textShine 4s linear infinite;
        }
        @keyframes textShine { to { background-position: 200% center; } }

        .hero-subtitle { font-size: 1.4rem; color: var(--text-dark); font-weight: 500; margin-top: 15px; opacity: 0.8; }

        /* CONTENT */
        .container { max-width: 1100px; margin: 0 auto; padding: 80px 20px; }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 80px;
        }
        .card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 25px;
            box-shadow: var(--soft-shadow);
            border-bottom: 5px solid var(--pastel-green-main);
        }
        .card:hover { transform: translateY(-10px); border-bottom-color: var(--pastel-green-dark); }
        .card-icon { font-size: 3rem; margin-bottom: 20px; }
        .card h3 { color: var(--text-dark); margin-bottom: 10px; }
        .card p { color: #888; line-height: 1.6; }

        /* TIMELINE */
        .timeline-section {
            background: var(--card-bg);
            padding: 80px 20px;
            border-radius: 40px;
            margin-bottom: 80px;
            text-align: center;
            box-shadow: var(--soft-shadow);
        }
        .timeline-title { font-size: 2.5rem; color: var(--pastel-green-dark); margin-bottom: 50px; }
        
        .timeline-steps {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
        }
        .timeline-steps::before {
            content: ''; position: absolute; top: 40px; left: 10%; right: 10%; height: 4px; background: #f0f0f0; z-index: 0;
        }
        [data-theme="dark"] .timeline-steps::before { background: #3a4240; }
        
        .step { position: relative; z-index: 1; width: 220px; }
        .step-circle {
            width: 80px; height: 80px; background: var(--pastel-green-light); color: var(--pastel-green-dark);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 700; margin: 0 auto 20px; border: 4px solid var(--card-bg);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .step h4 { margin: 0; font-size: 1.1rem; color: var(--text-dark); }
        .step p { font-size: 0.9rem; color: #888; margin-top: 5px; }

        /* STATS */
        .stats-banner {
            background: var(--text-dark);
            color: white;
            padding: 60px;
            border-radius: 30px;
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin-bottom: 80px;
        }
        [data-theme="dark"] .stats-banner { background: var(--pastel-green-dark); color: #2d3436; }
        
        .stat-item h2 { font-size: 3rem; margin: 0; color: var(--pastel-green-main); }
        [data-theme="dark"] .stat-item h2 { color: white; }
        .stat-item p { margin: 5px 0 0; opacity: 0.7; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }

        /* FOOTER */
        footer {
            background: var(--white);
            padding: 60px 20px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        [data-theme="dark"] footer { border-top-color: #333; }
        .footer-links { margin-bottom: 20px; }
        .footer-links a { color: #888; text-decoration: none; margin: 0 15px; font-weight: 600; }
        .footer-links a:hover { color: var(--pastel-green-dark); }

        /* LOGOUT MODAL */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);
            display: none; justify-content: center; align-items: center; z-index: 2000;
        }
        .modal-box {
            background: var(--card-bg); padding: 40px; border-radius: 30px;
            width: 90%; max-width: 400px; text-align: center;
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
            animation: popIn 0.3s ease-out;
        }
        @keyframes popIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .btn-yes { background: #ff6b6b; color: white; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; margin-left:10px; }
        .btn-no { background: #eee; color: #555; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; }

        @media (max-width: 768px) {
            .hero-banner { height: 400px; }
            .welcome-title { font-size: 3rem; }
            .timeline-steps::before { display: none; }
            .step { width: 100%; margin-bottom: 30px; }
            .stats-banner { flex-direction: column; gap: 30px; }
        }
    </style>
</head>
<body>

    <div class="marquee-container">
        <div class="marquee-text">
            <span>✨ Staff Portal: Monitor internship attendance, view reports, and track student leave requests in real-time.</span>
        </div>
    </div>

    <nav>
        <a href="dashboardstaff.php" class="logo-text">
            <span class="intern">Intern</span><span class="leave">Leave</span>
        </a>
        <div class="nav-links">
            <a href="dashboardstaff.php" class="active">Dashboard</a>
            <a href="staff_students.php">Students</a>
            <a href="staff_history.php">History</a>
            <a href="profilesetting.php">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="hero-banner">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="hero-content">
            <h1 class="welcome-title">Welcome, Staff</h1>
            <p class="hero-subtitle">Faculty Dashboard for Internship Supervision</p>
        </div>
    </div>

    <div class="container">
        
        <div class="features-grid">
            <div class="card">
                <div class="card-icon">👥</div>
                <h3>Student Oversight</h3>
                <p>View list of active interns under your supervision and their current status.</p>
            </div>
            <div class="card">
                <div class="card-icon">📊</div>
                <h3>Leave Records</h3>
                <p>Access complete leave history logs for attendance grading and compliance checking.</p>
            </div>
            <div class="card">
                <div class="card-icon">🔔</div>
                <h3>Notifications</h3>
                <p>Receive alerts when students apply for leave (Approval done by Industry Supervisor).</p>
            </div>
        </div>

        <div class="timeline-section">
            <h2 class="timeline-title">Monitoring Process</h2>
            <div class="timeline-steps">
                <div class="step">
                    <div class="step-circle">1</div>
                    <h4>Applied</h4>
                    <p>Student submits request.</p>
                </div>
                <div class="step">
                    <div class="step-circle">2</div>
                    <h4>Approval</h4>
                    <p><strong>Industry Supervisor</strong> reviews.</p>
                </div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <h4>Alert</h4>
                    <p><strong>Faculty Staff</strong> notified.</p>
                </div>
                <div class="step">
                    <div class="step-circle">4</div>
                    <h4>Record</h4>
                    <p>Logged for attendance.</p>
                </div>
            </div>
        </div>

        <div class="stats-banner">
            <div class="stat-item">
                <h2><?php echo $active_leaves; ?></h2>
                <p>Students on Leave Today</p>
            </div>
            <div class="stat-item">
                <h2>24/7</h2>
                <p>System Uptime</p>
            </div>
            <div class="stat-item">
                <h2>100%</h2>
                <p>Digital Records</p>
            </div>
        </div>

    </div>
    <footer>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Use</a>
            <a href="#">Support</a>
        </div>
        <p>&copy; 2024 InternLeave Portal. All Rights Reserved.</p>
    </footer>

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