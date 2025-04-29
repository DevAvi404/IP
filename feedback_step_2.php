<?php
include("configASL.php");
session_start();

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("location:index.php");
    exit();
}

// Retrieve student details
$student_id = $_SESSION['student_id'];
$stmt = mysqli_prepare($al, "SELECT name FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, "s", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_array($result);
$name = $student['name'];
mysqli_stmt_close($stmt);

// Store faculty_id from POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['faculty_id'])) {
    $_SESSION['faculty_id'] = $_POST['faculty_id'];
    $_SESSION['subject'] = $_POST['subject'];
} elseif (empty($_SESSION['faculty_id']) || empty($_SESSION['subject'])) {
    echo '<script type="text/javascript">
            alert("Error: Faculty or subject not selected.");
            window.location="feedback.php";
          </script>';
    exit();
}

// Fetch faculty name
$stmt = mysqli_prepare($al, "SELECT name FROM faculty WHERE faculty_id = ?");
mysqli_stmt_bind_param($stmt, "s", $_SESSION['faculty_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$nm = mysqli_fetch_array($result);
if ($nm) {
    $_SESSION['name'] = $nm['name'];
} else {
    echo '<script type="text/javascript">
            alert("Error: Faculty not found.");
            window.location="feedback.php";
          </script>';
    exit();
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Step 2 - Student Feedback System</title>
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
        
        body {
            background-color: var(--neutral-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-main));
            color: var(--white);
            padding: 1rem 0;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
        }
        
        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .header .tag {
            font-size: 1rem;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .container {
            display: flex;
            flex: 1;
            margin-top: 70px;
        }
        
        .sidebar {
            width: 260px;
            background-color: var(--white);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            min-height: calc(100vh - 70px);
            position: fixed;
            top: 70px;
            left: 0;
            transition: transform 0.3s ease;
            z-index: 90;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, var(--accent-student), #FBBF24);
            color: var(--white);
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(rgba(255, 255, 255, 0.1), transparent 70%);
            opacity: 0.6;
            transform: rotate(30deg);
        }
        
        .welcome-text {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .student-name {
            font-size: 1.25rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        
        .student-avatar {
            width: 70px;
            height: 70px;
            background-color: var(--white);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1;
        }
        
        .student-avatar i {
            font-size: 2rem;
            color: var(--accent-student);
        }
        
        .nav-menu {
            padding: 1rem 0;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--neutral-main);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover {
            background-color: var(--primary-light);
            color: var(--primary-main);
            border-left-color: var(--primary-main);
        }
        
        .nav-item.active {
            background-color: var(--primary-light);
            color: var(--primary-main);
            border-left-color: var(--primary-main);
            font-weight: 500;
        }
        
        .nav-item i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        .logout-btn {
            margin-top: 1rem;
            background-color: var(--error);
            color: var(--white);
        }
        
        .logout-btn:hover {
            background-color: #DC2626;
            color: var(--white);
            border-left-color: #DC2626;
        }
        
        .content {
            flex: 1;
            padding: 2rem;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.75rem;
            color: var(--neutral-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: var(--neutral-main);
            font-size: 1rem;
        }
        
        .card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--neutral-dark);
            font-weight: 500;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-main);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn i {
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--accent-student);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: #D97706;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: var(--neutral-light);
            color: var(--neutral-dark);
        }
        
        .btn-secondary:hover {
            background-color: var(--neutral-main);
            color: var(--white);
            transform: translateY(-2px);
        }
        
        .toggle-sidebar {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 110;
            background-color: var(--primary-main);
            color: var(--white);
            border: none;
            border-radius: 5px;
            padding: 0.5rem;
            cursor: pointer;
        }
        
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .content {
                margin-left: 0;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .toggle-sidebar {
                display: block;
            }
            
            body.sidebar-active .toggle-sidebar {
                left: 275px;
            }
        }
        
        @media (max-width: 768px) {
            .content {
                padding: 1.5rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .header h1 {
                font-size: 1.25rem;
            }
            
            .header .tag {
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <button id="toggleSidebar" class="toggle-sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <header class="header">
        <h1>INTERNET PROGRAMMING PROJECT</h1>
        <div class="tag">STUDENT FEEDBACK SYSTEM</div>
    </header>

    <div class="container">
        <aside class="sidebar" id="sidebar">
            <div class="welcome-card">
                <div class="student-avatar">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="welcome-text">Welcome Student</div>
                <div class="student-name"><?php echo htmlspecialchars($name); ?></div>
            </div>
            
            <nav class="nav-menu">
                <a href="feedback.php" class="nav-item active">
                    <i class="fas fa-comments"></i> Submit Feedback
                </a>
                <a href="changePass.php" class="nav-item">
                    <i class="fas fa-key"></i> Change Password
                </a>
                <a href="feedback.php?logout=true" class="nav-item logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <main class="content">
            <div class="page-header">
                <h2 class="page-title">Feedback Step 2</h2>
                <p class="page-subtitle">Confirm your faculty selection</p>
            </div>

            <div class="card">
                <form method="post" action="feedback_step_3.php">
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" class="form-input" value="<?php echo htmlspecialchars($student_id); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Faculty</label>
                        <input type="text" class="form-input" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" disabled>
                        <input type="hidden" name="faculty_id" value="<?php echo htmlspecialchars($_SESSION['faculty_id']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" class="form-input" value="<?php echo htmlspecialchars($_SESSION['subject']); ?>" disabled>
                        <input type="hidden" name="subject" value="<?php echo htmlspecialchars($_SESSION['subject']); ?>">
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary" onclick="window.location='feedback.php'">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> Next
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.body.classList.toggle('sidebar-active');
        });
    </script>
</body>
</html>