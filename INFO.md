# Bootstrap 5 Utility Classes Reference

| Category                 | Class / Pattern                                                                          | Description & Examples                                                                                                                                                                                                                                                                          |
| :----------------------- | :--------------------------------------------------------------------------------------- | :---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Spacing**              | `{property}{sides}-{size}`                                                               | Properties: `m` (margin), `p` (padding). Sides: `t`, `b`, `s` (start), `e` (end), `x`, `y`, _(blank)_ (all). Sizes: `0`, `1`, `2`, `3`, `4`, `5`, `auto`. Examples: `mt-3`, `px-0`, `my-auto`.                                                                                                  |
| **Display**              | `d-{value}` / `d-{breakpoint}-{value}`                                                   | Controls `display`. Values: `none`, `inline`, `inline-block`, `block`, `grid`, `table`, `table-row`, `table-cell`, `flex`, `inline-flex`. Examples: `d-none`, `d-md-block`, `d-lg-flex`.                                                                                                        |
| **Flexbox**              | `flex-{direction}` / `justify-content-{value}` / `align-items-{value}` / `order-{value}` | Directions: `row`, `row-reverse`, `column`, `column-reverse`. Justify: `start`, `end`, `center`, `between`, `around`, `evenly`. Align: `start`, `end`, `center`, `baseline`, `stretch`. Order: `first`, `last`, `0`–`5`. Examples: `justify-content-center`, `align-items-start`, `order-md-1`. |
| **Color & Background**   | `text-{color}` / `bg-{color}`                                                            | Context colors: `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `light`, `dark`, `body`, `muted`, `white`, `black-50`, `white-50`. Examples: `text-danger`, `bg-warning`, `bg-light`.                                                                                           |
| **Typography**           | `text-{alignment}` / `fw-{weight}` / `fs-{size}`                                         | Alignment: `start`, `center`, `end`. Weight: `light`, `normal`, `bold`, `bolder` (e.g., `fw-bold`). Size: `fs-1` (largest) to `fs-6` (smallest). Others: `text-lowercase`, `text-uppercase`, `text-decoration-underline`, `lh-1`, `lh-base`. Examples: `text-center`, `fw-bold`, `fs-4`.        |
| **Sizing**               | `w-{size}` / `h-{size}` / `mw-{value}`                                                   | Sizes: `25`, `50`, `75`, `100`, `auto`. Examples: `w-50`, `h-100`, `mw-100` (max-width: 100%).                                                                                                                                                                                                  |
| **Borders**              | `border` / `border-{side}` / `border-{color}` / `rounded-{type}`                         | Sides: `border-top`, `border-end`, `border-bottom`, `border-start`. Radius: `rounded`, `rounded-top`, `rounded-circle`, `rounded-pill`, `rounded-0`. Examples: `border border-danger`, `rounded-pill`.                                                                                          |
| **Shadows**              | `shadow-{size}`                                                                          | Box-shadow sizes: `none`, `sm`, _(default)_, `lg`. Examples: `shadow-sm`, `shadow-lg`, `shadow-none`.                                                                                                                                                                                           |
| **Position**             | `position-{type}` / `{side}-{value}`                                                     | Types: `static`, `relative`, `absolute`, `fixed`, `sticky`. Sides: `top`, `bottom`, `start`, `end`. Values: `0`, `50`, `100`. Examples: `position-absolute`, `top-0`, `start-50`, `translate-middle`.                                                                                           |
| **Visibility & Opacity** | `visible` / `invisible` / `opacity-{value}`                                              | Visibility: `visible`, `invisible`. Opacity values: `0`, `25`, `50`, `75`, `100`. Examples: `invisible`, `opacity-75`.                                                                                                                                                                          |
| **Overflow**             | `overflow-{type}`                                                                        | Types: `auto`, `hidden`, `visible`, `scroll`. Examples: `overflow-auto`, `overflow-hidden`.                                                                                                                                                                                                     |
| **Interactions**         | `user-select-{value}` / `pe-{value}`                                                     | User select: `all`, `auto`, `none`. Pointer events: `none`, `auto`. Examples: `user-select-none`, `pe-none`.                                                                                                                                                                                    |
| **Vertical Alignment**   | `align-{value}`                                                                          | Inline alignment values: `baseline`, `top`, `middle`, `bottom`, `text-top`, `text-bottom`. Examples: `align-middle`, `align-bottom`.                                                                                                                                                            |
| **Object Fit**           | `object-fit-{value}`                                                                     | For replaced content (images, video): `contain`, `cover`, `fill`, `none`, `scale-down`. Example: `object-fit-cover`.                                                                                                                                                                            |

---

### Key Concept: Responsive Breakpoints

Most utility classes can be made responsive by inserting a breakpoint abbreviation.

| Breakpoint        | Abbreviation | Screen Width |
| :---------------- | :----------- | :----------- |
| Small             | `sm`         | ≥576px       |
| Medium            | `md`         | ≥768px       |
| Large             | `lg`         | ≥992px       |
| Extra Large       | `xl`         | ≥1200px      |
| Extra Extra Large | `xxl`        | ≥1400px      |

**Format:** `{utility}-{breakpoint}-{value}`

**Examples:**

- `text-md-center` (Center text on medium screens and up)
- `mb-lg-0` (Remove margin-bottom on large screens and up)
- `d-xl-none` (Hide element on extra large screens and up)

---

|     |
| --- |

---

# MySQL Syntax & Schema Reference

### Database Operations

| Category     | Syntax                     | Description         |
| ------------ | -------------------------- | ------------------- |
| **Database** | `CREATE DATABASE db_name;` | Create new database |
|              | `USE db_name;`             | Switch to database  |
|              | `DROP DATABASE db_name;`   | Delete database     |
|              | `SHOW DATABASES;`          | List all databases  |

### Table Operations

| Category         | Syntax                                                                                                                                                                                                                                      | Description                               |
| ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------- |
| **Create Table** | `sql<br>CREATE TABLE table_name (<br>    id INT AUTO_INCREMENT PRIMARY KEY,<br>    name VARCHAR(100) NOT NULL,<br>    email VARCHAR(255) UNIQUE,<br>    age INT DEFAULT 0,<br>    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP<br>);<br>` | Create table with columns and constraints |
| **Modify Table** | `ALTER TABLE table_name ADD column_name DATA_TYPE;`                                                                                                                                                                                         | Add new column                            |
|                  | `ALTER TABLE table_name DROP COLUMN column_name;`                                                                                                                                                                                           | Remove column                             |
|                  | `ALTER TABLE table_name MODIFY column_name NEW_DATA_TYPE;`                                                                                                                                                                                  | Change column type                        |
|                  | `DROP TABLE table_name;`                                                                                                                                                                                                                    | Delete table                              |
|                  | `TRUNCATE TABLE table_name;`                                                                                                                                                                                                                | Remove all data from table                |
| **Table Info**   | `SHOW TABLES;`                                                                                                                                                                                                                              | List all tables                           |
|                  | `DESCRIBE table_name;` or `DESC table_name;`                                                                                                                                                                                                | Show table structure                      |

### Data Types

| Category      | Data Types                                          | Description                   |
| ------------- | --------------------------------------------------- | ----------------------------- |
| **Numeric**   | `INT`, `TINYINT`, `SMALLINT`, `MEDIUMINT`, `BIGINT` | Integer types                 |
|               | `DECIMAL(10,2)`, `FLOAT`, `DOUBLE`                  | Floating-point types          |
| **String**    | `CHAR(n)`, `VARCHAR(n)`                             | Fixed/variable length strings |
|               | `TEXT`, `TINYTEXT`, `MEDIUMTEXT`, `LONGTEXT`        | Large text storage            |
|               | `ENUM('val1', 'val2')`                              | Predefined list of values     |
| **Date/Time** | `DATE`, `TIME`, `DATETIME`, `TIMESTAMP`, `YEAR`     | Date and time types           |
| **Binary**    | `BLOB`, `TINYBLOB`, `MEDIUMBLOB`, `LONGBLOB`        | Binary large objects          |

### Constraints

| Constraint         | Syntax                                    | Description                            |
| ------------------ | ----------------------------------------- | -------------------------------------- |
| **Primary Key**    | `PRIMARY KEY`                             | Uniquely identifies each record        |
| **Foreign Key**    | `FOREIGN KEY (col) REFERENCES table(col)` | Links to another table's primary key   |
| **Unique**         | `UNIQUE`                                  | All values must be unique              |
| **Not Null**       | `NOT NULL`                                | Column cannot contain NULL values      |
| **Check**          | `CHECK (condition)`                       | Validates data against condition       |
| **Default**        | `DEFAULT value`                           | Sets default value                     |
| **Auto Increment** | `AUTO_INCREMENT`                          | Automatically increments integer value |

### CRUD Operations

| Operation  | Syntax                                                | Description               |
| ---------- | ----------------------------------------------------- | ------------------------- |
| **INSERT** | `INSERT INTO table (col1, col2) VALUES (val1, val2);` | Add new records           |
|            | `INSERT INTO table VALUES (val1, val2, val3);`        | Add records (all columns) |
| **SELECT** | `SELECT col1, col2 FROM table;`                       | Retrieve specific columns |
|            | `SELECT * FROM table;`                                | Retrieve all columns      |
|            | `SELECT DISTINCT col FROM table;`                     | Retrieve unique values    |
| **UPDATE** | `UPDATE table SET col1 = val1 WHERE condition;`       | Modify existing records   |
| **DELETE** | `DELETE FROM table WHERE condition;`                  | Remove records            |

### SELECT Clauses & Keywords

| Clause       | Syntax                                                              | Description              |
| ------------ | ------------------------------------------------------------------- | ------------------------ |
| **WHERE**    | `SELECT * FROM table WHERE condition;`                              | Filter records           |
| **ORDER BY** | `SELECT * FROM table ORDER BY col ASC/DESC;`                        | Sort results             |
| **LIMIT**    | `SELECT * FROM table LIMIT 10;`                                     | Limit number of results  |
| **GROUP BY** | `SELECT col, COUNT(*) FROM table GROUP BY col;`                     | Group rows by column     |
| **HAVING**   | `SELECT col, COUNT(*) FROM table GROUP BY col HAVING COUNT(*) > 5;` | Filter grouped results   |
| **JOIN**     | `SELECT * FROM table1 JOIN table2 ON table1.id = table2.table1_id;` | Combine rows from tables |

### JOIN Types

| JOIN Type           | Syntax                                                  | Description                                   |
| ------------------- | ------------------------------------------------------- | --------------------------------------------- |
| **INNER JOIN**      | `SELECT * FROM t1 INNER JOIN t2 ON t1.id = t2.id;`      | Returns matching rows from both tables        |
| **LEFT JOIN**       | `SELECT * FROM t1 LEFT JOIN t2 ON t1.id = t2.id;`       | All rows from left table, matches from right  |
| **RIGHT JOIN**      | `SELECT * FROM t1 RIGHT JOIN t2 ON t1.id = t2.id;`      | All rows from right table, matches from left  |
| **FULL OUTER JOIN** | `SELECT * FROM t1 FULL OUTER JOIN t2 ON t1.id = t2.id;` | All rows when there's a match in either table |

### Aggregate Functions

| Function         | Syntax                                                       | Description                     |
| ---------------- | ------------------------------------------------------------ | ------------------------------- |
| **COUNT**        | `SELECT COUNT(*) FROM table;`                                | Count number of rows            |
| **SUM**          | `SELECT SUM(column) FROM table;`                             | Calculate sum of values         |
| **AVG**          | `SELECT AVG(column) FROM table;`                             | Calculate average value         |
| **MIN/MAX**      | `SELECT MIN(column), MAX(column) FROM table;`                | Find minimum/maximum values     |
| **GROUP_CONCAT** | `SELECT GROUP_CONCAT(column) FROM table GROUP BY group_col;` | Concatenate values from a group |

### Indexes

| Operation           | Syntax                                                   | Description               |
| ------------------- | -------------------------------------------------------- | ------------------------- |
| **Create Index**    | `CREATE INDEX index_name ON table_name (column);`        | Create standard index     |
| **Unique Index**    | `CREATE UNIQUE INDEX index_name ON table_name (column);` | Create unique index       |
| **Composite Index** | `CREATE INDEX index_name ON table_name (col1, col2);`    | Index on multiple columns |
| **Drop Index**      | `DROP INDEX index_name ON table_name;`                   | Remove index              |

### Views

| Operation       | Syntax                                                                | Description             |
| --------------- | --------------------------------------------------------------------- | ----------------------- |
| **Create View** | `CREATE VIEW view_name AS SELECT columns FROM table WHERE condition;` | Create virtual table    |
| **Use View**    | `SELECT * FROM view_name;`                                            | Query view like a table |
| **Drop View**   | `DROP VIEW view_name;`                                                | Remove view             |

### Common Functions

| Category      | Function Examples                                                              | Description              |
| ------------- | ------------------------------------------------------------------------------ | ------------------------ |
| **String**    | `CONCAT()`, `SUBSTRING()`, `UPPER()`, `LOWER()`, `LENGTH()`, `TRIM()`          | String manipulation      |
| **Numeric**   | `ROUND()`, `CEIL()`, `FLOOR()`, `ABS()`, `POWER()`, `SQRT()`                   | Mathematical operations  |
| **Date/Time** | `NOW()`, `CURDATE()`, `CURTIME()`, `DATE_FORMAT()`, `DATEDIFF()`, `DATE_ADD()` | Date and time operations |

### Transactions

| Command       | Syntax                      | Description                      |
| ------------- | --------------------------- | -------------------------------- |
| **Start**     | `START TRANSACTION;`        | Begin transaction                |
| **Commit**    | `COMMIT;`                   | Save changes permanently         |
| **Rollback**  | `ROLLBACK;`                 | Undo changes in transaction      |
| **Savepoint** | `SAVEPOINT savepoint_name;` | Set savepoint within transaction |

### User & Privileges

| Operation             | Syntax                                                    | Description           |
| --------------------- | --------------------------------------------------------- | --------------------- |
| **Create User**       | `CREATE USER 'username'@'host' IDENTIFIED BY 'password';` | Create new user       |
| **Grant Privileges**  | `GRANT privilege ON database.table TO 'user'@'host';`     | Assign permissions    |
| **Revoke Privileges** | `REVOKE privilege ON database.table FROM 'user'@'host';`  | Remove permissions    |
| **Show Privileges**   | `SHOW GRANTS FOR 'user'@'host';`                          | View user permissions |

### Example: Complete Schema Creation

```sql
-- Create database
CREATE DATABASE company;
USE company;

-- Create tables
CREATE TABLE departments (
    dept_id INT AUTO_INCREMENT PRIMARY KEY,
    dept_name VARCHAR(100) NOT NULL UNIQUE,
    budget DECIMAL(12,2) DEFAULT 0.00
);

CREATE TABLE employees (
    emp_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    salary DECIMAL(10,2) CHECK (salary >= 0),
    dept_id INT,
    hire_date DATE DEFAULT (CURDATE()),
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE SET NULL
);

-- Create indexes
CREATE INDEX idx_employee_name ON employees (last_name, first_name);
CREATE INDEX idx_employee_dept ON employees (dept_id);

-- Create view
CREATE VIEW employee_details AS
SELECT e.emp_id, CONCAT(e.first_name, ' ', e.last_name) AS full_name,
       e.email, d.dept_name, e.salary
FROM employees e
LEFT JOIN departments d ON e.dept_id = d.dept_id;
```

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
