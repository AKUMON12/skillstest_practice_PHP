<?php
// Include configuration and helper functions
require_once '../includes/config.php';  // Contains database connection, constants, and session start
require_once '../includes/functions.php'; // Common helper functions (like redirect, display_alert, etc.)

// -----------------------------
// 1. Fetch Election Results
// -----------------------------
// This SQL query retrieves election results per position and candidate
$results = mysqli_query($conn, "
    SELECT 
        p.PosID,                                                    -- Position ID
        p.PosName,                                                  -- Position Name
        p.PosNoOfPositions,                                         -- Number of winners allowed for the position
        c.CandID,                                                   -- Candidate ID
        CONCAT(c.CandFName, ' ', c.CandLName) as CandidateName,     -- Full name of candidate
        COUNT(v.VoteID) as TotalVotes,                              -- Total votes received by this candidate
        (SELECT COUNT(DISTINCT VoterID) FROM votes WHERE PosID = p.PosID) as TotalVoters
                                                                    -- Total number of voters who voted for this position
    FROM positions p
    LEFT JOIN candidates c ON p.PosID = c.CandPosID                 -- Join candidates to their positions
    LEFT JOIN votes v ON c.CandID = v.CandID                        -- Join votes to candidates
    WHERE p.PosStat = 'open'                                        -- Only include positions that are currently open
    GROUP BY p.PosID, c.CandID                                      -- Group by position and candidate for aggregation
    ORDER BY p.PosID, TotalVotes DESC                               -- Sort by position and highest votes first
");
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap for styling -->
</head>

<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Election System</a>
            <div class="navbar-nav">
                <a class="nav-link" href="../index.php">Dashboard</a>
                <a class="nav-link" href="winners.php">View Winners</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Election Results</h2>

        <?php
        $current_position = ''; // Track the current position while looping through results
        
        // Loop through each row of results
        while ($row = mysqli_fetch_assoc($results)):

            // Check if we're on a new position
            if ($current_position != $row['PosName']):
                // Close previous table if it exists
                if ($current_position != ''):
                    echo '</tbody></table></div></div>';
                endif;

                // Update current position
                $current_position = $row['PosName'];
                ?>
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><?php echo $row['PosName']; ?></h4> <!-- Show position name -->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Candidate</th>
                                        <th>Total Votes</th>
                                        <th>Voting Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php endif; ?>

                                <tr>
                                    <td><?php echo $row['CandidateName']; ?></td>
                                    <td><?php echo $row['TotalVotes']; ?></td>
                                    <td>
                                        <?php
                                        // Calculate voting percentage
                                        if ($row['TotalVoters'] > 0) {
                                            $percentage = ($row['TotalVotes'] / $row['TotalVoters']) * 100;
                                            echo number_format($percentage, 2) . '%'; // Format to 2 decimal places
                                        } else {
                                            echo '0%'; // No votes cast for this position
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.min.js"></script>
</body>

</html>