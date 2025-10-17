<?php
include 'config.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $hashed_password_escaped = mysqli_real_escape_string($con, $hashed_password);
        $qry = "UPDATE candidate SET password = '$hashed_password_escaped' WHERE email = '$email'";
        $result = mysqli_query($con, $qry);
        if (mysqli_affected_rows($con) > 0) {
            $success = "Password reset successfully.";
        } else {
            $sql = "UPDATE recruiter SET password = '$hashed_password_escaped' WHERE email = '$email'";
            $result = mysqli_query($con, $qry);
            if (mysqli_affected_rows($con) > 0) {
                $success = "Password reset successfully.";
            } else {
                $error = "Email not found.";
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
    <title>Forgot Password - Placement Consultancy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="card p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <img src="assets/images/logo.png" alt="PlacementPro Logo" style="height: 40px;">
            <h2 class="mt-2">Reset Password</h2>
        </div>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><img src="assets/images/login-icon.png" alt="Email Icon"></span>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <img src="assets/images/save-icon.png" alt="Reset Icon"> Reset Password
            </button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Back to Login</a>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container">
        <?php if ($error): ?>
            <div class="toast align-items-center text-white bg-danger border-0" role="alert" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body"><?php echo $error; ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="toast align-items-center text-white bg-success border-0" role="alert" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body"><?php echo $success; ?></div>
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