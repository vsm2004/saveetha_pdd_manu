<?php
require 'config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$user_id = $input['user_id'] ?? '';
$feedback = $input['feedback'] ?? '';

if (empty($user_id) || empty($feedback)) {
    echo json_encode([
        "status" => false,
        "message" => "User ID and feedback are required."
    ]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO feedback (user_id, feedback_text) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $feedback);

if ($stmt->execute()) {
    echo json_encode([
        "status" => true,
        "message" => "Thanks for your feedback!"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Failed to submit feedback."
    ]);
}

$stmt->close();
$conn->close();
?>
