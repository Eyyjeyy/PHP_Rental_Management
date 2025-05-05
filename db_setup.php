<?php
// Database connection settings
$host = 'localhost';
$dbname = 'project_rental_db';
$username = 'root';
$password = '';
$sqlFile = 'project_rental_db.sql';

// Create a new MySQLi instance to connect to the server (not a specific database)
$mysqli = new mysqli($host, $username, $password);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the database exists
$dbExists = $mysqli->query("SHOW DATABASES LIKE '$dbname'");
if ($dbExists->num_rows == 0) {
    // Database does not exist, create it
    if ($mysqli->query("CREATE DATABASE `$dbname`")) {
        $_SESSION['dbmessage'] = "Database `$dbname` created successfully.";
        // echo "Database `$dbname` created successfully.<br>";
    } else {
        die("Error creating database: " . $mysqli->error);
    }
} else {
    return;
}

// Connect to the newly created or existing database
$mysqli->select_db($dbname);

// Read the .sql file
$sql = file_get_contents($sqlFile);

// Split the SQL statements
$sqlStatements = explode(';', $sql);

// Execute each statement
foreach ($sqlStatements as $statement) {
    $statement = trim($statement);
    if ($statement) {
        if (!$mysqli->query($statement)) {
            echo "Error executing statement: " . $mysqli->error;
        }
    }
}

// echo "Database setup completed successfully.";

$mysqli->close();
?>
