<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | InternLeave Portal</title>
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
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; scroll-behavior: smooth; }

        /* --- MARQUEE --- */
        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; position: relative; z-index: 1001; }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        /* --- NAVIGATION --- */
        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.4rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }

        .nav-links { display: flex; gap: 8px; align-items: center; }
        .nav-links a { text-decoration: none; color: #888; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        [data-theme="dark"] .nav-links a:hover, [data-theme="dark"] .nav-links a.active { background: rgba(255,255,255,0.1); }

        .logout-link { background: #ffeded !important; color: #ff6b6b !important; font-weight: 700 !important; border: 1px solid #ffcccc; cursor: pointer; }
        .logout-link:hover { background: #ff6b6b !important; color: white !important; }

        /* --- HERO BANNER (ADDED BACK) --- */
        .hero-banner {
            width: 100%;
            height: 400px; /* Slightly shorter for dashboard feel */
            background: linear-gradient(135deg, var(--pastel-green-main) 0%, var(--pastel-green-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 40px;
        }

        /* Abstract Floating Shapes */
        .bubble { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.4); animation: float 6s infinite ease-in-out; }
        .b1 { width: 120px; height: 120px; top: 15%; left: 10%; }
        .b2 { width: 180px; height: 180px; bottom: 10%; right: 10%; animation-delay: 2s; }
        .b3 { width: 80px; height: 80px; bottom: 20%; left: 40%; animation-delay: 4s; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-25px); } 100% { transform: translateY(0px); } }

        .hero-content { position: relative; z-index: 10; text-align: center; }

        /* RGB Gradient Font for Hero Title */
        .welcome-title {
            font-size: 4.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -2px;
            background: linear-gradient(to right, var(--text-dark), var(--pastel-green-dark), var(--text-dark));
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textShine 4s linear infinite;
            text-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        @keyframes textShine { to { background-position: 200% center; } }

        .hero-subtitle { font-size: 1.2rem; color: var(--text-dark); font-weight: 500; margin-top: 15px; opacity: 0.8; }


        /* --- DASHBOARD CONTENT --- */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px 40px; }

        .dash-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; }
        .dash-header h1 { margin: 0; font-size: 1.8rem; color: var(--text-dark); }
        .dash-header p { margin: 5px 0 0; color: #888; font-size: 0.9rem; }
        .date-badge { background: var(--white); padding: 8px 15px; border-radius: 20px; box-shadow: var(--soft-shadow); font-weight: 700; color: var(--pastel-green-dark); font-size: 0.9rem; }

        /* KPI Cards */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .kpi-card { background: var(--card-bg); padding: 25px; border-radius: 20px; box-shadow: var(--soft-shadow); display: flex; justify-content: space-between; align-items: center; border-bottom: 4px solid transparent; transition: transform 0.3s; }
        .kpi-card:hover { transform: translateY(-5px); }
        
        .kpi-content h3 { margin: 0; font-size: 2.2rem; color: var(--text-dark); }
        .kpi-content p { margin: 5px 0 0; color: #888; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-icon { font-size: 2.5rem; opacity: 0.15; }

        .kpi-orange { border-color: #f59e0b; } .kpi-orange .kpi-icon { color: #f59e0b; }
        .kpi-blue { border-color: #3b82f6; } .kpi-blue .kpi-icon { color: #3b82f6; }
        .kpi-green { border-color: #10b981; } .kpi-green .kpi-icon { color: #10b981; }
        .kpi-purple { border-color: #8b5cf6; } .kpi-purple .kpi-icon { color: #8b5cf6; }

        /* Content Grid */
        .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }

        .dash-panel { background: var(--card-bg); border-radius: 25px; padding: 30px; box-shadow: var(--soft-shadow); }
        .panel-title { font-size: 1.1rem; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        .panel-title a { font-size: 0.8rem; color: var(--pastel-green-dark); text-decoration: none; }

        /* Recent List */
        .recent-list { list-style: none; padding: 0; margin: 0; }
        .recent-item { display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid var(--border-color); }
        .recent-item:last-child { border-bottom: none; }
        
        .user-avatar { width: 45px; height: 45px; background: #e1f2eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--pastel-green-dark); font-weight: 700; margin-right: 15px; }
        .req-info { flex-grow: 1; }
        .req-info h4 { margin: 0; font-size: 0.95rem; color: var(--text-dark); }
        .req-info p { margin: 3px 0 0; font-size: 0.8rem; color: #888; }
        .req-status { padding: 5px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
        
        .status-pending { background: #fff7ed; color: #c2410c; }
        .status-approved { background: #ecfdf5; color: #047857; }

        /* Quick Actions */
        .action-btn { display: flex; align-items: center; gap: 15px; width: 100%; padding: 15px; margin-bottom: 10px; background: var(--pastel-green-light); border: none; border-radius: 15px; cursor: pointer; color: var(--text-dark); font-weight: 600; transition: 0.2s; text-decoration: none; }
        .action-btn:hover { background: #e1f2eb; transform: translateX(5px); }
        .action-btn i { color: var(--pastel-green-dark); font-size: 1.1rem; }

        footer { background: var(--white); padding: 40px 20px; text-align: center; border-top: 1px solid var(--border-color); margin-top: 60px; }
        .footer-links a { color: #888; text-decoration: none; margin: 0 10px; font-size: 0.9rem; }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .modal-box { background: var(--card-bg); padding: 40px; border-radius: 30px; width: 90%; max-width: 400px; text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,0.2); animation: popIn 0.3s ease-out; }
        @keyframes popIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .btn-yes { background: #ff6b6b; color: white; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; margin-left:10px; }
        .btn-no { background: #eee; color: #555; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; }

        @media (max-width: 900px) { 
            .content-grid { grid-template-columns: 1fr; } 
            .hero-banner { height: 300px; }
            .welcome-title { font-size: 3rem; }
        }
    </style>
</head>
<body>

    <div class="marquee-container">
        <div class="marquee-text">
            <span>✨ Staff Portal: Welcome! Monitor student attendance and manage records efficiently.</span>
        </div>
    </div>

    <nav>
        <a href="dashboardstaff.php" class="logo-text">
            <span class="intern">Intern</span><span class="leave">Leave</span>
        </a>
        <div class="nav-links">
            <a href="dashboardstaff.php" class="active">Dashboard</a>
            <a href="studentlist.php">Student List</a>
            <a href="staffsetting.php">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="hero-banner">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="hero-content">
            <h1 class="welcome-title">Welcome Back, Staff</h1>
            <p class="hero-subtitle">Efficiently manage internship attendance and student records.</p>
        </div>
    </div>

    <div class="container">
        
        <div class="dash-header">
            <div>
                <h1>Dashboard Overview</h1>
                <p>Here is what's happening today.</p>
            </div>
            <div class="date-badge" id="currentDate"></div>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card kpi-orange">
                <div class="kpi-content">
                    <h3>5</h3>
                    <p>Pending Approval</p>
                </div>
                <i class="fas fa-clock kpi-icon"></i>
            </div>
            <div class="kpi-card kpi-blue">
                <div class="kpi-content">
                    <h3>3</h3>
                    <p>Students on Leave</p>
                </div>
                <i class="fas fa-bed kpi-icon"></i>
            </div>
            <div class="kpi-card kpi-green">
                <div class="kpi-content">
                    <h3>42</h3>
                    <p>Total Students</p>
                </div>
                <i class="fas fa-users kpi-icon"></i>
            </div>
            <div class="kpi-card kpi-purple">
                <div class="kpi-content">
                    <h3>12</h3>
                    <p>Reports Generated</p>
                </div>
                <i class="fas fa-file-alt kpi-icon"></i>
            </div>
        </div>

        <div class="content-grid">
            
            <div class="dash-panel">
                <div class="panel-title">
                    <span>Recent Leave Requests</span>
                    <a href="studentlist.php">View All</a>
                </div>
                <ul class="recent-list">
                    <li class="recent-item">
                        <div class="user-avatar">AD</div>
                        <div class="req-info">
                            <h4>Ahmad Daniel</h4>
                            <p>Medical Leave • 2 Days</p>
                        </div>
                        <span class="req-status status-pending">Pending Review</span>
                    </li>
                    <li class="recent-item">
                        <div class="user-avatar" style="background:#e0f2fe; color:#0ea5e9;">SJ</div>
                        <div class="req-info">
                            <h4>Sarah Jenkins</h4>
                            <p>Personal Leave • 1 Day</p>
                        </div>
                        <span class="req-status status-approved">Approved</span>
                    </li>
                    <li class="recent-item">
                        <div class="user-avatar" style="background:#fef3c7; color:#d97706;">MK</div>
                        <div class="req-info">
                            <h4>Michael K.</h4>
                            <p>Emergency Leave • 3 Days</p>
                        </div>
                        <span class="req-status status-pending">Pending Review</span>
                    </li>
                    <li class="recent-item">
                        <div class="user-avatar">NL</div>
                        <div class="req-info">
                            <h4>Nur Liyana</h4>
                            <p>Academic Event • 1 Day</p>
                        </div>
                        <span class="req-status status-approved">Approved</span>
                    </li>
                </ul>
            </div>

            <div class="dash-panel">
                <div class="panel-title">Quick Actions</div>
                <a href="studentlist.php" class="action-btn">
                    <i class="fas fa-list-ul"></i> View Student List
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-file-download"></i> Download Report
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-user-plus"></i> Add New Student
                </a>
                <a href="staffsetting.php" class="action-btn">
                    <i class="fas fa-cog"></i> System Settings
                </a>
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
            <div style="margin-top:20px;">
                <button class="btn-no" onclick="closeLogout()">Cancel</button>
                <button class="btn-yes" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('currentDate').innerText = new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' });

        function openLogout() { document.getElementById('logoutModal').style.display = 'flex'; }
        function closeLogout() { document.getElementById('logoutModal').style.display = 'none'; }
        function confirmLogout() { window.location.href = 'index.php'; }
        window.onclick = function(e) { if(e.target == document.getElementById('logoutModal')) closeLogout(); }
    </script>
</body>
</html>