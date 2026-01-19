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
        .page-header h1 { margin: 0; color: var(--text-dark); }

        /* Approval Table/Cards */
        .approval-card { 
            background: var(--card-bg); 
            border-radius: 20px; 
            padding: 25px; 
            margin-bottom: 20px; 
            box-shadow: var(--soft-shadow);
            border: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: 80px 1fr 200px;
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
        .details-box .meta { font-size: 0.85rem; color: #888; display: flex; gap: 15px; margin-bottom: 10px; }
        .details-box .meta i { color: var(--pastel-green-dark); }
        
        .impact-preview {
            background: #fafafa;
            padding: 10px 15px;
            border-radius: 10px;
            font-size: 0.85rem;
            border-left: 4px solid var(--pastel-green-main);
        }
        [data-theme="dark"] .impact-preview { background: #1a1f1e; }

        .action-group { display: flex; flex-direction: column; gap: 10px; }
        
        .btn {
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        .btn-approve { background: var(--success); color: white; }
        .btn-approve:hover { background: #0e9f6e; transform: scale(1.02); }
        .btn-reject { background: #fff1f1; color: var(--danger); border: 1px solid #ffcccc; }
        .btn-reject:hover { background: var(--danger); color: white; }

        .empty-state {
            text-align: center;
            padding: 60px;
            background: var(--card-bg);
            border-radius: 30px;
            color: #888;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .approval-card { grid-template-columns: 1fr; text-align: center; }
            .student-avatar { margin: 0 auto; }
            .action-group { flex-direction: row; }
            .btn { flex: 1; }
        }
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
            <a href="supervisorsetting.php">Settings</a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Pending Approvals</h1>
            <p>Review leave requests and check their impact on work schedules.</p>
        </div>

        <div class="approval-card">
            <div class="student-avatar">AD</div>
            <div class="details-box">
                <h3>Ahmad Daniel bin Yusof</h3>
                <div class="meta">
                    <span><i class="far fa-calendar-alt"></i> 24 Oct - 25 Oct (2 Days)</span>
                    <span><i class="fas fa-tag"></i> Medical Leave</span>
                </div>
                <div class="impact-preview">
                    <strong>Leave Impact:</strong> "Will miss the Wednesday Sprint meeting. Task JIRA-402 will be handed over to Sarah."
                </div>
            </div>
            <div class="action-group">
                <button class="btn btn-approve" onclick="handleAction('Approved', 'Ahmad Daniel')">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button class="btn btn-reject" onclick="handleAction('Rejected', 'Ahmad Daniel')">
                    <i class="fas fa-times"></i> Reject
                </button>
            </div>
        </div>

        <div class="approval-card">
            <div class="student-avatar" style="background:#fef3c7; color:#d97706;">MK</div>
            <div class="details-box">
                <h3>Michael Khoo</h3>
                <div class="meta">
                    <span><i class="far fa-calendar-alt"></i> 28 Oct - 30 Oct (3 Days)</span>
                    <span><i class="fas fa-tag"></i> Emergency Leave</span>
                </div>
                <div class="impact-preview">
                    <strong>Leave Impact:</strong> "Family emergency. I have updated the documentation for the current API module."
                </div>
            </div>
            <div class="action-group">
                <button class="btn btn-approve" onclick="handleAction('Approved', 'Michael Khoo')">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button class="btn btn-reject" onclick="handleAction('Rejected', 'Michael Khoo')">
                    <i class="fas fa-times"></i> Reject
                </button>
            </div>
        </div>

        <div class="empty-state" style="display:none;">
            <i class="fas fa-clipboard-check" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.2;"></i>
            <h2>All Caught Up!</h2>
            <p>There are no pending leave applications to review.</p>
        </div>
    </div>

    <script>
        function handleAction(status, name) {
            const reason = status === 'Rejected' ? prompt("Please provide a reason for rejection:") : null;
            
            if (status === 'Rejected' && reason === null) return; // Cancel if no reason for rejection

            alert(`${status}: Request for ${name} has been processed.`);
            // In a real app, you would use fetch() here to update your database.
            // location.reload(); 
        }
    </script>
</body>
</html>