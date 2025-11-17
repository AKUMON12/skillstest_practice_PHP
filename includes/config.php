<?php
// ─────────────────────────────────────────────
// 🗃️ DATABASE CONFIGURATION CONSTANTS
// ─────────────────────────────────────────────
// These values are used to connect to your MySQL database.
// You only need to edit these if your server username/password changes.
define('DB_SERVER', 'localhost');   // Host name (usually localhost when using XAMPP)
define('DB_USERNAME', 'root');      // MySQL username (default for XAMPP)
define('DB_PASSWORD', '');          // MySQL password (default is empty)
define('DB_NAME', 'election_acctdb'); // Name of your database



// ─────────────────────────────────────────────
// 🔌 CREATE DATABASE CONNECTION
// ─────────────────────────────────────────────
// mysqli_connect() attempts to connect to the MySQL database using the credentials you defined above.
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);


// ─────────────────────────────────────────────
// ❗ CHECK IF CONNECTION FAILED
// ─────────────────────────────────────────────
// If connection fails, $conn becomes false.
// die() stops the script and shows an error message.
if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}



// ─────────────────────────────────────────────
// 🧑‍💻 START A SESSION
// ─────────────────────────────────────────────
// session_start() allows your website to store and access session data.
// Sessions are used for:
//   - Logging in users
//   - Tracking user activity
//   - Storing temporary variables (e.g., userID)
session_start();



// ─────────────────────────────────────────────
// ⏰ SET DEFAULT TIMEZONE
// ─────────────────────────────────────────────
// Ensures that all time/date functions use Philippine time.
date_default_timezone_set('Asia/Manila');

?>