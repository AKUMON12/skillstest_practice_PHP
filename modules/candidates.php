<?php
// Include configuration and helper functions
require_once '../includes/config.php';   // config.php usually contains database connection info
require_once '../includes/functions.php'; // functions.php can include reusable functions, e.g., sanitize_input, display_alert

// ---------------------------
// Handle form submissions
// ---------------------------
// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // ---------------------------
    // Add new candidate
    // ---------------------------
    if (isset($_POST['add_candidate'])) {

        // Sanitize input to prevent XSS or SQL injection
        $cand_fname = sanitize_input($_POST['cand_fname']); // Candidate first name
        $cand_lname = sanitize_input($_POST['cand_lname']); // Candidate last name
        $cand_mname = sanitize_input($_POST['cand_mname']); // Candidate middle name (optional)
        $cand_pos_id = sanitize_input($_POST['cand_pos_id']); // Position ID linked to candidate

        // Prepare SQL statement to insert a new candidate
        $sql = "INSERT INTO candidates (CandFName, CandLName, CandMName, CandPosID) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters: "sssi" = string, string, string, integer
        mysqli_stmt_bind_param($stmt, "sssi", $cand_fname, $cand_lname, $cand_mname, $cand_pos_id);

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Candidate added successfully!";
        } else {
            $error_msg = "Error adding candidate!";
        }
    }

    // ---------------------------
    // Update existing candidate
    // ---------------------------
    if (isset($_POST['update_candidate'])) {

        // Sanitize input
        $cand_id = sanitize_input($_POST['cand_id']);        // Candidate ID (hidden field)
        $cand_fname = sanitize_input($_POST['cand_fname']);
        $cand_lname = sanitize_input($_POST['cand_lname']);
        $cand_mname = sanitize_input($_POST['cand_mname']);
        $cand_pos_id = sanitize_input($_POST['cand_pos_id']);
        $cand_stat = sanitize_input($_POST['cand_stat']);    // Candidate status (active/inactive)

        // Prepare SQL statement to update candidate
        $sql = "UPDATE candidates SET CandFName=?, CandLName=?, CandMName=?, CandPosID=?, CandStat=? WHERE CandID=?";
        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters: "sssisi" = string, string, string, integer, string, integer
        mysqli_stmt_bind_param($stmt, "sssisi", $cand_fname, $cand_lname, $cand_mname, $cand_pos_id, $cand_stat, $cand_id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Candidate updated successfully!";
        } else {
            $error_msg = "Error updating candidate!";
        }
    }
}

// ---------------------------
// Fetch all candidates
// ---------------------------
// Join candidates table with positions table to get the position name
$candidates = mysqli_query($conn, "
    SELECT c.*, p.PosName 
    FROM candidates c 
    LEFT JOIN positions p ON c.CandPosID = p.PosID 
    ORDER BY c.CandID
");

// ---------------------------
// Fetch positions for dropdown
// ---------------------------
// Only fetch positions that are 'open' (available for new candidates)
$positions = mysqli_query($conn, "SELECT * FROM positions WHERE PosStat = 'open'");
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates</title>
    <!-- Bootstrap CSS for styling -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Election System</a>
            <div class="navbar-nav">
                <a class="nav-link" href="../index.php">Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Manage Candidates</h2>
        
        <!-- Display success/error messages -->
        <?php
        if (isset($success_msg)) echo display_alert('success', $success_msg); // display_alert() is likely a custom function in functions.php
        if (isset($error_msg)) echo display_alert('danger', $error_msg);
        ?>

        <div class="row">
            <!-- Add Candidate Form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h5>Add New Candidate</h5></div>
                    <div class="card-body">
                        <form method="POST">
                            <!-- Candidate First Name -->
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="cand_fname" required>
                            </div>
                            <!-- Candidate Last Name -->
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="cand_lname" required>
                            </div>
                            <!-- Candidate Middle Name -->
                            <div class="mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="cand_mname">
                            </div>
                            <!-- Candidate Position Dropdown -->
                            <div class="mb-3">
                                <label class="form-label">Position</label>
                                <select class="form-control" name="cand_pos_id" required>
                                    <option value="">Select Position</option>
                                    <?php while($pos = mysqli_fetch_assoc($positions)): ?>
                                        <option value="<?php echo $pos['PosID']; ?>">
                                            <?php echo $pos['PosName']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <button type="submit" name="add_candidate" class="btn btn-primary">Add Candidate</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Candidates Table -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h5>Existing Candidates</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($candidates)): ?>
                                    <tr>
                                        <td><?php echo $row['CandID']; ?></td>
                                        <td><?php echo $row['CandFName'] . ' ' . $row['CandLName']; ?></td>
                                        <td><?php echo $row['PosName']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['CandStat'] == 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($row['CandStat']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <!-- Edit button triggers modal -->
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                    data-bs-target="#editModal<?php echo $row['CandID']; ?>">Edit</button>
                                        </td>
                                    </tr>

                                    <!-- Edit Candidate Modal -->
                                    <div class="modal fade" id="editModal<?php echo $row['CandID']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Candidate</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <!-- Hidden field to keep candidate ID -->
                                                        <input type="hidden" name="cand_id" value="<?php echo $row['CandID']; ?>">
                                                        <!-- Form fields pre-filled with current values -->
                                                        <div class="mb-3">
                                                            <label class="form-label">First Name</label>
                                                            <input type="text" class="form-control" name="cand_fname" 
                                                                   value="<?php echo $row['CandFName']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Last Name</label>
                                                            <input type="text" class="form-control" name="cand_lname" 
                                                                   value="<?php echo $row['CandLName']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Middle Name</label>
                                                            <input type="text" class="form-control" name="cand_mname" 
                                                                   value="<?php echo $row['CandMName']; ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Position</label>
                                                            <select class="form-control" name="cand_pos_id" required>
                                                                <?php 
                                                                $positions2 = mysqli_query($conn, "SELECT * FROM positions");
                                                                while($pos = mysqli_fetch_assoc($positions2)): 
                                                                ?>
                                                                    <option value="<?php echo $pos['PosID']; ?>" 
                                                                        <?php echo $pos['PosID'] == $row['CandPosID'] ? 'selected' : ''; ?>>
                                                                        <?php echo $pos['PosName']; ?>
                                                                    </option>
                                                                <?php endwhile; ?>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-control" name="cand_stat" required>
                                                                <option value="active" <?php echo $row['CandStat'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                                <option value="inactive" <?php echo $row['CandStat'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" name="update_candidate" class="btn btn-primary">Update</button>
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
</body>
</html>
