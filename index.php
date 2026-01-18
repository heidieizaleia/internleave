<?php
// ==========================================
// BACKEND LOGIC: MULTI-USER LOGIN & SIGNUP
// ==========================================
session_start();

// 1. DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "internleave");
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- ACTION: REGISTER (Students Only) ---
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        $id = $_POST['student_id'];
        $name = $_POST['full_name'];
        $prog = $_POST['programme_code'];
        $sem = $_POST['year_semester'];
        $phone = $_POST['phone_no'];
        $email = $_POST['email'];

        // Check if ID exists
        $check = $conn->query("SELECT * FROM students WHERE student_id='$id'");
        if ($check->num_rows > 0) {
            $message = "<div class='alert error'>Student ID already exists. Please Login.</div>";
        } else {
            $sql = "INSERT INTO students (student_id, full_name, programme_code, year_semester, phone_no, email)
                    VALUES ('$id', '$name', '$prog', '$sem', '$phone', '$email')";
            
            if ($conn->query($sql) === TRUE) {
                $message = "<div class='alert success'>✅ Account Created! Please Login.</div>";
            } else {
                $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
            }
        }
    }

    // --- ACTION: LOGIN (All Users) ---
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        $id = $_POST['login_id'];
        $pass = $_POST['password'];

        $user_found = false;

        // 1. CHECK IF STUDENT
        $result = $conn->query("SELECT * FROM students WHERE student_id='$id'");
        if ($result->num_rows > 0) {
            $user_found = true;
            if ($pass === 'student123') { // Hardcoded Password Check
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = 'student';
                header("Location: home.php"); // Student Page
                exit();
            } else {
                $message = "<div class='alert error'>Wrong password for Student.</div>";
            }
        }

        // 2. CHECK IF INDUSTRY SUPERVISOR
        if (!$user_found) {
            $result = $conn->query("SELECT * FROM industry_supervisors WHERE supervisor_id='$id'");
            if ($result->num_rows > 0) {
                $user_found = true;
                if ($pass === 'supervisor123') { // Hardcoded Password Check
                    $_SESSION['user_id'] = $id;
                    $_SESSION['role'] = 'supervisor';
                    header("Location: supervisor_home.php"); // Create this page later
                    exit();
                } else {
                    $message = "<div class='alert error'>Wrong password for Supervisor.</div>";
                }
            }
        }

        // 3. CHECK IF UNIVERSITY STAFF
        if (!$user_found) {
            $result = $conn->query("SELECT * FROM staffs WHERE staff_id='$id'");
            if ($result->num_rows > 0) {
                $user_found = true;
                if ($pass === 'staff123') { // Hardcoded Password Check
                    $_SESSION['user_id'] = $id;
                    $_SESSION['role'] = 'staff';
                    header("Location: staff_home.php"); // Create this page later
                    exit();
                } else {
                    $message = "<div class='alert error'>Wrong password for Staff.</div>";
                }
            }
        }

        // 4. IF NO USER FOUND IN ANY TABLE
        if (!$user_found) {
            $message = "<div class='alert error'>User ID not found in system.</div>";
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

        /* CARD CONTAINER */
        .container {
            background: var(--white);
            width: 450px;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* LOGO */
        .logo-text { font-size: 2.5rem; font-weight: 700; letter-spacing: -2px; display: block; margin-bottom: 20px; }
        .logo-text .intern { color: var(--pastel-green-dark); }
        .logo-text .leave { color: var(--pastel-green-main); font-weight: 300; }

        /* TOGGLE BUTTONS */
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
        
        /* Sliding Highlight */
        #btnHighlight {
            position: absolute; top: 0; left: 0;
            width: 50%; height: 100%;
            background: var(--pastel-green-dark);
            border-radius: 30px;
            transition: 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 1;
        }

        /* FORMS */
        .form-group { margin-bottom: 15px; text-align: left; }
        label { display: block; margin-bottom: 5px; font-weight: 700; color: #555; font-size: 0.85rem; }
        input {
            width: 100%; padding: 12px 15px;
            border: 2px solid #f0f0f0; border-radius: 12px;
            background: #fafafa; outline: none; font-size: 0.95rem;
        }
        input:focus { border-color: var(--pastel-green-main); background: white; }

        .btn-submit {
            width: 100%; padding: 15px;
            background: var(--pastel-green-dark); color: white;
            border: none; border-radius: 12px;
            font-size: 1rem; font-weight: 700; cursor: pointer;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(92, 141, 137, 0.2);
        }
        .btn-submit:hover { transform: translateY(-2px); opacity: 0.9; }

        /* ALERTS */
        .alert { padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; text-align: left; }
        .success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Hidden Form State */
        #registerForm { display: none; }
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
                <label>User ID (Student / Staff / Supervisor ID)</label>
                <input type="text" inputmode="numeric" pattern="[0-9]*" name="login_id" placeholder="Enter ID" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>
            <button type="submit" class="btn-submit">Login</button>
            </form>

        <form id="registerForm" method="POST">
            <input type="hidden" name="action" value="register">
            
            <div class="form-group">
                <label>Student ID</label>
                <input type="text" inputmode="numeric" pattern="[0-9]*" name="student_id" placeholder="e.g. 2024881234" required>
            </div>
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="e.g. Ahmad Daniel" required>
            </div>

            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Program</label>
                    <input type="text" name="programme_code" placeholder="IM244" required>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Semester</label>
                    <input type="text" name="year_semester" placeholder="Sem 5" required>
                </div>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone_no" placeholder="012-3456789" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="student@uitm.edu.my" required>
            </div>

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
    </script>

</body>
</html>