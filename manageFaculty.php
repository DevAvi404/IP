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

$success_message = '';
$error_message = '';

if (!empty($_POST)) {
    // Prepare and bind
    $stmt = $al->prepare("INSERT INTO faculty (faculty_id, name, s1, s2, s3, s4, s5) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $faculty_id = uniqid();
        $fc = trim($_POST['fc']);
        $sub = trim($_POST['sub']);
        $subb = trim($_POST['subb']);
        $subbb = trim($_POST['subbb']);
        $subbbb = trim($_POST['subbbb']);
        $subbbbb = trim($_POST['subbbbb']);

        $stmt->bind_param("sssssss", $faculty_id, $fc, $sub, $subb, $subbb, $subbbb, $subbbbb);

        if ($stmt->execute()) {
            $success_message = "Faculty successfully added!";
        } else {
            $error_message = "Error adding faculty: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing statement: " . $al->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculty - Student Feedback System</title>
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
            max-width: 600px;
            margin: 0 auto 2rem;
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
            background-color: var(--white);
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

        .faculty-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem auto;
            max-width: 1000px;
        }

        .faculty-table th,
        .faculty-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--neutral-light);
        }

        .faculty-table th {
            color: var(--neutral-dark);
            font-weight: 600;
            font-size: 0.95rem;
            background-color: var(--primary-light);
        }

        .faculty-table td {
            color: var(--neutral-main);
            font-size: 0.9rem;
        }

        .faculty-table .delete-btn {
            color: var(--error);
            text-decoration: none;
            font-size: 1.1rem;
        }

        .faculty-table .delete-btn:hover {
            color: #DC2626;
        }

        .notification {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1rem;
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .notification.success {
            background-color: var(--secondary-light);
            color: var(--secondary-dark);
        }

        .notification.error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error);
        }

        .notification.hide {
            opacity: 0;
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

            .faculty-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .faculty-table th,
            .faculty-table td {
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
            .header, .sidebar, .toggle-sidebar, .btn-group, .notification {
                display: none;
            }
            .content {
                margin-left: 0;
                padding: 0;
            }
            .card, .faculty-table {
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
                <a href="feeds.php" class="nav-item">
                    <i class="fas fa-comments"></i> Feedbacks
                </a>
                <a href="manageFaculty.php" class="nav-item active">
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
                <h2 class="page-title">Manage Faculty</h2>
                <p class="page-subtitle">Add new faculty members and manage existing ones</p>
            </div>

            <div class="card">
                <?php if ($success_message): ?>
                    <div class="notification success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="notification error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="form-group">
                        <label for="faculty-name">Faculty Name</label>
                        <input type="text" id="faculty-name" name="fc" class="form-input" required placeholder="Enter Faculty Name">
                    </div>
                    <div class="form-group">
                        <label for="subject-1">Subject I</label>
                        <input type="text" id="subject-1" name="sub" class="form-input" required placeholder="Enter Subject">
                    </div>
                    <div class="form-group">
                        <label for="subject-2">Subject II</label>
                        <input type="text" id="subject-2" name="subb" class="form-input" required placeholder="Enter Subject">
                    </div>
                    <div class="form-group">
                        <label for="subject-3">Subject III</label>
                        <input type="text" id="subject-3" name="subbb" class="form-input" required placeholder="Enter Subject">
                    </div>
                    <div class="form-group">
                        <label for="subject-4">Subject IV</label>
                        <input type="text" id="subject-4" name="subbbb" class="form-input" required placeholder="Enter Subject">
                    </div>
                    <div class="form-group">
                        <label for="subject-5">Subject V</label>
                        <input type="text" id="subject-5" name="subbbbb" class="form-input" required placeholder="Enter Subject">
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Faculty
                        </button>
                        <a href="home.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>

            <table class="faculty-table">
                <thead>
                    <tr>
                        <th>Sr. No.</th>
                        <th>Name</th>
                        <th>Subject I</th>
                        <th>Subject II</th>
                        <th>Subject III</th>
                        <th>Subject IV</th>
                        <th>Subject V</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sr = 1;
                    $h = mysqli_query($al, "SELECT * FROM faculty");
                    while ($j = mysqli_fetch_array($h)) {
                    ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo htmlspecialchars($j['name']); ?></td>
                            <td><?php echo htmlspecialchars($j['s1']); ?></td>
                            <td><?php echo htmlspecialchars($j['s2']); ?></td>
                            <td><?php echo htmlspecialchars($j['s3']); ?></td>
                            <td><?php echo htmlspecialchars($j['s4']); ?></td>
                            <td><?php echo htmlspecialchars($j['s5']); ?></td>
                            <td>
                                <a href="delete.php?del=<?php echo htmlspecialchars($j['faculty_id']); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this faculty?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </main>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.body.classList.toggle('sidebar-active');
        });

        // Auto-dismiss notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.classList.add('hide');
                    setTimeout(() => notification.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>