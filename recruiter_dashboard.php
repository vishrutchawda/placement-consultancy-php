<?php
include 'config.php';
requireLogin();
if ($_SESSION['user_role'] !== 'recruiter') {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$min_marks = isset($_GET['min_marks']) ? floatval($_GET['min_marks']) : 0;
$qualification = isset($_GET['qualification']) ? sanitize($_GET['qualification']) : 'All';

// Fetch candidates
$qry = "SELECT id, name, marks, qualification FROM candidate WHERE marks >= $min_marks";
if ($qualification !== 'All') {
    $qry .= " AND qualification = '$qualification'";
}
$result = mysqli_query($con, $qry);
$candidates = [];
while ($row = mysqli_fetch_assoc($result)) {
    $candidates[] = $row;
}
mysqli_free_result($result);

// Fetch sent offers
$qry = "SELECT o.id, o.status, o.estimated_salary, c.name AS candidate_name 
        FROM offers o 
        JOIN candidate c ON o.candidate_id = c.id 
        WHERE o.recruiter_id = $user_id";
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
    <title>Recruiter Dashboard - Placement Consultancy</title>
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
                <img src="assets/images/dashboard-icon.png" alt="Dashboard Icon"> Recruiter Dashboard
            </h2>
            <!-- Filter Form -->
            <form method="GET" action="" class="mb-4 p-3 rounded" style="background-color: #eff6ff;">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="min_marks" class="form-label">Min Marks</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                            <input type="number" step="0.01" class="form-control" id="min_marks" name="min_marks" value="<?php echo htmlspecialchars($min_marks); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="qualification" class="form-label">Qualification</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-book"></i></span>
                            <select class="form-select" id="qualification" name="qualification">
                                <option value="All" <?php echo $qualification === 'All' ? 'selected' : ''; ?>>All</option>
                                <option value="B.Tech" <?php echo $qualification === 'B.Tech' ? 'selected' : ''; ?>>B.Tech</option>
                                <option value="M.Tech" <?php echo $qualification === 'M.Tech' ? 'selected' : ''; ?>>M.Tech</option>
                                <option value="BE" <?php echo $qualification === 'BE' ? 'selected' : ''; ?>>BE</option>
                                <option value="Other" <?php echo $qualification === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <img src="assets/images/filter-icon.png" alt="Filter Icon"> Apply Filter
                        </button>
                    </div>
                </div>
            </form>
            <!-- Candidates Table -->
            <h3 class="mb-3">Candidates</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Marks</th>
                            <th>Qualification</th>
                            <th>CV</th>
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
                                    <td><?php echo htmlspecialchars($candidate['marks']); ?></td>
                                    <td><?php echo htmlspecialchars($candidate['qualification'] ?? 'N/A'); ?></td>
                                    <td>
                                        <a href="view_cv.php?candidate_id=<?php echo $candidate['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-file-pdf"></i> View CV
                                        </a>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#offerModal" onclick="setCandidateId(<?php echo $candidate['id']; ?>)">
                                            <img src="assets/images/hire-icon.png" alt="Offer Icon"> Send Offer
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Offers Table -->
        <div class="card p-4">
            <h3 class="mb-3">Your Offers</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Status</th>
                            <th>Estimated Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($offers)): ?>
                            <tr><td colspan="3" class="text-center">No offers sent.</td></tr>
                        <?php else: ?>
                            <?php foreach ($offers as $offer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($offer['candidate_name']); ?></td>
                                    <td><?php echo htmlspecialchars($offer['status']); ?></td>
                                    <td>$<?php echo number_format($offer['estimated_salary'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Logout Button -->
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <a href="logout.php" class="btn btn-danger btn-lg">
                <img src="assets/images/logout-icon.png" alt="Logout Icon"> Logout
            </a>
        </div>
    </div>

    <!-- Offer Modal -->
    <div class="modal fade" id="offerModal" tabindex="-1" aria-labelledby="offerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="send_offer.php">
                    <div class="modal-body">
                        <input type="hidden" id="candidate_id" name="candidate_id">
                        <div class="mb-3">
                            <label for="estimated_salary" class="form-label">Estimated Salary</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" step="0.01" class="form-control" id="estimated_salary" name="estimated_salary" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <img src="assets/images/hire-icon.png" alt="Send Icon"> Send Offer
                        </button>
                    </div>
                </form>
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
        function setCandidateId(id) {
            document.getElementById('candidate_id').value = id;
        }
        // Show toasts
        document.querySelectorAll('.toast').forEach(toast => {
            new bootstrap.Toast(toast).show();
        });
    </script>
</body>
</html>