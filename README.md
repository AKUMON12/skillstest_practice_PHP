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
- `modules/winners.php`
