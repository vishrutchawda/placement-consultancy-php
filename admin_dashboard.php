<?php
include 'config.php';
requireLogin();
if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

// Fetch candidates
$qry = "SELECT id, name, email, marks, qualification FROM candidate";
$candidates_result = mysqli_query($con, $qry);
$candidates = [];
while ($row = mysqli_fetch_assoc($candidates_result)) {
    $candidates[] = $row;
}
mysqli_free_result($candidates_result);

// Fetch recruiters
$qry = "SELECT id, name, email, company FROM recruiter";
$recruiters_result = mysqli_query($con, $qry);
$recruiters = [];
while ($row = mysqli_fetch_assoc($recruiters_result)) {
    $recruiters[] = $row;
}
mysqli_free_result($recruiters_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Placement Consultancy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Main Content -->
    <div class="container mt-5 pt-4">
        <div class="card p-4 mb-4">
            <h2 class="mb-4">
                <img src="assets/images/dashboard-icon.png" alt="Dashboard Icon"> Admin Dashboard
            </h2>
            
            <!-- Candidates Table -->
            <h3 class="mb-3">Candidates</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Marks</th>
                            <th>Qualification</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($candidates)): ?>
                            <tr><td colspan="5" class="text-center">No candidates found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($candidates as $candidate): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                                    <td><?php echo htmlspecialchars($candidate['email']); ?></td>
                                    <td><?php echo htmlspecialchars($candidate['marks']); ?></td>
                                    <td><?php echo htmlspecialchars($candidate['qualification'] ?? 'N/A'); ?></td>
                                    <td>
                                        <a href="delete_user.php?type=candidate&id=<?php echo $candidate['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this candidate?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recruiters Table -->
            <h3 class="mb-3 mt-5">Recruiters</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recruiters)): ?>
                            <tr><td colspan="4" class="text-center">No recruiters found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recruiters as $recruiter): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($recruiter['name']); ?></td>
                                    <td><?php echo htmlspecialchars($recruiter['email']); ?></td>
                                    <td><?php echo htmlspecialchars($recruiter['company']); ?></td>
                                    <td>
                                        <a href="delete_user.php?type=recruiter&id=<?php echo $recruiter['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this recruiter?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Logout Button -->
            <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
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