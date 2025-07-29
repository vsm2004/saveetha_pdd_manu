<?php
include 'config.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if email already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Email already registered.";
    exit;
}

// Insert new user with hashed password
$insert = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$insert->bind_param("sss", $name, $email, $hashed_password);

if ($insert->execute()) {
    echo "Registration successful.";
} else {
    echo "Error during registration.";
}

$check->close();
$insert->close();
$conn->close();
?>