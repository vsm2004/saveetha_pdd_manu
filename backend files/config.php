<?php
$servername = "localhost";
$username = "root";        // Default for XAMPP
$password = "";            // Default for XAMPP (empty password)
$database = "manusaipdd"; // Make sure this DB exists in phpMyAdmin

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
