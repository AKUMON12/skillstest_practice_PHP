<?php
// Include configuration and helper functions
require_once '../includes/config.php';   // Contains database connection and session setup
require_once '../includes/functions.php'; // Helper functions like redirect(), display_alert(), etc.

// -----------------------------
// 1. Fetch Election Winners
// -----------------------------
// This query retrieves the winning candidate(s) for each position. 
// Since MySQL (pre-8.0) does not support window functions natively, we simulate it using a subquery.

$winners = mysqli_query($conn, "
    SELECT 
        p.PosID,                                                    -- Position ID
        p.PosName,                                                  -- Position name
        p.PosNoOfPositions,                                         -- Number of winners allowed for this position
        c.CandID,                                                   -- Candidate ID
        CONCAT(c.CandFName, ' ', c.CandLName) as CandidateName,     -- Full candidate name
        COUNT(v.VoteID) as TotalVotes,                              -- Total votes received by this candidate
        (SELECT COUNT(*) FROM votes v2 
         WHERE v2.PosID = p.PosID AND v2.CandID = c.CandID) as VoteCount
         -- VoteCount is calculated again to use in HAVING clause for filtering winners
    FROM positions p
    LEFT JOIN candidates c ON p.PosID = c.CandPosID                 -- Join candidates to their positions
    LEFT JOIN votes v ON c.CandID = v.CandID                        -- Join votes to candidates
    WHERE p.PosStat = 'open'                                        -- Only include positions that are open
    GROUP BY p.PosID, c.CandID                                      -- Aggregate votes per candidate per position
    HAVING VoteCount = (
        -- Subquery to find the maximum votes for each position
        SELECT COUNT(v3.VoteID) 
        FROM candidates c3 
        LEFT JOIN votes v3 ON c3.CandID = v3.CandID 
        WHERE c3.CandPosID = p.PosID 
        GROUP BY c3.CandID 
        ORDER BY COUNT(v3.VoteID) DESC 
        LIMIT 1
    )
    ORDER BY p.PosID, TotalVotes DESC                               -- Sort by position and votes
");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Winners</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap for styling -->
</head>

<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Election System</a>
            <div class="navbar-nav">
                <a class="nav-link" href="../index.php">Dashboard</a>
                <a class="nav-link" href="results.php">View Results</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Election Winners</h2>

        <!-- Card container for winners -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Official Winners</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <!-- Table to display winners -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Elective Position</th>
                                <th>Winner</th>
                                <th>Total Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through winners and display each -->
                            <?php while ($row = mysqli_fetch_assoc($winners)): ?>
                                <tr>
                                    <td><?php echo $row['PosName']; ?></td> <!-- Position name -->
                                    <td><?php echo $row['CandidateName']; ?></td> <!-- Winner name -->
                                    <td><?php echo $row['TotalVotes']; ?></td> <!-- Votes received -->
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