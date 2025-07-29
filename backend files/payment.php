<?php
require 'config.php';
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get and sanitize input
$input = json_decode(file_get_contents("php://input"), true);

$booking_id = isset($input['booking_id']) ? intval($input['booking_id']) : null;
$user_id    = isset($input['user_id']) ? intval($input['user_id']) : null;
$amount     = isset($input['amount']) ? floatval($input['amount']) : null;
$method     = isset($input['method']) ? trim($input['method']) : null;
$status     = "Success";

// Validate input
if (!$booking_id || !$user_id || !$amount || !$method) {
    echo json_encode([
        "status" => false,
        "message" => "All payment fields are required."
    ]);
    exit;
}

// Prepare and execute statement
$stmt = $conn->prepare("INSERT INTO payments (booking_id, user_id, amount, method, status) VALUES (?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode([
        "status" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("iidss", $booking_id, $user_id, $amount, $method, $status);

if ($stmt->execute()) {
    echo json_encode([
        "status" => true,
        "message" => "Payment successful.",
        "transaction_id" => $stmt->insert_id
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Execution failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
