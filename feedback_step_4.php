<?php
include("configASL.php");
session_start();

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("location:index.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['faculty_id']) && !empty($_POST['subject']) &&
    !empty($_POST['q1']) && !empty($_POST['q2']) && !empty($_POST['q3']) && 
    !empty($_POST['q4']) && !empty($_POST['q5']) && !empty($_POST['q6']) && 
    !empty($_POST['q7']) && !empty($_POST['q8']) && !empty($_POST['q9']) && !empty($_POST['q10'])) {
    
    $roll = $student_id;
    $subject = $_POST['subject'];
    $faculty_id = $_POST['faculty_id'];
    $q1 = (int)$_POST['q1'];
    $q2 = (int)$_POST['q2'];
    $q3 = (int)$_POST['q3'];
    $q4 = (int)$_POST['q4'];
    $q5 = (int)$_POST['q5'];
    $q6 = (int)$_POST['q6'];
    $q7 = (int)$_POST['q7'];
    $q8 = (int)$_POST['q8'];
    $q9 = (int)$_POST['q9'];
    $q10 = (int)$_POST['q10'];

    // Validate ratings
    $ratings = [$q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $q10];
    foreach ($ratings as $rating) {
        if ($rating < 1 || $rating > 5) {
            echo '<script type="text/javascript">
                    alert("Error: Invalid rating provided.");
                    window.location="feedback_step_3.php";
                  </script>';
            exit();
        }
    }

    $total = $q1 + $q2 + $q3 + $q4 + $q5 + $q6 + $q7 + $q8 + $q9 + $q10;
    $per = ($total / 50) * 100;

    // Check for duplicate feedback
    $stmt = mysqli_prepare($al, "SELECT COUNT(*) FROM feeds WHERE roll = ? AND faculty_id = ? AND subject = ?");
    if (!$stmt) {
        echo '<script type="text/javascript">
                alert("Database error: Unable to check for duplicate feedback.");
                window.location="feedback_step_3.php";
              </script>';
        exit();
    }
    mysqli_stmt_bind_param($stmt, "sss", $roll, $faculty_id, $subject);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($count > 0) {
        echo '<script type="text/javascript">
                alert("Feedback already submitted for this faculty and subject.");
                window.location="feedback.php";
              </script>';
        exit();
    }

    // Insert feedback into feeds table
    $stmt = mysqli_prepare($al, "INSERT INTO feeds (faculty_id, roll, name, subject, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, total, percent) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo '<script type="text/javascript">
                alert("Database error: Unable to prepare feedback insertion.");
                window.location="feedback_step_3.php";
              </script>';
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ssssiiiiiiiiiiid", $faculty_id, $roll, $_SESSION['name'], $subject, 
                          $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $q10, $total, $per);
    $x = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Insert comment into comments table
    $comment = !empty($_POST['comment']) ? $_POST['comment'] : '';
    if ($comment) {
        $stmt = mysqli_prepare($al, "INSERT INTO comments (faculty_id, comment) VALUES (?, ?)");
        if (!$stmt) {
            echo '<script type="text/javascript">
                    alert("Database error: Unable to prepare comment insertion.");
                    window.location="feedback_step_3.php";
                  </script>';
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ss", $faculty_id, $comment);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Clean up session variables
    unset($_SESSION['faculty_id']);
    unset($_SESSION['name']);
    unset($_SESSION['subject']);

    if ($x) {
        echo '<script type="text/javascript">
                alert("Feedback successfully submitted");
                window.location="feedback.php";
              </script>';
    } else {
        echo '<script type="text/javascript">
                alert("Error submitting feedback. Please try again.");
                window.location="feedback_step_3.php";
              </script>';
    }
} else {
    echo '<script type="text/javascript">
            alert("Error: Incomplete feedback form.");
            window.location="feedback_step_3.php";
          </script>';
}
?>