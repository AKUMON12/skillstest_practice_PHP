<?php
// Include configuration and helper functions
// config.php usually contains database connection details
// functions.php may contain reusable functions like sanitize_input(), display_alert(), etc.
require_once '../includes/config.php';
require_once '../includes/functions.php';

// -------------------------------
// Handle form submissions
// -------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the "Add Voter" form was submitted
    if (isset($_POST['add_voter'])) {
        // Sanitize input to prevent SQL injection and remove unwanted characters
        $voter_fname = sanitize_input($_POST['voter_fname']);
        $voter_lname = sanitize_input($_POST['voter_lname']);
        $voter_mname = sanitize_input($_POST['voter_mname']);

        // Combine names into full name
        $voter_name = $voter_fname . ' ' . $voter_lname;
        if (!empty($voter_mname)) {
            $voter_name = $voter_fname . ' ' . $voter_mname . ' ' . $voter_lname;
        }

        // Prepare SQL statement to insert new voter
        // Use prepared statements to prevent SQL injection
        $sql = "INSERT INTO voters (VoterName, VoterFName, VoterLName, VoterMName) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters: "ssss" means 4 strings
        mysqli_stmt_bind_param($stmt, "ssss", $voter_name, $voter_fname, $voter_lname, $voter_mname);

        // Execute statement and set success or error message
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Voter added successfully!";
        } else {
            $error_msg = "Error adding voter!";
        }
    }

    // Check if the "Update Voter" form was submitted
    if (isset($_POST['update_voter'])) {
        $voter_id = sanitize_input($_POST['voter_id']);
        $voter_fname = sanitize_input($_POST['voter_fname']);
        $voter_lname = sanitize_input($_POST['voter_lname']);
        $voter_mname = sanitize_input($_POST['voter_mname']);
        $voter_stat = sanitize_input($_POST['voter_stat']);

        // Optional: password field may be left blank
        $voter_password = !empty($_POST['voter_password']) ? sanitize_input($_POST['voter_password']) : null;

        // Combine names into full name
        $voter_name = $voter_fname . ' ' . $voter_lname;
        if (!empty($voter_mname)) {
            $voter_name = $voter_fname . ' ' . $voter_mname . ' ' . $voter_lname;
        }

        // If password is provided, hash it before updating
        if ($voter_password !== null) {
            $hashed_password = password_hash($voter_password, PASSWORD_DEFAULT); // Securely hash password
            $sql = "UPDATE voters SET VoterName=?, VoterFName=?, VoterLName=?, VoterMName=?, VoterStat=?, VoterPass=? WHERE VoterID=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssssi", $voter_name, $voter_fname, $voter_lname, $voter_mname, $voter_stat, $hashed_password, $voter_id);
        } else {
            // Update without changing password
            $sql = "UPDATE voters SET VoterName=?, VoterFName=?, VoterLName=?, VoterMName=?, VoterStat=? WHERE VoterID=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssi", $voter_name, $voter_fname, $voter_lname, $voter_mname, $voter_stat, $voter_id);
        }

        // Execute statement and set success or error message
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Voter updated successfully!";
        } else {
            $error_msg = "Error updating voter!";
        }
    }
}

// -------------------------------
// Fetch all voters from the database
// -------------------------------
$voters = mysqli_query($conn, "SELECT * FROM voters ORDER BY VoterID");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Voters</title>
    <!-- Bootstrap CSS for styling -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Styling for password input toggle */
        .password-field {
            position: relative;
        }

        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            font-size: 18px;
            user-select: none;
        }

        .password-field input {
            padding-right: 40px;
        }
    </style>
</head>

<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Election System</a>
            <div class="navbar-nav">
                <a class="nav-link" href="../index.php">Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Manage Voters</h2>

        <!-- Display success or error messages -->
        <?php
        if (isset($success_msg))
            echo display_alert('success', $success_msg);
        if (isset($error_msg))
            echo display_alert('danger', $error_msg);
        ?>

        <div class="row">
            <!-- Add new voter form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Add New Voter</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <!-- First Name input -->
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="voter_fname" required>
                            </div>

                            <!-- Last Name input -->
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="voter_lname" required>
                            </div>

                            <!-- Middle Name input (optional) -->
                            <div class="mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="voter_mname">
                            </div>

                            <!-- Submit button -->
                            <button type="submit" name="add_voter" class="btn btn-primary">Add Voter</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- List of existing voters -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Existing Voters</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Voted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Loop through voters and display them -->
                                    <?php while ($row = mysqli_fetch_assoc($voters)): ?>
                                        <tr>
                                            <td><?php echo $row['VoterID']; ?></td>
                                            <td><?php echo $row['VoterName']; ?></td>
                                            <td>
                                                <span
                                                    class="badge bg-<?php echo $row['VoterStat'] == 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($row['VoterStat']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-<?php echo $row['Voted'] == 'Y' ? 'warning' : 'info'; ?>">
                                                    <?php echo $row['Voted']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <!-- Edit button triggers modal -->
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?php echo $row['VoterID']; ?>">Edit</button>
                                            </td>
                                        </tr>

                                        <!-- Edit voter modal -->
                                        <div class="modal fade" id="editModal<?php echo $row['VoterID']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Voter</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <!-- Hidden input to track voter ID -->
                                                            <input type="hidden" name="voter_id"
                                                                value="<?php echo $row['VoterID']; ?>">

                                                            <!-- First Name -->
                                                            <div class="mb-3">
                                                                <label class="form-label">First Name</label>
                                                                <input type="text" class="form-control" name="voter_fname"
                                                                    value="<?php echo $row['VoterFName']; ?>" required>
                                                            </div>

                                                            <!-- Last Name -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Last Name</label>
                                                                <input type="text" class="form-control" name="voter_lname"
                                                                    value="<?php echo $row['VoterLName']; ?>" required>
                                                            </div>

                                                            <!-- Middle Name -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Middle Name</label>
                                                                <input type="text" class="form-control" name="voter_mname"
                                                                    value="<?php echo $row['VoterMName']; ?>">
                                                            </div>

                                                            <!-- Password (optional) -->
                                                            <div class="mb-3 password-field">
                                                                <label class="form-label">Password <span
                                                                        class="text-muted">(leave blank to keep
                                                                        current)</span></label>
                                                                <input type="password" class="form-control"
                                                                    name="voter_password"
                                                                    id="voter_password_<?php echo $row['VoterID']; ?>"
                                                                    placeholder="Enter new password to change">
                                                                <span class="password-toggle"
                                                                    onclick="togglePassword('voter_password_<?php echo $row['VoterID']; ?>')"
                                                                    title="Toggle password visibility">👁️</span>
                                                                <div class="form-text">Password must be at least 6
                                                                    characters long if provided</div>
                                                            </div>

                                                            <!-- Status -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select class="form-control" name="voter_stat" required>
                                                                    <option value="active" <?php echo $row['VoterStat'] == 'active' ? 'selected' : ''; ?>>
                                                                        Active</option>
                                                                    <option value="inactive" <?php echo $row['VoterStat'] == 'inactive' ? 'selected' : ''; ?>>
                                                                        Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" name="update_voter"
                                                                class="btn btn-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../js/bootstrap.min.js"></script>

    <script>
        // Toggle password visibility function
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
        }

        // Validate password length when updating voter
        document.addEventListener('submit', function (e) {
            if (e.target.method === 'POST' && e.target.querySelector('[name="update_voter"]')) {
                const passwordField = e.target.querySelector('[name="voter_password"]');
                if (passwordField && passwordField.value && passwordField.value.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long!');
                    return false;
                }
            }
        });
    </script>
</body>

</html>