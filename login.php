<?php
// Include configuration and helper functions
require_once 'includes/config.php';   // Database connection, constants, etc.
require_once 'includes/functions.php'; // Reusable helper functions like input sanitization

// -----------------------------
// 1. Handle Voter Registration
// -----------------------------
// Triggered when form is submitted with POST and the 'register' button is clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {

    // Sanitize input to prevent XSS or SQL injection
    $voter_fname = sanitize_input($_POST['reg_fname']);   // First name
    $voter_lname = sanitize_input($_POST['reg_lname']);   // Last name
    $voter_mname = sanitize_input($_POST['reg_mname']);   // Middle name (optional)
    $voter_password = sanitize_input($_POST['reg_password']); // Password
    $confirm_password = sanitize_input($_POST['reg_confirm_password']); // Confirm password

    // -----------------------------
    // 1a. Validate Inputs
    // -----------------------------
    if (empty($voter_fname) || empty($voter_lname)) {
        $register_error = "First name and last name are required!";
    } elseif (empty($voter_password)) {
        $register_error = "Password is required!";
    } elseif ($voter_password !== $confirm_password) {
        $register_error = "Passwords do not match!";
    } elseif (strlen($voter_password) < 6) {
        $register_error = "Password must be at least 6 characters long!";
    } else {
        // -----------------------------
        // 1b. Construct Full Name
        // -----------------------------
        $voter_name = $voter_fname . ' ' . $voter_lname;
        if (!empty($voter_mname)) {
            $voter_name = $voter_fname . ' ' . $voter_mname . ' ' . $voter_lname;
        }

        // -----------------------------
        // 1c. Hash the Password
        // -----------------------------
        // Using password_hash() ensures passwords are securely stored
        $hashed_password = password_hash($voter_password, PASSWORD_DEFAULT);

        // -----------------------------
        // 1d. Insert into Database
        // -----------------------------
        $sql = "INSERT INTO voters (VoterName, VoterFName, VoterLName, VoterMName, VoterPass) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql); // Prepare statement to prevent SQL injection
        mysqli_stmt_bind_param($stmt, "sssss", $voter_name, $voter_fname, $voter_lname, $voter_mname, $hashed_password);

        // Execute query and check for success
        if (mysqli_stmt_execute($stmt)) {
            $register_success = "Registration successful! Your Voter ID is: " . mysqli_insert_id($conn) . ". You can now login.";
        } else {
            $register_error = "Error during registration: " . mysqli_error($conn);
        }
    }
}
// -----------------------------

// -----------------------------
// 2. Handle Voter Login
// -----------------------------
// Triggered when form is submitted with POST and 'login' button is clicked
if (isset($_POST['login'])) {
    $voter_id = sanitize_input($_POST['voter_id']); // Voter ID input
    $password = sanitize_input($_POST['password']); // Password input

    // -----------------------------
    // 2a. Check if voter exists and is active
    // -----------------------------
    $sql = "SELECT * FROM voters WHERE VoterID = ? AND VoterStat = 'active'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $voter_id); // Bind integer parameter
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $voter = mysqli_fetch_assoc($result); // Fetch voter record as associative array

    if ($voter) {
        // -----------------------------
        // 2b. Verify password
        // -----------------------------
        if (password_verify($password, $voter['VoterPass'])) {
            // -----------------------------
            // 2c. Check if voter has already voted
            // -----------------------------
            if ($voter['Voted'] == 'N') {
                // Set session variables for logged-in voter
                $_SESSION['voter_id'] = $voter['VoterID'];
                $_SESSION['voter_name'] = $voter['VoterName'];
                $_SESSION['success_msg'] = "Login successful! You can now vote.";

                // Redirect to voting page
                redirect('modules/voting.php');
            } else {
                $login_error = "You have already voted!";
            }
        } else {
            $login_error = "Invalid password!";
        }
    } else {
        $login_error = "Voter ID not found or inactive!";
    }
}

// -----------------------------
// 3. Display Success Message
// -----------------------------
if (isset($_SESSION['success_msg'])) {
    echo '<script>alert("' . $_SESSION['success_msg'] . '");</script>';
    unset($_SESSION['success_msg']);
}
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election System - Voter Portal</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .password-field {
            position: relative;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
        }

        .tab-content {
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            padding: 1.5rem;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">Election System - Voter Portal</h4>
                    </div>

                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login"
                                type="button" role="tab" aria-selected="true">
                                Voter Login
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register"
                                type="button" role="tab" aria-selected="false">
                                New Voter Registration
                            </button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="authTabsContent">

                        <!-- Login Tab -->
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <h5 class="text-center mb-4">Voter Login</h5>

                            <?php if (isset($login_error)): ?>
                                <div class="alert alert-danger"><?php echo $login_error; ?></div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="voter_id" class="form-label">Voter ID</label>
                                    <input type="number" class="form-control" id="voter_id" name="voter_id" required
                                        placeholder="Enter your Voter ID">
                                </div>
                                <div class="mb-3 password-field">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="login_password" name="password"
                                        required placeholder="Enter your password">
                                    <span class="password-toggle" onclick="togglePassword('login_password')">👁</span>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>

                        <!-- Register Tab -->
                        <div class="tab-pane fade" id="register" role="tabpanel">
                            <h5 class="text-center mb-4">New Voter Registration</h5>

                            <?php if (isset($register_success)): ?>
                                <div class="alert alert-success"><?php echo $register_success; ?></div>
                            <?php endif; ?>

                            <?php if (isset($register_error)): ?>
                                <div class="alert alert-danger"><?php echo $register_error; ?></div>
                            <?php endif; ?>

                            <form method="POST" action="" id="registerForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">First Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="reg_fname" required
                                                placeholder="Enter first name">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Last Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="reg_lname" required
                                                placeholder="Enter last name">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" name="reg_mname"
                                        placeholder="Enter middle name (optional)">
                                </div>

                                <div class="mb-3 password-field">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="reg_password" id="reg_password"
                                        required minlength="6" placeholder="Enter password (min. 6 characters)">
                                    <span class="password-toggle" onclick="togglePassword('reg_password')">👁</span>
                                    <div class="form-text">Password must be at least 6 characters long</div>
                                </div>

                                <div class="mb-3 password-field">
                                    <label class="form-label">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="reg_confirm_password"
                                        id="reg_confirm_password" required minlength="6"
                                        placeholder="Confirm your password">
                                    <span class="password-toggle"
                                        onclick="togglePassword('reg_confirm_password')">👁</span>
                                </div>

                                <button type="submit" name="register" class="btn btn-success w-100">Register</button>
                            </form>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <small class="text-muted">
                            <a href="index.php" class="text-decoration-none">Admin Dashboard</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
        }

        // Switch to register tab
        function switchToRegister() {
            const registerTab = new bootstrap.Tab(document.getElementById('register-tab'));
            registerTab.show();
        }

        // Switch to login tab
        function switchToLogin() {
            const loginTab = new bootstrap.Tab(document.getElementById('login-tab'));
            loginTab.show();
        }

        // Form validation for registration
        document.getElementById('registerForm')?.addEventListener('submit', function (e) {
            const password = document.getElementById('reg_password').value;
            const confirmPassword = document.getElementById('reg_confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });

        // Auto-switch to register tab if there's register error/success
        <?php if (isset($register_error) || isset($register_success)): ?>
            document.addEventListener('DOMContentLoaded', function () {
                switchToRegister();
            });
        <?php endif; ?>
    </script>
</body>

</html>