<?php
include 'config.php';
requireLogin();
if ($_SESSION['user_role'] !== 'candidate') {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Fetch candidate name
$qry = "SELECT name FROM candidate WHERE id = $user_id";
$result = mysqli_query($con, $qry);
$candidate = mysqli_fetch_assoc($result);
$name = $candidate['name'] ?? 'Candidate';
mysqli_free_result($result);

// Fetch offers
$qry = "SELECT o.id, o.status, o.estimated_salary, r.company 
        FROM offers o 
        JOIN recruiter r ON o.recruiter_id = r.id 
        WHERE o.candidate_id = $user_id";
$result = mysqli_query($con, $qry);
$offers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $offers[] = $row;
}
mysqli_free_result($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Dashboard - Placement Consultancy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>


    <!-- Main Content -->
    <div class="container mt-5 pt-4">
        <div class="card p-4">
            <h2 class="mb-4">
                <img src="assets/images/dashboard-icon.png" alt="Dashboard Icon"> Welcome  <?php echo htmlspecialchars($name); ?> !!!
            </h2>
            <h3 class="mb-3">Your Offers</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Recruiter</th>
                            <th>Status</th>
                            <th>Estimated Salary</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($offers)): ?>
                            <tr><td colspan="4" class="text-center">No offers available.</td></tr>
                        <?php else: ?>
                            <?php foreach ($offers as $offer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($offer['company']); ?></td>
                                    <td><?php echo htmlspecialchars($offer['status']); ?></td>
                                    <td>$<?php echo number_format($offer['estimated_salary'], 2); ?></td>
                                    <td>
                                        <?php if ($offer['status'] === 'PENDING'): ?>
                                            <a href="update_offer.php?id=<?php echo $offer['id']; ?>&status=ACCEPTED" class="btn btn-success btn-sm me-2">
                                                <img src="assets/images/hire-icon.png" alt="Accept Icon"> Accept
                                            </a>
                                            <a href="update_offer.php?id=<?php echo $offer['id']; ?>&status=REJECTED" class="btn btn-danger btn-sm">
                                                <img src="assets/images/reject.png" alt="Reject Icon"> Reject
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                <a href="candidate_profile.php" class="btn btn-primary btn-lg">
                    <img src="assets/images/profile-icon.png" alt="Profile Icon"> Edit Profile
                </a>
                <a href="logout.php" class="btn btn-danger btn-lg">
                    <img src="assets/images/logout-icon.png" alt="Logout Icon"> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="toast align-items-center bg-success border-0" role="alert" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="toast align-items-center bg-danger border-0" role="alert" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show toasts
        document.querySelectorAll('.toast').forEach(toast => {
            new bootstrap.Toast(toast).show();
        });
    </script>
</body>
</html>