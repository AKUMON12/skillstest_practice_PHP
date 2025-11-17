<?php
// Include configuration and helper functions
require_once 'includes/config.php';  // Contains database connection and other settings
require_once 'includes/functions.php';  // Contains reusable helper functions

// -----------------------------
// 1. Check Admin Login
// -----------------------------
// Here, $is_admin is hardcoded to true for demonstration.
// In a real system, you would check user session or authentication to confirm admin access.
$is_admin = true;
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election System - Admin Dashboard</title>
    <!-- Bootstrap CSS for styling -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <!-- Brand / Home Link -->
            <a class="navbar-brand" href="index.php">Election System</a>
            <div class="navbar-nav">
                <!-- Navigation Link for Voter Login -->
                <a class="nav-link" href="login.php">Voter Login</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page Heading -->
        <h2>Admin Dashboard</h2>

        <!-- -----------------------------
             Dashboard Cards / Modules
             -----------------------------
             Using Bootstrap grid system:
             - Each module is a card in a 4-column layout (col-md-3)
             - Each card represents a functionality: Positions, Candidates, Voters, Results
        -->
        <div class="row mt-4">
            <!-- Positions Management Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Positions</h5>
                        <p class="card-text">Manage election positions</p>
                        <!-- Button links to positions management page -->
                        <a href="modules/positions.php" class="btn btn-light">Manage</a>
                    </div>
                </div>
            </div>

            <!-- Candidates Management Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Candidates</h5>
                        <p class="card-text">Manage candidates</p>
                        <a href="modules/candidates.php" class="btn btn-light">Manage</a>
                    </div>
                </div>
            </div>

            <!-- Voters Management Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Voters</h5>
                        <p class="card-text">Manage voter records</p>
                        <a href="modules/voters.php" class="btn btn-light">Manage</a>
                    </div>
                </div>
            </div>

            <!-- Election Results Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Results</h5>
                        <p class="card-text">View election results</p>
                        <a href="modules/results.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS for interactive components -->
    <script src="js/bootstrap.min.js"></script>
</body>

</html>