<?php
include("configASL.php");
session_start(); // Now moved to the top before ANY output

// Admin login handling
if (isset($_SESSION['aid'])) {
    header("location:home.php");
    exit();
}

// Admin login processing
if (isset($_POST['aid']) && isset($_POST['pass'])) {
    $aid = mysqli_real_escape_string($al, $_POST['aid']);
    $pass = mysqli_real_escape_string($al, sha1($_POST['pass']));
    $sql = mysqli_query($al, "SELECT * FROM admin WHERE aid='$aid' AND password='$pass'");
    
    if (mysqli_num_rows($sql) == 1) {
        $_SESSION['aid'] = $_POST['aid'];
        header("location:home.php");
        exit();
    } else {
        $admin_error = "Incorrect Admin ID or Password";
    }
}

// Student login handling
if (isset($_SESSION['student_id'])) {
    header("location:feedback.php");
    exit();
}

if (!empty($_POST['student_id']) && !empty($_POST['password'])) {
    $student_id = mysqli_real_escape_string($al, $_POST['student_id']);
    $password = mysqli_real_escape_string($al, $_POST['password']);

    $sql = mysqli_query($al, "SELECT * FROM students WHERE student_id='$student_id'");

    if (mysqli_num_rows($sql) == 1) {
        $student = mysqli_fetch_array($sql);
        if (password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['name'] = $student['name'];  // Save student name in session

            header("Location: feedback.php");  // Redirect to the feedback page
            exit();
        } else {
            $student_error = "Incorrect Student ID or Password";
        }
    } else {
        $student_error = "Incorrect Student ID or Password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #1E3A8A;    
            --primary-main: #2563EB;    
            --primary-light: #DBEAFE;   

            --secondary-dark: #065F46;   
            --secondary-main: #10B981;   
            --secondary-light: #D1FAE5; 
 
            --accent-student: #F59E0B;   
            --accent-admin: #8B5CF6;     

            --neutral-dark: #1F2937;    
            --neutral-main: #6B7280;    
            --neutral-light: #F3F4F6;  
            --white: #FFFFFF;

            --error: #EF4444;           
            --success: #10B981;     
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header img{
            width: 200px;
        }
        body {
            background-color: var(--neutral-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-main));
            color: var(--white);
            padding: 1.5rem 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .header .tag {
            font-size: 1.2rem;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .login-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            width: 100%;
            margin: 1rem 0;
        }
        
        .login-box {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 380px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .login-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .admin-box {
            border-top: 5px solid var(--accent-admin);
        }
        
        .student-box {
            border-top: 5px solid var(--accent-student);
        }
        
        .login-title {
            font-size: 1.5rem;
            color: var(--neutral-dark);
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--neutral-main);
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-main);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        
        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            text-align: center;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-admin {
            background-color: var(--accent-admin);
            color: white;
        }
        
        .btn-admin:hover {
            background-color: #7C3AED;
        }
        
        .btn-student {
            background-color: var(--accent-student);
            color: white;
        }
        
        .btn-student:hover {
            background-color: #D97706;
        }
        
        .register-section {
            margin-top: 2rem;
            text-align: center;
        }
        
        .register-text {
            color: var(--neutral-main);
            font-size: 1rem;
        }
        
        .register-link {
            text-decoration: none;
            color: var(--primary-main);
            font-weight: 500;
            transition: color 0.3s;
            border-bottom: 1px solid transparent;
            padding-bottom: 2px;
        }
        
        .register-link:hover {
            color: var(--primary-dark);
            border-bottom-color: var(--primary-dark);
        }
        
        .footer {
            background-color: var(--neutral-dark);
            color: var(--white);
            text-align: center;
            padding: 1rem 0;
            margin-top: 2rem;
        }
        
        .footer p {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-box {
            animation: fadeIn 0.6s ease-out;
        }
        
        .student-box {
            animation-delay: 0.2s;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .login-box {
                width: 100%;
                max-width: 380px;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
            
            .header .tag {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php if(isset($admin_error)): ?>
        <script type="text/javascript">
            alert("<?php echo $admin_error; ?>");
        </script>
    <?php endif; ?>
    
    <?php if(isset($student_error)): ?>
        <script type="text/javascript">
            alert("<?php echo $student_error; ?>");
        </script>
    <?php endif; ?>

    <header class="header">
        <img src="PUC.png" alt="">
        <h1>INTERNET PROGRAMMING PROJECT</h1>
        <div class="tag">STUDENT FEEDBACK SYSTEM</div>
    </header>

    <div class="container">
        <div class="login-section">
            <!-- Admin Login Box -->
            <div class="login-box admin-box">
                <div class="login-title">
                    <i class="fas fa-user-shield"></i> Admin Login
                </div>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="admin-id">Admin ID</label>
                        <input type="text" id="admin-id" name="aid" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="admin-password">Password</label>
                        <input type="password" id="admin-password" name="pass" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-admin">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>

            <!-- Student Login Box -->
            <div class="login-box student-box">
                <div class="login-title">
                    <i class="fas fa-user-graduate"></i> Student Login
                </div>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="student-id">Student ID</label>
                        <input type="text" id="student-id" name="student_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="student-password">Password</label>
                        <input type="password" id="student-password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-student">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>
        </div>

        <div class="register-section">
            <p class="register-text">New? <a href="student_register.php" class="register-link">Register Here</a></p>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Student Feedback System. All rights reserved.</p>
    </footer>
</body>
</html>