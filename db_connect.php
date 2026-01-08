<?php
$servername = "sql208.infinityfree.com";
$username = "if0_40835285";
$password = "j2fwl0GSnClb7C";
$dbname = "if0_40835285_autopulse_db";  // replace XXX with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // Uncomment for testing connection
?>
