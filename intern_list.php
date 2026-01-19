<?php
// ==========================================
// BACKEND LOGIC: MANAGE ASSIGNED INTERNS
// ==========================================
session_start();

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// --- HANDLE ADD STUDENT (Linked to this supervisor) ---
if (isset($_POST['add_student'])) {
    $id = $_POST['s_id'];
    $name = $_POST['s_name'];
    $prog = $_POST['s_prog'];
    $sem = $_POST['s_sem'];
    $phone = $_POST['s_phone'];
    $email = $_POST['s_email'];
    $company = $_POST['s_company'];

    // 1. Add to student table
    $sql = "INSERT INTO students (student_id, full_name, programme_code, year_semester, phone_no, email) 
            VALUES ('$id', '$name', '$prog', '$sem', '$phone', '$email')";
    
    if ($conn->query($sql)) {
        // 2. Automatically link to the logged-in supervisor
        $sql_place = "INSERT INTO internship_placements (student_id, company_name, supervisor_id) 
                      VALUES ('$id', '$company', '$supervisor_id')";
        $conn->query($sql_place);
        echo "<script>alert('✅ Intern Added and Assigned to You!'); window.location.href='intern_list.php';</script>";
    } else {
        echo "<script>alert('Error: ID already exists.');</script>";
    }
}

// --- FETCH ONLY ASSIGNED STUDENTS ---
// Using INNER JOIN ensures only students linked to THIS supervisor show up
$sql_list = "SELECT s.*, p.company_name 
             FROM students s 
             INNER JOIN internship_placements p ON s.student_id = p.student_id 
             WHERE p.supervisor_id = '$supervisor_id'
             ORDER BY s.full_name ASC";
$students_res = $conn->query($sql_list);

// --- MODAL LOGIC (VIEW DETAILS) ---
$view_student = null;
$view_history = null;
$show_view_modal = false;

if (isset($_GET['view_id'])) {
    $vid = $_GET['view_id'];
    // Verification: Ensure the intern belongs to the supervisor before showing details
    $v_sql = "SELECT s.*, p.company_name FROM students s 
              JOIN internship_placements p ON s.student_id = p.student_id 
              WHERE s.student_id = '$vid' AND p.supervisor_id = '$supervisor_id'";
    $view_student = $conn->query($v_sql)->fetch_assoc();
    
    if ($view_student) {
        $show_view_modal = true;
        $h_sql = "SELECT * FROM intern_leave_applications WHERE student_id = '$vid' ORDER BY start_date DESC";
        $view_history = $conn->query($h_sql);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Interns | Supervisor Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root { --pastel-green-light: #f1f8f6; --pastel-green-main: #a7d7c5; --pastel-green-dark: #5c8d89; --white: #ffffff; --text-dark: #2d3436; --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); --card-bg: #ffffff; --border-color: #eee; }
        [data-theme="dark"] { --pastel-green-light: #1a1f1e; --white: #252b2a; --text-dark: #e1f2eb; --card-bg: #252b2a; --border-color: #3a4240; }
        
        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); }
        
        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; overflow: hidden; }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; padding-left: 100%; }
        @keyframes marqueeMove { 0% { transform: translateX(0); } 100% { transform: translateX(-100%); } }

        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.4rem; font-weight: 700; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .nav-links a { text-decoration: none; color: #888; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .btn-add { background: var(--pastel-green-dark); color: white; padding: 12px 25px; border-radius: 30px; border: none; cursor: pointer; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        
        .table-card { background: var(--card-bg); padding: 30px; border-radius: 25px; box-shadow: var(--soft-shadow); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #888; border-bottom: 2px solid var(--border-color); font-size: 0.85rem; }
        td { padding: 15px; border-bottom: 1px solid var(--border-color); }
        
        .student-link { font-weight: 700; color: var(--pastel-green-dark); text-decoration: none; }
        
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 2000; backdrop-filter: blur(5px); }
        .modal-box { background: var(--card-bg); padding: 40px; border-radius: 25px; width: 90%; max-width: 600px; position: relative; }
        .close-btn { position: absolute; top: 20px; right: 20px; border: none; background: none; font-size: 1.5rem; cursor: pointer; color: #888; }

        .status-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; }
        .st-Approved { background: #ecfdf5; color: #047857; }
        .st-Pending { background: #fff7ed; color: #c2410c; }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.85rem; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 10px; }
        .btn-save { width: 100%; background: var(--pastel-green-dark); color: white; padding: 12px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body>

    <div class="marquee-container">
        <div class="marquee-text"><span>👋 Welcome, Supervisor. You are viewing only the interns officially assigned to your profile.</span></div>
    </div>

    <nav>
        <a href="dashboardsupervisor.php" class="logo-text"><span class="intern">Intern</span>Leave</a>
        <div class="nav-links">
            <a href="dashboardsupervisor.php">Dashboard</a>
            <a href="approval.php">Approvals</a>
            <a href="intern_list.php" class="active">My Interns</a>
            <a href="supervisorsetting.php">Settings</a>
            <a href="logout.php" style="color:#ff6b6b;">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="header-row">
            <div>
                <h1>My Intern Directory</h1>
                <p style="color:#888;">List of interns currently under your supervision.</p>
            </div>
            <button class="btn-add" onclick="document.getElementById('addModal').style.display='flex'">
                <i class="fas fa-plus"></i> Add Intern
            </button>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Intern ID</th>
                        <th>Full Name</th>
                        <th>Program</th>
                        <th>Company</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students_res->num_rows > 0): ?>
                        <?php while($row = $students_res->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['student_id']; ?></td>
                                <td>
                                    <a href="intern_list.php?view_id=<?php echo $row['student_id']; ?>" class="student-link">
                                        <?php echo $row['full_name']; ?>
                                    </a>
                                </td>
                                <td><?php echo $row['programme_code']; ?></td>
                                <td><?php echo $row['company_name']; ?></td>
                                <td>
                                    <a href="intern_list.php?view_id=<?php echo $row['student_id']; ?>" style="color:var(--pastel-green-dark);">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding:40px; color:#ccc;">No interns assigned to you yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="addModal">
        <div class="modal-box">
            <button class="close-btn" onclick="document.getElementById('addModal').style.display='none'">&times;</button>
            <h2>Add & Assign Intern</h2>
            <form method="POST">
                <input type="hidden" name="add_student" value="1">
                <div class="form-group"><label>Intern ID</label><input type="text" name="s_id" required></div>
                <div class="form-group"><label>Full Name</label><input type="text" name="s_name" required></div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div class="form-group"><label>Program</label><input type="text" name="s_prog" required></div>
                    <div class="form-group"><label>Semester</label><input type="text" name="s_sem" required></div>
                </div>
                <div class="form-group"><label>Email</label><input type="email" name="s_email" required></div>
                <div class="form-group"><label>Phone</label><input type="text" name="s_phone" required></div>
                <div class="form-group"><label>Company Name</label><input type="text" name="s_company" required></div>
                <button type="submit" class="btn-save">Assign Intern to Me</button>
            </form>
        </div>
    </div>

    <?php if ($show_view_modal): ?>
    <div class="modal-overlay" style="display:flex;" onclick="window.location.href='intern_list.php'">
        <div class="modal-box" onclick="event.stopPropagation()">
            <button class="close-btn" onclick="window.location.href='intern_list.php'">&times;</button>
            <div style="display:flex; gap:20px; align-items:center; margin-bottom:20px;">
                <div style="width:70px; height:70px; background:#e1f2eb; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.5rem; color:var(--pastel-green-dark);">
                    <?php echo strtoupper(substr($view_student['full_name'], 0, 1)); ?>
                </div>
                <div>
                    <h2 style="margin:0;"><?php echo $view_student['full_name']; ?></h2>
                    <p style="color:#888; margin:0;"><?php echo $view_student['programme_code']; ?></p>
                </div>
            </div>
            
            <h3>Leave History</h3>
            <div style="max-height:250px; overflow-y:auto;">
                <table style="font-size:0.9rem;">
                    <thead><tr><th>Date</th><th>Type</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php while($h = $view_history->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($h['start_date'])); ?></td>
                            <td><?php echo $h['leave_type']; ?></td>
                            <td><span class="status-badge st-<?php echo $h['status']; ?>"><?php echo $h['status']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>