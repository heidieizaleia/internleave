<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Approvals | InternLeave Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
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

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); }

        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.4rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }

        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .section-header { margin: 40px 0 20px; font-size: 1.5rem; color: var(--pastel-green-dark); border-bottom: 2px solid var(--pastel-green-main); padding-bottom: 10px; display: flex; align-items: center; gap: 10px; }

        /* Approval Card */
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

        .details-box h3 { margin: 0 0 5px 0; font-size: 1.2rem; }
        .meta { font-size: 0.85rem; color: #888; display: flex; gap: 15px; margin-bottom: 8px; }
        .meta i { color: var(--pastel-green-dark); }

        .btn-link { color: var(--pastel-green-dark); text-decoration: underline; font-size: 0.85rem; font-weight: 700; cursor: pointer; background: none; border: none; padding: 0; }

        .action-group { display: flex; flex-direction: column; gap: 10px; }
        .btn { border: none; padding: 10px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; }
        .btn-approve { background: var(--success); color: white; }
        .btn-reject { background: #fff1f1; color: var(--danger); border: 1px solid #ffcccc; }

        /* Status Pills for History */
        .status-pill { padding: 5px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-approved { background: #ecfdf5; color: #047857; }
        .status-rejected { background: #fef2f2; color: #dc2626; }

        /* MODAL STYLE */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .modal-content { background: var(--card-bg); width: 90%; max-width: 500px; border-radius: 25px; padding: 30px; position: relative; }
        .doc-preview { border: 2px dashed var(--border-color); padding: 15px; border-radius: 15px; display: flex; align-items: center; gap: 15px; background: var(--pastel-green-light); text-decoration: none; color: var(--text-dark); margin: 15px 0; }
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