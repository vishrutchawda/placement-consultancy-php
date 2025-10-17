<?php
include 'config.php';
requireLogin();
if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['type']) || !isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$type = sanitize($_GET['type']);
$id = (int)$_GET['id'];

if ($type === 'candidate') {
    // Delete associated offers first
    $qry = "DELETE FROM offers WHERE candidate_id = $id";
    mysqli_query($con, $qry);
    
    // Delete candidate
    $qry = "DELETE FROM candidate WHERE id = $id";
    if (mysqli_query($con, $qry)) {
        $_SESSION['success'] = "Candidate deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete candidate.";
    }
} elseif ($type === 'recruiter') {
    // Delete associated offers first
    $qry = "DELETE FROM offers WHERE recruiter_id = $id";
    mysqli_query($con, $qry);
    
    // Delete recruiter
    $qry = "DELETE FROM recruiter WHERE id = $id";
    if (mysqli_query($con, $qry)) {
        $_SESSION['success'] = "Recruiter deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete recruiter.";
    }
} else {
    $_SESSION['error'] = "Invalid user type.";
}

header("Location: admin_dashboard.php");
exit();
?>