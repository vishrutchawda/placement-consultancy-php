<?php
include 'config.php';
requireLogin();
if ($_SESSION['user_role'] !== 'candidate') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: candidate_dashboard.php");
    exit();
}

$offer_id = (int)$_GET['id'];
$status = sanitize($_GET['status']);
$user_id = (int)$_SESSION['user_id'];

if (!in_array($status, ['ACCEPTED', 'REJECTED'])) {
    $_SESSION['error'] = "Invalid status.";
} else {
    // Verify offer belongs to candidate
    $qry = "SELECT id FROM offers WHERE id = $offer_id AND candidate_id = $user_id AND status = 'PENDING'";
    $result = mysqli_query($con, $qry);
    if (mysqli_num_rows($result) > 0) {
        $qry = "UPDATE offers SET status = '$status' WHERE id = $offer_id";
        if (mysqli_query($con, $qry)) {
            $_SESSION['success'] = "Offer " . strtolower($status) . " successfully.";
        } else {
            $_SESSION['error'] = "Failed to update offer.";
        }
    } else {
        $_SESSION['error'] = "Offer not found or already processed.";
    }
    mysqli_free_result($result);
}
header("Location: candidate_dashboard.php");
exit();
?>