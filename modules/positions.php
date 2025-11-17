<?php
// ─────────────────────────────────────────────────────────────
// 📌 INCLUDE NECESSARY FILES
// config.php   → database connection, session start, timezone
// functions.php → reusable functions (sanitize_input, display_alert, redirect, etc.)
// ─────────────────────────────────────────────────────────────
require_once '../includes/config.php';
require_once '../includes/functions.php';


// ─────────────────────────────────────────────────────────────
// 🔄 HANDLE FORM SUBMISSIONS
// Check if the request method is POST (form submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // ----------------------
    // ➕ ADD NEW POSITION
    // ----------------------
    if (isset($_POST['add_position'])) {
        // Sanitize user input to prevent SQL injection
        $pos_name = sanitize_input($_POST['pos_name']);              // Position name
        $no_of_positions = sanitize_input($_POST['no_of_positions']); // Number of available positions

        // SQL: Insert new position into the "positions" table
        $sql = "INSERT INTO positions (PosName, PosNoOfPositions) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql); // Use prepared statement for security
        mysqli_stmt_bind_param($stmt, "si", $pos_name, $no_of_positions); // "s"=string, "i"=integer

        // Execute query and set success/error message
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Position added successfully!";
        } else {
            $error_msg = "Error adding position!";
        }
    }

    // ----------------------
    // ✏️ UPDATE EXISTING POSITION
    // ----------------------
    if (isset($_POST['update_position'])) {
        $pos_id = sanitize_input($_POST['pos_id']);                 // ID of position to update
        $pos_name = sanitize_input($_POST['pos_name']);             // Updated name
        $no_of_positions = sanitize_input($_POST['no_of_positions']); // Updated number of positions
        $pos_stat = sanitize_input($_POST['pos_stat']);             // Updated status (open/closed)

        // SQL: Update position details
        $sql = "UPDATE positions SET PosName = ?, PosNoOfPositions = ?, PosStat = ? WHERE PosID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sisi", $pos_name, $no_of_positions, $pos_stat, $pos_id);

        // Execute query and set success/error message
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Position updated successfully!";
        } else {
            $error_msg = "Error updating position!";
        }
    }
}


// ─────────────────────────────────────────────────────────────
// 🔍 FETCH ALL POSITIONS FROM DATABASE
// Display them in a table, ordered by PosID
$positions = mysqli_query($conn, "SELECT * FROM positions ORDER BY PosID");
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Positions</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap for styling -->
</head>

<body>
    <!-- ─────────────────────────────────────────────
         🧭 NAVIGATION BAR
         Contains a link to dashboard and brand name
    ───────────────────────────────────────────── -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Election System</a>
            <div class="navbar-nav">
                <a class="nav-link" href="../index.php">Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Manage Positions</h2>

        <!-- Display alert messages if any -->
        <?php
        if (isset($success_msg))
            echo display_alert('success', $success_msg); // green alert
        if (isset($error_msg))
            echo display_alert('danger', $error_msg);   // red alert
        ?>

        <div class="row">
            <!-- ─────────────────────────────────────────────
                 ➕ ADD NEW POSITION FORM
            ───────────────────────────────────────────── -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Add New Position</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Position Name</label>
                                <input type="text" class="form-control" name="pos_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Number of Positions</label>
                                <input type="number" class="form-control" name="no_of_positions" required>
                            </div>
                            <button type="submit" name="add_position" class="btn btn-primary">Add Position</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ─────────────────────────────────────────────
                 ✏️ EXISTING POSITIONS TABLE + EDIT MODAL
            ───────────────────────────────────────────── -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Existing Positions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Position Name</th>
                                        <th>No. of Positions</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Loop through all positions fetched from database -->
                                    <?php while ($row = mysqli_fetch_assoc($positions)): ?>
                                        <tr>
                                            <td><?php echo $row['PosID']; ?></td>
                                            <td><?php echo $row['PosName']; ?></td>
                                            <td><?php echo $row['PosNoOfPositions']; ?></td>
                                            <td>
                                                <!-- Badge color based on status -->
                                                <span
                                                    class="badge bg-<?php echo $row['PosStat'] == 'open' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($row['PosStat']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <!-- Button triggers modal to edit this position -->
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?php echo $row['PosID']; ?>">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- ─────────────────────────────────────────────
                                             Edit Modal for updating position
                                             Uses Bootstrap modal
                                        ───────────────────────────────────────────── -->
                                        <div class="modal fade" id="editModal<?php echo $row['PosID']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Position</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <!-- Hidden field to store position ID -->
                                                            <input type="hidden" name="pos_id"
                                                                value="<?php echo $row['PosID']; ?>">

                                                            <div class="mb-3">
                                                                <label class="form-label">Position Name</label>
                                                                <input type="text" class="form-control" name="pos_name"
                                                                    value="<?php echo $row['PosName']; ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Number of Positions</label>
                                                                <input type="number" class="form-control"
                                                                    name="no_of_positions"
                                                                    value="<?php echo $row['PosNoOfPositions']; ?>"
                                                                    required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select class="form-control" name="pos_stat" required>
                                                                    <option value="open" <?php echo $row['PosStat'] == 'open' ? 'selected' : ''; ?>>Open</option>
                                                                    <option value="closed" <?php echo $row['PosStat'] == 'closed' ? 'selected' : ''; ?>>
                                                                        Closed</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" name="update_position"
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

    <!-- Bootstrap JS for modals -->
    <script src="../js/bootstrap.min.js"></script>
</body>

</html>