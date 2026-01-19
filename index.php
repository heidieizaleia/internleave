<?php
// ==========================================
// BACKEND LOGIC: MULTI-ROLE AUTHENTICATION
// ==========================================
session_start();

// 1. DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "internleave");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$message = "";
$register_success = false; // Flag to check if we should switch to login view

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- ACTION: REGISTER ---
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        $role = $_POST['reg_role'] ?? '';
        $pass = $_POST['reg_password'] ?? ''; 

        if (empty($role) || empty($pass)) {
            $message = "<div class='alert error'>Please fill in all required fields.</div>";
        } 
        else {
            // A. STUDENT REGISTRATION
            if ($role == 'student') {
                $id = $_POST['student_id'] ?? '';
                $name = $_POST['full_name'] ?? '';
                $prog = $_POST['programme_code'] ?? '';
                $sem = $_POST['year_semester'] ?? '';
                $phone = $_POST['phone_no'] ?? '';
                $email = $_POST['email'] ?? '';
                $company = $_POST['company_name'] ?? '';

                $check = $conn->query("SELECT * FROM students WHERE student_id='$id'");
                if ($check->num_rows > 0) {
                    $message = "<div class='alert error'>Student ID already exists.</div>";
                } else {
                    $sql = "INSERT INTO students (student_id, full_name, programme_code, year_semester, phone_no, email, password)
                            VALUES ('$id', '$name', '$prog', '$sem', '$phone', '$email', '$pass')";
                    
                    if ($conn->query($sql) === TRUE) {
                        
                        // --- FIX START: Create Default Staff/Supervisor if missing to prevent Crash ---
                        $conn->query("INSERT IGNORE INTO staffs (staff_id, staff_name, email, password) VALUES ('1', 'Default Staff', 'admin@uitm.edu.my', '123')");
                        $conn->query("INSERT IGNORE INTO industry_supervisors (supervisor_id, supervisor_name, company_name, email, password) VALUES (1, 'Default Supervisor', 'Pending', 'admin@company.com', '123')");
                        // --- FIX END ---

                        // Create Placement
                        $place_sql = "INSERT INTO internship_placements 
                                      (student_id, company_name, supervisor_id, staff_id, start_date, end_date) 
                                      VALUES ('$id', '$company', 1, '1', CURDATE(), CURDATE())";
                        
                        if($conn->query($place_sql)) {
                            $message = "<div class='alert success'>✅ Account Created! Please Login.</div>";
                            $register_success = true; // Trigger JS to switch tabs
                        } else {
                            // Rollback: Delete student if placement fails to avoid broken accounts
                            $conn->query("DELETE FROM students WHERE student_id='$id'");
                            $message = "<div class='alert error'>System Error: " . $conn->error . "</div>";
                        }
                    } else {
                        $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
                    }
                }
            }

            // B. STAFF REGISTRATION
            elseif ($role == 'staff') {
                $id = $_POST['staff_id'] ?? '';
                $name = $_POST['staff_name'] ?? '';
                $phone = $_POST['staff_phone'] ?? '';
                $email = $_POST['staff_email'] ?? '';

                $check = $conn->query("SELECT * FROM staffs WHERE staff_id='$id'");
                if ($check->num_rows > 0) {
                    $message = "<div class='alert error'>Staff ID already exists.</div>";
                } else {
                    $sql = "INSERT INTO staffs (staff_id, staff_name, phone_no, email, password)
                            VALUES ('$id', '$name', '$phone', '$email', '$pass')";
                    if ($conn->query($sql) === TRUE) {
                        $message = "<div class='alert success'>✅ Account Created! Please Login.</div>";
                        $register_success = true;
                    } else {
                        $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
                    }
                }
            }

            // C. SUPERVISOR REGISTRATION
            elseif ($role == 'supervisor') {
                $id = $_POST['sup_id'] ?? '';
                $name = $_POST['sup_name'] ?? '';
                $company = $_POST['sup_company'] ?? '';
                $email = $_POST['sup_email'] ?? '';

                $check = $conn->query("SELECT * FROM industry_supervisors WHERE supervisor_id='$id'");
                if ($check->num_rows > 0) {
                    $message = "<div class='alert error'>Supervisor ID already exists.</div>";
                } else {
                    $sql = "INSERT INTO industry_supervisors (supervisor_id, supervisor_name, company_name, email, password)
                            VALUES ('$id', '$name', '$company', '$email', '$pass')";
                    if ($conn->query($sql) === TRUE) {
                        $message = "<div class='alert success'>✅ Account Created! Please Login.</div>";
                        $register_success = true;
                    } else {
                        $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
                    }
                }
            }
        }
    }

    // --- ACTION: LOGIN ---
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        $role = $_POST['login_role'] ?? '';
        $id = $_POST['login_id'] ?? '';
        $pass = $_POST['password'] ?? '';

        // 1. STUDENT LOGIN
        if ($role == 'student') {
            $result = $conn->query("SELECT * FROM students WHERE student_id='$id' AND password='$pass'");
            if ($result && $result->num_rows > 0) {
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = 'student';
                header("Location: home.php");
                exit();
            } else {
                $message = "<div class='alert error'>Invalid Student ID or Password.</div>";
            }
        }

        // 2. STAFF LOGIN
        elseif ($role == 'staff') {
            $result = $conn->query("SELECT * FROM staffs WHERE staff_id='$id' AND password='$pass'");
            if ($result && $result->num_rows > 0) {
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = 'staff';
                header("Location: dashboardstaff.php");
                exit();
            } else {
                $message = "<div class='alert error'>Invalid Staff ID or Password.</div>";
            }
        }

        // 3. SUPERVISOR LOGIN
        elseif ($role == 'supervisor') {
            $result = $conn->query("SELECT * FROM industry_supervisors WHERE supervisor_id='$id' AND password='$pass'");
            if ($result && $result->num_rows > 0) {
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = 'supervisor';
                header("Location: dashboardsupervisor.php");
                exit();
            } else {
                $message = "<div class='alert error'>Invalid Supervisor ID or Password.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal | InternLeave</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --pastel-green-light: #f1f8f6;
            --pastel-green-main: #a7d7c5;
            --pastel-green-dark: #5c8d89;
            --white: #ffffff;
            --text-dark: #2d3436;
        }

        * { box-sizing: border-box; font-family: 'Quicksand', sans-serif; transition: all 0.3s ease; }
        
        body {
            margin: 0; padding: 0;
            background: linear-gradient(135deg, var(--pastel-green-main), var(--pastel-green-light));
            height: 100vh;
            display: flex; justify-content: center; align-items: center;
        }

        .container {
            background: var(--white);
            width: 450px;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
            max-height: 90vh;
            overflow-y: auto;
        }

        .logo-text { font-size: 2.5rem; font-weight: 700; letter-spacing: -2px; display: block; margin-bottom: 20px; }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }

        .toggle-box {
            background: #f0fdf4;
            border-radius: 30px;
            margin-bottom: 30px;
            display: flex;
            position: relative;
            border: 1px solid #dcfce7;
            overflow: hidden;
            height: 50px;
        }
        .toggle-btn {
            flex: 1;
            padding: 12px;
            border: none;
            background: transparent;
            font-weight: 700;
            color: #888;
            cursor: pointer;
            z-index: 2;
            outline: none;
        }
        .toggle-btn.active { color: white; }
        
        #btnHighlight {
            position: absolute; top: 0; left: 0;
            width: 50%; height: 100%;
            background: var(--pastel-green-dark);
            border-radius: 30px;
            transition: 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 1;
        }

        .form-group { margin-bottom: 15px; text-align: left; }
        label { display: block; margin-bottom: 5px; font-weight: 700; color: #555; font-size: 0.85rem; }
        input, select {
            width: 100%; padding: 12px 15px;
            border: 2px solid #f0f0f0; border-radius: 12px;
            background: #fafafa; outline: none; font-size: 0.95rem;
            color: var(--text-dark);
        }
        input:focus, select:focus { border-color: var(--pastel-green-main); background: white; }

        .btn-submit {
            width: 100%; padding: 15px;
            background: var(--pastel-green-dark); color: white;
            border: none; border-radius: 12px;
            font-size: 1rem; font-weight: 700; cursor: pointer;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(92, 141, 137, 0.2);
        }
        .btn-submit:hover { transform: translateY(-2px); opacity: 0.9; }

        .alert { padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; text-align: left; }
        .success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        #registerForm { display: none; }
        .role-section { display: none; } 
    </style>
</head>
<body>

    <div class="container">
        <div class="logo-text">
            <span class="intern">Intern</span><span class="leave">Leave</span>
        </div>

        <?php echo $message; ?>

        <div class="toggle-box">
            <div id="btnHighlight"></div>
            <button class="toggle-btn active" onclick="showLogin()">Login</button>
            <button class="toggle-btn" onclick="showRegister()">Register</button>
        </div>

        <form id="loginForm" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label>I am a...</label>
                <select name="login_role" required>
                    <option value="student">Student</option>
                    <option value="staff">University Staff</option>
                    <option value="supervisor">Industry Supervisor</option>
                </select>
            </div>
            <div class="form-group"><label>User ID</label><input type="text" name="login_id" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
            <button type="submit" class="btn-submit">Login</button>
        </form>

        <form id="registerForm" method="POST">
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label>Register As</label>
                <select name="reg_role" id="regRoleSelect" onchange="updateRegisterForm()" required>
                    <option value="student">Student</option>
                    <option value="staff">University Staff</option>
                    <option value="supervisor">Industry Supervisor</option>
                </select>
            </div>

            <div id="studentFields" class="role-section" style="display:block;">
                <div class="form-group"><label>Student ID</label><input type="text" name="student_id"></div>
                <div class="form-group"><label>Full Name</label><input type="text" name="full_name"></div>
                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;"><label>Program</label><input type="text" name="programme_code"></div>
                    <div class="form-group" style="flex:1;"><label>Semester</label><input type="text" name="year_semester"></div>
                </div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone_no"></div>
                <div class="form-group"><label>Email</label><input type="email" name="email"></div>
                <div class="form-group"><label>Company Name</label><input type="text" name="company_name"></div>
            </div>

            <div id="staffFields" class="role-section">
                <div class="form-group"><label>Staff ID</label><input type="text" name="staff_id"></div>
                <div class="form-group"><label>Full Name</label><input type="text" name="staff_name"></div>
                <div class="form-group"><label>Phone</label><input type="text" name="staff_phone"></div>
                <div class="form-group"><label>Email</label><input type="email" name="staff_email"></div>
            </div>

            <div id="supervisorFields" class="role-section">
                <div class="form-group"><label>Supervisor ID</label><input type="text" name="sup_id"></div>
                <div class="form-group"><label>Full Name</label><input type="text" name="sup_name"></div>
                <div class="form-group"><label>Company</label><input type="text" name="sup_company"></div>
                <div class="form-group"><label>Email</label><input type="email" name="sup_email"></div>
            </div>

            <div class="form-group"><label>Create Password</label><input type="password" name="reg_password" required></div>
            <button type="submit" class="btn-submit">Create Account</button>
        </form>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const regForm = document.getElementById('registerForm');
        const highlight = document.getElementById('btnHighlight');
        const btns = document.querySelectorAll('.toggle-btn');

        function showLogin() {
            loginForm.style.display = 'block';
            regForm.style.display = 'none';
            highlight.style.left = '0';
            btns[0].classList.add('active');
            btns[1].classList.remove('active');
        }

        function showRegister() {
            loginForm.style.display = 'none';
            regForm.style.display = 'block';
            highlight.style.left = '50%';
            btns[0].classList.remove('active');
            btns[1].classList.add('active');
        }

        function updateRegisterForm() {
            const role = document.getElementById('regRoleSelect').value;
            document.querySelectorAll('.role-section').forEach(el => el.style.display = 'none');
            if(role === 'student') document.getElementById('studentFields').style.display = 'block';
            if(role === 'staff') document.getElementById('staffFields').style.display = 'block';
            if(role === 'supervisor') document.getElementById('supervisorFields').style.display = 'block';
        }

        // AUTO-SWITCH TO LOGIN ON SUCCESS
        <?php if ($register_success): ?>
            showLogin();
        <?php endif; ?>
    </script>

</body>
</html>