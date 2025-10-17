<?php
include 'config.php';
requireLogin();
if ($_SESSION['user_role'] !== 'recruiter') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['candidate_id'])) {
    header("Location: recruiter_dashboard.php");
    exit();
}

$candidate_id = (int)$_GET['candidate_id'];
$qry = "SELECT name, cv FROM candidate WHERE id = $candidate_id";
$result = mysqli_query($con, $qry);
$candidate = mysqli_fetch_assoc($result);
mysqli_free_result($result);

if (!$candidate || !$candidate['cv']) {
    header("Location: recruiter_dashboard.php");
    exit();
}

// Output CV as PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="candidate_' . $candidate_id . '_cv.pdf"');
echo $candidate['cv'];
exit();
?>