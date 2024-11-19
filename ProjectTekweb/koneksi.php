<?php
// Create a new database connection
$conn = new mysqli("localhost", "root", "", "project_tekweb");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
