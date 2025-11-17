<?php
// ─────────────────────────────────────────────────────────────
// 📌 INCLUDE REQUIRED FILES
// ─────────────────────────────────────────────────────────────
// config.php: Contains database connection + session + timezone
// functions.php: Contains helper functions (ex: sanitize_input(), redirect())
require_once 'config.php';
require_once 'functions.php';



// ─────────────────────────────────────────────────────────────
// 🔐 SECTION 1 — HANDLE VOTER LOGIN
// This block runs only when the login form is submitted.
// The form must contain: <input type="submit" name="login">
// ─────────────────────────────────────────────────────────────
if (isset($_POST['login'])) {

    // 🧹 1. Sanitize user input to prevent SQL injection or malicious entries
    $voter_id = sanitize_input($_POST['voter_id']);
    $password = sanitize_input($_POST['password']);



    // ─────────────────────────────────────────────────────────────
    // 2. Check if the voter exists AND is active
    // Using a prepared statement to prevent SQL injection
    // ─────────────────────────────────────────────────────────────
    $sql = "SELECT * FROM voters WHERE VoterID = ? AND VoterStat = 'active'";
    $stmt = mysqli_prepare($conn, $sql); // Prepare SQL

    // "i" = integer type. (Bind voter_id to the ? placeholder)
    mysqli_stmt_bind_param($stmt, "i", $voter_id);

    // Execute the query
    mysqli_stmt_execute($stmt);

    // Fetch results as associative array
    $result = mysqli_stmt_get_result($stmt);
    $voter = mysqli_fetch_assoc($result); // Either array OR null



    // ─────────────────────────────────────────────────────────────
    // 3. Validate login
    // ─────────────────────────────────────────────────────────────
    if ($voter) {

        // ⚠️ NOTE: This system uses plain text passwords.
        // In a real system you MUST replace this with password_hash() + password_verify()
        if ($password == $voter['VoterPass']) {

            // Check if voter has NOT voted yet
            if ($voter['Voted'] == 'N') {

                // Store voter information inside SESSION
                // These are used to identify who is voting
                $_SESSION['voter_id'] = $voter['VoterID'];
                $_SESSION['voter_name'] = $voter['VoterName'];

                // Optional success message
                $_SESSION['success_msg'] = "Login successful! You can now vote.";

                // Send user to the voting page
                redirect('voting.php');

            } else {
                // Voter already voted
                $_SESSION['error_msg'] = "You have already voted!";
            }

        } else {
            // Password does not match
            $_SESSION['error_msg'] = "Invalid password!";
        }

    } else {
        // Voter ID does not exist OR status is inactive
        $_SESSION['error_msg'] = "Voter ID not found or inactive!";
    }
}



// ─────────────────────────────────────────────────────────────
// 🚪 SECTION 2 — HANDLE LOGOUT
// If the URL contains: login.php?logout=true
// This destroys the session and redirects back to login page.
// ─────────────────────────────────────────────────────────────
if (isset($_GET['logout'])) {

    // Remove all session data (logout effect)
    session_destroy();

    // Redirect user back to login page
    redirect('login.php');
}

?>