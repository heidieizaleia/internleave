<?php
// ==========================================
// BACKEND LOGIC: MANAGE STUDENTS
// ==========================================
session_start();

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$staff_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// --- HANDLE ADD STUDENT ---
if (isset($_POST['add_student'])) {
    $id = $_POST['s_id'];
    $name = $_POST['s_name'];
    $prog = $_POST['s_prog'];
    $sem = $_POST['s_sem'];
    $phone = $_POST['s_phone'];
    $email = $_POST['s_email'];
    $company = $_POST['s_company'];

    $sql = "INSERT INTO students (student_id, full_name, programme_code, year_semester, phone_no, email) 
            VALUES ('$id', '$name', '$prog', '$sem', '$phone', '$email')";
    
    if ($conn->query($sql)) {
        $sql_place = "INSERT INTO internship_placements (student_id, staff_id, company_name, supervisor_id, start_date, end_date) 
                      VALUES ('$id', '$staff_id', '$company', 1, '2024-03-01', '2024-08-01')";
        $conn->query($sql_place);
        echo "<script>alert('✅ Student Added Successfully!'); window.location.href='studentlist.php';</script>";
    } else {
        echo "<script>alert('Error: ID already exists.');</script>";
    }
}

// --- HANDLE UPDATE STUDENT (FIXED FOR ID EDIT) ---
if (isset($_POST['update_student'])) {
    $original_id = $_POST['original_id']; // OLD ID
    $new_id = $_POST['edit_id'];          // NEW ID
    $name = $_POST['edit_name'];
    $prog = $_POST['edit_prog'];
    $sem = $_POST['edit_sem'];
    $phone = $_POST['edit_phone'];
    $email = $_POST['edit_email'];
    $company = $_POST['edit_company'];

    // 1. DISABLE FOREIGN KEY CHECKS (Allows us to edit the ID)
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    // 2. Update Student Table (Parent)
    $up_sql = "UPDATE students SET student_id='$new_id', full_name='$name', programme_code='$prog', year_semester='$sem', phone_no='$phone', email='$email' WHERE student_id='$original_id'";
    
    if ($conn->query($up_sql)) {
        // 3. Update Placements Table (Child) - Link to new ID
        $conn->query("UPDATE internship_placements SET student_id='$new_id', company_name='$company' WHERE student_id='$original_id'");
        
        // 4. Update Leave History (Child) - Ensure history moves to new ID
        $conn->query("UPDATE intern_leave_applications SET student_id='$new_id' WHERE student_id='$original_id'");

        // 5. RE-ENABLE FOREIGN KEY CHECKS
        $conn->query("SET FOREIGN_KEY_CHECKS=1");

        echo "<script>alert('✅ Student Updated Successfully!'); window.location.href='studentlist.php';</script>";
    } else {
        // Re-enable even if error
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        echo "<script>alert('Error updating student: " . $conn->error . "');</script>";
    }
}

// --- HANDLE DELETE STUDENT ---
if (isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    $conn->query("DELETE FROM students WHERE student_id = '$del_id'");
    $conn->query("DELETE FROM internship_placements WHERE student_id = '$del_id'");
    header("Location: studentlist.php");
    exit();
}

// --- FETCH STUDENTS LIST ---
$sql_list = "SELECT s.*, p.company_name 
             FROM students s 
             LEFT JOIN internship_placements p ON s.student_id = p.student_id 
             ORDER BY s.full_name ASC";
$students_res = $conn->query($sql_list);

// --- HANDLE MODALS (VIEW & EDIT) ---
$view_student = null;
$view_history = null;
$edit_student = null;
$show_view_modal = false;
$show_edit_modal = false;

// View Logic
if (isset($_GET['view_id'])) {
    $vid = $_GET['view_id'];
    $show_view_modal = true;
    $v_sql = "SELECT s.*, p.company_name FROM students s LEFT JOIN internship_placements p ON s.student_id = p.student_id WHERE s.student_id = '$vid'";
    $view_student = $conn->query($v_sql)->fetch_assoc();
    $h_sql = "SELECT * FROM intern_leave_applications WHERE student_id = '$vid' ORDER BY start_date DESC";
    $view_history = $conn->query($h_sql);
}

// Edit Logic
if (isset($_GET['edit_id'])) {
    $eid = $_GET['edit_id'];
    $show_edit_modal = true;
    $e_sql = "SELECT s.*, p.company_name FROM students s LEFT JOIN internship_placements p ON s.student_id = p.student_id WHERE s.student_id = '$eid'";
    $edit_student = $conn->query($e_sql)->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student List | InternLeave Portal</title>
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
        /* [Styles kept exactly as is] */
        :root { --pastel-green-light: #f1f8f6; --pastel-green-main: #a7d7c5; --pastel-green-dark: #5c8d89; --white: #ffffff; --text-dark: #2d3436; --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); --card-bg: #ffffff; --border-color: #eee; }
        [data-theme="dark"] { --pastel-green-light: #1a1f1e; --white: #252b2a; --text-dark: #e1f2eb; --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); --card-bg: #252b2a; --border-color: #3a4240; }
        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        body { margin: 0; padding: 0; background-color: var(--pastel-green-light); color: var(--text-dark); overflow-x: hidden; }
        .marquee-container { background: var(--pastel-green-dark); color: white; padding: 12px 0; font-weight: 600; font-size: 0.9rem; }
        .marquee-text { display: inline-block; white-space: nowrap; animation: marqueeMove 30s linear infinite; }
        @keyframes marqueeMove { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
        nav { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--soft-shadow); position: sticky; top: 0; z-index: 1000; }
        .logo-text { font-size: 2.4rem; font-weight: 700; letter-spacing: -2px; text-decoration: none; color: var(--text-dark); }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }
        .nav-links a { text-decoration: none; color: #888; font-weight: 600; font-size: 0.8rem; padding: 10px 14px; border-radius: 12px; }
        .nav-links a:hover, .nav-links a.active { background: #e1f2eb; color: var(--pastel-green-dark); }
        [data-theme="dark"] .nav-links a:hover, [data-theme="dark"] .nav-links a.active { background: rgba(255,255,255,0.1); }
        .logout-link { background: #ffeded !important; color: #ff6b6b !important; border: 1px solid #ffcccc; cursor: pointer; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-row h1 { margin: 0; font-size: 1.8rem; color: var(--text-dark); }
        .btn-add { background: var(--pastel-green-dark); color: white; padding: 12px 25px; border-radius: 30px; text-decoration: none; font-weight: 700; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .btn-add:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .table-card { background: var(--card-bg); padding: 30px; border-radius: 25px; box-shadow: var(--soft-shadow); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { text-align: left; padding: 15px; color: #888; font-size: 0.85rem; font-weight: 700; border-bottom: 2px solid var(--border-color); text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid var(--border-color); color: var(--text-dark); font-size: 0.95rem; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: rgba(0,0,0,0.02); }
        .student-link { font-weight: 700; color: var(--pastel-green-dark); text-decoration: none; cursor: pointer; }
        .student-link:hover { text-decoration: underline; }
        .actions a { color: #888; margin: 0 5px; font-size: 1rem; transition: 0.2s; }
        .actions a:hover { color: var(--pastel-green-dark); }
        .actions a.del:hover { color: #ff6b6b; }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .modal-box { background: var(--card-bg); padding: 40px; border-radius: 25px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; position: relative; animation: popIn 0.3s ease-out; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        @keyframes popIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; }
        .modal-header h2 { margin: 0; color: var(--text-dark); }
        .close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #888; }
        .profile-view { display: flex; gap: 20px; margin-bottom: 30px; }
        .pv-avatar { width: 80px; height: 80px; background: #e1f2eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--pastel-green-dark); }
        .pv-info h3 { margin: 0 0 5px; color: var(--text-dark); }
        .pv-info p { margin: 2px 0; font-size: 0.9rem; color: #666; }
        .history-table th { background: #f9f9f9; padding: 10px; font-size: 0.75rem; }
        [data-theme="dark"] .history-table th { background: #333; }
        .history-table td { padding: 10px; font-size: 0.85rem; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.85rem; color: #666; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 10px; background: var(--card-bg); color: var(--text-dark); outline: none; }
        .btn-save { width: 100%; background: var(--pastel-green-dark); color: white; padding: 12px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 10px; }
        .status-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; }
        .st-Approved { background: #ecfdf5; color: #047857; }
        .st-Pending { background: #fff7ed; color: #c2410c; }
        .st-Rejected { background: #fef2f2; color: #b91c1c; }
        footer { background: var(--white); padding: 40px 20px; text-align: center; border-top: 1px solid var(--border-color); margin-top: 60px; }
        .footer-links a { color: #888; text-decoration: none; margin: 0 10px; font-size: 0.9rem; }
    </style>
</head>
<body>

    <div class="marquee-container">
        <div class="marquee-text"><span>✨ Customize your supervisor profile and application preferences here.</span></div>
    </div>

    <nav>
        <a href="dashboardsupervisor.php" class="logo-text"><span class="intern">Intern</span><span class="leave">Leave</span></a>
        
        <div class="nav-links">
            <a href="dashboardsupervisor.php">Dashboard</a>
            <a href="approval.php">Approvals</a>
            <a href="intern_list.php">My Interns</a>
            <a href="supervisorsetting.php" class="active">Settings</a>
            <a class="logout-link" onclick="openLogout()">Logout</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="header-row">
            <div>
                <h1>Intern Directory</h1>
                <p style="color:#888; margin-top:5px;">Manage intern profiles and view leave history.</p>
            </div>
            <button class="btn-add" onclick="openModal('addModal')">
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students_res->num_rows > 0): ?>
                        <?php while($row = $students_res->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['student_id']; ?></td>
                                <td>
                                    <a href="studentlist.php?view_id=<?php echo $row['student_id']; ?>" class="student-link">
                                        <?php echo $row['full_name']; ?>
                                    </a>
                                </td>
                                <td><?php echo $row['programme_code']; ?></td>
                                <td>
                                    <?php echo $row['company_name'] ? $row['company_name'] : '<span style="color:#ccc;">Not Assigned</span>'; ?>
                                </td>
                                <td class="actions">
                                    <a href="studentlist.php?edit_id=<?php echo $row['student_id']; ?>" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="studentlist.php?delete_id=<?php echo $row['student_id']; ?>" class="del" title="Delete" onclick="return confirm('Are you sure? This will delete the student and all records.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding:30px; color:#999;">No interns found. Add one!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <div class="modal-overlay" id="addModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Add New Intern</h2>
                <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="add_student" value="1">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div class="form-group"><label>Intern ID</label><input type="number" name="s_id" required></div>
                    <div class="form-group"><label>Full Name</label><input type="text" name="s_name" required></div>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div class="form-group"><label>Program</label><input type="text" name="s_prog" required></div>
                    <div class="form-group"><label>Semester</label><input type="text" name="s_sem" required></div>
                </div>
                <div class="form-group"><label>Email</label><input type="email" name="s_email" required></div>
                <div class="form-group"><label>Phone</label><input type="text" name="s_phone" required></div>
                <div class="form-group"><label>Company Name</label><input type="text" name="s_company" required></div>
                <button type="submit" class="btn-save">Create Intern</button>
            </form>
        </div>
    </div>

    <?php if ($show_edit_modal && $edit_student): ?>
    <div class="modal-overlay" id="editModal" style="display:flex;">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Edit Intern Details</h2>
                <button class="close-btn" onclick="window.location.href='studentlist.php'">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="update_student" value="1">
                <input type="hidden" name="original_id" value="<?php echo $edit_student['student_id']; ?>">
                
                <div class="form-group">
                    <label>Intern ID</label>
                    <input type="text" name="edit_id" value="<?php echo $edit_student['student_id']; ?>" required>
                </div>

                <div class="form-group"><label>Full Name</label><input type="text" name="edit_name" value="<?php echo $edit_student['full_name']; ?>" required></div>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div class="form-group"><label>Program</label><input type="text" name="edit_prog" value="<?php echo $edit_student['programme_code']; ?>" required></div>
                    <div class="form-group"><label>Semester</label><input type="text" name="edit_sem" value="<?php echo $edit_student['year_semester']; ?>" required></div>
                </div>

                <div class="form-group"><label>Email</label><input type="email" name="edit_email" value="<?php echo $edit_student['email']; ?>" required></div>
                <div class="form-group"><label>Phone</label><input type="text" name="edit_phone" value="<?php echo $edit_student['phone_no']; ?>" required></div>
                <div class="form-group"><label>Company Name</label><input type="text" name="edit_company" value="<?php echo $edit_student['company_name']; ?>" required></div>
                
                <button type="submit" class="btn-save">Update Changes</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($show_view_modal && $view_student): ?>
    <div class="modal-overlay" id="viewModal" style="display:flex;">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Intern Profile</h2>
                <button class="close-btn" onclick="window.location.href='studentlist.php'">&times;</button>
            </div>
            
            <div class="profile-view">
                <div class="pv-avatar"><i class="fas fa-user"></i></div>
                <div class="pv-info">
                    <h3><?php echo $view_student['full_name']; ?></h3>
                    <p><strong>ID:</strong> <?php echo $view_student['student_id']; ?></p>
                    <p><strong>Program:</strong> <?php echo $view_student['programme_code']; ?> (Sem <?php echo $view_student['year_semester']; ?>)</p>
                    <p><strong>Company:</strong> <?php echo $view_student['company_name'] ?? 'Not Assigned'; ?></p>
                    <p><strong>Email:</strong> <?php echo $view_student['email']; ?></p>
                </div>
            </div>

            <h3 style="font-size:1.1rem; color:var(--text-dark); margin-bottom:10px;">Leave History</h3>
            <div style="max-height: 250px; overflow-y: auto;">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($view_history && $view_history->num_rows > 0): ?>
                            <?php while($h = $view_history->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d/m/y', strtotime($h['start_date'])); ?></td>
                                    <td><?php echo $h['leave_type']; ?></td>
                                    <td style="font-size:0.8rem;"><?php echo substr($h['reason'],0,20); ?>...</td>
                                    <td><span class="status-badge st-<?php echo $h['status']; ?>"><?php echo $h['status']; ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; color:#ccc;">No leave history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <?php endif; ?>

    <footer>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Use</a>
            <a href="#">Support</a>
        </div>
        <p>&copy; 2024 InternLeave Portal. All Rights Reserved.</p>
    </footer>

    <script>
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        
        window.onclick = function(e) {
            if(e.target.classList.contains('modal-overlay')) {
                e.target.style.display = 'none';
                if(e.target.id === 'viewModal' || e.target.id === 'editModal') window.location.href = 'studentlist.php';
            }
        }
    </script>
</body>
</html>