<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard | InternLeave Portal</title>
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

        .logout-link { background: #ffeded !important; color: #ff6b6b !important; font-weight: 700 !important; border: 1px solid #ffcccc; cursor: pointer; }
        .logout-link:hover { background: #ff6b6b !important; color: white !important; }

        /* --- HERO BANNER --- */
        .hero-banner {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, var(--pastel-green-main) 0%, var(--pastel-green-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 40px;
        }
        .bubble { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.4); animation: float 6s infinite ease-in-out; }
        .b1 { width: 120px; height: 120px; top: 15%; left: 10%; }
        .b2 { width: 180px; height: 180px; bottom: 10%; right: 10%; animation-delay: 2s; }
        .b3 { width: 80px; height: 80px; bottom: 20%; left: 40%; animation-delay: 4s; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-25px); } 100% { transform: translateY(0px); } }

        .hero-content { position: relative; z-index: 10; text-align: center; }
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

        /* Recent List */
        .recent-list { list-style: none; padding: 0; margin: 0; }
        .recent-item { display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid var(--border-color); }
        .user-avatar { width: 45px; height: 45px; background: #e1f2eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--pastel-green-dark); font-weight: 700; margin-right: 15px; }
        .req-info { flex-grow: 1; }
        .req-info h4 { margin: 0; font-size: 0.95rem; color: var(--text-dark); }
        .req-info p { margin: 3px 0 0; font-size: 0.8rem; color: #888; }
        .req-status { padding: 5px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; }
        
        .status-pending { background: #fff7ed; color: #c2410c; }
        .status-approved { background: #ecfdf5; color: #047857; }

        /* Quick Actions */
        .action-btn { display: flex; align-items: center; gap: 15px; width: 100%; padding: 15px; margin-bottom: 10px; background: var(--pastel-green-light); border: none; border-radius: 15px; cursor: pointer; color: var(--text-dark); font-weight: 600; transition: 0.2s; text-decoration: none; }
        .action-btn:hover { background: #e1f2eb; transform: translateX(5px); }
        .action-btn i { color: var(--pastel-green-dark); font-size: 1.1rem; }

        footer { background: var(--white); padding: 40px 20px; text-align: center; border-top: 1px solid var(--border-color); margin-top: 60px; }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .modal-box { background: var(--card-bg); padding: 40px; border-radius: 30px; width: 90%; max-width: 400px; text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,0.2); }
        .btn-yes { background: #ff6b6b; color: white; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; margin-left:10px; }
        .btn-no { background: #eee; color: #555; padding: 12px 30px; border:none; border-radius:10px; font-weight:700; cursor:pointer; }
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
            <a href="supervisorsetting.php">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="section-header">
            <i class="fas fa-hourglass-half"></i> Pending Queue
        </div>

        <div id="pending-list">
            <div class="approval-card">
                <div class="student-avatar">AD</div>
                <div class="details-box">
                    <h3>Ahmad Daniel</h3>
                    <div class="meta">
                        <span><i class="far fa-calendar"></i> 2 Days</span>
                        <span><i class="fas fa-notes-medical"></i> Medical</span>
                    </div>
                    <button class="btn-link" onclick="openModal('Ahmad Daniel', 'Fever and Flu. Doctor advised 2 days rest.', 'mc_001.pdf')">Review Details</button>
                </div>
                <div class="action-group">
                    <button class="btn btn-approve">Approve</button>
                    <button class="btn btn-reject">Reject</button>
                </div>
            </div>

            <div class="approval-card">
                <div class="student-avatar" style="background:#fef3c7; color:#d97706;">MK</div>
                <div class="details-box">
                    <h3>Michael Khoo</h3>
                    <div class="meta">
                        <span><i class="far fa-calendar"></i> 3 Days</span>
                        <span><i class="fas fa-exclamation-triangle"></i> Emergency</span>
                    </div>
                    <button class="btn-link" onclick="openModal('Michael Khoo', 'Family emergency back in hometown.', 'none')">Review Details</button>
                </div>
                <div class="action-group">
                    <button class="btn btn-approve">Approve</button>
                    <button class="btn btn-reject">Reject</button>
                </div>
            </div>
        </div>

        <div class="section-header">
            <i class="fas fa-history"></i> Decision History
        </div>

        <div id="history-list">
            <div class="approval-card" style="opacity: 0.7; grid-template-columns: 60px 1fr 120px;">
                <div class="student-avatar" style="width:50px; height:50px; font-size:1rem;">SJ</div>
                <div class="details-box">
                    <h3 style="font-size:1rem;">Sarah Jenkins</h3>
                    <div class="meta" style="margin:0;"><span>Personal Leave • 1 Day</span></div>
                </div>
                <div style="text-align: right;">
                    <span class="status-pill status-approved">Approved</span>
                </div>
            </div>

            <div class="approval-card" style="opacity: 0.7; grid-template-columns: 60px 1fr 120px;">
                <div class="student-avatar" style="width:50px; height:50px; font-size:1rem; background:#fee2e2; color:#ef4444;">RB</div>
                <div class="details-box">
                    <h3 style="font-size:1rem;">Robert Bruce</h3>
                    <div class="meta" style="margin:0;"><span>Vacation • 5 Days</span></div>
                </div>
                <div style="text-align: right;">
                    <span class="status-pill status-rejected">Rejected</span>
                </div>
            </div>
        </div>

    </div>

    <div class="modal-overlay" id="detailModal">
        <div class="modal-content">
            <h2 id="modalName">Request Details</h2>
            <p><strong>Reason:</strong></p>
            <p id="modalReason" style="color:#666; font-style: italic;">---</p>
            
            <div id="docSection">
                <p><strong>Supporting Document:</strong></p>
                <div class="doc-preview">
                    <i class="fas fa-file-pdf"></i>
                    <div>
                        <div style="font-weight:700;" id="modalDocName">document.pdf</div>
                        <div style="font-size:0.8rem; color:#888;">Click to view</div>
                    </div>
                </div>
            </div>

            <button onclick="closeModal()" style="width:100%; padding:12px; background:var(--pastel-green-dark); color:white; border:none; border-radius:10px; font-weight:700; cursor:pointer; margin-top:10px;">Close Preview</button>
        </div>
    </div>

    <script>
        function openModal(name, reason, doc) {
            document.getElementById('modalName').innerText = name;
            document.getElementById('modalReason').innerText = '"' + reason + '"';
            document.getElementById('modalDocName').innerText = doc;
            document.getElementById('detailModal').style.display = 'flex';
            
            if(doc === 'none') {
                document.getElementById('docSection').style.display = 'none';
            } else {
                document.getElementById('docSection').style.display = 'block';
            }
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
        }
    </script>
</body>
</html>