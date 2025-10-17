<?php
include 'config.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = sanitize($_POST['role']);
    $company = isset($_POST['company']) ? sanitize($_POST['company']) : '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif ($role === 'recruiter' && empty($company)) {
        $error = "Company name is required for recruiters.";
    } else {
        // Check if email exists
        $qry = "SELECT id FROM candidate WHERE email = '$email' 
                UNION 
                SELECT id FROM recruiter WHERE email = '$email'";
        $result = mysqli_query($con, $qry);
        if (mysqli_num_rows($result) > 0) {
            $error = "Email already in use.";
            mysqli_free_result($result);
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $hashed_password_escaped = mysqli_real_escape_string($con, $hashed_password);
            if ($role === 'candidate') {
                $qry = "INSERT INTO candidate (name, email, password, marks) 
                        VALUES ('$name', '$email', '$hashed_password_escaped', 0)";
            } else {
                $qry = "INSERT INTO recruiter (name, email, password, company) 
                        VALUES ('$name', '$email', '$hashed_password_escaped', '$company')";
            }
            if (mysqli_query($con, $qry)) {
                $success = "Registration successful! Please log in.";
                header("Location: login.php");
            } else {
                $error = "Registration failed. Please try again.";
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
    <title>Sign Up - Placement Consultancy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="card p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <img src="assets/images/logo.png" alt="PlacementPro Logo" style="height: 40px;">
            <h2 class="mt-2">Create Your Account</h2>
        </div>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><img src="assets/images/login-icon.png" alt="Email Icon"></span>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <div class="input-group">
                    <span class="input-group-text"><img src="assets/images/recruiter-icon.png" alt="Role Icon"></span>
                    <select class="form-select" id="role" name="role" onchange="toggleCompanyField()">
                        <option value="candidate">Candidate</option>
                        <option value="recruiter">Recruiter</option>
                    </select>
                </div>
            </div>
            <div class="mb-3" id="company_field" style="display: none;">
                <label for="company" class="form-label">Company Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                    <input type="text" class="form-control" id="company" name="company">
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <img src="assets/images/signup-icon.png" alt="Signup Icon"> Sign Up
            </button>
        </form>
        <div class="text-center mt-3">
            Already have an account? <a href="login.php" class="text-decoration-none">Log In</a>
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
        function toggleCompanyField() {
            var role = document.getElementById('role').value;
            document.getElementById('company_field').style.display = role === 'recruiter' ? 'block' : 'none';
        }
        // Show toasts
        document.querySelectorAll('.toast').forEach(toast => {
            new bootstrap.Toast(toast).show();
        });
    </script>
</body>
</html>