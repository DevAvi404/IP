<?php
include("configASL.php");
session_start();
if (!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}
$aid = $_SESSION['aid'];
$x = mysqli_query($al, "SELECT * FROM admin WHERE aid='$aid'");
$y = mysqli_fetch_array($x);
$name = $y['name'];

// Fetch counts from the database
// Feedback count from feeds table
$feedback_result = mysqli_query($al, "SELECT COUNT(*) AS feedback_count FROM feeds");
$feedback_row = mysqli_fetch_assoc($feedback_result);
$feedback_count = $feedback_row['feedback_count'];

// Faculty count from faculty table
$faculty_result = mysqli_query($al, "SELECT COUNT(*) AS faculty_count FROM faculty");
$faculty_row = mysqli_fetch_assoc($faculty_result);
$faculty_count = $faculty_row['faculty_count'];

// Subjects count from subjects table
$subjects_result = mysqli_query($al, "SELECT COUNT(*) AS subjects_count FROM subjects");
$subjects_row = mysqli_fetch_assoc($subjects_result);
$subjects_count = $subjects_row['subjects_count'];

// Students count from students table
$students_result = mysqli_query($al, "SELECT COUNT(*) AS students_count FROM students");
$students_row = mysqli_fetch_assoc($students_result);
$students_count = $students_row['students_count'];

// Check database connection status
$db_status = mysqli_ping($al) ? "Connected" : "Disconnected";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Feedback System</title>
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
            background: linear-gradient(135deg, var(--accent-admin), #9333EA);
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
        
        .admin-name {
            font-size: 1.25rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        
        .admin-avatar {
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
        
        .admin-avatar i {
            font-size: 2rem;
            color: var(--accent-admin);
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
            margin-left: 260px; /* Sidebar width */
            transition: margin-left 0.3s ease;
        }
        
        .dashboard-header {
            margin-bottom: 2rem;
        }
        
        .dashboard-title {
            font-size: 1.75rem;
            color: var(--neutral-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .dashboard-subtitle {
            color: var(--neutral-main);
            font-size: 1rem;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            color: inherit;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .stat-icon i {
            font-size: 1.5rem;
        }
        
        .stat-icon.feedback {
            background-color: rgba(139, 92, 246, 0.1);
            color: var(--accent-admin);
        }
        
        .stat-icon.faculty {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--secondary-main);
        }
        
        .stat-icon.subjects {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary-main);
        }
        
        .stat-icon.students {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--accent-student);
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-number {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--neutral-dark);
            line-height: 1;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: var(--neutral-main);
            font-size: 0.875rem;
        }
        
        .quick-links {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.25rem;
            color: var(--neutral-dark);
            font-weight: 600;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 0.75rem;
            color: var(--primary-main);
        }
        
        .link-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .link-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        
        .link-btn i {
            margin-right: 0.5rem;
        }
        
        .link-btn.faculty {
            background-color: var(--secondary-light);
            color: var(--secondary-dark);
        }
        
        .link-btn.faculty:hover {
            background-color: var(--secondary-main);
            color: var(--white);
            transform: translateY(-2px);
        }
        
        .link-btn.subject {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }
        
        .link-btn.subject:hover {
            background-color: var(--primary-main);
            color: var(--white);
            transform: translateY(-2px);
        }
        
        .link-btn.student {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--accent-student);
        }
        
        .link-btn.student:hover {
            background-color: var(--accent-student);
            color: var(--white);
            transform: translateY(-2px);
        }
        
        .system-status {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }
        
        .status-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--neutral-light);
        }
        
        .status-item:last-child {
            border-bottom: none;
        }
        
        .status-label {
            color: var(--neutral-dark);
            font-weight: 500;
        }
        
        .status-value {
            color: var(--neutral-main);
        }
        
        .status-value.connected {
            color: var(--success);
        }
        
        .status-value.disconnected {
            color: var(--error);
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
        
        /* Responsive styles */
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
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 1.5rem;
            }
            
            .header h1 {
                font-size: 1.25rem;
            }
            
            .header .tag {
                font-size: 0.875rem;
            }
            
            .link-buttons {
                flex-direction: column;
            }
            
            .link-btn {
                width: 100%;
                justify-content: center;
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
                <div class="admin-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="welcome-text">Welcome Admin</div>
                <div class="admin-name"><?php echo htmlspecialchars($name); ?></div>
            </div>
            
            <nav class="nav-menu">
                <a href="home.php" class="nav-item active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="feeds.php" class="nav-item">
                    <i class="fas fa-comments"></i> Feedbacks
                </a>
                <a href="manageFaculty.php" class="nav-item">
                    <i class="fas fa-chalkboard-teacher"></i> Manage Faculty
                </a>
                <a href="manageSubjects.php" class="nav-item">
                    <i class="fas fa-book"></i> Manage Subjects
                </a>
                <a href="students.php" class="nav-item">
                    <i class="fas fa-user-graduate"></i> Student List
                </a>
                <a href="changePass.php" class="nav-item">
                    <i class="fas fa-key"></i> Change Password
                </a>
                <a href="logout.php" class="nav-item logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <main class="content">
            <div class="dashboard-header">
                <h2 class="dashboard-title">Admin Dashboard</h2>
                <p class="dashboard-subtitle">Overview of feedback system statistics and quick actions</p>
            </div>

            <div class="dashboard-stats">
                <a href="feeds.php" class="stat-card">
                    <div class="stat-icon feedback">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo htmlspecialchars($feedback_count); ?></div>
                        <div class="stat-label">Total Feedbacks</div>
                    </div>
                </a>
                
                <a href="manageFaculty.php" class="stat-card">
                    <div class="stat-icon faculty">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo htmlspecialchars($faculty_count); ?></div>
                        <div class="stat-label">Faculty Members</div>
                    </div>
                </a>
                
                <a href="manageSubjects.php" class="stat-card">
                    <div class="stat-icon subjects">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo htmlspecialchars($subjects_count); ?></div>
                        <div class="stat-label">Subjects</div>
                    </div>
                </a>
                
                <a href="students.php" class="stat-card">
                    <div class="stat-icon students">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo htmlspecialchars($students_count); ?></div>
                        <div class="stat-label">Registered Students</div>
                    </div>
                </a>
            </div>

            <div class="quick-links">
                <h3 class="section-title"><i class="fas fa-link"></i> Quick Links</h3>
                <div class="link-buttons">
                    <a href="manageFaculty.php" class="link-btn faculty">
                        <i class="fas fa-chalkboard-teacher"></i> Add Faculty
                    </a>
                    <a href="manageSubjects.php" class="link-btn subject">
                        <i class="fas fa-book"></i> Add Subject
                    </a>
                    <a href="students.php" class="link-btn student">
                        <i class="fas fa-user-graduate"></i> Add Student
                    </a>
                </div>
            </div>

            <div class="system-status">
                <h3 class="section-title"><i class="fas fa-server"></i> System Status</h3>
                <div class="status-item">
                    <span class="status-label">Current Date</span>
                    <span class="status-value"><?php echo date('F d, Y'); ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label">Database Connection</span>
                    <span class="status-value <?php echo strtolower($db_status); ?>"><?php echo htmlspecialchars($db_status); ?></span>
                </div>
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