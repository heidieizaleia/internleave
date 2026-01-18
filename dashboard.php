<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternLeave | Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --pastel-green-light: #f1f8f6;
            --pastel-green-main: #a7d7c5;
            --pastel-green-dark: #5c8d89;
            --white: #ffffff;
            --sidebar-width: 260px;
        }

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; }
        body { margin: 0; background-color: var(--pastel-green-light); display: flex; height: 100vh; overflow: hidden; }

        /* --- SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--white);
            border-right: 1px solid #eee;
            display: flex;
            flex-direction: column;
            padding: 30px 20px;
        }

        .logo-small {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -1px;
            margin-bottom: 40px;
            text-align: center;
        }
        .logo-small .intern { color: var(--pastel-green-dark); }
        .logo-small .leave { color: var(--pastel-green-main); font-weight: 400; }

        .nav-menu { list-style: none; padding: 0; flex-grow: 1; }
        .nav-item {
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 10px;
            color: #777;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: 0.3s;
            cursor: pointer;
        }
        .nav-item:hover, .nav-item.active {
            background: var(--pastel-green-light);
            color: var(--pastel-green-dark);
            font-weight: 700;
        }

        .logout-btn { color: #ffb3b3; margin-top: auto; font-weight: 700; }

        /* --- MAIN CONTENT --- */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 45px;
            height: 45px;
            background: var(--pastel-green-main);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        /* --- DASHBOARD ELEMENTS --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            border-bottom: 4px solid var(--pastel-green-main);
        }

        .stat-card h3 { margin: 0; color: #888; font-size: 0.85rem; text-transform: uppercase; }
        .stat-card p { margin: 5px 0 0; font-size: 1.8rem; font-weight: 700; color: var(--pastel-green-dark); }

        .content-sections {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 30px;
        }

        .card {
            background: var(--white);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        .card h2 { margin-top: 0; color: var(--pastel-green-dark); font-size: 1.3rem; margin-bottom: 20px; }

        /* History Table */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #aaa; font-size: 0.8rem; padding: 10px; border-bottom: 1px solid #f5f5f5; }
        td { padding: 15px 10px; font-size: 0.9rem; color: #555; }
        .status-pill {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .pending { background: #fff4e5; color: #ff9800; }
        .approved { background: #e6fcf5; color: #0ca678; }

        /* Form Styling */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 0.85rem; font-weight: 700; color: var(--pastel-green-dark); }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
            background: #fcfcfc;
            outline: none;
        }
        .apply-btn {
            width: 100%;
            padding: 12px;
            background: var(--pastel-green-dark);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo-small">
            <span class="intern">Intern</span><span class="leave">Leave</span>
        </div>
        
        <nav class="nav-menu">
            <a class="nav-item active">Dashboard</a>
            <a class="nav-item">My Applications</a>
            <a class="nav-item">Internship Details</a>
            <a class="nav-item">Profile Settings</a>
        </nav>

        <a class="nav-item logout-btn" href="index.html">Log Out</a>
    </div>

    <main class="main-content">
        <header>
            <div>
                <h1 style="margin: 0; color: #333;">Welcome Back, Intern!</h1>
                <p style="margin: 5px 0 0; color: #888;">Track and manage your internship leave requests.</p>
            </div>
            <div class="user-profile">
                <div style="text-align: right;">
                    <div style="font-weight: 700; color: #333;">Ahmad Daniel</div>
                    <div style="font-size: 0.75rem; color: #888;">CS240 • Semester 6</div>
                </div>
                <div class="avatar">AD</div>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Leave Days</h3>
                <p>12</p>
            </div>
            <div class="stat-card">
                <h3>Approved</h3>
                <p>08</p>
            </div>
            <div class="stat-card">
                <h3>Pending</h3>
                <p>02</p>
            </div>
            <div class="stat-card" style="border-color: #ffb3b3;">
                <h3>Remaining Balance</h3>
                <p>02</p>
            </div>
        </div>

        <div class="content-sections">
            <section class="card">
                <h2>Recent Applications</h2>
                <table>
                    <thead>
                        <tr>
                            <th>TYPE</th>
                            <th>DATE RANGE</th>
                            <th>DAYS</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Medical Leave</td>
                            <td>12 Jan - 13 Jan</td>
                            <td>2 Days</td>
                            <td><span class="status-pill approved">Approved</span></td>
                        </tr>
                        <tr>
                            <td>Personal</td>
                            <td>25 Jan - 25 Jan</td>
                            <td>1 Day</td>
                            <td><span class="status-pill pending">Pending</span></td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="card">
                <h2>Apply New Leave</h2>
                <form action="submit_leave.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Leave Type</label>
                        <select name="leave_type" required>
                            <option value="Medical">Medical Leave</option>
                            <option value="Personal">Personal Reason</option>
                            <option value="Emergency">Emergency</option>
                            <option value="Academic">Academic Event</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <div class="form-group" style="flex: 1;">
                            <label>Start Date</label>
                            <input type="date" name="start_date" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>End Date</label>
                            <input type="date" name="end_date" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Reason / Details</label>
                        <textarea name="reason" rows="3" placeholder="Brief explanation..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Supporting Document (PDF/JPG)</label>
                        <input type="file" name="supporting_doc" accept=".pdf,.jpg,.jpeg">
                    </div>
                    <button type="submit" class="apply-btn">Submit Application</button>
                </form>
            </section>
        </div>
    </main>

</body>
</html>