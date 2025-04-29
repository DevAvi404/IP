<?php
include("configASL.php");
session_start();
if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}
$aid = $_SESSION['aid'];
$x = mysqli_query($al, "SELECT * FROM admin WHERE aid='$aid'");
$y = mysqli_fetch_array($x);
$name = $y['name'];

if(!empty($_POST)) {
    $faculty_id = $_POST['faculty_id'];
    // Fetch Name
    $name_query = mysqli_query($al, "SELECT * FROM faculty WHERE faculty_id='$faculty_id'");
    $name_row = mysqli_fetch_array($name_query);
    $faculty_name = $name_row['name'];
    $subject = $_POST['subject'];
    $sql = mysqli_query($al, "SELECT * FROM feeds WHERE faculty_id='$faculty_id' AND subject='$subject'");

    $q1 = $q2 = $q3 = $q4 = $q5 = $q6 = $q7 = $q8 = $q9 = $q10 = $total = $s = 0;

    while($z = mysqli_fetch_array($sql)) {
        $q1 += $z['q1'];
        $q2 += $z['q2'];
        $q3 += $z['q3'];
        $q4 += $z['q4'];
        $q5 += $z['q5'];
        $q6 += $z['q6'];
        $q7 += $z['q7'];
        $q8 += $z['q8'];
        $q9 += $z['q9'];
        $q10 += $z['q10'];
        $total = $q1 + $q2 + $q3 + $q4 + $q5 + $q6 + $q7 + $q8 + $q9 + $q10;
        $s++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Results - Student Feedback System</title>
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
            max-width: 800px;
            margin: 0 auto;
        }

        .feedback-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .feedback-table th,
        .feedback-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--neutral-light);
        }

        .feedback-table th {
            color: var(--neutral-dark);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .feedback-table td {
            color: var(--neutral-main);
            font-size: 0.9rem;
        }

        .feedback-table .highlight {
            font-weight: 600;
            color: var(--neutral-dark);
        }

        .comments-section {
            margin-top: 2rem;
        }

        .comments-title {
            font-size: 1.25rem;
            color: var(--neutral-dark);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .comments-title i {
            margin-right: 0.5rem;
            color: var(--accent-admin);
        }

        .comment {
            background-color: var(--neutral-light);
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            color: var(--neutral-dark);
            font-size: 0.9rem;
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
            background-color: var(--accent-admin);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: #7C3AED;
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

            .feedback-table th,
            .feedback-table td {
                font-size: 0.85rem;
                padding: 0.5rem;
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

        @media print {
            .header, .sidebar, .toggle-sidebar, .btn-group {
                display: none;
            }
            .content {
                margin-left: 0;
                padding: 0;
            }
            .card {
                box-shadow: none;
                border: none;
                max-width: 100%;
            }
            .page-header {
                margin-bottom: 1rem;
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
                <a href="home.php" class="nav-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="feeds.php" class="nav-item active">
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
            <div class="page-header">
                <h2 class="page-title">Feedback Results</h2>
                <p class="page-subtitle">Summary of student feedback for selected faculty and subject</p>
            </div>

            <div class="card">
                <table class="feedback-table">
                    <tr>
                        <th>Faculty Name</th>
                        <td class="highlight"><?php echo isset($faculty_name) ? htmlspecialchars($faculty_name) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td class="highlight"><?php echo isset($subject) ? htmlspecialchars($subject) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>1. Description of course objectives & assignments</th>
                        <td><?php echo isset($q1) ? $q1 : 0; ?></td>
                    </tr>
                    <tr>
                        <th>2. Communication of ideas & information</th>
                        <td><?php echo isset($q2) ? $q2 : 0; ?></td>
                    </tr>
                    <tr>
                        <th>3. Expression of expectations for performance</th>
                        <td><?php echo isset($q3) ? $q3 : 0; ?></td>
                    </tr>
                    <tr>
                        <th>4. Availability to assist students in or out of class</th>
                        <td><?php echo isset($q4) ? $q4 : 0; ?></td>
                    </tr>
                    <tr>
                        <th>5. Respect or concern for students</th>
                        <td><?php echo isset($q5) ? $q5 : 0; ?></td>
                    </tr>
                    <tr>
                        <th>6. Stimulation of interest in course</th>
                        <td><?php echo isset($q6) ? $q6 : 0; ?></td>
                    </tr>
                    <tr>
                        <th>7. Facilitation of learning</th>
                        <td><?php echo isset($q7) ? $q7 : 0; ?></td>
                    </tr>
                    <tr>
                        <th>8. Enthusiasm for the subject</th>
                        <td><?php echo isset($q8) ? $q8 : 0; ?></td>
                    </tr>
                    <tr>
                        <th>9. Encourage students to think independently, creatively & critically</th>
                        <td><?php echo isset($q9) ? $q9 : 0; ?></td>
                    </tr>
                    <tr>
                        <th>10. Overall rating</th>
                        <td><?php echo isset($q10) ? $q10 : 0; ?></td>
                    </tr>
                    <tr>
                        <th>Total Students</th>
                        <td class="highlight"><?php echo isset($s) ? $s : 0; ?></td>
                    </tr>
                    <tr>
                        <th>Total Score</th>
                        <td class="highlight"><?php echo isset($total) ? $total : 0; ?></td>
                    </tr>
                </table>

                <div class="comments-section">
                    <h3 class="comments-title"><i class="fas fa-comments"></i> Comments</h3>
                    <?php
                    $cc = mysqli_query($al, "SELECT * FROM comments WHERE faculty_id='$faculty_id' ORDER BY id DESC");
                    if (mysqli_num_rows($cc) > 0) {
                        while($pr = mysqli_fetch_array($cc)) {
                            echo '<div class="comment">' . htmlspecialchars($pr['comment']) . '</div>';
                        }
                    } else {
                        echo '<div class="comment">No comments available.</div>';
                    }
                    ?>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="window.print();">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="feeds.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
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