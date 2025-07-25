<?php
require 'config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$booking_id   = $input['booking_id'] ?? '';
$user_id      = $input['user_id'] ?? '';
$amount       = $input['amount'] ?? '';
$method       = $input['method'] ?? ''; // e.g., "UPI", "Credit Card", etc.
$status       = "Success"; // For now, assume all payments succeed

if (empty($booking_id) || empty($user_id) || empty($amount) || empty($method)) {
    echo json_encode([
        "status" => false,
        "message" => "All payment fields are required."
    ]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO payments (booking_id, user_id, amount, method, status) VALUES (?, ?, ?, ?, ?)");
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
        "message" => "Payment failed."
    ]);
}

$stmt->close();
$conn->close();
?>
