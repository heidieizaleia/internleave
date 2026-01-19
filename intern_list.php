<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Interns | InternLeave Portal</title>
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
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; }

        /* --- MARQUEE --- Added back here --- */
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

        /* --- CONTENT --- */
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .section-header { margin: 30px 0; font-size: 1.8rem; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 15px; }

        .intern-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .intern-card { background: var(--card-bg); border-radius: 25px; padding: 30px; text-align: center; box-shadow: var(--soft-shadow); border: 1px solid var(--border-color); cursor: pointer; }
        .intern-card:hover { transform: translateY(-10px); border-color: var(--pastel-green-main); }
        .profile-img { width: 100px; height: 100px; background: #e1f2eb; border-radius: 30px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--pastel-green-dark); font-weight: 700; }
        .intern-card h3 { margin: 10px 0 5px; color: var(--text-dark); }
        .intern-card p { margin: 0; color: #888; font-size: 0.9rem; }
        .stats-row { display: flex; justify-content: center; gap: 20px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color); }
        .stat-item b { display: block; font-size: 1.2rem; color: var(--pastel-green-dark); }
        .stat-item span { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #aaa; }

        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .profile-modal { background: var(--card-bg); width: 90%; max-width: 800px; max-height: 90vh; border-radius: 35px; overflow-y: auto; position: relative; padding: 40px; }
        .close-modal { position: absolute; top: 25px; right: 25px; font-size: 1.5rem; cursor: pointer; color: #ccc; }
        .modal-grid { display: grid; grid-template-columns: 250px 1fr; gap: 40px; }
        .leave-row { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--pastel-green-light); border-radius: 15px; margin-bottom: 10px; }
        .impact-tag { background: #fee2e2; color: #ef4444; font-size: 0.7rem; padding: 4px 8px; border-radius: 6px; font-weight: 700; cursor: help; }
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
            <a href="dashboardsupervisor.php">Dashboard</a>
            <a href="approval.php">Approvals</a>
            <a href="intern_list.php" class="active">My Interns</a>
            <a href="supervisorsetting.php">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="section-header">
            <i class="fas fa-user-graduate"></i> My Assigned Interns
        </div>

        <div class="intern-grid">
            <div class="intern-card" onclick="openProfile('Ahmad Daniel', 'Record Management', 'AD', '1', '2')">
                <div class="profile-img">AD</div>
                <h3>Ahmad Daniel</h3>
                <p>CDIM262 - Sem 6</p>
                <div class="stats-row">
                    <div class="stat-item"><b>1</b><span>Pending</span></div>
                    <div class="stat-item"><b>2</b><span>Taken</span></div>
                </div>
            </div>

            <div class="intern-card" onclick="openProfile('Sarah Jenkins', 'System Management', 'SJ', '0', '3')">
                <div class="profile-img" style="background:#e0f2fe; color:#0ea5e9;">SJ</div>
                <h3>Sarah Jenkins</h3>
                <p>CDIM262 - Sem 6</p>
                <div class="stats-row">
                    <div class="stat-item"><b>0</b><span>Pending</span></div>
                    <div class="stat-item"><b>3</b><span>Taken</span></div>
                </div>
            </div>

            <div class="intern-card" onclick="openProfile('Michael Khoo', 'Content Management', 'MK', '1', '2')">
                <div class="profile-img" style="background:#fef3c7; color:#d97706;">MK</div>
                <h3>Michael Khoo</h3>
                <p>CDIM263 - Sem 6</p>
                <div class="stats-row">
                    <div class="stat-item"><b>1</b><span>Pending</span></div>
                    <div class="stat-item"><b>2</b><span>Taken</span></div>
                </div>
            </div>

            <div class="intern-card" onclick="openProfile('Robert Bruce', 'Content Management', 'RB', '0', '2')">
                <div class="profile-img" style="background:#fee2e2; color:#ef4444;">RB</div>
                <h3>Robert Bruce</h3>
                <p>CDIM263 - Sem 6</p>
                <div class="stats-row">
                    <div class="stat-item"><b>0</b><span>Pending</span></div>
                    <div class="stat-item"><b>2</b><span>Taken</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="profileModal">
        <div class="profile-modal">
            <i class="fas fa-times close-modal" onclick="closeProfile()"></i>
            <div class="modal-grid">
                <div style="text-align: center; border-right: 1px solid var(--border-color); padding-right: 40px;">
                    <div id="m-img" class="profile-img" style="width:120px; height:120px; font-size:3rem;"></div>
                    <h2 id="m-name" style="margin-bottom:5px;">---</h2>
                    <p id="m-course" style="color:#888; margin-bottom:20px;">---</p>
                    <div style="text-align:left; background:var(--pastel-green-light); padding:15px; border-radius:20px; font-size:0.85rem;">
                        <p><b>Email:</b> intern@company.com</p>
                        <p><b>Phone:</b> +60 12-345 6789</p>
                    </div>
                </div>
                <div>
                    <h3 style="margin-top:0;"><i class="fas fa-history"></i> Leave History</h3>
                    <div id="leave-history-container">
                        <div class="leave-row">
                            <div>
                                <div style="font-weight:700;">Medical Leave</div>
                                <div style="font-size:0.8rem; color:#888;">Oct 12 - Oct 14 (2 Days)</div>
                            </div>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span class="impact-tag" title="Affected: Weekly Sprint Meeting">1 Impact</span>
                                <span style="color:var(--pastel-green-dark); font-weight:700;">Approved</span>
                            </div>
                        </div>
                        <div class="leave-row">
                            <div>
                                <div style="font-weight:700;">Personal Leave</div>
                                <div style="font-size:0.8rem; color:#888;">Sep 05 - Sep 05 (1 Day)</div>
                            </div>
                            <span style="color:var(--pastel-green-dark); font-weight:700;">Approved</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box" style="background:var(--card-bg); padding:40px; border-radius:30px; text-align:center;">
            <div style="font-size: 4rem; margin-bottom: 10px;">👋</div>
            <h2 style="margin-top:0;">End Session?</h2>
            <div style="margin-top:20px;">
                <button class="btn-no" onclick="closeLogout()">Cancel</button>
                <button class="btn-yes" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>

    <script>
        function openProfile(name, course, initials, pending, taken) {
            document.getElementById('m-name').innerText = name;
            document.getElementById('m-course').innerText = course;
            document.getElementById('m-img').innerText = initials;
            const img = document.getElementById('m-img');
            if(initials === 'SJ') { img.style.background = '#e0f2fe'; img.style.color = '#0ea5e9'; }
            else if(initials === 'MK') { img.style.background = '#fef3c7'; img.style.color = '#d97706'; }
            else if(initials === 'RB') { img.style.background = '#fee2e2'; img.style.color = '#ef4444'; }
            else { img.style.background = '#e1f2eb'; img.style.color = 'var(--pastel-green-dark)'; }
            document.getElementById('profileModal').style.display = 'flex';
        }

        function closeProfile() { document.getElementById('profileModal').style.display = 'none'; }
        function openLogout() { document.getElementById('logoutModal').style.display = 'flex'; }
        function closeLogout() { document.getElementById('logoutModal').style.display = 'none'; }
        function confirmLogout() { window.location.href = 'index.php'; }

        window.onclick = function(e) {
            if(e.target.className === 'modal-overlay') {
                closeProfile();
                closeLogout();
            }
        }
    </script>
</body>
</html>