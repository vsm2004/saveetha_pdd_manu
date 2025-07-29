<?php
require 'config.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'] ?? '';
    $source_address = trim($_POST['current_location'] ?? '');
    $destination_address = trim($_POST['destination'] ?? '');

    if (empty($user_id) || empty($source_address) || empty($destination_address)) {
        echo json_encode([
            "status" => false,
            "message" => "All fields are required."
        ]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO locations (user_id, current_location, destination) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $source_address, $destination_address);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => true,
            "message" => "Location data saved successfully."
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Error saving data: " . $stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        "status" => false,
        "message" => "Invalid request method."
    ]);
}
?>
