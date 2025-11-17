<?php
// ─────────────────────────────────────────────────────────────
// 📌 INCLUDE CONFIG FILE
// ─────────────────────────────────────────────────────────────
// config.php contains:
//   - Database connection ($conn)
//   - Session start
//   - Timezone settings
require_once 'config.php';



// ─────────────────────────────────────────────────────────────
// 🧹 FUNCTION 1: Sanitize user input
// PURPOSE: Protect your database from SQL injection + harmful characters
// HOW IT WORKS:
//   - trim() removes spaces at the beginning/end
//   - mysqli_real_escape_string() escapes dangerous SQL characters
// ─────────────────────────────────────────────────────────────
function sanitize_input($data)
{
    global $conn; // Use the database connection defined in config.php
    return mysqli_real_escape_string($conn, trim($data));
}



// ─────────────────────────────────────────────────────────────
// 🔐 FUNCTION 2: Check if voter is logged in
// PURPOSE: Used to prevent unauthorized page access
// RETURNS:
//   - true  → user is logged in
//   - false → user is NOT logged in
// This checks if voter_id exists in session storage
// ─────────────────────────────────────────────────────────────
function is_logged_in()
{
    return isset($_SESSION['voter_id']);
}



// ─────────────────────────────────────────────────────────────
// 🔁 FUNCTION 3: Redirect user to another page
// PURPOSE: Change the current page using PHP
// EXAMPLE:
//     redirect('login.php');
// NOTES:
// - header() MUST be called before any HTML output
// - exit() stops the script to ensure redirect happens
// ─────────────────────────────────────────────────────────────
function redirect($page)
{
    header("Location: $page");
    exit();
}



// ─────────────────────────────────────────────────────────────
// 🔔 FUNCTION 4: Display Bootstrap alert message
// PURPOSE: Create reusable UI messages (success/error/warning/info)
// PARAMETERS:
//   $type    → Bootstrap alert type (success, danger, warning, info)
//   $message → The message to display
// RETURNS: HTML string for an alert box
// Example usage:
//   echo display_alert("success", "Saved successfully!");
// ─────────────────────────────────────────────────────────────
function display_alert($type, $message)
{
    return "
        <div class='alert alert-$type alert-dismissible fade show' role='alert'>
            $message
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
}



// ─────────────────────────────────────────────────────────────
// 🏷️ FUNCTION 5: Get position name using Position ID
// PURPOSE: Convert numeric PosID → readable position name
// HOW IT WORKS:
//   - Uses prepared statements (secure)
//   - Queries the 'positions' table
//   - Returns the position name or "Unknown Position" if missing
// Example:
//     get_position_name(3) → "Vice President"
// ─────────────────────────────────────────────────────────────
function get_position_name($pos_id)
{
    global $conn;

    $sql = "SELECT PosName FROM positions WHERE PosID = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // 'i' = integer data type
    mysqli_stmt_bind_param($stmt, "i", $pos_id);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the row (or null if no result)
    $row = mysqli_fetch_assoc($result);

    // If PosName exists → return it
    // Else → return fallback label
    return $row['PosName'] ?? 'Unknown Position';
}

?>