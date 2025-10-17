<?php
include 'config.php';
requireLogin();
if ($_SESSION['user_role'] !== 'candidate') {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$error = '';
$success = '';

// Fetch candidate data
$qry = "SELECT name, email, marks, qualification FROM candidate WHERE id = $user_id";
$result = mysqli_query($con, $qry);
$candidate = mysqli_fetch_assoc($result);
mysqli_free_result($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $marks = floatval($_POST['marks']);
    $qualification = sanitize($_POST['qualification']);
    $cv = null;

    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif ($marks < 0 || $marks > 100) {
        $error = "Marks must be between 0 and 100.";
    } else {
        // Check if email is taken by another user
        $qry = "SELECT id FROM candidate WHERE email = '$email' AND id != $user_id 
                UNION 
                SELECT id FROM recruiter WHERE email = '$email'";
        $result = mysqli_query($con, $qry);
        if (mysqli_num_rows($result) > 0) {
            $error = "Email is already in use.";
            mysqli_free_result($result);
        } else {
            // Handle CV upload
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $cv = file_get_contents($_FILES['cv']['tmp_name']);
                $cv_escaped = mysqli_real_escape_string($con, $cv);
                $qry = "UPDATE candidate SET name = '$name', email = '$email', marks = $marks, 
                        qualification = '$qualification', cv = '$cv_escaped' WHERE id = $user_id";
            } else {
                $qry = "UPDATE candidate SET name = '$name', email = '$email', marks = $marks, 
                        qualification = '$qualification' WHERE id = $user_id";
            }

            if (mysqli_query($con, $qry)) {
                $success = "Profile updated successfully.";
                // Refresh candidate data
                $qry = "SELECT name, email, marks, qualification FROM candidate WHERE id = $user_id";
                $result = mysqli_query($con, $qry);
                $candidate = mysqli_fetch_assoc($result);
                mysqli_free_result($result);
            } else {
                $error = "Failed to update profile.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Placement Consultancy</title>
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
                <img src="assets/images/profile-icon.png" alt="Profile Icon"> Your Profile
            </h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($candidate['name']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($candidate['email']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="marks" class="form-label">Marks</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                            <input type="number" step="0.01" class="form-control" id="marks" name="marks" value="<?php echo htmlspecialchars($candidate['marks']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="qualification" class="form-label">Qualification</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-book"></i></span>
                            <select class="form-select" id="qualification" name="qualification">
                                <option value="">Select Qualification</option>
                                <option value="B.Tech" <?php echo $candidate['qualification'] === 'B.Tech' ? 'selected' : ''; ?>>B.Tech</option>
                                <option value="M.Tech" <?php echo $candidate['qualification'] === 'M.Tech' ? 'selected' : ''; ?>>M.Tech</option>
                                <option value="BE" <?php echo $candidate['qualification'] === 'BE' ? 'selected' : ''; ?>>BE</option>
                                <option value="Other" <?php echo $candidate['qualification'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="cv" class="form-label">Upload CV (PDF)</label>
                        <div class="input-group">
                            <span class="input-group-text"><img src="assets/images/choose_file.png" alt="Upload Icon"></span>
                            <input type="file" class="form-control" id="cv" name="cv" accept=".pdf">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <img src="assets/images/save-icon.png" alt="Save Icon"> Save Profile
                    </button>
                    <a href="candidate_dashboard.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-lg">
                        <img src="assets/images/logout-icon.png" alt="Logout Icon"> Logout
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container">
        <?php if ($error): ?>
            <div class="toast align-items-center bg-danger border-0" role="alert" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="toast align-items-center bg-success border-0" role="alert" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
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