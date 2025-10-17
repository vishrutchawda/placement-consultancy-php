<?php
include 'config.php';
requireLogin();
if ($_SESSION['user_role'] !== 'recruiter') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidate_id = (int)$_POST['candidate_id'];
    $estimated_salary = floatval($_POST['estimated_salary']);
    $recruiter_id = (int)$_SESSION['user_id'];

    if ($estimated_salary <= 0) {
        $_SESSION['error'] = "Salary must be a positive number.";
    } else {
        $qry = "INSERT INTO offers (candidate_id, recruiter_id, status, estimated_salary) 
                VALUES ($candidate_id, $recruiter_id, 'PENDING', $estimated_salary)";
        if (mysqli_query($con, $qry)) {
            $_SESSION['success'] = "Offer sent successfully.";
        } else {
            $_SESSION['error'] = "Failed to send offer.";
        }
    }
}
header("Location: recruiter_dashboard.php");
exit();
?>