<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard | InternLeave Portal</title>
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

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; scroll-behavior: smooth; }

        /* MARQUEE */
        .marquee-container {
            background: #e67e22; /* Different color to alert supervisor */
            color: white;
            padding: 12px 0;
            font-weight: 600;
            font-size: 0.9rem;
            position: relative;
            z-index: 1001;
        }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

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
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        
        [data-theme="dark"] .nav-links a:hover, [data-theme="dark"] .nav-links a.active { background: rgba(255,255,255,0.1); }

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
            height: 450px;
            background: linear-gradient(135deg, var(--pastel-green-dark) 0%, var(--pastel-green-main) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .bubble { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.2); animation: float 6s infinite ease-in-out; }
        .b1 { width: 120px; height: 120px; top: 15%; left: 10%; }
        .b2 { width: 180px; height: 180px; bottom: 10%; right: 10%; animation-delay: 2s; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-25px); } 100% { transform: translateY(0px); } }

        .hero-content { position: relative; z-index: 10; text-align: center; color: white; }
        .welcome-title {
            font-size: 4.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -2px;
            color: white;
            text-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .hero-subtitle { font-size: 1.4rem; color: white; font-weight: 500; margin-top: 15px; opacity: 0.9; }

        /* CONTENT SECTIONS */
        .container { max-width: 1100px; margin: 0 auto; padding: 60px 20px; }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 80px;
            margin-top: -100px; /* Overlap effect */
            position: relative;
            z-index: 20;
        }
        .card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-bottom: 5px solid var(--pastel-green-main);
            text-align: center;
        }
        .card-icon { font-size: 3rem; margin-bottom: 15px; }
        .card h3 { color: var(--text-dark); margin: 10px 0; font-size: 1.2rem; }
        .card .stat-number { font-size: 2.5rem; font-weight: 700; color: var(--pastel-green-dark); }

        /* TIMELINE */
        .timeline-section {
            background: var(--card-bg);
            padding: 60px 20px;
            border-radius: 40px;
            margin-bottom: 80px;
            text-align: center;
            box-shadow: var(--soft-shadow);
        }
        .timeline-steps { display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px; position: relative; }
        .timeline-steps::before { content: ''; position: absolute; top: 40px; left: 10%; right: 10%; height: 4px; background: #f0f0f0; z-index: 0; }
        .step { position: relative; z-index: 1; width: 220px; }
        .step-circle {
            width: 80px; height: 80px; background: var(--pastel-green-light); color: var(--pastel-green-dark);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 700; margin: 0 auto 20px; border: 4px solid var(--card-bg);
        }

        /* FAQ */
        .faq-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-top: 40px; text-align: left; }
        .faq-item {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            border-left: 5px solid var(--pastel-green-dark);
        }

        footer { background: var(--white); padding: 40px 20px; text-align: center; border-top: 1px solid #eee; }
        
        /* MODAL */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);
            display: none; justify-content: center; align-items: center; z-index: 2000;
        }
        .modal-box {
            background: var(--card-bg); padding: 40px; border-radius: 30px;
            width: 90%; max-width: 400px; text-align: center;
        }
    </style>
</head>
<body>

    <div class="marquee-container">
        <div class="marquee-text">
            <span>📢 Attention Supervisor: There are pending leave applications requiring your decision.</span>
            <span>✅ Tip: Review the "Leave Impact" section before making a decision to ensure project continuity.</span>
        </div>
    </div>

    <nav>
        <a href="dashboardsupervisor.php" class="logo-text">
            <span class="intern">Intern</span><span class="leave">Leave</span>
        </a>
        <div class="nav-links">
            <a href="dashboardsupervisor.php" class="active">Dashboard</a>
            <a href="approval.php">Approvals</a>
            <a href="intern_list.php">My Interns</a>
            <a href="profilesetting.php">Profile</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="hero-banner">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="hero-content">
            <h1 class="welcome-title">Supervisor Portal</h1>
            <p class="hero-subtitle">Manage, Review, and Guide your interns efficiently.</p>
        </div>
    </div>

    <div class="container">
        
        <div class="features-grid">
            <div class="card">
                <div class="card-icon">⏳</div>
                <h3>Pending Actions</h3>
                <div class="stat-number"></div>
                <p>Applications awaiting your approval.</p>
            </div>
            <div class="card">
                <div class="card-icon">👥</div>
                <h3>Active Interns</h3>
                <div class="stat-number"></div>
                <p>Total interns under your supervision.</p>
            </div>
            <div class="card">
                <div class="card-icon">📊</div>
                <h3>Quick Link</h3>
                <a href="approval.php" style="display:inline-block; margin-top:15px; padding:10px 20px; background:var(--pastel-green-dark); color:white; border-radius:10px; text-decoration:none; font-weight:700;">Review Leaves</a>
            </div>
        </div>

        

        <div class="timeline-section">
            <h2 style="color: var(--pastel-green-dark); margin-bottom: 40px;">Your Approval Process</h2>
            <div class="timeline-steps">
                <div class="step">
                    <div class="step-circle">1</div>
                    <h4>Review</h4>
                    <p>Check leave dates and intern's reason.</p>
                </div>
                <div class="step">
                    <div class="step-circle">2</div>
                    <h4>Impact</h4>
                    <p>Check affected tasks & handovers.</p>
                </div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <h4>Decision</h4>
                    <p>Approve or Reject the application.</p>
                </div>
                <div class="step">
                    <div class="step-circle">4</div>
                    <h4>Sync</h4>
                    <p>System auto-notifies University Staff.</p>
                </div>
            </div>
        </div>

        <div class="faq-section">
            <h2 style="text-align:center; color: var(--pastel-green-dark); font-size: 2.2rem;">Supervisor FAQ</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h4>Can I undo a decision?</h4>
                    <p>Once a decision is made, it is logged. To change it, please contact the University Coordinator.</p>
                </div>
                <div class="faq-item">
                    <h4>Who sees my comments?</h4>
                    <p>Both the student and the University Staff can see the status and any remarks you provide.</p>
                </div>
                <div class="faq-item">
                    <h4>What is 'Leave Impact'?</h4>
                    <p>It's a list of tasks/meetings the student will miss. They must fill this before applying.</p>
                </div>
                <div class="faq-item">
                    <h4>How to view intern history?</h4>
                    <p>Go to 'My Interns' and select a specific profile to see their full leave records.</p>
                </div>
            </div>
        </div>

    </div>

    <footer>
        <p>&copy; 2024 InternLeave Portal | Supervisor Workspace</p>
    </footer>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <div style="font-size: 4rem; margin-bottom: 10px;">👋</div>
            <h2 style="margin-top:0; color:var(--text-dark);">End Session?</h2>
            <p style="color:#666;">Confirm to logout from Supervisor Portal.</p>
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