<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | InternLeave Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    
    <script>
        (function() {
            // Read settings from memory
            const savedTheme = localStorage.getItem('theme') || 'light';
            const savedColor = localStorage.getItem('accentColor');

            // Apply Dark Mode Class
            document.documentElement.setAttribute('data-theme', savedTheme);

            // Apply Custom Accent Color if it exists
            if (savedColor) {
                document.documentElement.style.setProperty('--pastel-green-dark', savedColor);
                // Also tweak the gradient main color slightly to match
                document.documentElement.style.setProperty('--pastel-green-main', savedColor); 
            }
        })();
    </script>

    <style>
        :root {
            /* Default Light Mode Colors */
            --pastel-green-light: #f1f8f6;
            --pastel-green-main: #a7d7c5;
            --pastel-green-dark: #5c8d89; /* This will be overridden by the script */
            --white: #ffffff;
            --text-dark: #2d3436;
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --card-bg: #ffffff; /* Added for card background control */
        }

        /* 2. DARK MODE CSS OVERRIDES */
        [data-theme="dark"] {
            --pastel-green-light: #1a1f1e; /* Dark Background */
            --white: #252b2a;              /* Dark Nav/Header */
            --text-dark: #e1f2eb;          /* Light Text */
            --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            --card-bg: #252b2a;            /* Dark Card Background */
        }

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; scroll-behavior: smooth; }

        /* --- MARQUEE --- */
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

        /* --- NAVIGATION --- */
        nav {
            background: var(--white); /* Uses variable for Dark Mode compatibility */
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
        
        /* Dark Mode tweak for nav links hover */
        [data-theme="dark"] .nav-links a:hover, 
        [data-theme="dark"] .nav-links a.active {
            background: rgba(255,255,255,0.1); 
        }

        .logout-link {
            background: #ffeded !important;
            color: #ff6b6b !important;
            font-weight: 700 !important;
            border: 1px solid #ffcccc;
            cursor: pointer;
        }
        .logout-link:hover { background: #ff6b6b !important; color: white !important; }

        /* --- HERO BANNER --- */
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

        /* Abstract Floating Shapes */
        .bubble { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.4); animation: float 6s infinite ease-in-out; }
        .b1 { width: 120px; height: 120px; top: 15%; left: 10%; }
        .b2 { width: 180px; height: 180px; bottom: 10%; right: 10%; animation-delay: 2s; }
        .b3 { width: 80px; height: 80px; bottom: 20%; left: 40%; animation-delay: 4s; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-25px); } 100% { transform: translateY(0px); } }

        .hero-content { position: relative; z-index: 10; text-align: center; }

        /* RGB Gradient Font */
        .welcome-title {
            font-size: 5.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -3px;
            background: linear-gradient(to right, var(--text-dark), var(--pastel-green-dark), var(--text-dark)); /* Updated for theme */
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textShine 4s linear infinite;
            text-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        @keyframes textShine { to { background-position: 200% center; } }

        .hero-subtitle { font-size: 1.4rem; color: var(--text-dark); font-weight: 500; margin-top: 15px; opacity: 0.8; }

        /* --- CONTENT SECTIONS --- */
        .container { max-width: 1100px; margin: 0 auto; padding: 80px 20px; }
        
        /* 1. FEATURES GRID */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 80px;
        }
        .card {
            background: var(--card-bg); /* Uses theme variable */
            padding: 40px;
            border-radius: 25px;
            box-shadow: var(--soft-shadow);
            border-bottom: 5px solid var(--pastel-green-main);
        }
        .card:hover { transform: translateY(-10px); border-bottom-color: var(--pastel-green-dark); }
        .card-icon { font-size: 3rem; margin-bottom: 20px; }
        .card h3 { color: var(--text-dark); margin-bottom: 10px; }
        .card p { color: #888; line-height: 1.6; }

        /* 2. TIMELINE SECTION (UPDATED LOGIC) */
        .timeline-section {
            background: var(--card-bg); /* Uses theme variable */
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
        /* Connecting Line */
        .timeline-steps::before {
            content: ''; position: absolute; top: 40px; left: 10%; right: 10%; height: 4px; background: #f0f0f0; z-index: 0;
        }
        [data-theme="dark"] .timeline-steps::before { background: #3a4240; } /* Dark mode line */
        
        .step { position: relative; z-index: 1; width: 220px; }
        .step-circle {
            width: 80px; height: 80px; background: var(--pastel-green-light); color: var(--pastel-green-dark);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 700; margin: 0 auto 20px; border: 4px solid var(--card-bg);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .step h4 { margin: 0; font-size: 1.1rem; color: var(--text-dark); }
        .step p { font-size: 0.9rem; color: #999; margin-top: 5px; }

        /* 3. SPLIT SECTION */
        .split-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 80px;
        }
        .split-box {
            padding: 60px;
            border-radius: 30px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .box-student { background: linear-gradient(135deg, var(--pastel-green-main), #86efac); }
        .box-supervisor { background: linear-gradient(135deg, var(--pastel-green-dark), #3b6978); }
        .split-box h3 { font-size: 2rem; margin-top: 0; }
        .split-box p { font-size: 1.1rem; opacity: 0.9; line-height: 1.6; }
        .overlay-icon { position: absolute; bottom: -20px; right: -20px; font-size: 10rem; opacity: 0.2; }

        /* 4. STATS COUNTER */
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
        [data-theme="dark"] .stats-banner { background: var(--pastel-green-dark); color: #2d3436; } /* Invert for dark mode visibility */
        
        .stat-item h2 { font-size: 3rem; margin: 0; color: var(--pastel-green-main); }
        [data-theme="dark"] .stat-item h2 { color: white; }
        
        .stat-item p { margin: 5px 0 0; opacity: 0.7; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }

        /* 5. FAQ GRID */
        .faq-section { text-align: center; margin-bottom: 80px; }
        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 40px;
            text-align: left;
        }
        .faq-item {
            background: var(--card-bg); /* Uses theme variable */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            border-left: 5px solid var(--pastel-green-dark);
        }
        .faq-item h4 { margin: 0 0 10px; color: var(--pastel-green-dark); font-size: 1.1rem; }
        .faq-item p { margin: 0; color: #888; font-size: 0.9rem; }

        /* FOOTER */
        footer {
            background: var(--white); /* Uses theme variable */
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
            background: var(--card-bg); /* Uses theme variable */
            padding: 40px; border-radius: 30px;
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
            .split-section { grid-template-columns: 1fr; }
            .stats-banner { flex-direction: column; gap: 30px; }
        }
    </style>
</head>
<body>

    <div class="marquee-container">
        <div class="marquee-text">
            <span>✨ Welcome to InternLeave – A seamless digital hub for internship leave management.</span>
            <span>📍 Notice: Industry Supervisor approval is required for all applications.</span>
            <span>📝 Reminder: University staff are automatically notified upon submission.</span>
        </div>
    </div>

    <nav>
        <a href="dashboardstaff.php" class="logo-text">
            <span class="intern">Intern</span><span class="leave">Leave</span>
        </a>
        <div class="nav-links">
            <a href="dashboardstaff.php" class="active">Dashboard</a>
            <a href="apply.php">Apply</a>
            <a href="status.php">Status</a>
            <a href="impact.php">Impact</a>
            <a href="history.php">History</a>
            <a href="profilesetting.php">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="hero-banner">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="hero-content">
            <h1 class="welcome-title">Welcome to INTERNLEAVE</h1>
            <p class="hero-subtitle">Simplify your internship. Manage your time. Succeed.</p>
        </div>
    </div>

    <div class="container">
        
        <div class="features-grid">
            <div class="card">
                <div class="card-icon">🚀</div>
                <h3>Quick Application</h3>
                <p>Submit leave requests in seconds. The system immediately routes your application to your company supervisor.</p>
            </div>
            <div class="card">
                <div class="card-icon">📂</div>
                <h3>Auto-Documentation</h3>
                <p>Once approved, your leave is automatically logged. University faculty can view these records anytime for attendance grading.</p>
            </div>
            <div class="card">
                <div class="card-icon">🔔</div>
                <h3>Status Alerts</h3>
                <p>Track exactly when your Industry Supervisor views and approves your request.</p>
            </div>
        </div>

        <div class="timeline-section">
            <h2 class="timeline-title">The Workflow</h2>
            <div class="timeline-steps">
                <div class="step">
                    <div class="step-circle">1</div>
                    <h4>Submit</h4>
                    <p>Student applies & attaches docs.</p>
                </div>
                <div class="step">
                    <div class="step-circle">2</div>
                    <h4>Approve</h4>
                    <p><strong>Industry Supervisor</strong> reviews & approves.</p>
                </div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <h4>Notify</h4>
                    <p><strong>University Staff</strong> receives record.</p>
                </div>
                <div class="step">
                    <div class="step-circle">4</div>
                    <h4>Complete</h4>
                    <p>System updates your leave balance.</p>
                </div>
            </div>
        </div>

        <div class="split-section">
            <div class="split-box box-student">
                <h3>For Students</h3>
                <p>Focus on your work. We ensure your university faculty are kept in the loop automatically whenever you take leave.</p>
                <div class="overlay-icon">🎓</div>
            </div>
            <div class="split-box box-supervisor">
                <h3>For Supervisors</h3>
                <p>Industry supervisors have full control to Approve or Reject based on project needs. Faculty staff monitor for compliance.</p>
                <div class="overlay-icon">💼</div>
            </div>
        </div>

        <div class="stats-banner">
            <div class="stat-item">
                <h2>100%</h2>
                <p>Digital Workflow</p>
            </div>
            <div class="stat-item">
                <h2>24/7</h2>
                <p>Faculty Access</p>
            </div>
            <div class="stat-item">
                <h2>0</h2>
                <p>Paper Forms</p>
            </div>
        </div>

        <div class="faq-section">
            <h2 style="color: var(--pastel-green-dark); font-size: 2.2rem;">Common Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h4>Who approves my leave?</h4>
                    <p>Only your <strong>Industry Supervisor</strong> (at the company) has the authority to Approve or Reject your request.</p>
                </div>
                <div class="faq-item">
                    <h4>Does my lecturer see my leave?</h4>
                    <p>Yes. Once you submit, the University Staff has "View Only" access to monitor your attendance record.</p>
                </div>
                <div class="faq-item">
                    <h4>What documents do I need?</h4>
                    <p>For medical leave, a valid MC is required. For personal leave, a justification letter is recommended.</p>
                </div>
                <div class="faq-item">
                    <h4>Can I cancel a request?</h4>
                    <p>Yes, but only before the Industry Supervisor has processed it.</p>
                </div>
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