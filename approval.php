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
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); }

        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.4rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }

        .nav-links { display: flex; gap: 8px; align-items: center; }
        .nav-links a { text-decoration: none; color: #888; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }

        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .page-header { margin-bottom: 30px; }

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

        .btn-link { 
            color: var(--pastel-green-dark); 
            text-decoration: underline; 
            font-size: 0.85rem; 
            font-weight: 700; 
            cursor: pointer; 
            background: none; 
            border: none; 
            padding: 0;
        }

        .action-group { display: flex; flex-direction: column; gap: 10px; }
        
        .btn {
            border: none; padding: 10px; border-radius: 10px;
            font-weight: 700; cursor: pointer; display: flex;
            align-items: center; justify-content: center; gap: 8px; font-size: 0.85rem;
        }
        .btn-approve { background: var(--success); color: white; }
        .btn-reject { background: #fff1f1; color: var(--danger); border: 1px solid #ffcccc; }

        /* MODAL STYLE */
        .modal-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.5); display: none; 
            justify-content: center; align-items: center; z-index: 2000; 
        }
        .modal-content { 
            background: var(--card-bg); width: 90%; max-width: 600px; 
            border-radius: 25px; padding: 30px; position: relative;
            max-height: 90vh; overflow-y: auto;
        }
        .close-modal { position: absolute; top: 20px; right: 20px; font-size: 1.5rem; cursor: pointer; color: #888; }
        
        .detail-row { margin-bottom: 20px; }
        .detail-label { font-size: 0.8rem; color: #888; font-weight: 700; text-transform: uppercase; }
        .detail-value { font-size: 1rem; color: var(--text-dark); margin-top: 5px; }

        .doc-preview {
            border: 2px dashed var(--border-color);
            padding: 15px; border-radius: 15px;
            display: flex; align-items: center; gap: 15px;
            background: var(--pastel-green-light);
            text-decoration: none; color: var(--text-dark);
        }
        .doc-preview i { font-size: 2rem; color: var(--pastel-green-dark); }
    </style>
</head>
<body>

    <nav>
        <a href="dashboardsupervisor.php" class="logo-text">
            <span class="intern">Intern</span><span class="leave">Leave</span>
        </a>
        <div class="nav-links">
            <a href="dashboardsupervisor.php">Dashboard</a>
            <a href="approval.php" class="active">Approvals</a>
            <a href="intern_list.php">My Interns</a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Review Applications</h1>
            <p>You have 2 pending requests to verify.</p>
        </div>

        <div class="approval-card">
            <div class="student-avatar">AD</div>
            <div class="details-box">
                <h3>Ahmad Daniel</h3>
                <div class="meta">
                    <span><i class="far fa-calendar"></i> 2 Days</span>
                    <span><i class="fas fa-notes-medical"></i> Medical</span>
                </div>
                <button class="btn-link" onclick="openDetails('Ahmad Daniel', 'Medical', 'Fever and Flu. Doctor advised 2 days rest.', 'mc_document.pdf')">
                    View full details & documents
                </button>
            </div>
            <div class="action-group">
                <button class="btn btn-approve" onclick="alert('Approved')">Approve</button>
                <button class="btn btn-reject" onclick="alert('Rejected')">Reject</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="detailModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalName">Request Details</h2>
            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 20px 0;">

            <div class="detail-row">
                <div class="detail-label">Reason for Leave</div>
                <div class="detail-value" id="modalReason">---</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Supporting Document</div>
                <a href="#" class="doc-preview" id="modalDocLink" target="_blank">
                    <i class="fas fa-file-pdf"></i>
                    <div>
                        <div style="font-weight:700;" id="modalDocName">document.pdf</div>
                        <div style="font-size:0.8rem; color:#888;">Click to view/download</div>
                    </div>
                </a>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button class="btn btn-approve" style="flex:1;" onclick="closeModal()">Process Approval</button>
                <button class="btn btn-reject" style="flex:1;" onclick="closeModal()">Close Window</button>
            </div>
        </div>
    </div>

    <script>
        function openDetails(name, type, reason, docName) {
            document.getElementById('modalName').innerText = name + "'s Request";
            document.getElementById('modalReason').innerText = reason;
            document.getElementById('modalDocName').innerText = docName;
            // document.getElementById('modalDocLink').href = 'uploads/' + docName; // Real path
            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('detailModal')) closeModal();
        }
    </script>
</body>
</html>