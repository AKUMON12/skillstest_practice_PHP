# Skillstest Election System using PHP (prctce) Overall Instructions

## Step 1: Creating Database

1. **Create the database** using the preloaded database name.
2. **Create each table**: `positions`, `voters`, `candidates`, `votes`.
3. **After creating the first table**, you will be redirected to a page named **[Structure]**.
   - Inside this page, list each information (Name) of each column:
     - **Column Name** = name for each column for each table
     - **Data Type** = `INT` (for numbers), `VARCHAR` (for characters or words), `ENUM` (for enumerations like yes/no)
     - **Length/Values** = 50 (short words), 100 (passwords), 11 (numbers), 20 (additional words), 'Y', 'N' (for enumeration; example only)
     - **Index** = choose below if it is **PRIMARY KEY** (**PRIMARY**), **FOREIGN KEY** (**INDEX**)
     - **A_I** = check this box if it is **PRIMARY KEY**
     - Click **Save**
4. **Define foreign keys (Relationships)**
   - Go to the Structure page for the table with the foreign key
   - Click the **Relation view** tab (or link)
   - Define the constraint for a specific foreign key:
     - Constraint name: `"fk_nameofkey_nameofwheretable"` (e.g., `fk_posID_positions`)
     - Column: name of the primary key (e.g., `posID`)
     - Foreign Key Constraint:
       - Database: your database name
       - Table: source table (e.g., `Positions`)
       - Column: name of the key from the source table (e.g., `PosID`)
     - Click **Save**

### Alternative: SQL Script in phpMyAdmin

```sql
CREATE DATABASE election_acctdb;
USE election_acctdb;

-- Positions Table
CREATE TABLE positions (
PosID INT AUTO_INCREMENT PRIMARY KEY,
PosName VARCHAR(100) NOT NULL,
PosNoOfPositions INT NOT NULL,
PosStat ENUM('open', 'closed') DEFAULT 'open'
);

-- Voters Table
CREATE TABLE voters (
VoterID INT AUTO_INCREMENT PRIMARY KEY,
VoterName VARCHAR(255) NOT NULL,
VoterFName VARCHAR(100) NOT NULL,
VoterLName VARCHAR(100) NOT NULL,
VoterMName VARCHAR(100),
VoterStat ENUM('active', 'inactive') DEFAULT 'active',
Voted ENUM('Y', 'N') DEFAULT 'N',
VoterPass VARCHAR(255) DEFAULT 'password123'
);

-- Candidates Table
CREATE TABLE candidates (
CandID INT AUTO_INCREMENT PRIMARY KEY,
CandPosID INT NOT NULL,
CandFName VARCHAR(100) NOT NULL,
CandLName VARCHAR(100) NOT NULL,
CandMName VARCHAR(100),
CandStat ENUM('active', 'inactive') DEFAULT 'active',
FOREIGN KEY (CandPosID) REFERENCES positions(PosID)
);

-- Votes Table
CREATE TABLE votes (
VoteID INT AUTO_INCREMENT PRIMARY KEY,
VoterID INT NOT NULL,
CandID INT NOT NULL,
PosID INT NOT NULL,
VoteDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (VoterID) REFERENCES voters(VoterID),
FOREIGN KEY (CandID) REFERENCES candidates(CandID),
FOREIGN KEY (PosID) REFERENCES positions(PosID)
);
```

---

## Step 2: File Set-up

### Project Folder Structure

```
YourFamilyName/
├── css/
│   └── bootstrap.min.css
├── js/
│   └── bootstrap.min.js
├── includes/
│   ├── config.php
│   ├── auth.php
│   └── functions.php
├── modules/
│   ├── positions.php
│   ├── candidates.php
│   ├── voters.php
│   ├── voting.php
│   ├── results.php
│   └── winners.php
├── index.php
└── login.php
```

### Download Bootstrap Files

1. Go to [getbootstrap.com](https://getbootstrap.com/docs/5.3/getting-started/download/)
2. Download Bootstrap 5.x compiled CSS and JS
3. Save `bootstrap.min.css` in `css/` folder
4. Save `bootstrap.min.js` in `js/` folder

---

## Step 3: Core Configuration Files

Provide the designated code for each file:

- `includes/config.php`
- `includes/auth.php`
- `includes/functions.php`

# Simple Definitions of the Core PHP Files

## 1. **config.php** - Database Configuration File

**Purpose**: Sets up the connection to your database

**What it does:**

- Connects to MySQL database using XAMPP
- Stores database credentials (server, username, password, database name)
- Starts the session to track logged-in users
- Sets the timezone

**Simple analogy**: Like setting up a phone line to communicate with your database

```php
<?php
// Database connection settings
define('DB_SERVER', 'localhost');      // Where the database is
define('DB_USERNAME', 'root');         // Database username (XAMPP default)
define('DB_PASSWORD', '');             // Database password (XAMPP default is empty)
define('DB_NAME', 'election_acctdb');  // Which database to use

// Create the connection
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check if connection worked
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Start user session (like starting a user's visit)
session_start();

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');
?>
```

## 2. **functions.php** - Helper Functions File

**Purpose**: Contains reusable code snippets used throughout the system

**What it does:**

- **Sanitizes input** - Cleans user data to prevent hacking attempts
- **Checks login status** - Verifies if user is logged in
- **Redirects users** - Sends users to different pages
- **Displays messages** - Shows success/error alerts
- **Gets position names** - Fetches position information

**Simple analogy**: Like a toolbox with common tools all workers can use

```php
<?php
// Include the database connection first
require_once 'config.php';

/**
 * Clean user input to prevent SQL injection attacks
 * Like washing vegetables before cooking
 */
function sanitize_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

/**
 * Check if user is logged in
 * Like checking if someone has an entry ticket
 */
function is_logged_in() {
    return isset($_SESSION['voter_id']);
}

/**
 * Send user to another page
 * Like a GPS redirecting to new destination
 */
function redirect($page) {
    header("Location: $page");
    exit();
}

/**
 * Show notification messages
 * Like a notification popup on your phone
 */
function display_alert($type, $message) {
    return "<div class='alert alert-$type'>$message</div>";
}
?>
```

## 3. **auth.php** - Authentication File

**Purpose**: Handles user login and logout functionality

**What it does:**

- **Processes login** - Checks username/password against database
- **Verifies credentials** - Makes sure login details are correct
- **Manages sessions** - Remembers who is logged in
- **Handles logout** - Ends user session

**Simple analogy**: Like a security guard checking IDs at the entrance

```php
<?php
// Need both database connection and helper functions
require_once 'config.php';
require_once 'functions.php';

/**
 * Handle voter login when form is submitted
 */
if (isset($_POST['login'])) {
    $voter_id = sanitize_input($_POST['voter_id']);    // Clean the input
    $password = sanitize_input($_POST['password']);    // Clean the input

    // Check if voter exists in database
    $sql = "SELECT * FROM voters WHERE VoterID = ? AND VoterStat = 'active'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $voter_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $voter = mysqli_fetch_assoc($result);

    if ($voter) {
        // Verify password matches the hashed version in database
        if (password_verify($password, $voter['VoterPass'])) {
            if ($voter['Voted'] == 'N') {
                // Login successful - store user info in session
                $_SESSION['voter_id'] = $voter['VoterID'];
                $_SESSION['voter_name'] = $voter['VoterName'];
                $_SESSION['success_msg'] = "Login successful!";
                redirect('modules/voting.php');  // Send to voting page
            } else {
                $_SESSION['error_msg'] = "You have already voted!";
            }
        } else {
            $_SESSION['error_msg'] = "Invalid password!";
        }
    } else {
        $_SESSION['error_msg'] = "Voter ID not found!";
    }
}

/**
 * Handle logout when user clicks logout
 */
if (isset($_GET['logout'])) {
    session_destroy();        // End the session
    redirect('login.php');    // Send back to login page
}
?>
```

## Summary in Simple Terms:

- **config.php** = **"The Connector"** - Talks to the database
- **functions.php** = **"The Helper"** - Provides useful tools for everyone
- **auth.php** = **"The Security Guard"** - Handles login/logout security

## How They Work Together:

1. **Any page** → Includes `config.php` to connect to database
2. **Any page** → Includes `functions.php` to use helper functions
3. **Login page** → Includes `auth.php` to handle login process
4. **All together** → Create a secure, functional system

**File Dependencies:**

```
login.php → includes → auth.php → includes → functions.php → includes → config.php
```

This modular approach makes the code organized, secure, and easy to maintain!

### Summary of Functions

| Function Name         | Purpose                                    | Example                                            |
| --------------------- | ------------------------------------------ | -------------------------------------------------- |
| `sanitize_input()`    | Cleans user input to prevent SQL injection | `sanitize_input($_POST['voter_id'])`               |
| `is_logged_in()`      | Checks if the voter is logged in           | `if (!is_logged_in()) redirect('login.php');`      |
| `redirect()`          | Redirects user to another page             | `redirect('dashboard.php')`                        |
| `display_alert()`     | Shows Bootstrap alert messages             | `echo display_alert("danger", "Error occurred!");` |
| `get_position_name()` | Converts PosID → Position Name             | `echo get_position_name(2);`                       |

---

## Step 4: Main Pages

- `index.php` (Admin Dashboard)
- `login.php` (Voter's Login)

---

## Step 5: Management Modules

- `modules/positions.php`
- `modules/candidates.php`
- `modules/voters.php`
- `modules/voting.php`
- `modules/results.php`
- `modules/winne

---

# Important: How to Show Databases in XAMPP

To show databases in XAMPP:

1. Open **XAMPP Control Panel**
2. Click **Shell**
3. Type the following command:

````bash
mysql -u root

Then inside the MySQL prompt, type:

```sql
SHOW DATABASES;
````

## Below is a clean **table-formatted cheat sheet** of the most commonly used **MySQL/MariaDB database commands** — from scanning databases → selecting one → listing tables → viewing table structure → showing table data.

---

# ✅ **MySQL Command**

### **1. Database-Level Commands**

| Purpose                          | Command                          |
| -------------------------------- | -------------------------------- |
| Show all databases               | `SHOW DATABASES;`                |
| Select (enter) a database        | `USE database_name;`             |
| Show currently selected database | `SELECT DATABASE();`             |
| Create a new database            | `CREATE DATABASE database_name;` |
| Delete a database                | `DROP DATABASE database_name;`   |

---

### **2. Table-Level Commands**

| Purpose                              | Command                                         |
| ------------------------------------ | ----------------------------------------------- |
| Show all tables in selected database | `SHOW TABLES;`                                  |
| Create a new table                   | `CREATE TABLE table_name (column definitions);` |
| Show table structure                 | `DESCRIBE table_name;`                          |
| or                                   | `SHOW COLUMNS FROM table_name;`                 |
| Show table creation SQL              | `SHOW CREATE TABLE table_name;`                 |
| Delete a table                       | `DROP TABLE table_name;`                        |
| Rename a table                       | `RENAME TABLE old_name TO new_name;`            |

---

### **3. Viewing Data in Tables**

| Purpose                    | Command                                     |
| -------------------------- | ------------------------------------------- |
| Show all data from a table | `SELECT * FROM table_name;`                 |
| Show only certain columns  | `SELECT column1, column2 FROM table_name;`  |
| Show only first N rows     | `SELECT * FROM table_name LIMIT N;`         |
| Show filtered data         | `SELECT * FROM table_name WHERE condition;` |
| Count rows                 | `SELECT COUNT(*) FROM table_name;`          |

---

### **4. Manipulating Data**

| Purpose         | Command                                                    |
| --------------- | ---------------------------------------------------------- |
| Insert a row    | `INSERT INTO table_name (col1, col2) VALUES (val1, val2);` |
| Update data     | `UPDATE table_name SET col1=value WHERE condition;`        |
| Delete rows     | `DELETE FROM table_name WHERE condition;`                  |
| Delete all rows | `TRUNCATE TABLE table_name;`                               |

---

# 🎯 **Most common workflow (in order)**

### **Step 1 — Show all databases**

```
SHOW DATABASES;
```

### **Step 2 — Select a database**

```
USE election_system_db;
```

### **Step 3 — Show the tables in that database**

```
SHOW TABLES;
```

### **Step 4 — Show table structure**

```
DESCRIBE table_name;
```

### **Step 5 — Show data inside a table**

```
SELECT * FROM table_name;
```

---
