    <?php
    include 'config.php';

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $error = "Please enter both email and password.";
        } else {
            // Check admin table first
            $qry = "SELECT id, password FROM admin WHERE email = '$email'";
            $result = mysqli_query($con, $qry);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_role'] = 'admin';
                    $_SESSION['user_id'] = $row['id'];
                    header("Location: admin_dashboard.php");
                    exit();
                }
            }
            mysqli_free_result($result);

            // Check candidate table
            $qry = "SELECT id, password FROM candidate WHERE email = '$email'";
            $result = mysqli_query($con, $qry);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_role'] = 'candidate';
                    $_SESSION['user_id'] = $row['id'];
                    header("Location: candidate_dashboard.php");
                    exit();
                }
            }
            mysqli_free_result($result);

            // Check recruiter table
            $qry = "SELECT id, password FROM recruiter WHERE email = '$email'";
            $result = mysqli_query($con, $qry);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_role'] = 'recruiter';
                    $_SESSION['user_id'] = $row['id'];
                    header("Location: recruiter_dashboard.php");
                    exit();
                }
            }
            mysqli_free_result($result);

            $error = "Invalid credentials.";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Placement Consultancy</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <link href="assets/css/styles.css" rel="stylesheet">
    </head>
    <body class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="card p-4" style="max-width: 400px; width: 100%;">
            <div class="text-center mb-4">
                <img src="assets/images/logo.png" alt="PlacementPro Logo" style="height: 40px;">
                <h2 class="mt-2">Welcome Back</h2>
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
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <img src="assets/images/login-icon.png" alt="Login Icon"> Login
                </button>
            </form>
            <div class="text-center mt-3">
                <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
            </div>
            <div class="text-center mt-2">
                Don't have an account? <a href="signup.php" class="text-decoration-none">Sign Up</a>
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