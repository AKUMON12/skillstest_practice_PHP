<?php
// Include configuration and helper functions
require_once '../includes/config.php';  // Database connection, constants, and session start
require_once '../includes/functions.php'; // Common functions like sanitize_input, redirect, is_logged_in, display_alert

// -----------------------------
// 1. Access Control
// -----------------------------
// Check if the voter is logged in
if (!is_logged_in()) { // Function likely checks if a session variable for login exists
    $_SESSION['error_msg'] = "Please login first!"; // Set an error message in session
    redirect('../login.php'); // Redirect user to login page
}

// Get the voter ID from session
$voter_id = $_SESSION['voter_id'];

// -----------------------------
// 2. Prevent Multiple Voting
// -----------------------------
// Check if voter has already voted
$check_voted = mysqli_query($conn, "SELECT Voted FROM voters WHERE VoterID = $voter_id"); // Query the 'voters' table
$voter_data = mysqli_fetch_assoc($check_voted); // Fetch single row as associative array

if ($voter_data['Voted'] == 'Y') { // 'Y' means the voter has already cast their vote
    $_SESSION['error_msg'] = "You have already voted!";
    redirect('../login.php');
}

// -----------------------------
// 3. Handle Vote Submission
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_vote'])) {
    $has_error = false; // Flag to track validation errors

    // Fetch all positions that are currently open
    $positions = mysqli_query($conn, "SELECT * FROM positions WHERE PosStat = 'open'");

    while ($position = mysqli_fetch_assoc($positions)) {
        $pos_id = $position['PosID']; // Position ID
        $max_votes = $position['PosNoOfPositions']; // Maximum allowed candidates for this position

        // Check if the user submitted votes for this position
        if (isset($_POST["position_$pos_id"])) {
            $selected_candidates = $_POST["position_$pos_id"]; // Array of selected candidate IDs

            // Validation: check if number of selected candidates exceeds allowed
            if (count($selected_candidates) > $max_votes) {
                $error_msg = "You can only vote for $max_votes candidate(s) for " . $position['PosName'];
                $has_error = true;
                break; // Stop processing further positions
            }

            // Insert votes into the 'votes' table
            foreach ($selected_candidates as $cand_id) {
                $sql = "INSERT INTO votes (VoterID, CandID, PosID) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql); // Prepared statement to prevent SQL injection
                mysqli_stmt_bind_param($stmt, "iii", $voter_id, $cand_id, $pos_id); // Bind variables as integers
                mysqli_stmt_execute($stmt); // Execute insertion
            }
        }
    }

    if (!$has_error) {
        // Mark voter as having voted
        mysqli_query($conn, "UPDATE voters SET Voted = 'Y' WHERE VoterID = $voter_id");

        $_SESSION['success_msg'] = "Thank you for voting!"; // Feedback message
        session_destroy(); // End session for security
        redirect('../login.php'); // Redirect after voting
    }
}

// -----------------------------
// 4. Fetch Open Positions & Candidate Counts
// -----------------------------
$positions = mysqli_query($conn, "
    SELECT p.*, 
           (SELECT COUNT(*) FROM candidates c WHERE c.CandPosID = p.PosID AND c.CandStat = 'active') as candidate_count
    FROM positions p 
    WHERE p.PosStat = 'open'
    ORDER BY p.PosID
");
?>




<!-- Bootstrap layout -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Booth</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Election System - Voting Booth</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, <?php echo $_SESSION['voter_name']; ?> <!-- Display voter name -->
                </span>
                <a class="nav-link" href="?logout=true">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Cast Your Vote</h2>
        <p class="text-muted">Please select your candidates for each position. You may select up to the number of vacant positions.</p>
        
        <!-- Display error messages -->
        <?php if (isset($error_msg)) echo display_alert('danger', $error_msg); ?>
        
        <form method="POST" action="">
            <?php while($position = mysqli_fetch_assoc($positions)): ?>
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <?php echo $position['PosName']; ?>
                        <small class="text-muted">(Select up to <?php echo $position['PosNoOfPositions']; ?> candidate(s))</small>
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    // Fetch active candidates for this position
                    $candidates = mysqli_query($conn, "
                        SELECT * FROM candidates 
                        WHERE CandPosID = {$position['PosID']} AND CandStat = 'active'
                        ORDER BY CandLName, CandFName
                    ");
                    
                    if (mysqli_num_rows($candidates) > 0):
                        $max_selections = $position['PosNoOfPositions'];
                    ?>
                        <div class="row">
                            <?php while($candidate = mysqli_fetch_assoc($candidates)): ?>
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input position-<?php echo $position['PosID']; ?>" 
                                           type="<?php echo $max_selections > 1 ? 'checkbox' : 'radio'; ?>"
                                           name="position_<?php echo $position['PosID']; ?>[]"
                                           value="<?php echo $candidate['CandID']; ?>"
                                           id="cand_<?php echo $candidate['CandID']; ?>"
                                           <?php echo $max_selections > 1 ? '' : 'required'; ?>>
                                    <label class="form-check-label" for="cand_<?php echo $candidate['CandID']; ?>">
                                        <?php echo $candidate['CandFName'] . ' ' . $candidate['CandLName']; ?>
                                    </label>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php if ($max_selections > 1): ?>
                        <small class="text-muted">You may select up to <?php echo $max_selections; ?> candidates</small>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <p class="text-muted">No candidates available for this position.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
            
            <div class="card">
                <div class="card-body text-center">
                    <button type="submit" name="submit_vote" class="btn btn-success btn-lg"
                            onclick="return confirm('Are you sure you want to submit your vote? This action cannot be undone.')">
                        Submit Vote
                    </button>
                    <a href="?logout=true" class="btn btn-secondary btn-lg">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <script src="../js/bootstrap.min.js"></script>
    <script>
        // -----------------------------
        // 5. Limit selections for checkboxes dynamically
        // -----------------------------
        document.addEventListener('DOMContentLoaded', function() {
            <?php
            $positions2 = mysqli_query($conn, "SELECT PosID, PosNoOfPositions FROM positions WHERE PosStat = 'open'");
            while($pos = mysqli_fetch_assoc($positions2)) {
                if ($pos['PosNoOfPositions'] > 1) {
                    echo "
                    const checkboxes{$pos['PosID']} = document.querySelectorAll('.position-{$pos['PosID']}');
                    const maxChecked{$pos['PosID']} = {$pos['PosNoOfPositions']};
                    
                    checkboxes{$pos['PosID']}.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const checkedCount = document.querySelectorAll('.position-{$pos['PosID']}:checked').length;
                            if (checkedCount > maxChecked{$pos['PosID']}) {
                                this.checked = false;
                                alert('You can only select up to ' + maxChecked{$pos['PosID']} + ' candidates for this position.');
                            }
                        });
                    });
                    ";
                }
            }
            ?>
        });
    </script>
</body>
</html>
