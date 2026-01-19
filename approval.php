<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Approvals | InternLeave Portal</title>
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
            --danger: #ff6b6b;
            --success: #10b981;
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

        /* --- CONTENT STYLES --- */
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .section-header { margin: 40px 0 20px; font-size: 1.5rem; color: var(--pastel-green-dark); border-bottom: 2px solid var(--pastel-green-main); padding-bottom: 10px; display: flex; align-items: center; gap: 10px; }

        .approval-card { 
            background: var(--card-bg); 
            border-radius: 20px; 
            padding: 25px; 
            margin-bottom: 20px; 
            box-shadow: var(--soft-shadow);
            border: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: 80px 1fr 180px;
            gap: 20px;
            align-items: center;
        }

        .student-avatar {
            width: 70px; height: 70px;
            background: var(--pastel-green-light);
            border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 700; color: var(--pastel-green-dark);
        }

        .details-box h3 { margin: 0; font-size: 1.2rem; color: var(--text-dark); }
        .meta { font-size: 0.85rem; color: #888; display: flex; gap: 15px; margin-top: 5px; margin-bottom: 8px; }
        .meta i { color: var(--pastel-green-dark); }
        .btn-link { color: var(--pastel-green-dark); text-decoration: underline; font-size: 0.85rem; font-weight: 700; cursor: pointer; background: none; border: none; padding: 0; }

        .action-group { display: flex; flex-direction: column; gap: 10px; }
        .btn { border: none; padding: 10px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; }
        .btn-approve { background: var(--success); color: white; }
        .btn-reject { background: #fff1f1; color: var(--danger); border: 1px solid #ffcccc; }

        .status-pill { padding: 5px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-approved { background: #ecfdf5; color: #047857; }
        .status-rejected { background: #fef2f2; color: #dc2626; }

        /* --- MODAL --- */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .modal-box { background: var(--card-bg); padding: 40px; border-radius: 30px; width: 90%; max-width: 450px; text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,0.2); }
        .doc-preview { border: 2px dashed var(--border-color); padding: 15px; border-radius: 15px; display: flex; align-items: center; gap: 15px; background: var(--pastel-green-light); text-decoration: none; color: var(--text-dark); margin: 15px 0; text-align: left; }
        
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
            <a href="approval.php" class="active">Approvals</a>
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
                <button class="btn-link" onclick="openDetailModal('Ahmad Daniel', 'Fever and Flu. Doctor advised 2 days rest.', 'mc_001.pdf')">Review Details</button>
            </div>
            <div class="action-group">
    <button class="btn btn-approve" onclick="processLeave(this, 'Approved')">Approve</button>
    <button class="btn btn-reject" onclick="processLeave(this, 'Rejected')">Reject</button>
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
                <button class="btn-link" onclick="openDetailModal('Michael Khoo', 'Family emergency back in hometown.', 'none')">Review Details</button>
            </div>
            <div class="action-group">
    <button class="btn btn-approve" onclick="processLeave(this, 'Approved')">Approve</button>
    <button class="btn btn-reject" onclick="processLeave(this, 'Rejected')">Reject</button>
</div>
        </div>
    </div>

    <div class="section-header">
        <i class="fas fa-history"></i> Decision History
    </div>

    <div id="history-list">
        <div class="approval-card" style="border-left: 5px solid var(--success);">
            <div class="student-avatar">SJ</div>
            <div class="details-box">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <h3>Sarah Jenkins</h3>
                    <span class="status-pill status-approved">Approved</span>
                </div>
                <div class="meta">
                    <span><i class="far fa-calendar"></i> 1 Day</span>
                    <span><i class="fas fa-user"></i> Personal</span>
                </div>
                <button class="btn-link" onclick="openDetailModal('Sarah Jenkins', 'Personal matters at home.', 'none')">View Final Details</button>
            </div>
            <div class="action-group" style="align-items: center; justify-content: center; opacity: 0.6;">
                <i class="fas fa-check-circle" style="font-size: 1.5rem; color: var(--success);"></i>
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--success);">PROCESSED</span>
            </div>
        </div>

        <div class="approval-card" style="border-left: 5px solid var(--danger);">
            <div class="student-avatar" style="background:#fee2e2; color:#ef4444;">RB</div>
            <div class="details-box">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <h3>Robert Bruce</h3>
                    <span class="status-pill status-rejected">Rejected</span>
                </div>
                <div class="meta">
                    <span><i class="far fa-calendar"></i> 5 Days</span>
                    <span><i class="fas fa-plane"></i> Vacation</span>
                </div>
                <button class="btn-link" onclick="openDetailModal('Robert Bruce', 'Vacation during peak project period.', 'itinerary.pdf')">View Final Details</button>
            </div>
            <div class="action-group" style="align-items: center; justify-content: center; opacity: 0.6;">
                <i class="fas fa-times-circle" style="font-size: 1.5rem; color: var(--danger);"></i>
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--danger);">PROCESSED</span>
            </div>
        </div>
    </div>
</div>

    <div class="modal-overlay" id="detailModal">
        <div class="modal-box">
            <h2 id="modalName" style="margin-top:0;">Request Details</h2>
            <p style="text-align:left;"><strong>Reason:</strong></p>
            <p id="modalReason" style="color:#666; font-style: italic; text-align:left; background: var(--pastel-green-light); padding:10px; border-radius:10px;">---</p>
            
            <div id="docSection">
                <p style="text-align:left;"><strong>Supporting Document:</strong></p>
                <div class="doc-preview">
                    <i class="fas fa-file-pdf" style="font-size:2rem; color:var(--pastel-green-dark);"></i>
                    <div>
                        <div style="font-weight:700;" id="modalDocName">document.pdf</div>
                        <div style="font-size:0.8rem; color:#888;">Click to view</div>
                    </div>
                </div>
            </div>
            <button class="btn-no" onclick="closeDetailModal()" style="width:100%; margin:0;">Close Preview</button>
        </div>
    </div>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <div style="font-size: 4rem; margin-bottom: 10px;">👋</div>
            <h2 style="margin-top:0; color:var(--text-dark);">End Session?</h2>
            <div style="margin-top:20px;">
                <button class="btn-no" onclick="closeLogout()">Cancel</button>
                <button class="btn-yes" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>

    <script>
        // Modal Logic
        function openDetailModal(name, reason, doc) {
            document.getElementById('modalName').innerText = name;
            document.getElementById('modalReason').innerText = '"' + reason + '"';
            document.getElementById('modalDocName').innerText = doc;
            document.getElementById('detailModal').style.display = 'flex';
            document.getElementById('docSection').style.display = (doc === 'none') ? 'none' : 'block';
        }

        function closeDetailModal() { document.getElementById('detailModal').style.display = 'none'; }

        // Logout Logic (Matched to Dashboard)
        function openLogout() { document.getElementById('logoutModal').style.display = 'flex'; }
        function closeLogout() { document.getElementById('logoutModal').style.display = 'none'; }
        function confirmLogout() { window.location.href = 'index.php'; }

        // Close on clicking outside
        window.onclick = function(e) {
            if(e.target.className === 'modal-overlay') {
                closeDetailModal();
                closeLogout();
            }
        }
        
        function processLeave(btn, status) {
    // 1. Find the card and the lists
    const card = btn.closest('.approval-card');
    const historyList = document.getElementById('history-list');
    const actionGroup = card.querySelector('.action-group');
    const detailsBox = card.querySelector('.details-box');
    const name = detailsBox.querySelector('h3').innerText;

    // 2. Update Card Styling based on status
    if (status === 'Approved') {
        card.style.borderLeft = "5px solid var(--success)";
        actionGroup.innerHTML = `
            <div style="align-items: center; justify-content: center; opacity: 0.6; display:flex; flex-direction:column;">
                <i class="fas fa-check-circle" style="font-size: 1.5rem; color: var(--success);"></i>
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--success);">PROCESSED</span>
            </div>`;
    } else {
        card.style.borderLeft = "5px solid var(--danger)";
        actionGroup.innerHTML = `
            <div style="align-items: center; justify-content: center; opacity: 0.6; display:flex; flex-direction:column;">
                <i class="fas fa-times-circle" style="font-size: 1.5rem; color: var(--danger);"></i>
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--danger);">PROCESSED</span>
            </div>`;
    }

    // 3. Add the Status Pill next to the name
    const titleArea = detailsBox.querySelector('h3');
    const pillClass = status === 'Approved' ? 'status-approved' : 'status-rejected';
    titleArea.parentElement.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
            <h3>${name}</h3>
            <span class="status-pill ${pillClass}">${status}</span>
        </div>`;

    // 4. Update the "Review Details" link text
    detailsBox.querySelector('.btn-link').innerText = "View Final Details";

    // 5. Move with a small fade-out animation
    card.style.opacity = '0';
    setTimeout(() => {
        historyList.prepend(card); // Move to top of history
        card.style.opacity = '0.85'; // Set history opacity
    }, 300);
}
    </script>
</body>
</html>