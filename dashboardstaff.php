<?php
// ==========================================
// BACKEND LOGIC: STAFF DASHBOARD
// ==========================================
session_start();

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: index.php");
    exit();
}

$staff_id = $_SESSION['user_id'];
// You can add database connection here if you want to fetch real stats later
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | InternLeave</title>
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
        /* --- SHARED THEME STYLES (Matches Student Home) --- */
        :root {
            --pastel-green-light: #f1f8f6;
            --pastel-green-main: #a7d7c5;
            --pastel-green-dark: #5c8d89;
            --white: #ffffff;
            --text-dark: #2d3436;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --card-bg: #ffffff;
        }

        [data-theme="dark"] {
            --pastel-green-light: #1a1f1e;
            --white: #252b2a;
            --text-dark: #e1f2eb;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            --card-bg: #252b2a;
        }

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; scroll-behavior: smooth; }

        /* MARQUEE */
        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; position: relative; z-index: 1001; }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        /* NAV */
        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.2rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }
        
        .nav-links { display: flex; gap: 8px; align-items: center; }
        .nav-links a { text-decoration: none; color: #888; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        [data-theme="dark"] .nav-links a:hover, [data-theme="dark"] .nav-links a.active { background: rgba(255,255,255,0.1); }
        .logout-link { background: #ffeded !important; color: #ff6b6b !important; border: 1px solid #ffcccc; cursor: pointer; }

        /* HERO BANNER */
        .hero-banner {
            width: 100%; height: 450px;
            background: linear-gradient(135deg, var(--pastel-green-main) 0%, var(--pastel-green-light) 100%);
            display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        .bubble { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.2); animation: float 6s infinite ease-in-out; }
        .b1 { width: 120px; height: 120px; top: 15%; left: 10%; }
        .b2 { width: 180px; height: 180px; bottom: 10%; right: 10%; animation-delay: 2s; }
        .b3 { width: 80px; height: 80px; bottom: 20%; left: 40%; animation-delay: 4s; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-25px); } 100% { transform: translateY(0px); } }

        .hero-content { position: relative; z-index: 10; text-align: center; }
        .welcome-title {
            font-size: 4.5rem; font-weight: 700; margin: 0; letter-spacing: -2px;
            background: linear-gradient(to right, var(--text-dark), var(--pastel-green-dark), var(--text-dark));
            background-size: 200% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: textShine 4s linear infinite;
        }
        @keyframes textShine { to { background-position: 200% center; } }
        .hero-subtitle { font-size: 1.2rem; color: var(--text-dark); font-weight: 500; margin-top: 15px; opacity: 0.8; }

        /* CONTENT */
        .container { max-width: 1100px; margin: 0 auto; padding: 60px 20px; }

        .dashboard-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-bottom: 60px;
        }
        .card {
            background: var(--card-bg); padding: 30px; border-radius: 20px;
            box-shadow: var(--soft-shadow); border-bottom: 5px solid var(--pastel-green-main);
            text-align: center; transition: 0.3s;
        }
        .card:hover { transform: translateY(-5px); border-bottom-color: var(--pastel-green-dark); }
        .card-icon { font-size: 2.5rem; margin-bottom: 15px; color: var(--pastel-green-dark); }
        .card h3 { margin: 0; font-size: 2.5rem; color: var(--text-dark); }
        .card p { color: #888; margin: 5px 0 0; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; }

        /* TIMELINE SECTION */
        .timeline-section {
            background: var(--card-bg); padding: 60px 30px; border-radius: 30px;
            text-align: center; box-shadow: var(--soft-shadow); margin-bottom: 60px;
        }
        .timeline-title { font-size: 2rem; color: var(--pastel-green-dark); margin-bottom: 40px; }
        
        .timeline-steps {
            display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px; position: relative;
        }
        .timeline-steps::before {
            content: ''; position: absolute; top: 35px; left: 10%; right: 10%; height: 3px; background: #eee; z-index: 0;
        }
        [data-theme="dark"] .timeline-steps::before { background: #3a4240; }

        .step { position: relative; z-index: 1; width: 200px; }
        .step-circle {
            width: 70px; height: 70px; background: var(--pastel-green-light); color: var(--pastel-green-dark);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; font-weight: 700; margin: 0 auto 15px; border: 4px solid var(--card-bg);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .step h4 { margin: 0; font-size: 1rem; color: var(--text-dark); }
        .step p { font-size: 0.85rem; color: #888; margin-top: 5px; }

        /* FOOTER */
        footer { background: var(--white); padding: 40px 20px; text-align: center; border-top: 1px solid #eee; }
        [data-theme="dark"] footer { border-top-color: #333; }
        
        /* LOGOUT MODAL */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);
            display: none; justify-content: center; align-items: center; z-index: 2000;
        }
        .modal-box {
            background: var(--card-bg); padding: 40px; border-radius: 30px;
            width: 90%; max-width: 400px; text-align: center;
            box-shadow: 0 30px 60px rgba(0,0,0,0.2); animation: popIn 0.3s ease-out;
        }
        @keyframes popIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .btn-yes { background: #ff6b6b; color: white; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; margin-left:10px; }
        .btn-no { background: #eee; color: #555; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; }

        @media (max-width: 768px) {
            .hero-banner { height: 350px; }
            .welcome-title { font-size: 2.5rem; }
            .timeline-steps::before { display: none; }
            .step { width: 100%; margin-bottom: 30px; }
        }
    </style>
</head>
<body>

    <div class="marquee-container">
        <div class="marquee-text">
            <span>✨ Staff Portal – Monitor student attendance and leave records efficiently.</span>
            <span>📍 Reminder: Final grading for Semester 6 starts next week.</span>
        </div>
    </div>

    <nav>
        <a href="dashboardstaff.php" class="logo-text">
            <span class="intern">Intern</span><span class="leave">Leave</span>
        </a>
        <div class="nav-links">
            <a href="dashboardstaff.php" class="active">Home</a>
            <a href="#">Students</a>
            <a href="#">Leave Records</a>
            <a href="#">Reports</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="hero-banner">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="hero-content">
            <h1 class="welcome-title">Staff Dashboard</h1>
            <p class="hero-subtitle">Manage internships with ease. Monitor progress. Verify records.</p>
        </div>
    </div>

    <div class="container">
        
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-icon"><i class="fas fa-user-graduate"></i></div>
                <h3>120</h3>
                <p>Active Students</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-clock"></i></div>
                <h3>5</h3>
                <p>Leaves Pending Review</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-check-circle"></i></div>
                <h3>45</h3>
                <p>Approved This Month</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>
                <h3>2</h3>
                <p>Issues Reported</p>
            </div>
        </div>

        <div class="timeline-section">
            <h2 class="timeline-title">Your Workflow</h2>
            <div class="timeline-steps">
                <div class="step">
                    <div class="step-circle">1</div>
                    <h4>Monitor</h4>
                    <p>Track student attendance daily.</p>
                </div>
                <div class="step">
                    <div class="step-circle">2</div>
                    <h4>Verify</h4>
                    <p>Check Supervisor approvals.</p>
                </div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <h4>Report</h4>
                    <p>Generate monthly summaries.</p>
                </div>
                <div class="step">
                    <div class="step-circle">4</div>
                    <h4>Grade</h4>
                    <p>Finalize internship scores.</p>
                </div>
            </div>
        </div>

    </div>

    <footer>
        <p>&copy; 2024 InternLeave Portal. Staff Module.</p>
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